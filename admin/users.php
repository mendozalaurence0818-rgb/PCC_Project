<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";

function getNextSequentialStudentNo($conn, $current_year) {
    $stmt = $conn->query("SELECT student_number FROM students WHERE student_number LIKE '{$current_year}-%' ORDER BY student_number DESC LIMIT 1");
    $last_id = $stmt->fetchColumn();
    $sequence = $last_id ? intval(substr($last_id, -5)) + 1 : 1;
    return $current_year . "-" . str_pad($sequence, 5, '0', STR_PAD_LEFT);
}

if (isset($_GET['provision_approved_id'])) {
    $applicant_id = intval($_GET['provision_approved_id']);
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT first_name, last_name, email_address, preferred_program FROM applicants WHERE application_id = :id AND application_status = 'Approved' LIMIT 1");
        $stmt->execute([':id' => $applicant_id]);
        $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($applicant) {
            $current_year = date('Y');
            $student_no = getNextSequentialStudentNo($conn, $current_year);
            $default_password_hash = password_hash('student123', PASSWORD_DEFAULT);

            $ins_stmt = $conn->prepare("INSERT INTO students (student_number, password_hash, application_id, first_name, last_name, email, current_course, year_level, classification, enrollment_status) 
                VALUES (:num, :pwd, :app_id, :first, :last, :email, :course, 1, 'Regular', 'Not Enrolled')");
            $ins_stmt->execute([
                ':num'    => $student_no,
                ':pwd'    => $default_password_hash,
                ':app_id' => $applicant_id,
                ':first'  => $applicant['first_name'],
                ':last'   => $applicant['last_name'],
                ':email'  => $applicant['email_address'],
                ':course' => $applicant['preferred_program']
            ]);

            $upd_app = $conn->prepare("UPDATE applicants SET student_number = :num, application_status = 'Enrolled' WHERE application_id = :id");
            $upd_app->execute([':num' => $student_no, ':id' => $applicant_id]);

            $conn->commit();
            echo "SUCCESS:" . $student_no;
        } else {
            throw new Exception("Applicant not found or not in Approved state.");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo "ERROR:" . $e->getMessage();
    }
    exit();
}

if (isset($_GET['get_next_id'])) {
    $role_type = trim($_GET['get_next_id']);
    $current_year = date('Y');
    
    if ($role_type === 'Admin') {
        $stmt = $conn->query("SELECT admin_id FROM admins WHERE admin_id LIKE 'ADM-{$current_year}-%' ORDER BY admin_id DESC LIMIT 1");
        $last_id = $stmt->fetchColumn();
        $sequence = $last_id ? intval(substr($last_id, -5)) + 1 : 1;
        echo "ADM-" . $current_year . "-" . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    } else if ($role_type === 'Student') {
        echo getNextSequentialStudentNo($conn, $current_year);
    }
    exit();
}

if (isset($_GET['delete_user_id']) && isset($_GET['role_type'])) {
    $target_uid = trim($_GET['delete_user_id']);
    $role_type = trim($_GET['role_type']);
    
    try {
        if ($role_type === 'Admin') {
            if ($target_uid === $_SESSION['admin_id'] || $target_uid === 'admin_01') {
                throw new Exception("Security Constraint: Cannot purge active root management account sessions.");
            }
            $del_stmt = $conn->prepare("DELETE FROM admins WHERE admin_id = :id");
            $del_stmt->execute([':id' => $target_uid]);
        } else {
            $del_stmt = $conn->prepare("DELETE FROM students WHERE student_number = :num");
            $del_stmt->execute([':num' => $target_uid]);
        }
        $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Record Deleted!</strong> Account profile entry #" . htmlspecialchars($target_uid) . " dropped from memory indices.</div>";
    } catch (Exception $e) {
        $toast_notification = "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_new_user'])) {
    $generated_id = trim($_POST['generated_id']);
    $account_name = trim($_POST['account_name']);
    $email = trim($_POST['email']);
    $role_type = trim($_POST['role_type']);
    $plain_password = trim($_POST['password']);
    $pwd_hash = password_hash($plain_password, PASSWORD_DEFAULT);

    try {
        if ($role_type === 'Admin') {
            $ins_stmt = $conn->prepare("INSERT INTO admins (admin_id, pcc_email, access_code, admin_name) VALUES (:id, :email, :pwd, :name)");
            $ins_stmt->execute([':id' => $generated_id, ':email' => $email, ':pwd' => $pwd_hash, ':name' => $account_name]);
        } else {
            $name_parts = explode(',', $account_name);
            $last_name = trim($name_parts[0] ?? '');
            $first_name = trim($name_parts[1] ?? $account_name);
            
            $ins_stmt = $conn->prepare("INSERT INTO students (student_number, password_hash, first_name, last_name, email, current_course) VALUES (:num, :pwd, :first, :last, :email, 'BSIT')");
            $ins_stmt->execute([':num' => $generated_id, ':pwd' => $pwd_hash, ':first' => $first_name, ':last' => $last_name, ':email' => $email]);
        }
        $toast_notification = "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>New portal account initialized successfully!</div>";
    } catch (PDOException $e) {
        $toast_notification = "<div class='alert alert-danger m-3'>Account Allocation Loop Error: " . $e->getMessage() . "</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_profile'])) {
    $target_username = trim($_POST['target_username']);
    $account_name = trim($_POST['account_name']);
    $email = trim($_POST['email']);
    $role_type = trim($_POST['role_type']);
    $new_password = trim($_POST['password']);

    try {
        if ($role_type === 'Admin') {
            if (!empty($new_password)) {
                $pwd_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $upd_stmt = $conn->prepare("UPDATE admins SET admin_name = :name, pcc_email = :email, access_code = :pwd WHERE admin_id = :id");
                $upd_stmt->execute([':name' => $account_name, ':email' => $email, ':pwd' => $pwd_hash, ':id' => $target_username]);
            } else {
                $upd_stmt = $conn->prepare("UPDATE admins SET admin_name = :name, pcc_email = :email WHERE admin_id = :id");
                $upd_stmt->execute([':name' => $account_name, ':email' => $email, ':id' => $target_username]);
            }
        } else {
            $name_parts = explode(',', $account_name);
            $last_name = trim($name_parts[0] ?? '');
            $first_name = trim($name_parts[1] ?? $account_name);

            if (!empty($new_password)) {
                $pwd_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $upd_stmt = $conn->prepare("UPDATE students SET first_name = :first, last_name = :last, email = :email, password_hash = :pwd WHERE student_number = :num");
                $upd_stmt->execute([':first' => $first_name, ':last' => $last_name, ':email' => $email, ':pwd' => $pwd_hash, ':num' => $target_username]);
            } else {
                $upd_stmt = $conn->prepare("UPDATE students SET first_name = :first, last_name = :last, email = :email WHERE student_number = :num");
                $upd_stmt->execute([':first' => $first_name, ':last' => $last_name, ':email' => $email, ':num' => $target_username]);
            }
        }
        $toast_notification = "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Record updated successfully!</div>";
    } catch (PDOException $e) {
        $toast_notification = "<div class='alert alert-danger m-3'>Update Error: " . $e->getMessage() . "</div>";
    }
}

$new_admissions = 0;
$users_list = [];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_param = '%' . $search_query . '%';
$filter_role = isset($_GET['role_filter']) ? trim($_GET['role_filter']) : 'All';

try {
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();

    if ($filter_role === 'All' || $filter_role === 'Admin') {
        $admin_query = $conn->prepare("SELECT admin_id, admin_name, pcc_email FROM admins WHERE admin_id LIKE :search OR admin_name LIKE :search OR pcc_email LIKE :search");
        $admin_query->execute([':search' => $search_param]);
        while ($admin = $admin_query->fetch(PDO::FETCH_ASSOC)) {
            $users_list[] = ['account_id' => $admin['admin_id'], 'name' => $admin['admin_name'], 'school_email' => $admin['pcc_email'], 'role' => 'Admin'];
        }
    }

    if ($filter_role === 'All' || $filter_role === 'Student') {
        $student_query = $conn->prepare("SELECT student_number, first_name, last_name, suffix, email FROM students WHERE student_number LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR email LIKE :search");
        $student_query->execute([':search' => $search_param]);
        while ($student = $student_query->fetch(PDO::FETCH_ASSOC)) {
            $suffix_display = !empty($student['suffix']) ? ' ' . $student['suffix'] : '';
            $users_list[] = ['account_id' => $student['student_number'], 'name' => $student['last_name'] . ', ' . $student['first_name'] . $suffix_display, 'school_email' => $student['email'], 'role' => 'Student'];
        }
    }
} catch (PDOException $e) {
    $toast_notification = "<div class='alert alert-danger m-3'>Query Engine Failure: " . $e->getMessage() . "</div>";
}

$edit_mode = false;
$add_mode = isset($_GET['action']) && $_GET['action'] === 'new';
$selected_user = null;
$edit_id = $_GET['edit_uid'] ?? null;
$edit_role = $_GET['role'] ?? null;

if ($edit_id && $edit_role) {
    foreach ($users_list as $user) {
        if ($user['account_id'] === $edit_id && $user['role'] === $edit_role) {
            $selected_user = $user;
            $edit_mode = true;
            break;
        }
    }
}

$current_semester = "1st Semester, AY 2026-2027";

$pending_enrollment_count = 0;
$pending_drop_count = 0;

try {
    $enroll_count_stmt = $conn->query("SELECT COUNT(*) FROM students WHERE enrollment_status = 'Pending Approval'");
    $pending_enrollment_count = $enroll_count_stmt->fetchColumn();

    $drop_count_stmt = $conn->query("SELECT COUNT(*) FROM drop_requests WHERE status = 'Pending Review'");
    $pending_drop_count = $drop_count_stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Sidebar Badges Fetch Error: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Users Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --pcc-blue: #002c5e; --pcc-gold: #f1b813; --pcc-dark-blue: #001d3d; }
        .sidebar-bg { background-color: var(--pcc-blue) !important; transition: transform 0.3s ease-in-out, width 0.3s ease-in-out; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: var(--pcc-blue) !important; background-color: var(--pcc-gold) !important; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: var(--pcc-dark-blue); }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .sidebar-semester-text { color: #adb5bd; font-size: 11px; font-weight: 500; display: block; margin-top: 2px; }
        .nav-date { font-weight: 600; color: var(--pcc-blue); }
        .tab-indicator { font-weight: 600; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; }
        @media (min-width: 992px) {
            .sidebar-collapse .app-sidebar { margin-left: -250px !important; }
            .sidebar-collapse .app-main, .sidebar-collapse .app-footer, .sidebar-collapse .app-header { margin-left: 0 !important; }
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php echo $toast_notification; ?>

    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body px-1">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)" role="button"><i class="bi bi-list fs-5"></i></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu"><span class="d-md-inline"><div class="nav-date" id="liveClockDisplay">Loading...</div></span></li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand"
                style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="dashboard.php" class="brand-link">
                    <img src="../assets/images/PCC_logo.png" alt="PCC Logo" class="brand-image" />
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
                        <li class="nav-item"><a href="students.php" class="nav-link "><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students</p>
                            </a></li>
                        <li class="nav-item"><a href="admissions.php" class="nav-link "><i
                                    class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions
                                    <?php if ($new_admissions > 0): ?>
                                        <span id="admissionsBadge"
                                            class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $new_admissions; ?></span>
                                    <?php endif; ?>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="verify_enrollment.php" class="nav-link"><i
                                    class="nav-icon bi bi-shield-check"></i>
                                <p>Enrollment
                                    <?php if ($pending_enrollment_count > 0): ?>
                                        <span class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $pending_enrollment_count; ?></span>
                                    <?php endif; ?>
                                </p>
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
                                <p>Drop Requests
                                    <?php if ($pending_drop_count > 0): ?>
                                        <span class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $pending_drop_count; ?></span>
                                    <?php endif; ?>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules</p>
                            </a></li>
                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item"><a href="notice.php" class="nav-link"><i
                                    class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice</p>
                            </a></li>
                        <li class="nav-item"><a href="users.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users</p>
                            </a></li>
                        <li class="nav-item"><a href="settings.php" class="nav-link "><i
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
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 mt-3 fw-bold text-dark">System Users Accounts</h3>
                        </div>
                        <div class="col-sm-6 text-end mt-3">
                            <a href="?action=new" class="btn btn-primary shadow-sm fw-semibold btn-sm"><i class="bi bi-plus-circle me-2"></i>Create New User</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content mt-3">
                <div class="container-fluid">
                    <?php if ($add_mode): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-plus-circle-fill me-2"></i>Create New Account</h5>
                                <a href="users.php" class="btn-close" aria-label="Close"></a>
                            </div>
                            <form method="POST" action="users.php">
                                <div class="card-body text-dark">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Select Role</label>
                                            <select name="role_type" id="create-role-selector" class="form-select form-select-sm border shadow-sm" required onchange="fetchNextSequentialID(this.value)">
                                                <option value="" disabled selected>Select Portal Role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Student">Student</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Account ID</label>
                                            <input type="text" name="generated_id" id="create-id-input" class="form-control form-control-sm border bg-light font-monospace fw-bold text-primary" placeholder="Waiting for Role" readonly required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Account Owner Full Name</label>
                                            <input type="text" name="account_name" class="form-control form-control-sm border shadow-sm" placeholder="Lastname, Firstname" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Institutional Email</label>
                                            <input type="email" name="email" class="form-control form-control-sm border shadow-sm font-monospace" placeholder="ln.fn@pcc.edu.ph" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold text-secondary">Set Login Password</label>
                                            <input type="password" name="password" class="form-control form-control-sm border shadow-sm" placeholder="••••••••" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-end py-3 border-top">
                                    <a href="users.php" class="btn btn-sm btn-secondary px-3 me-2">Cancel</a>
                                    <button type="submit" name="create_new_user" class="btn btn-sm btn-primary px-3 shadow-sm">Save</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($edit_mode && $selected_user): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Edit Account Profile: #<?= htmlspecialchars($selected_user['account_id']) ?></h5>
                                <a href="users.php" class="btn-close" aria-label="Close"></a>
                            </div>
                            <form method="POST" action="users.php">
                                <div class="card-body text-dark">
                                    <input type="hidden" name="target_username" value="<?= htmlspecialchars($selected_user['account_id']) ?>">
                                    <input type="hidden" name="role_type" value="<?= htmlspecialchars($selected_user['role']) ?>">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary">Account ID</label>
                                            <input type="text" class="form-control form-control-sm border bg-light font-monospace" value="<?= htmlspecialchars($selected_user['account_id']) ?>" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary">Name</label>
                                            <input type="text" name="account_name" class="form-control form-control-sm border shadow-sm" value="<?= htmlspecialchars($selected_user['name']) ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary">Assigned Portal Clearance Access</label>
                                            <input type="text" class="form-control form-control-sm border bg-light fw-bold" value="<?= htmlspecialchars($selected_user['role']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary">Email Address (School Email)</label>
                                            <input type="email" name="email" class="form-control form-control-sm border shadow-sm font-monospace" value="<?= htmlspecialchars($selected_user['school_email']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-danger">Reset Password (Danger Zone)</label>
                                            <input type="password" name="password" class="form-control form-control-sm border shadow-sm" placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 border-top">
                                    <button type="button" class="btn btn-sm btn-danger px-3" onclick="if(confirm('Completely revoke system login access and drop account entry permanently?')) window.location.href='?delete_user_id=<?= urlencode($selected_user['account_id']) ?>&role_type=<?= urlencode($selected_user['role']) ?>'"><i class="bi bi-trash-fill me-1"></i>Delete Account</button>
                                    <div class="ms-auto">
                                        <a href="users.php" class="btn btn-sm btn-secondary px-3 me-2">Cancel</a>
                                        <button type="submit" name="update_user_profile" class="btn btn-sm btn-primary px-3 shadow-sm">Save Modifications</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-0 bg-white" style="border-radius: 10px;">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Active Portal Accounts</h5>
                            <div class="card-tools">
                                <form method="GET" action="users.php" class="d-flex gap-2">
                                    <div class="input-group input-group-sm rounded" style="width: 12rem">
                                        <select name="role_filter" class="form-select border shadow-sm" onchange="this.form.submit()">
                                            <option value="All" <?= $filter_role === 'All' ? 'selected' : '' ?>>All Roles</option>
                                            <option value="Admin" <?= $filter_role === 'Admin' ? 'selected' : '' ?>>Admin Only</option>
                                            <option value="Student" <?= $filter_role === 'Student' ? 'selected' : '' ?>>Student Only</option>
                                        </select>
                                    </div>
                                    <div class="input-group input-group-sm border rounded shadow-sm" style="width: 14rem">
                                        <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                                        <input id="table-filter" type="search" name="search" class="form-control border-0 bg-light" placeholder="Search name or key..." value="<?= htmlspecialchars($search_query) ?>" />
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-dark">
                                    <thead class="table-light small text-uppercase text-secondary border-bottom">
                                        <tr>
                                            <th>Account ID</th>
                                            <th>Email Address</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th class="pe-4 text-end" style="width: 180px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users_list)): ?>
                                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-person-x fs-1 opacity-50 mb-3 d-block"></i>No Live Operational Portal Identities Indexed</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($users_list as $user): ?>
                                                <tr class="<?= ($edit_id === $user['account_id'] && $edit_role === $user['role']) ? 'table-warning-subtle' : '' ?>">
                                                    <td class="ps-4 font-monospace fw-bold text-secondary small"><?= htmlspecialchars($user['account_id']) ?></td>
                                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($user['school_email'] ?? 'N/A') ?></td>
                                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($user['name']) ?></td>
                                                    <td>
                                                        <?php 
                                                        $is_head_admin = ($user['role'] === 'Admin'); 
                                                        $badge_theme = $is_head_admin ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle';
                                                        ?>
                                                        <span class="badge <?= $badge_theme; ?> tab-indicator d-inline-block text-center px-2 py-1"><?= htmlspecialchars($user['role']) ?></span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <a href="?edit_uid=<?= urlencode($user['account_id']) ?>&role=<?= urlencode($user['role']) ?><?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?><?= $filter_role !== 'All' ? '&role_filter='.urlencode($filter_role) : '' ?>" class="btn btn-xs btn-outline-primary border py-1 px-2" style="font-size: 0.75rem;"><i class="bi bi-pencil-square me-1"></i>Edit / Manage</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        function fetchNextSequentialID(role) {
            if (!role) return;
            const inputField = document.getElementById('create-id-input');
            inputField.value = "Calculating profile ID code...";
            
            fetch(`users.php?get_next_id=${encodeURIComponent(role)}`)
                .then(response => response.text())
                .then(data => { inputField.value = data.trim(); })
                .catch(err => { inputField.value = "Generation Error"; });
        }

        document.addEventListener("DOMContentLoaded", function () {
            runLiveDashboardClock();
            setInterval(runLiveDashboardClock, 1000);
        });
    </script>
</body>
</html>