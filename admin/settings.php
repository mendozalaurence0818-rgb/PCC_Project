<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";
$config_file = 'portal_config.json';

// Load extra settings from local server configuration file if it exists
$local_config = [];
if (file_exists($config_file)) {
    $local_config = json_decode(file_get_contents($config_file), true) ?? [];
}

$current_school_year = '2026-2027';
$current_semester = '1st Semester';
$enrollment_status = 'Open';
$drop_subject_status = $local_config['drop_subject_status'] ?? 'Open';
$grading_status = 'Closed';
$system_maintenance = 'Disabled';

try {
    $config_stmt = $conn->query("SELECT * FROM system_settings LIMIT 1");
    $config_data = $config_stmt->fetch(PDO::FETCH_ASSOC);
    if ($config_data) {
        $current_school_year    = $config_data['school_year'] ?? '2026-2027';
        $current_semester       = $config_data['semester'] ?? '1st Semester';
        $enrollment_status      = $config_data['enrollment_status'] ?? 'Open';
        $grading_status         = $config_data['grading_status'] ?? 'Closed';
        $system_maintenance     = $config_data['system_maintenance'] ?? 'Disabled';
    }
} catch (PDOException $e) { /* Log error */ }

if ($system_maintenance === 'Enabled') {
    $drop_subject_status = 'Closed';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['save_settings'])) {
        $school_year = trim($_POST['school_year']);
        $semester = trim($_POST['semester']);
        $grading_status_form = trim($_POST['grading_status']);
        $maintenance = trim($_POST['system_maintenance']);

        $enroll_status = isset($_POST['enrollment_status']) ? trim($_POST['enrollment_status']) : $enrollment_status;
        $drop_status = isset($_POST['drop_subject_status']) ? trim($_POST['drop_subject_status']) : $drop_subject_status;

        try {
            $stmt = $conn->prepare("
                UPDATE system_settings 
                SET school_year = :sy, 
                    semester = :sem, 
                    enrollment_status = :es, 
                    grading_status = :gs, 
                    system_maintenance = :sm 
                WHERE id = 1
            ");

            $stmt->execute([
                ':sy' => $school_year,
                ':sem' => $semester,
                ':es' => $enroll_status,
                ':gs' => $grading_status_form,
                ':sm' => $maintenance
            ]);

            $local_config['drop_subject_status'] = $drop_status;
            file_put_contents($config_file, json_encode($local_config, JSON_PRETTY_PRINT));

            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white'><div class='toast-body'>Settings updated successfully.</div></div></div>";
            
            $current_school_year = $school_year;
            $current_semester = $semester;
            $enrollment_status = $enroll_status;
            $grading_status = $grading_status_form;
            $system_maintenance = $maintenance;
            $drop_subject_status = ($system_maintenance === 'Enabled') ? 'Closed' : $drop_status;

        } catch (PDOException $e) {
            $toast_notification = "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
        }
    }

    if (isset($_POST['change_admin_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'>Passwords do not match.</div>";
        } else {
            try {
                $pass_stmt = $conn->prepare("SELECT password_hash FROM admins WHERE admin_id = :id");
                $pass_stmt->execute([':id' => $_SESSION['admin_id']]);
                $stored_hash = $pass_stmt->fetchColumn();

                if ($stored_hash && password_verify($current_password, $stored_hash)) {
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $upd_pass = $conn->prepare("UPDATE admins SET password_hash = :hash WHERE admin_id = :id");
                    $upd_pass->execute([':hash' => $new_hash, ':id' => $_SESSION['admin_id']]);
                    $toast_notification = "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Password updated successfully.</div>";
                } else {
                    $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'>Current password incorrect.</div>";
                }
            } catch (PDOException $e) {
                $toast_notification = "<div class='alert alert-danger m-3'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

$clean_school_year = str_replace(' - ', '-', $current_school_year);
$display_semester_year = $current_semester . ", AY " . $clean_school_year;
$new_admissions = 0;

try {
    $total_applications = $conn->query("SELECT COUNT(*) FROM applicants")->fetchColumn();
    $new_student_count = $conn->query("SELECT COUNT(*) FROM applicants WHERE classification = 'freshman'")->fetchColumn();
    $transferee_count = $conn->query("SELECT COUNT(*) FROM applicants WHERE classification = 'transferee'")->fetchColumn();
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();
} catch (PDOException $e) { }

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
                            <span class="sidebar-semester-text"><?php echo $display_semester_year; ?></span>
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
                        <li class="nav-item"><a href="users.php" class="nav-link"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users</p>
                            </a></li>
                        <li class="nav-item"><a href="settings.php" class="nav-link sidebar-bg-active"><i
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
                    <h3 class="mb-0 mt-3 fw-bold">Portal Settings</h3>
                </div>
            </div>
            <div class="app-content mt-3">
                <div class="container-fluid">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-sliders me-2 text-primary"></i>Academic Controls</h5>
                                </div>
                                <form method="POST" action="settings.php">
                                    <div class="card-body bg-white text-dark">
                                        <div class="row g-3">
                                            <div class="col-md-4"><label class="form-label small fw-bold">Active School
                                                    Year</label><select name="school_year"
                                                    class="form-select form-select-sm">
                                                    <option value="2025 - 2026" <?php echo trim($current_school_year) === '2025 - 2026' ? 'selected' : ''; ?>>2025 - 2026</option>
                                                    <option value="2026 - 2027" <?php echo trim($current_school_year) === '2026 - 2027' ? 'selected' : ''; ?>>2026 - 2027</option>
                                                </select></div>
                                            <div class="col-md-4"><label class="form-label small fw-bold">Current
                                                    Academic Semester</label><select name="semester"
                                                    class="form-select form-select-sm">
                                                    <option value="1st Semester" <?php echo $current_semester === '1st Semester' ? 'selected' : ''; ?>>1st Semester</option>
                                                    <option value="2nd Semester" <?php echo $current_semester === '2nd Semester' ? 'selected' : ''; ?>>2nd Semester</option>
                                                </select></div>
                                            <div class="col-md-4"><label class="form-label small fw-bold">Admission
                                                    State</label>
                                                <select name="enrollment_status" class="form-select form-select-sm"
                                                    <?php echo ($system_maintenance === 'Enabled') ? 'disabled style="background-color: #e9ecef; cursor: not-allowed;"' : ''; ?>>
                                                    <option value="Open" <?php echo $enrollment_status === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                    <option value="Closed" <?php echo $enrollment_status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4"><label class="form-label small fw-bold">Dropping of
                                                    Subject</label>
                                                <select name="drop_subject_status" class="form-select form-select-sm"
                                                    <?php echo ($system_maintenance === 'Enabled') ? 'disabled style="background-color: #e9ecef; cursor: not-allowed;"' : ''; ?>>
                                                    <option value="Open" <?php echo $drop_subject_status === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                    <option value="Closed" <?php echo $drop_subject_status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4"><label class="form-label small fw-bold">Grading
                                                    Module</label><select name="grading_status"
                                                    class="form-select form-select-sm">
                                                    <option value="Open" <?php echo $grading_status === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                    <option value="Closed" <?php echo $grading_status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                                </select></div>
                                            <div class="col-md-4"><label class="form-label small fw-bold">Maintenance
                                                    Mode</label><select name="system_maintenance"
                                                    class="form-select form-select-sm">
                                                    <option value="Disabled" <?php echo $system_maintenance === 'Disabled' ? 'selected' : ''; ?>>Disabled</option>
                                                    <option value="Enabled" <?php echo $system_maintenance === 'Enabled' ? 'selected' : ''; ?>>Enabled</option>
                                                </select></div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light text-end"><button type="submit"
                                            name="save_settings" class="btn btn-sm btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-shield-lock-fill me-2 text-primary"></i>Change Password</h5>
                                </div>
                                <form method="POST" action="settings.php">
                                    <div class="card-body bg-white text-dark">
                                        <div class="row g-3">
                                            <div class="col-md-4"><label
                                                    class="form-label small fw-bold">Current</label><input
                                                    type="password" name="current_password"
                                                    class="form-control form-control-sm" placeholder="....." required>
                                            </div>
                                            <div class="col-md-4"><label
                                                    class="form-label small fw-bold">New</label><input type="password"
                                                    name="new_password" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4"><label
                                                    class="form-label small fw-bold">Confirm</label><input
                                                    type="password" name="confirm_password"
                                                    class="form-control form-control-sm" required></div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light text-end"><button type="submit"
                                            name="change_admin_password" class="btn btn-sm btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) + " - " + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }
        setInterval(runLiveDashboardClock, 1000);
        runLiveDashboardClock();
    </script>
</body>

</html>