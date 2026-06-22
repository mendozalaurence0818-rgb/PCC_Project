<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";

function generateInstitutionalEmail($firstName, $lastName)
{
    $clean_first = strtolower(preg_replace('/[^A-Za-z0-9]/', '', str_replace(' ', '', $firstName)));
    $clean_last = strtolower(preg_replace('/[^A-Za-z0-9]/', '', str_replace(' ', '', $lastName)));
    return $clean_last . '.' . $clean_first . '@pcc.edu.ph';
}

if (isset($_GET['edit_id'])) {
    $state_id = trim($_GET['edit_id']);
    try {
        $check_stmt = $conn->prepare("UPDATE applicants SET application_status = 'Under Review' WHERE application_id = :id AND application_status = 'Pending'");
        $check_stmt->execute([':id' => $state_id]);
    } catch (PDOException $e) {
    }
}

if (isset($_GET['delete_id'])) {
    $target_id = trim($_GET['delete_id']);
    try {
        $del_stmt = $conn->prepare("DELETE FROM applicants WHERE application_id = :id");
        $del_stmt->execute([':id' => $target_id]);
        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-danger border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-trash3-fill me-2'></i>Applicant registration target <strong>#{$target_id}</strong> dropped from log arrays.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
    } catch (PDOException $e) {
        $toast_notification = "<script>alert('Deletion Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

if (isset($_GET['accept_id'])) {
    $accept_id = trim($_GET['accept_id']);
    try {
        $conn->beginTransaction();

        // 1. Fetch Applicant Details
        $stmt = $conn->prepare("SELECT * FROM applicants WHERE application_id = :id");
        $stmt->execute([':id' => $accept_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($app) {
            $computed_email = generateInstitutionalEmail($app['first_name'], $app['last_name']);

            $current_year = date('Y');
            $count_stmt = $conn->query("SELECT COUNT(*) FROM students");
            $next_sequence = $count_stmt->fetchColumn() + 1;
            $generated_student_no = $current_year . '-' . str_pad($next_sequence, 5, '0', STR_PAD_LEFT);

            $pwd_hash = password_hash('student123', PASSWORD_DEFAULT);
            $ins_student = $conn->prepare("INSERT INTO students 
                (student_number, password_hash, application_id, first_name, last_name, email, current_course, year_level, classification, enrollment_status) 
                VALUES (:num, :pwd, :app_id, :first, :last, :email, :course, 1, 'Regular', 'Not Enrolled')");

            $ins_student->execute([
                ':num' => $generated_student_no,
                ':pwd' => $pwd_hash,
                ':app_id' => $accept_id,
                ':first' => $app['first_name'],
                ':last' => $app['last_name'],
                ':email' => $computed_email, // Assigned institutional account layout destination profile
                ':course' => $app['preferred_program']
            ]);

            // Form data updates logic: Saved personal email unchanged inside tracking logs table configuration arrays.
            $upd_stmt = $conn->prepare("UPDATE applicants SET application_status = 'Approved', student_number = :student_no WHERE application_id = :id");
            $upd_stmt->execute([
                ':student_no' => $generated_student_no,
                ':id' => $accept_id
            ]);

            $log_msg = "Approved Admission for " . $app['first_name'] . " " . $app['last_name'] . ". Account created: " . $generated_student_no;
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, student_id, module_tab, custom_message) VALUES (:admin_id, :student_id, 'ADMISSIONS', :msg)");
            $log_stmt->execute([':admin_id' => $_SESSION['admin_id'], ':student_id' => $accept_id, ':msg' => $log_msg]);

            $conn->commit();
            $toast_notification = "<div class='toast show align-items-center text-white bg-success border-0 shadow position-fixed bottom-0 end-0 m-3 z-3' role='alert'><div class='d-flex'><div class='toast-body'>Approved! Student <strong>{$generated_student_no}</strong> created.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div>";
        } else {
            $conn->rollBack();
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<script>alert('Approval Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

if (isset($_GET['reject_id'])) {
    $reject_id = trim($_GET['reject_id']);
    try {
        $conn->beginTransaction();

        $upd_stmt = $conn->prepare("UPDATE applicants SET application_status = 'Rejected' WHERE application_id = :id");
        $upd_stmt->execute([':id' => $reject_id]);

        $log_msg = "Declined the Application of {student_name} .";
        $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, student_id, module_tab, custom_message) VALUES (:admin_id, :student_id, 'ADMISSIONS', :msg)");
        $log_stmt->execute([
            ':admin_id' => $_SESSION['admin_id'],
            ':student_id' => $reject_id,
            ':msg' => $log_msg
        ]);

        $conn->commit();
        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-danger border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-x-circle-fill me-2'></i>Application record <strong>#{$reject_id}</strong> set to Rejected status.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<script>alert('Rejection Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

$edit_mode = false;
$selected_applicant = null;
$edit_id = $_GET['edit_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_applicant'])) {
    try {
        $conn->beginTransaction();

        $post_applicant_id = $_POST['applicant_id'];
        $current_status_stmt = $conn->prepare("SELECT application_status FROM applicants WHERE application_id = :id");
        $current_status_stmt->execute([':id' => $post_applicant_id]);
        $current_status = $current_status_stmt->fetchColumn();

        $target_status = $_POST['application_status'];
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email_address = trim($_POST['email_address']);

        $upd_stmt = $conn->prepare("UPDATE applicants SET 
            first_name = :first_name, 
            middle_name = :middle_name, 
            last_name = :last_name, 
            suffix = :suffix,
            classification = :classification, 
            date_of_birth = :date_of_birth,
            gender = :gender,
            civil_status = :civil_status,
            nationality = :nationality,
            religious_affiliation = :religious_affiliation,
            email_address = :email_address,
            mobile_number = :mobile_number,
            address_region = :address_region,
            address_province = :address_province,
            address_city = :address_city,
            address_barangay = :address_barangay,
            address_street = :address_street,
            address_postal = :address_postal,
            preferred_program = :program,
            application_status = :application_status
            WHERE application_id = :id");

        $upd_stmt->execute([
            ':first_name' => $first_name,
            ':middle_name' => !empty($_POST['middle_name']) ? trim($_POST['middle_name']) : null,
            ':last_name' => $last_name,
            ':suffix' => !empty($_POST['suffix']) ? trim($_POST['suffix']) : null,
            ':classification' => strtolower(trim($_POST['classification'])),
            ':date_of_birth' => $_POST['date_of_birth'],
            ':gender' => $_POST['gender'],
            ':civil_status' => $_POST['civil_status'],
            ':nationality' => trim($_POST['nationality']),
            ':religious_affiliation' => !empty($_POST['religious_affiliation']) ? trim($_POST['religious_affiliation']) : null,
            ':email_address' => $email_address,
            ':mobile_number' => trim($_POST['mobile_number']),
            ':address_region' => trim($_POST['address_region']),
            ':address_province' => trim($_POST['address_province']),
            ':address_city' => trim($_POST['address_city']),
            ':address_barangay' => trim($_POST['address_barangay']),
            ':address_street' => trim($_POST['address_street']),
            ':address_postal' => !empty($_POST['address_postal']) ? trim($_POST['address_postal']) : null,
            ':program' => $_POST['program'],
            ':application_status' => $target_status,
            ':id' => $post_applicant_id
        ]);

        if (!empty($_POST['guardian_id'])) {
            $g_stmt = $conn->prepare("UPDATE guardians SET 
                full_name = :full_name, 
                relationship = :relationship, 
                emergency_phone = :emergency_phone 
                WHERE guardian_id = :g_id");
            $g_stmt->execute([
                ':full_name' => trim($_POST['guardian_name']),
                ':relationship' => trim($_POST['guardian_relationship']),
                ':emergency_phone' => trim($_POST['emergency_phone']),
                ':g_id' => $_POST['guardian_id']
            ]);
        }

        $school_attended_input = isset($_POST['shs_school_name']) ? trim($_POST['shs_school_name']) : (isset($_POST['shs_school_attended']) ? trim($_POST['shs_school_attended']) : '');

        $ab_stmt = $conn->prepare("UPDATE academic_backgrounds SET 
            shs_school_attended = :school, 
            shs_strand = :strand, 
            shs_year_graduated = :year_grad, 
            shs_school_address = :school_addr 
            WHERE application_id = :id");
        $ab_stmt->execute([
            ':school' => $school_attended_input,
            ':strand' => $_POST['shs_strand'] ?? 'STEM',
            ':year_grad' => intval($_POST['shs_year_graduated'] ?? 0),
            ':school_addr' => trim($_POST['shs_school_address'] ?? ''),
            ':id' => $post_applicant_id
        ]);

        $conn->commit();

        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-success border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Applicant configurations saved dynamically.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
        $edit_mode = false;
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='alert alert-danger m-3'>Modification Error: " . $e->getMessage() . "</div>";
    }
}
$total_applications = 0;
$new_student_count = 0;
$transferee_count = 0;
$new_admissions = 0;

try {
    $total_applications = $conn->query("SELECT COUNT(*) FROM applicants")->fetchColumn();
    $new_student_count = $conn->query("SELECT COUNT(*) FROM applicants WHERE classification = 'freshman'")->fetchColumn();
    $transferee_count = $conn->query("SELECT COUNT(*) FROM applicants WHERE classification = 'transferee'")->fetchColumn();
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
}

$applicant_list = [];
try {
    $search_query = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : '%';
    $fetch_stmt = $conn->prepare("SELECT a.*, 
                                       g.full_name AS guardian_name, g.relationship AS guardian_relationship, g.emergency_phone,
                                       ab.shs_school_attended, ab.shs_strand, ab.shs_year_graduated, ab.shs_school_address
                                FROM applicants a
                                LEFT JOIN guardians g ON a.guardian_id = g.guardian_id
                                LEFT JOIN academic_backgrounds ab ON a.application_id = ab.application_id
                                WHERE a.reference_number LIKE :search 
                                   OR a.first_name LIKE :search 
                                   OR a.last_name LIKE :search 
                                ORDER BY a.application_id DESC");
    $fetch_stmt->execute([':search' => $search_query]);

    while ($row = $fetch_stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($edit_id && (int) $row['application_id'] === (int) $edit_id) {
            $selected_applicant = $row;
            $edit_mode = true;
        }
        $applicant_list[] = [
            'applicant_id' => $row['application_id'],
            'reference_no' => $row['reference_number'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'classification' => (strtolower($row['classification'] ?? 'freshman') === 'freshman') ? 'New Student' : 'Transferee',
            'program' => $row['preferred_program'] ?? '',
            'year' => $row['school_year'] ?? '',
            'status' => $row['application_status'] ?? 'Pending'
        ];
    }
} catch (PDOException $e) {
    $applicant_list = [];
}

$current_semester = "1st Semester, AY 2026-2027";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admissions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-dark-blue: #001d3d;
        }

        .sidebar-bg {
            background-color: var(--pcc-blue) !important;
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        .sidebar-bg .nav-link,
        .sidebar-bg .brand-link,
        .sidebar-bg .nav-header {
            color: #ffffff !important;
        }

        .sidebar-bg-active {
            color: var(--pcc-blue) !important;
            background-color: var(--pcc-gold) !important;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .avatar-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            background-color: var(--pcc-dark-blue);
        }

        .user-info .username {
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-semester-text {
            color: #adb5bd;
            font-size: 11px;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }

        .tab-indicator {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
        }

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .document-thumbnail-frame {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background-color: #f8f9fa;
            padding: 12px;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }

        .document-thumbnail-frame:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-color: var(--pcc-blue);
        }

        .clickable-header {
            cursor: pointer;
            user-select: none;
        }

        .clickable-header:hover {
            background-color: rgba(0, 44, 94, 0.04);
            color: var(--pcc-blue) !important;
        }

        @media (min-width: 992px) {
            .sidebar-collapse .app-sidebar {
                margin-left: -250px !important;
            }

            .sidebar-collapse .app-main,
            .sidebar-collapse .app-footer,
            .sidebar-collapse .app-header {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php echo $toast_notification; ?>

    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body px-1">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)" role="button"><i
                                class="bi bi-list fs-5"></i></a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-md-inline">
                            <div class="nav-date" id="liveClockDisplay">Loading Central Server Time...</div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand"
                style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="dashboard.php" class="brand-link">
                    <img src="../assets/images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Admin</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                        </div>
                        <div class="user-info">
                            <div class="username">
                                <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin Account'); ?>
                            </div>
                            <div class="status-text small" style="color: #35e400;">Online</div>
                            <span class="sidebar-semester-text"><?php echo $current_semester; ?></span>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header">MAIN MENU</li>
                        <li class="nav-item"><a href="dashboard.php" class="nav-link"><i
                                    class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="students.php" class="nav-link"><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students</p>
                            </a></li>
                        <li class="nav-item"><a href="admissions.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions <span id="admissionsBadge"
                                        class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                        style="background-color: white"><?php echo $new_admissions; ?></span></p>
                            </a></li>
                        <li class="nav-item"><a href="verify_enrollment.php" class="nav-link"><i
                                    class="nav-icon bi bi-shield-check"></i>
                                <p>Enrollment</p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs</p>
                            </a></li>
                        <li class="nav-item"><a href="subjects.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="drop_requests.php" class="nav-link"><i
                                    class="nav-icon bi bi-file-earmark-minus-fill"></i>
                                <p>Drop Requests</p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules</p>
                            </a></li>
                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item"><a href="notice.php" class="nav-link"><i
                                    class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice</p>
                            </a></li>
                        <li class="nav-item"><a href="users.php" class="nav-link"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users</p>
                            </a></li>
                        <li class="nav-item"><a href="settings.php" class="nav-link"><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings</p>
                            </a></li>
                        <li class="nav-item">
                            <a href="../index.php" class="nav-link text-danger-emphasis"
                                onclick="return confirm('Are you sure you want to end your snapshot session?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p class="text-danger font-bold">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0 mt-3 fw-bold text-dark">Admission Management</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content mt-3">
                <div class="container-fluid">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-file-earmark-text-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Total
                                        Applications</span>
                                    <h4 class="fw-bold mb-0 text-dark"><?php echo number_format($total_applications); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-person-badge-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">New
                                        Applicants</span>
                                    <h4 class="fw-bold mb-0 text-success">
                                        <?php echo number_format($new_student_count); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-arrow-left-right"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Transferees</span>
                                    <h4 class="fw-bold mb-0 text-warning">
                                        <?php echo number_format($transferee_count); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-danger text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-clock-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Pending
                                        Review</span>
                                    <h4 class="fw-bold mb-0 text-danger"><?php echo number_format($new_admissions); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($edit_mode && $selected_applicant): ?>
                        <?php $is_finalized = (($selected_applicant['application_status'] ?? '') === 'Approved' || ($selected_applicant['application_status'] ?? '') === 'Enrolled'); ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm mb-4 bg-white">
                                <div
                                    class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-sliders me-2"></i>Edit /
                                        Manage Profile: Reference
                                        #<?php echo htmlspecialchars($selected_applicant['reference_number'] ?? ''); ?></h5>
                                    <a href="admissions.php" class="btn-close" aria-label="Close"></a>
                                </div>
                                <form method="POST" action="admissions.php">
                                    <div class="card-body text-dark">
                                        <input type="hidden" name="applicant_id"
                                            value="<?php echo htmlspecialchars($selected_applicant['application_id'] ?? ''); ?>">
                                        <input type="hidden" name="guardian_id"
                                            value="<?php echo htmlspecialchars($selected_applicant['guardian_id'] ?? ''); ?>">

                                        <?php if (($selected_applicant['application_status'] ?? '') === 'Rejected'): ?>
                                            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                                <div>This academic file index data is marked under a <strong>Rejected</strong>
                                                    status assignment.</div>
                                            </div>
                                        <?php else: ?>
                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-person-bounding-box me-2"></i>1. Legal Identity Credentials
                                            </h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">First Name</label>
                                                    <input type="text" name="first_name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['first_name'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Middle Name</label>
                                                    <input type="text" name="middle_name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['middle_name'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?>>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Last Name</label>
                                                    <input type="text" name="last_name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['last_name'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Suffix</label>
                                                    <input type="text" name="suffix"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="e.g. Jr., III"
                                                        value="<?php echo htmlspecialchars($selected_applicant['suffix'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?>>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-calendar-event me-2"></i>2. Personal Demographics</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Date of Birth</label>
                                                    <input type="date" name="date_of_birth"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo $selected_applicant['date_of_birth'] ?? ''; ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Gender</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text" class="form-control form-control-sm border shadow-sm"
                                                            value="<?php echo $selected_applicant['gender'] ?? ''; ?>" readonly>
                                                        <input type="hidden" name="gender"
                                                            value="<?php echo $selected_applicant['gender'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="gender" class="form-select form-select-sm border shadow-sm">
                                                            <option value="Male" <?php echo ($selected_applicant['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                                            <option value="Female" <?php echo ($selected_applicant['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Civil Status</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text" class="form-control form-control-sm border shadow-sm"
                                                            value="<?php echo $selected_applicant['civil_status'] ?? ''; ?>"
                                                            readonly>
                                                        <input type="hidden" name="civil_status"
                                                            value="<?php echo $selected_applicant['civil_status'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="civil_status"
                                                            class="form-select form-select-sm border shadow-sm">
                                                            <option value="Single" <?php echo ($selected_applicant['civil_status'] ?? '') === 'Single' ? 'selected' : ''; ?>>Single</option>
                                                            <option value="Married" <?php echo ($selected_applicant['civil_status'] ?? '') === 'Married' ? 'selected' : ''; ?>>Married</option>
                                                            <option value="Widowed" <?php echo ($selected_applicant['civil_status'] ?? '') === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-secondary">Nationality</label>
                                                    <input type="text" name="nationality"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['nationality'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <label class="form-label small fw-bold text-secondary">Religious
                                                        Affiliation</label>
                                                    <input type="text" name="religious_affiliation"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['religious_affiliation'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?>>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-geo-alt-fill me-2"></i>3. Contact & Location Information</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Primary Contact Email
                                                        Address</label>
                                                    <input type="email" name="email_address"
                                                        class="form-control form-control-sm border shadow-sm font-monospace"
                                                        value="<?php echo htmlspecialchars($selected_applicant['email_address'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Mobile Number</label>
                                                    <input type="text" name="mobile_number"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['mobile_number'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Region</label>
                                                    <input type="text" name="address_region"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_region'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Province</label>
                                                    <input type="text" name="address_province"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_province'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">City</label>
                                                    <input type="text" name="address_city"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_city'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Barangay</label>
                                                    <input type="text" name="address_barangay"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_barangay'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Street / Subdivision
                                                        / House No.</label>
                                                    <input type="text" name="address_street"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_street'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold text-secondary">Postal Code</label>
                                                    <input type="text" name="address_postal"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['address_postal'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?>>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-mortarboard-fill me-2"></i>4. Course & Academic Preferences
                                            </h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Admission
                                                        Classification</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text" class="form-control form-control-sm border shadow-sm"
                                                            value="<?php echo (strtolower($selected_applicant['classification'] ?? '') === 'freshman') ? 'New Student' : 'Transferee'; ?>"
                                                            readonly>
                                                        <input type="hidden" name="classification"
                                                            value="<?php echo $selected_applicant['classification'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="classification"
                                                            class="form-select form-select-sm border shadow-sm">
                                                            <option value="freshman" <?php echo strtolower($selected_applicant['classification'] ?? '') === 'freshman' ? 'selected' : ''; ?>>New Student</option>
                                                            <option value="transferee" <?php echo strtolower($selected_applicant['classification'] ?? '') === 'transferee' ? 'selected' : ''; ?>>Transferee</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Preferred Academic
                                                        Program</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text" class="form-control form-control-sm border shadow-sm"
                                                            value="<?php echo $selected_applicant['preferred_program'] ?? ''; ?>"
                                                            readonly>
                                                        <input type="hidden" name="program"
                                                            value="<?php echo $selected_applicant['preferred_program'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="program" class="form-select form-select-sm border shadow-sm"
                                                            required>
                                                            <option value="BSIT" <?php echo ($selected_applicant['preferred_program'] ?? '') === 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                                                            <option value="BSCS" <?php echo ($selected_applicant['preferred_program'] ?? '') === 'BSCS' ? 'selected' : '' ?>>BSCS</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Application Review
                                                        State</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text"
                                                            class="form-control form-control-sm border shadow-sm text-dark fw-bold"
                                                            value="<?php echo $selected_applicant['application_status'] ?? ''; ?>"
                                                            readonly>
                                                        <input type="hidden" name="application_status"
                                                            value="<?php echo $selected_applicant['application_status'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="application_status"
                                                            class="form-select form-select-sm border shadow-sm text-dark fw-bold">
                                                            <option value="Pending" <?php echo ($selected_applicant['application_status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="Under Review" <?php echo ($selected_applicant['application_status'] ?? '') === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                                            <option value="Approved" <?php echo ($selected_applicant['application_status'] ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                                            <option value="Rejected" <?php echo ($selected_applicant['application_status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-telephone-alert-fill me-2"></i>5. Emergency Contact Profile
                                            </h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Contact Full
                                                        Name</label>
                                                    <input type="text" name="guardian_name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['guardian_name'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Relationship</label>
                                                    <input type="text" name="guardian_relationship"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="e.g. Parent, Guardian"
                                                        value="<?php echo htmlspecialchars($selected_applicant['guardian_relationship'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Contact Mobile
                                                        Number</label>
                                                    <input type="text" name="emergency_phone"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['emergency_phone'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-building-fill me-2"></i>6. Senior High School Background</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">School Name</label>
                                                    <input type="text" name="shs_school_name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['shs_school_attended'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Strand</label>
                                                    <?php if ($is_finalized): ?>
                                                        <input type="text" class="form-control form-control-sm border shadow-sm"
                                                            value="<?php echo $selected_applicant['shs_strand'] ?? ''; ?>" readonly>
                                                        <input type="hidden" name="shs_strand"
                                                            value="<?php echo $selected_applicant['shs_strand'] ?? ''; ?>">
                                                    <?php else: ?>
                                                        <select name="shs_strand"
                                                            class="form-select form-select-sm border shadow-sm" required>
                                                            <option value="STEM" <?php echo ($selected_applicant['shs_strand'] ?? '') === 'STEM' ? 'selected' : ''; ?>>STEM</option>
                                                            <option value="ABM" <?php echo ($selected_applicant['shs_strand'] ?? '') === 'ABM' ? 'selected' : ''; ?>>ABM</option>
                                                            <option value="HUMSS" <?php echo ($selected_applicant['shs_strand'] ?? '') === 'HUMSS' ? 'selected' : ''; ?>>HUMSS</option>
                                                            <option value="GAS" <?php echo ($selected_applicant['shs_strand'] ?? '') === 'GAS' ? 'selected' : ''; ?>>GAS</option>
                                                            <option value="TVL" <?php echo ($selected_applicant['shs_strand'] ?? '') === 'TVL' ? 'selected' : ''; ?>>TVL</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold text-secondary">Year
                                                        Graduated</label>
                                                    <input type="number" name="shs_year_graduated"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['shs_year_graduated'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-secondary">SHS School
                                                        Address</label>
                                                    <input type="text" name="shs_school_address"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?php echo htmlspecialchars($selected_applicant['shs_school_address'] ?? ''); ?>"
                                                        <?= $is_finalized ? 'readonly' : '' ?> required>
                                                </div>
                                            </div>

                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i
                                                    class="bi bi-images me-2"></i>7. Verification Documents Gallery Archive</h6>
                                            <div class="row g-3 mb-2">
                                                <div class="col-sm-6 col-md-3">
                                                    <div class="document-thumbnail-frame">
                                                        <span class="d-block small text-muted fw-bold mb-1">Applicant Photo
                                                            (2x2)</span>
                                                        <?php
                                                        $photo_db = $selected_applicant['applicant_photo_path'] ?? '';
                                                        $photo_path = str_replace('../', '../student/new/', $photo_db);
                                                        if (!empty($photo_db) && file_exists($photo_path)):
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($photo_path); ?>" target="_blank">
                                                                <img src="<?php echo htmlspecialchars($photo_path); ?>"
                                                                    class="img-thumbnail img-fluid mb-1 shadow-sm"
                                                                    style="max-height: 120px; object-fit: cover;"
                                                                    alt="Applicant Photo">
                                                            </a>
                                                            <span class="d-block text-success font-monospace"
                                                                style="font-size:10px;"><i class="bi bi-cloud-check-fill"></i>
                                                                Uploaded</span>
                                                        <?php else: ?>
                                                            <div class="py-4 text-muted"><i
                                                                    class="bi bi-image fs-3 opacity-50"></i><br><span
                                                                    style="font-size:11px;">No Photo Saved</span></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3">
                                                    <div class="document-thumbnail-frame">
                                                        <span class="d-block small text-muted fw-bold mb-1">Grade 12 Card (Form
                                                            138)</span>
                                                        <?php
                                                        $card_db = $selected_applicant['shs_card_path'] ?? '';
                                                        $card_path = str_replace('../', '../student/new/', $card_db);
                                                        if (!empty($card_db) && file_exists($card_path)):
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($card_path); ?>" target="_blank"
                                                                class="btn btn-xs btn-outline-secondary d-block my-3 p-2">
                                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i> View
                                                                Document
                                                            </a>
                                                            <span class="d-block text-success font-monospace"
                                                                style="font-size:10px;"><i class="bi bi-cloud-check-fill"></i>
                                                                Uploaded</span>
                                                        <?php else: ?>
                                                            <div class="py-4 text-muted"><i
                                                                    class="bi bi-file-earmark-x fs-3 opacity-50"></i><br><span
                                                                    style="font-size:11px;">No File Saved</span></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3">
                                                    <div class="document-thumbnail-frame">
                                                        <span class="d-block small text-muted fw-bold mb-1">PSA Birth
                                                            Certificate</span>
                                                        <?php
                                                        $psa_db = $selected_applicant['psa_cert_path'] ?? '';
                                                        $psa_path = str_replace('../', '../student/new/', $psa_db);
                                                        if (!empty($psa_db) && file_exists($psa_path)):
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($psa_path); ?>" target="_blank"
                                                                class="btn btn-xs btn-outline-secondary d-block my-3 p-2">
                                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i> View
                                                                Document
                                                            </a>
                                                            <span class="d-block text-success font-monospace"
                                                                style="font-size:10px;"><i class="bi bi-cloud-check-fill"></i>
                                                                Uploaded</span>
                                                        <?php else: ?>
                                                            <div class="py-4 text-muted"><i
                                                                    class="bi bi-file-earmark-x fs-3 opacity-50"></i><br><span
                                                                    style="font-size:11px;">No File Saved</span></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3">
                                                    <div class="document-thumbnail-frame">
                                                        <span class="d-block small text-muted fw-bold mb-1">Good Moral
                                                            Certification</span>
                                                        <?php
                                                        $moral_db = $selected_applicant['good_moral_path'] ?? '';
                                                        $moral_path = str_replace('../', '../student/new/', $moral_db);
                                                        if (!empty($moral_db) && file_exists($moral_path)):
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($moral_path); ?>" target="_blank"
                                                                class="btn btn-xs btn-outline-secondary d-block my-3 p-2">
                                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i> View
                                                                Document
                                                            </a>
                                                            <span class="d-block text-success font-monospace"
                                                                style="font-size:10px;"><i class="bi bi-cloud-check-fill"></i>
                                                                Uploaded</span>
                                                        <?php else: ?>
                                                            <div class="py-4 text-muted"><i
                                                                    class="bi bi-file-earmark-x fs-3 opacity-50"></i><br><span
                                                                    style="font-size:11px;">No File Saved</span></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="card-footer bg-light d-flex justify-content-between align-items-center py-3 border-top">
                                        <div>
                                            <?php if (($selected_applicant['application_status'] ?? '') === 'Rejected'): ?>
                                                <a href="?delete_id=<?php echo urlencode($selected_applicant['application_id']); ?>"
                                                    class="btn btn-sm btn-danger px-3 shadow-sm"
                                                    onclick="return confirm('Permanently drop row from server indexes?');">
                                                    <i class="bi bi-trash-fill me-1"></i>Drop Log
                                                </a>
                                            <?php elseif (!$is_finalized): ?>
                                                <a href="?accept_id=<?php echo urlencode($selected_applicant['application_id']); ?>"
                                                    class="btn btn-sm btn-success text-white me-2 px-3 shadow-sm"
                                                    onclick="return confirm('Approve entry track parameters?');">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Accept Admission
                                                </a>
                                                <a href="?reject_id=<?php echo urlencode($selected_applicant['application_id']); ?>"
                                                    class="btn btn-sm btn-danger me-2 px-3 shadow-sm"
                                                    onclick="return confirm('Reject applicant execution?');">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Reject Application
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="admissions.php"
                                                class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                            <?php if (!$is_finalized && ($selected_applicant['application_status'] ?? '') !== 'Rejected'): ?>
                                                <button type="submit" name="update_applicant"
                                                    class="btn btn-sm btn-primary px-3 shadow-sm">Save Profile Changes</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <div class="card shadow-sm border border-light-subtle bg-white" style="border-radius: 10px;">
                            <div
                                class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-journal-text me-2 text-primary"></i>Applicant Classification
                                    Records</h5>
                                <div class="card-tools d-flex flex-wrap align-items-center gap-2">
                                    <div
                                        class="d-flex align-items-center gap-1 border-end pe-2 me-1 border-light-subtle flex-wrap">
                                        <select id="filter-status"
                                            class="form-select form-select-sm border shadow-sm text-muted"
                                            style="width: 9rem;">
                                            <option selected>All Status</option>
                                            <option>Pending</option>
                                            <option>Under Review</option>
                                            <option>Approved</option>
                                            <option>Rejected</option>
                                        </select>
                                        <select id="filter-classification"
                                            class="form-select form-select-sm border shadow-sm text-muted"
                                            style="width: 10rem;">
                                            <option selected>All Classifications</option>
                                            <option>New Student</option>
                                            <option>Transferee</option>
                                        </select>
                                    </div>
                                    <form method="GET" action="admissions.php" class="d-flex gap-2">
                                        <div class="input-group input-group-sm border shadow-sm rounded"
                                            style="width: 14rem river;">
                                            <span class="input-group-text bg-light border-0 text-muted"><i
                                                    class="bi bi-search"></i></span>
                                            <input id="table-filter" type="search" name="search"
                                                class="form-control border-0 bg-light"
                                                placeholder="Search Ref No. or Name"
                                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 text-dark">
                                        <thead class="table-light small text-uppercase text-secondary border-bottom">
                                            <tr>
                                                <th class="clickable-header ps-4 py-3 font-weight-bold"
                                                    onclick="sortTable(0)">APP ID <i
                                                        class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                <th class="clickable-header py-3 font-weight-bold"
                                                    onclick="sortTable(1)">Reference No. <i
                                                        class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                <th class="clickable-header py-3 font-weight-bold"
                                                    onclick="sortTable(2)">Full Name <i
                                                        class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                <th>Classification</th>
                                                <th>Course / Program</th>
                                                <th>School Year</th>
                                                <th class="text-center">Status</th>
                                                <th class="pe-4 text-end" style="width: 240px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($applicant_list)): ?>
                                                <tr>
                                                    <td colspan="8" class="py-0">
                                                        <div class="text-center py-5 bg-white">
                                                            <i
                                                                class="bi bi-folder-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                                            <h5 class="fw-bold text-dark">No Active Applicants Located</h5>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($applicant_list as $app): ?>
                                                    <tr
                                                        class="<?php echo ($edit_id == $app['applicant_id']) ? 'table-warning-subtle' : ''; ?>">
                                                        <td class="ps-4 font-monospace small text-secondary">
                                                            #<?php echo $app['applicant_id']; ?></td>
                                                        <td class="font-monospace fw-bold small text-secondary">
                                                            <?php echo htmlspecialchars($app['reference_no']); ?>
                                                        </td>
                                                        <td class="fw-semibold text-dark">
                                                            <?= htmlspecialchars($app['last_name'] . ', ' . $app['first_name']); ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $is_new_student = ($app['classification'] === 'New Student');
                                                            $badge_theme = $is_new_student ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle';
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $badge_theme; ?> tab-indicator"><?php echo htmlspecialchars($app['classification']); ?></span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-secondary-subtle text-secondary-emphasis fw-medium px-2 py-1"><?php echo htmlspecialchars($app['program']); ?></span>
                                                        </td>
                                                        <td class="text-secondary small">
                                                            <?php echo htmlspecialchars($app['year']); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $status = $app['status'];
                                                            $status_badge = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                                            if ($status === 'Approved' || $status === 'Enrolled') {
                                                                $status_badge = 'bg-success-subtle text-success border border-success-subtle';
                                                            } elseif ($status === 'Rejected') {
                                                                $status_badge = 'bg-danger-subtle text-danger border border-danger-subtle';
                                                            } elseif ($status === 'Under Review') {
                                                                $status_badge = 'bg-info-subtle text-info border border-info-subtle';
                                                            }
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $status_badge; ?> tab-indicator d-inline-block w-75 text-center"><?php echo htmlspecialchars($status); ?></span>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_id=<?php echo urlencode($app['applicant_id']); ?>"
                                                                class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-pencil-square me-1"></i>Edit / Manage</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="card-footer bg-white border-top py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="small text-muted font-monospace">Showing
                                    <?php echo count($applicant_list); ?> applications tracked in server scope.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="app-footer px-4 py-3 border-top bg-white small text-muted">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - &copy; 2026</div>
            <strong><span class="float-end">&nbsp;All rights reserved.</span></strong>
            <div class="clearfix"></div>
        </footer>
    </div>

    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        let sortDirections = [true, true, true];
        function sortTable(columnIndex) {
            const table = document.querySelector("table tbody");
            const rows = Array.from(table.querySelectorAll("tr"));
            if (rows.length === 1 && rows[0].cells.length === 1) return;

            const isAscending = sortDirections[columnIndex];
            sortDirections[columnIndex] = !isAscending;

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[columnIndex]?.textContent.trim().toLowerCase() || "";
                let cellB = rowB.cells[columnIndex]?.textContent.trim().toLowerCase() || "";

                if (columnIndex === 0) {
                    cellA = parseInt(cellA.replace('#', '')) || 0;
                    cellB = parseInt(cellB.replace('#', '')) || 0;
                    return isAscending ? cellA - cellB : cellB - cellA;
                }
                return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });
            rows.forEach(row => table.appendChild(row));
        }

        document.addEventListener("DOMContentLoaded", function () {
            runLiveDashboardClock();
            setInterval(runLiveDashboardClock, 1000);

            const searchInput = document.getElementById("table-filter");
            const statusFilter = document.getElementById("filter-status");
            const classificationFilter = document.getElementById("filter-classification");
            const tableRows = document.querySelectorAll("table tbody tr");

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
                const selectedStatus = statusFilter ? statusFilter.value : "All Status";
                const selectedClassification = classificationFilter ? classificationFilter.value : "All Classifications";

                tableRows.forEach(row => {
                    const rowId = row.cells[0]?.textContent.toLowerCase().trim() || "";
                    const referenceNo = row.cells[1]?.textContent.toLowerCase().trim() || "";
                    const studentName = row.cells[2]?.textContent.toLowerCase().trim() || "";
                    const studentClassification = row.cells[3]?.textContent.trim() || "";
                    const studentStatus = row.cells[6]?.textContent.trim() || "";

                    const matchesSearch = rowId.includes(query) || referenceNo.includes(query) || studentName.includes(query);
                    const matchesStatus = selectedStatus === "All Status" || studentStatus === selectedStatus;
                    const matchesClassification = selectedClassification === "All Classifications" || studentClassification === selectedClassification;

                    if (matchesSearch && matchesStatus && matchesClassification) { row.style.display = ""; }
                    else { row.style.display = "none"; }
                });
            }

            if (searchInput) searchInput.addEventListener("input", filterTable);
            if (statusFilter) statusFilter.addEventListener("change", filterTable);
            if (classificationFilter) classificationFilter.addEventListener("change", filterTable);
        });
    </script>
</body>

</html>