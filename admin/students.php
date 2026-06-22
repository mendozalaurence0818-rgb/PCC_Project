<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";
$current_semester = "1st Semester, AY 2026-2027";

if (isset($_GET['delete_id'])) {
    $target_id = trim($_GET['delete_id']);
    try {
        $conn->beginTransaction();

        $del_stmt1 = $conn->prepare("DELETE FROM students WHERE student_number = :student_number");
        $del_stmt1->execute([':student_number' => $target_id]);

        $del_stmt2 = $conn->prepare("DELETE FROM applicants WHERE student_number = :student_number");
        $del_stmt2->execute([':student_number' => $target_id]);

        $log_msg = "Deleted Student profile record for " . $target_id . " from directory.";
        $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'STUDENTS', :msg)");
        $log_stmt->execute([
            ':admin_id' => $_SESSION['admin_id'],
            ':msg' => $log_msg
        ]);

        $conn->commit();
        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-danger border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-trash3-fill me-2'></i>Record <strong>#{$target_id}</strong> successfully dropped from database layers.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<script>alert('Deletion Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

$edit_mode = false;
$add_mode = isset($_GET['add_student']) || (isset($_GET['action']) && $_GET['action'] === 'new');
$selected_student = null;
$edit_id = $_GET['edit_id'] ?? null;

$generated_student_no = "";
if ($add_mode) {
    try {
        $current_year = date('Y');
        $count_stmt = $conn->query("SELECT COUNT(*) FROM students");
        $next_sequence = $count_stmt->fetchColumn() + 1;
        $generated_student_no = $current_year . '-' . str_pad($next_sequence, 5, '0', STR_PAD_LEFT);
    } catch (PDOException $e) {
        $generated_student_no = date('Y') . "-00001";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_new_student'])) {
    try {
        $conn->beginTransaction();

        $raw_name = trim($_POST['name']);
        $name_parts = explode(',', $raw_name);
        $last_name = trim($name_parts[0] ?? '');
        $first_name = trim($name_parts[1] ?? $raw_name);
        $stud_no = trim($_POST['student_no']);

        $default_password_hash = password_hash('student123', PASSWORD_DEFAULT);

        $ins_stmt = $conn->prepare("INSERT INTO students (student_number, password_hash, first_name, last_name, current_course, year_level, classification, enrollment_status, email) 
            VALUES (:student_number, :password_hash, :first_name, :last_name, :current_course, :year_level, :classification, 'Enrolled', :email)");

        $ins_stmt->execute([
            ':student_number' => $stud_no,
            ':password_hash' => $default_password_hash,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':current_course' => $_POST['program'],
            ':year_level' => intval(str_replace(['st', 'nd', 'rd', 'th', ' Year'], '', $_POST['year'])),
            ':classification' => $_POST['classification'],
            ':email' => trim($_POST['email'])
        ]);

        $log_msg = "Registered new Student profile entity into system rosters for " . $stud_no . " .";
        $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'STUDENTS', :msg)");
        $log_stmt->execute([
            ':admin_id' => $_SESSION['admin_id'],
            ':msg' => $log_msg
        ]);

        $conn->commit();
        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-success border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>New student profile successfully stored for <strong>{$stud_no}</strong>.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
        $add_mode = false;
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='alert alert-danger m-3'>Submission Fault Loop: " . $e->getMessage() . "</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    try {
        $conn->beginTransaction();

        $raw_name = trim($_POST['name']);
        $name_parts = explode(',', $raw_name);
        $last_name = trim($name_parts[0] ?? '');
        $first_name = trim($name_parts[1] ?? $raw_name);
        $numeric_year = intval(str_replace(['st', 'nd', 'rd', 'th', ' Year'], '', $_POST['year']));
        $target_classification = $_POST['classification'];
        $target_status = $_POST['account_status'];
        $stud_no = trim($_POST['student_no']);

        $check_exists = $conn->prepare("SELECT student_id FROM students WHERE student_number = :num");
        $check_exists->execute([':num' => $stud_no]);
        $associated_id = $check_exists->fetchColumn();

        if ($associated_id) {
            $upd_stmt = $conn->prepare("UPDATE students SET first_name = :first_name, last_name = :last_name, current_course = :current_course, year_level = :year_level, classification = :classification, enrollment_status = :enrollment_status, email = :email WHERE student_number = :student_number");
            $upd_stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':current_course' => $_POST['program'],
                ':year_level' => $numeric_year,
                ':classification' => $target_classification,
                ':enrollment_status' => $target_status,
                ':email' => trim($_POST['email']),
                ':student_number' => $stud_no
            ]);

            $log_msg = "Updated Student profile data for {student_name} (" . $stud_no . ") .";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, student_id, module_tab, custom_message) VALUES (:admin_id, :student_id, 'STUDENTS', :msg)");
            $log_stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':student_id' => $associated_id,
                ':msg' => $log_msg
            ]);
        }

        $conn->commit();
        $toast_notification = "
        <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
            <div class='toast show align-items-center text-white bg-success border-0 shadow' role='alert'>
                <div class='d-flex'>
                    <div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Modifications cleanly compiled for student profile <strong>{$stud_no}</strong>.</div>
                    <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button>
                </div>
            </div>
        </div>";
        $edit_mode = false;
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='alert alert-danger m-3'>Modification Fault Loop: " . $e->getMessage() . "</div>";
    }
}
$total_students_count = 0;
$total_enrolled_count = 0;
$total_pending_count = 0;
$new_admissions = 0;

try {
    $total_students_count = $conn->query("SELECT (SELECT COUNT(*) FROM students) + (SELECT COUNT(*) FROM applicants WHERE application_status = 'Approved' AND (student_number IS NULL OR student_number NOT IN (SELECT student_number FROM students WHERE student_number IS NOT NULL)))")->fetchColumn();
    $total_enrolled_count = $conn->query("SELECT COUNT(*) FROM students WHERE classification = 'Regular'")->fetchColumn();
    $total_pending_count = $conn->query("SELECT COUNT(*) FROM students WHERE classification = 'Irregular'")->fetchColumn();
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
}

$student_list = [];
try {
    $search_query = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : '%';

    // FIX: Optimized UNION queries using COALESCE to fallback to APP ID string if student number is unset/NULL
    $fetch_stmt = $conn->prepare("
        SELECT student_number, first_name, last_name, current_course, year_level, classification, enrollment_status, email 
        FROM students 
        WHERE student_number LIKE :search OR first_name LIKE :search OR last_name LIKE :search
        
        UNION ALL
        
        SELECT COALESCE(student_number, CONCAT('APP-', LPAD(application_id, 5, '0'))) AS student_number, first_name, last_name, preferred_program AS current_course, 1 AS year_level, 'Regular' AS classification, 'Not Enrolled' AS enrollment_status, email_address AS email
        FROM applicants 
        WHERE application_status = 'Approved' 
          AND (student_number IS NULL OR student_number NOT IN (SELECT student_number FROM students WHERE student_number IS NOT NULL))
          AND (student_number LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR CONCAT('APP-', LPAD(application_id, 5, '0')) LIKE :search)
        
        ORDER BY last_name ASC");

    $fetch_stmt->execute([':search' => $search_query]);

    while ($row = $fetch_stmt->fetch(PDO::FETCH_ASSOC)) {
        $formatted_year = $row['year_level'] . (($row['year_level'] == 1) ? 'st' : (($row['year_level'] == 2) ? 'nd' : (($row['year_level'] == 3) ? 'rd' : 'th'))) . ' Year';

        if ($edit_id && $row['student_number'] === $edit_id) {
            $selected_student = [
                'student_no' => $row['student_number'],
                'name' => $row['last_name'] . ', ' . $row['first_name'],
                'program' => $row['current_course'],
                'year' => $formatted_year,
                'classification' => $row['classification'],
                'status' => $row['enrollment_status'],
                'email' => $row['email']
            ];
            $edit_mode = true;
        }

        $student_list[] = [
            'student_no' => $row['student_number'],
            'name' => $row['last_name'] . ', ' . $row['first_name'],
            'program' => $row['current_course'],
            'year' => $formatted_year,
            'classification' => $row['classification'],
            'status' => $row['enrollment_status']
        ];
    }
} catch (PDOException $e) {
    $student_list = [];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Directory</title>
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
                        <li class="nav-item"><a href="students.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students</p>
                            </a></li>
                        <li class="nav-item"><a href="admissions.php" class="nav-link "><i
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
            <div class="app-sidebar-backdrop"></div>
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0 mt-3 fw-bold text-dark">Student Management</h3>
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
                                        class="bi bi-people-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Total
                                        Students</span>
                                    <h4 class="fw-bold mb-0 text-dark">
                                        <?php echo number_format($total_students_count); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-person-check-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Regular
                                        Students</span>
                                    <h4 class="fw-bold mb-0 text-success">
                                        <?php echo number_format($total_enrolled_count); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-clock-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Irregular
                                        Students</span>
                                    <h4 class="fw-bold mb-0 text-warning">
                                        <?php echo number_format($total_pending_count); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-calendar3"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">School Year</span>
                                    <h4 class="fw-bold mb-0 text-info">2026 - 2027</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php if ($add_mode): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4 bg-white">
                                    <div
                                        class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                                class="bi bi-person-plus-fill me-2 text-success"></i>Register New Student
                                            Profile</h5>
                                        <a href="students.php" class="btn-close"></a>
                                    </div>
                                    <form method="POST" action="students.php">
                                        <div class="card-body text-dark">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Student No. (Auto
                                                        Generated)</label>
                                                    <input type="text" name="student_no"
                                                        class="form-control form-control-sm border shadow-sm font-monospace bg-light"
                                                        value="<?php echo $generated_student_no; ?>" readonly required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Full Name
                                                        (Format: Lastname, Firstname)</label>
                                                    <input type="text" name="name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Institutional
                                                        Email</label>
                                                    <input type="email" name="email"
                                                        class="form-control form-control-sm border shadow-sm font-monospace"
                                                        placeholder="username@pcc.edu.ph" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Academic
                                                        Program</label>
                                                    <select name="program"
                                                        class="form-select form-select-sm border shadow-sm" required>
                                                        <option value="BSIT">BS in Information Technology</option>
                                                        <option value="BSCS">BS in Computer Science</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Year
                                                        Level</label>
                                                    <select name="year" class="form-select form-select-sm border shadow-sm">
                                                        <option value="1">1st Year</option>
                                                        <option value="2">2nd Year</option>
                                                        <option value="3">3rd Year</option>
                                                        <option value="4">4th Year</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label
                                                        class="form-label small fw-bold text-secondary">Classification</label>
                                                    <select name="classification"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option value="Regular">Regular</option>
                                                        <option value="Irregular">Irregular</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-end align-items-center py-3 border-top">
                                            <a href="students.php"
                                                class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                            <button type="submit" name="submit_new_student"
                                                class="btn btn-sm btn-success px-3 shadow-sm">Save New Entry</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($edit_mode && $selected_student): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4 bg-white">
                                    <div
                                        class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                                class="bi bi-pencil-square me-2 text-warning"></i>Edit / Manage Student
                                            Profile Workspace</h5>
                                        <a href="students.php" class="btn-close"></a>
                                    </div>
                                    <form method="POST" action="students.php">
                                        <div class="card-body text-dark">
                                            <input type="hidden" name="student_no" id="active-student-no"
                                                value="<?= htmlspecialchars($selected_student['student_no']) ?>">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Full Name
                                                        (Format: Lastname, Firstname)</label>
                                                    <input type="text" name="name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        value="<?= htmlspecialchars($selected_student['name']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Institutional
                                                        Email</label>
                                                    <input type="email" name="email"
                                                        class="form-control form-control-sm border shadow-sm font-monospace"
                                                        value="<?= htmlspecialchars($selected_student['email']) ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Academic Program
                                                        Option</label>
                                                    <select name="program"
                                                        class="form-select form-select-sm border shadow-sm" required>
                                                        <option value="BSIT" <?= $selected_student['program'] === 'BSIT' ? 'selected' : '' ?>>BS in Information Technology</option>
                                                        <option value="BSCS" <?= $selected_student['program'] === 'BSCS' ? 'selected' : '' ?>>BS in Computer Science</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Year Level
                                                        Status</label>
                                                    <select name="year" class="form-select form-select-sm border shadow-sm">
                                                        <option value="1" <?= strpos($selected_student['year'], '1') !== false ? 'selected' : '' ?>>1st Year</option>
                                                        <option value="2" <?= strpos($selected_student['year'], '2') !== false ? 'selected' : '' ?>>2nd Year</option>
                                                        <option value="3" <?= strpos($selected_student['year'], '3') !== false ? 'selected' : '' ?>>3rd Year</option>
                                                        <option value="4" <?= strpos($selected_student['year'], '4') !== false ? 'selected' : '' ?>>4th Year</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label small fw-bold text-secondary">Classification</label>
                                                    <select name="classification"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option value="Regular"
                                                            <?= $selected_student['classification'] === 'Regular' ? 'selected' : '' ?>>Regular</option>
                                                        <option value="Irregular"
                                                            <?= $selected_student['classification'] === 'Irregular' ? 'selected' : '' ?>>Irregular</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-secondary">Enrollment
                                                        Status</label>
                                                    <select name="account_status"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option value="Enrolled" <?= $selected_student['status'] === 'Enrolled' ? 'selected' : '' ?>>Enrolled</option>
                                                        <option value="Not Enrolled" <?= $selected_student['status'] === 'Not Enrolled' ? 'selected' : '' ?>>Not Enrolled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-between align-items-center py-3 border-top">
                                            <button type="button" class="btn btn-sm btn-danger px-3 shadow-sm"
                                                onclick="triggerRecordDeletion()"><i class="bi bi-trash-fill me-2"></i>Drop
                                                Record Stack</button>
                                            <div class="ms-auto">
                                                <a href="students.php"
                                                    class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                                <button type="submit" name="update_student"
                                                    class="btn btn-sm btn-primary px-3 shadow-sm">Save Profile
                                                    Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="card shadow-sm border border-light-subtle bg-white"
                                style="border-radius: 10px;">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-people-fill me-2 text-primary"></i>PCC Student List Directory
                                    </h5>
                                    <div class="card-tools d-flex flex-wrap align-items-center gap-2">
                                        <div
                                            class="d-flex align-items-center gap-1 border-end pe-2 me-1 border-light-subtle flex-wrap">
                                            <select id="filter-program"
                                                class="form-select form-select-sm border shadow-sm text-muted"
                                                style="width: 9rem;">
                                                <option selected>All Programs</option>
                                                <option>BSIT</option>
                                                <option>BSCS</option>
                                            </select>
                                            <select id="filter-year"
                                                class="form-select form-select-sm border shadow-sm text-muted"
                                                style="width: 8rem;">
                                                <option selected>All Year Levels</option>
                                                <option>1st Year</option>
                                                <option>2nd Year</option>
                                                <option>3rd Year</option>
                                                <option>4th Year</option>
                                            </select>
                                        </div>
                                        <form method="GET" action="students.php" class="d-flex gap-2">
                                            <div class="input-group input-group-sm border shadow-sm rounded"
                                                style="width: 14rem">
                                                <span class="input-group-text bg-light border-0 text-muted"><i
                                                        class="bi bi-search"></i></span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control border-0 bg-light" placeholder="Search"
                                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 text-dark">
                                            <thead
                                                class="table-light small text-uppercase text-secondary border-bottom">
                                                <tr>
                                                    <th class="clickable-header ps-4 py-3 font-weight-bold"
                                                        onclick="sortTable(0)">Student No. <i
                                                            class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                    <th class="clickable-header py-3 font-weight-bold"
                                                        onclick="sortTable(1)">Student Name <i
                                                            class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                    <th>Program</th>
                                                    <th>Year Level</th>
                                                    <th>Classification</th>
                                                    <th>Status</th>
                                                    <th class="pe-4 text-end" style="width: 240px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($student_list)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center py-5 text-muted"><i
                                                                class="bi bi-people fs-1 opacity-50 mb-3 d-block"></i>No
                                                            Active Records Located</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($student_list as $student): ?>
                                                        <tr
                                                            class="<?php echo ($edit_id === $student['student_no']) ? 'table-warning-subtle' : ''; ?>">
                                                            <td class="ps-4 font-monospace fw-bold text-secondary small">
                                                                <?= htmlspecialchars($student['student_no']) ?>
                                                            </td>
                                                            <td class="fw-semibold text-dark">
                                                                <?= htmlspecialchars($student['name']) ?>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-primary-subtle text-primary fw-medium px-2 py-1"><?= htmlspecialchars($student['program']) ?></span>
                                                            </td>
                                                            <td class="text-secondary small">
                                                                <?= htmlspecialchars($student['year']) ?>
                                                            </td>
                                                            <td class="text-dark small">
                                                                <?= htmlspecialchars($student['classification']) ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $is_not_enrolled = ($student['status'] === 'Not Enrolled');
                                                                $status_badge = $is_not_enrolled ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success';
                                                                ?>
                                                                <span
                                                                    class="badge <?= $status_badge ?> px-2 py-1"><?= htmlspecialchars($student['status']) ?></span>
                                                            </td>
                                                            <td class="pe-4 text-end">
                                                                <a href="?edit_id=<?= urlencode($student['student_no']) ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
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
                                    <div class="small text-muted font-monospace">Showing <?= count($student_list) ?>
                                        entries tracked in system scope.</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        function triggerRecordDeletion() {
            const studentId = document.getElementById('active-student-no').value;
            if (confirm("Are you sure you want to drop student profile target #" + studentId + " from system scope?")) {
                window.location.href = "?delete_id=" + encodeURIComponent(studentId);
            }
        }

        let sortDirections = [true, true];
        function sortTable(columnIndex) {
            const table = document.querySelector("table tbody");
            const rows = Array.from(table.querySelectorAll("tr"));
            if (rows.length === 1 && rows[0].cells.length === 1) return;

            const isAscending = sortDirections[columnIndex];
            sortDirections[columnIndex] = !isAscending;

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[columnIndex]?.textContent.trim().toLowerCase() || "";
                let cellB = rowB.cells[columnIndex]?.textContent.trim().toLowerCase() || "";
                return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });
            rows.forEach(row => table.appendChild(row));
        }

        document.addEventListener("DOMContentLoaded", function () {
            runLiveDashboardClock();
            setInterval(runLiveDashboardClock, 1000);

            const searchInput = document.getElementById("table-filter");
            const programFilter = document.getElementById("filter-program");
            const yearFilter = document.getElementById("filter-year");
            const tableRows = document.querySelectorAll("table tbody tr");

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
                const selectedProgram = programFilter ? programFilter.value : "All Programs";
                const selectedYear = yearFilter ? yearFilter.value : "All Year Levels";

                tableRows.forEach(row => {
                    if (row.cells.length <= 1) return;

                    const studentNo = row.cells[0]?.textContent.toLowerCase().trim() || "";
                    const studentName = row.cells[1]?.textContent.toLowerCase().trim() || "";
                    const studentProgram = row.cells[2]?.textContent.trim() || "";
                    const studentYear = row.cells[3]?.textContent.trim() || "";

                    const matchesSearch = studentNo.includes(query) || studentName.includes(query);
                    const matchesProgram = selectedProgram === "All Programs" || studentProgram.includes(selectedProgram);
                    const matchesYear = selectedYear === "All Year Levels" || studentYear === selectedYear;

                    if (matchesSearch && matchesProgram && matchesYear) { row.style.display = ""; }
                    else { row.style.display = "none"; }
                });
            }

            if (searchInput) searchInput.addEventListener("input", filterTable);
            if (programFilter) programFilter.addEventListener("change", filterTable);
            if (yearFilter) yearFilter.addEventListener("change", filterTable);
        });
    </script>
</body>

</html>