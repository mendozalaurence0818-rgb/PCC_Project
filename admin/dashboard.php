<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_system_logs'])) {
    try {
        $conn->exec("TRUNCATE TABLE system_updates");
        $toast_notification = "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>System updates feed cleared successfully!</div>";
    } catch (PDOException $e) {
        $toast_notification = "<div class='alert alert-danger m-3'>Clear Error: " . $e->getMessage() . "</div>";
    }
}

if (isset($_GET['fetch_live_counts'])) {
    header('Content-Type: application/json');
    try {
        $adm = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending' OR application_status IS NULL");
        $new_admissions = $adm->fetchColumn();

        $stud = $conn->query("SELECT (SELECT COUNT(*) FROM students) + (SELECT COUNT(*) FROM applicants WHERE application_status = 'Approved' AND student_number NOT IN (SELECT student_number FROM students WHERE student_number IS NOT NULL))");
        $total_students = $stud->fetchColumn();

        $sub = $conn->query("SELECT COUNT(*) FROM subjects");
        $active_subjects = $sub ? $sub->fetchColumn() : 0;

        $sect = $conn->query("SELECT COUNT(*) FROM sections");
        $active_sections = $sect ? $sect->fetchColumn() : 0;

        echo json_encode([
            'success' => true,
            'new_admissions' => (int) $new_admissions,
            'total_students' => (int) $total_students,
            'active_subjects' => (int) $active_subjects,
            'active_sections' => (int) $active_sections,
            'server_timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

$total_students = 0;
$new_admissions = 0;
$active_subjects = 0;
$active_sections = 0;
$saved_updates = [];

try {
    $adm_stmt = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending' OR application_status IS NULL");
    $new_admissions = $adm_stmt->fetchColumn();

    $stud_stmt = $conn->query("SELECT (SELECT COUNT(*) FROM students) + (SELECT COUNT(*) FROM applicants WHERE application_status = 'Approved' AND student_number NOT IN (SELECT student_number FROM students WHERE student_number IS NOT NULL))");
    $total_students = $stud_stmt->fetchColumn();

    $sub_stmt = $conn->query("SELECT COUNT(*) FROM subjects");
    $active_subjects = $sub_stmt ? $sub_stmt->fetchColumn() : 0;

    $sect_stmt = $conn->query("SELECT COUNT(*) FROM sections");
    $active_sections = $sect_stmt ? $sect_stmt->fetchColumn() : 0;

    $update_stmt = $conn->query("SELECT s.*, a.first_name, a.last_name, adm.admin_name, UNIX_TIMESTAMP(s.created_at) as unix_time
                                 FROM system_updates s 
                                 LEFT JOIN applicants a ON s.student_id = a.application_id 
                                 LEFT JOIN admins adm ON s.admin_id = adm.admin_id 
                                 ORDER BY s.id DESC LIMIT 30");
    $saved_updates = $update_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $total_students = 0;
    $new_admissions = 0;
    $active_subjects = 0;
    $active_sections = 0;
}

date_default_timezone_set('Asia/Manila');
$as_of_date = date('Y-m-d H:i:s');
$current_semester = "1st Semester, AY 2026-2027";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
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

        .activity-card {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .tab-indicator {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
        }

        .quick-access-btn {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            font-weight: 600;
            text-align: left;
        }

        .quick-access-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 44, 94, 0.08) !important;
            background-color: #fafbfc;
        }

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .as-of-date {
            font-size: 11px;
            color: #6c757d;
            font-weight: 500;
            margin-top: 3px;
            display: block;
        }

        .sidebar-semester-text {
            color: #adb5bd;
            font-size: 11px;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }

        .user-info .username {
            color: #ffffff;
            font-weight: 600;
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
    <?php if (!empty($toast_notification))
        echo $toast_notification; ?>

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
                        <li class="nav-item"><a href="dashboard.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="students.php" class="nav-link"><i
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
            <div class="app-content-header">
                <div class="container-fluid">
                    <h3 class="mb-0 mt-3 fw-bold text-dark">Dashboard</h3>
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
                                    <h4 id="totalStudentsCounter" class="fw-bold mb-0 text-dark">
                                        <?php echo number_format($total_students); ?>
                                    </h4><span class="as-of-date"><span id="studTimer">Last Updated: Just
                                            now</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-clipboard-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">New
                                        Applications</span>
                                    <h4 id="newAdmissionsCounter" class="fw-bold mb-0 text-warning">
                                        <?php echo number_format($new_admissions); ?>
                                    </h4><span class="as-of-date"><span id="admTimer">Last Updated: Just
                                            now</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-danger text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-book-half"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Active
                                        Subjects</span>
                                    <h4 id="activeSubjectsCounter" class="fw-bold mb-0 text-danger">
                                        <?php echo number_format($active_subjects); ?>
                                    </h4><span class="as-of-date"><span id="subTimer">Last Updated: Just
                                            now</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded-3 border">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-grid-3x3-gap-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block fw-semibold">Active
                                        Sections</span>
                                    <h4 id="activeSectionsCounter" class="fw-bold mb-0 text-success">
                                        <?php echo number_format($active_sections); ?>
                                    </h4><span class="as-of-date"><span id="sectTimer">Last Updated: Just
                                            now</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2"><a href="students.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-people text-primary fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Students</div><span class="text-muted small"
                                            style="font-size:10px;">Directory &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                        <div class="col-6 col-md-4 col-xl-2"><a href="admissions.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-clipboard-check text-warning fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Admissions</div><span class="text-muted small"
                                            style="font-size:10px;">Review &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                        <div class="col-6 col-md-4 col-xl-2"><a href="subjects.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-book text-success fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Subjects</div><span class="text-muted small"
                                            style="font-size:10px;">Curriculums &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                        <div class="col-6 col-md-4 col-xl-2"><a href="notice.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-megaphone text-danger fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Notice</div><span class="text-muted small"
                                            style="font-size:10px;">Advisories &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                        <div class="col-6 col-md-4 col-xl-2"><a href="users.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-person-lock text-info fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Users</div><span class="text-muted small"
                                            style="font-size:10px;">Accounts &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                        <div class="col-6 col-md-4 col-xl-2"><a href="settings.php"
                                class="btn bg-white w-100 py-3 rounded-3 shadow-sm quick-access-btn text-dark text-decoration-none">
                                <div class="d-flex align-items-center px-2"><i
                                        class="bi bi-gear text-secondary fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Settings</div><span class="text-muted small"
                                            style="font-size:10px;">System &raquo;</span>
                                    </div>
                                </div>
                            </a></div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card activity-card">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center"><i
                                            class="bi bi-arrow-repeat me-2 text-primary"></i>Recent System & Module
                                        Updates</h5>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <select id="tabSourceFilter" class="form-select form-select-sm border"
                                                style="width: 150px; border-radius:4px;"
                                                onchange="filterActivityLogFeed()">
                                                <option value="ALL">Show All Tabs</option>
                                                <option value="STUDENTS">Students</option>
                                                <option value="ADMISSIONS">Admissions</option>
                                                <option value="PROGRAMS">Programs</option>
                                                <option value="SUBJECTS">Subjects</option>
                                                <option value="SCHEDULES">Schedules / Sections</option>
                                                <option value="NOTICE">Notices</option>
                                                <option value="USERS">Users</option>
                                            </select>
                                        </div>
                                        <form method="POST" action="dashboard.php"
                                            onsubmit="return confirm('Completely purge recent module notification histories?');">
                                            <button type="submit" name="clear_system_logs"
                                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                                style="border-radius: 4px;"><i class="bi bi-trash3"></i> Clear
                                                Logs</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0" id="activityFeedTable">
                                            <thead
                                                class="table-light small text-uppercase text-secondary border-bottom">
                                                <tr>
                                                    <th class="ps-4" style="width: 18%;">Source Tab</th>
                                                    <th style="width: 57%;">Activity Log / Action Taken</th>
                                                    <th class="pe-4 text-end" style="width: 25%;">Time Elapsed</th>
                                                </tr>
                                            </thead>
                                            <tbody id="liveActivityLogContainer">
                                                <?php if (empty($saved_updates)): ?>
                                                    <tr id="emptyFeedFallbackPlaceholderRow">
                                                        <td colspan="3" class="text-center py-4 text-muted small fw-medium">
                                                            <i class="bi bi-info-circle me-1"></i> No live system histories
                                                            available.
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($saved_updates as $log): ?>
                                                        <?php
                                                        $badgeClass = "bg-primary-subtle text-primary";
                                                        $iconClass = "bi-people-fill";
                                                        $labelText = $log['module_tab'];

                                                        if ($log['module_tab'] === "ADMISSIONS") {
                                                            $badgeClass = "bg-warning-subtle text-warning-emphasis";
                                                            $iconClass = "bi-clipboard-fill";
                                                            $labelText = "Admissions";
                                                        } elseif ($log['module_tab'] === "STUDENTS") {
                                                            $badgeClass = "bg-primary-subtle text-primary";
                                                            $iconClass = "bi-people-fill";
                                                            $labelText = "Students";
                                                        } elseif ($log['module_tab'] === "PROGRAMS") {
                                                            $badgeClass = "bg-danger-subtle text-danger";
                                                            $iconClass = "bi-clipboard-data-fill";
                                                            $labelText = "Programs";
                                                        } elseif ($log['module_tab'] === "SUBJECTS") {
                                                            $badgeClass = "bg-success-subtle text-success";
                                                            $iconClass = "bi-book-half";
                                                            $labelText = "Subjects";
                                                        } elseif ($log['module_tab'] === "SCHEDULES") {
                                                            $badgeClass = "bg-info-subtle text-info-emphasis";
                                                            $iconClass = "bi-grid-3x3-gap-fill";
                                                            $labelText = "Subjects";
                                                        } elseif ($log['module_tab'] === "NOTICE" || (isset($log['custom_message']) && strpos(strtolower($log['custom_message']), 'notice') !== false)) {
                                                            $badgeClass = "bg-success-subtle text-success";
                                                            $iconClass = "bi-exclamation-circle-fill";
                                                            $labelText = "Notices";
                                                        } else {
                                                            $badgeClass = "bg-secondary-subtle text-secondary-emphasis";
                                                            $iconClass = "bi-person-check-fill";
                                                            $labelText = "Users";
                                                        }

                                                        $message = $log['custom_message'];
                                                        if (strpos(strtolower($message), 'submitted an application') !== false && empty($log['admin_id'])) {
                                                            $message = "A new application has been submitted and received.";
                                                        } else if (strpos($message, "{student_name}") !== false) {
                                                            $student_string = "";
                                                            if (!empty($log['first_name']) && !empty($log['last_name'])) {
                                                                $student_string = "<span class='fw-bold text-dark'>" . htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) . "</span>";
                                                            }
                                                            $message = str_replace("{student_name}", $student_string, $message);
                                                        }

                                                        $admin_prefix = "";
                                                        if (!empty($log['admin_name'])) {
                                                            $admin_prefix = "<span class='fw-bold text-dark'>" . htmlspecialchars($log['admin_name']) . " (" . htmlspecialchars($log['admin_id'] ?? '') . ")</span>: ";
                                                        } elseif (!empty($log['admin_id'])) {
                                                            $admin_prefix = "<span class='fw-bold text-dark'>" . htmlspecialchars($log['admin_id']) . "</span>: ";
                                                        }
                                                        ?>
                                                        <tr data-category="<?php echo $log['module_tab']; ?>"
                                                            class="db-saved-log-row"
                                                            data-timestamp="<?php echo $log['unix_time']; ?>">
                                                            <td class="ps-4"><span
                                                                    class="badge <?php echo $badgeClass; ?> tab-indicator"><i
                                                                        class="bi <?php echo $iconClass; ?> me-1"></i>
                                                                    <?php echo $labelText; ?></span></td>
                                                            <td><?php echo $admin_prefix . $message; ?></td>
                                                            <td class="pe-4 text-end text-muted small log-timestamp-cell">
                                                                Calculating...</td>
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

                </div>
            </div>
        </main>

        <footer class="app-footer px-4 py-3 border-top bg-white small text-muted">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - &copy; 2026</div>
            <strong><span class="float-end">&nbsp;All rights reserved.</span></strong>
            <div class="clearfix"></div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script>
        let studSecondsElapsed = 0; let admSecondsElapsed = 0;
        let subSecondsElapsed = 0; let sectSecondsElapsed = 0;
        let lastLogFetchTimestamp = '<?php echo $as_of_date; ?>';

        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        function formatRelativeTimeString(totalSeconds) {
            if (totalSeconds < 2) return "Just now";
            if (totalSeconds < 60) return `${totalSeconds} seconds ago`;
            const minutes = Math.floor(totalSeconds / 60);
            if (minutes < 60) return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
            return `${Math.floor(hours / 24)} days ago`;
        }

        function refreshSavedLogsTimestamps() {
            const currentUnixNow = Math.floor(Date.now() / 1000);
            document.querySelectorAll('.db-saved-log-row').forEach(row => {
                const rowTimestamp = parseInt(row.getAttribute('data-timestamp'));
                const cell = row.querySelector('.log-timestamp-cell');
                if (cell) cell.textContent = formatRelativeTimeString(Math.max(0, currentUnixNow - rowTimestamp));
            });
        }

        function incrementUpdateTimers() {
            studSecondsElapsed++; admSecondsElapsed++; subSecondsElapsed++; sectSecondsElapsed++;
            document.getElementById('studTimer').textContent = "Last Updated: " + formatRelativeTimeString(studSecondsElapsed);
            document.getElementById('admTimer').textContent = "Last Updated: " + formatRelativeTimeString(admSecondsElapsed);
            document.getElementById('subTimer').textContent = "Last Updated: " + formatRelativeTimeString(subSecondsElapsed);
            document.getElementById('sectTimer').textContent = "Last Updated: " + formatRelativeTimeString(sectSecondsElapsed);
            refreshSavedLogsTimestamps();
        }

        function filterActivityLogFeed() {
            const selection = document.getElementById("tabSourceFilter").value;
            document.querySelectorAll("#liveActivityLogContainer tr[data-category]").forEach(row => {
                const cat = row.getAttribute("data-category");
                if (selection === "ALL") { row.style.display = ""; }
                else if (selection === "NOTICE") { row.style.display = (cat === "NOTICE" || cat === "USERS") ? "" : "none"; }
                else { row.style.display = (cat === selection) ? "" : "none"; }
            });
        }

        function pollLiveDatabaseMetrics() {
            fetch(`?fetch_live_counts=1&last_fetch=${encodeURIComponent(lastLogFetchTimestamp)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const currentStudCount = parseInt(document.getElementById('totalStudentsCounter').textContent.replace(/,/g, ''));
                        const currentAdmCount = parseInt(document.getElementById('newAdmissionsCounter').textContent.replace(/,/g, ''));
                        const currentSubCount = parseInt(document.getElementById('activeSubjectsCounter').textContent.replace(/,/g, ''));
                        const currentSectCount = parseInt(document.getElementById('activeSectionsCounter').textContent.replace(/,/g, ''));

                        if (data.total_students !== currentStudCount) { document.getElementById('totalStudentsCounter').textContent = data.total_students.toLocaleString(); studSecondsElapsed = 0; }
                        if (data.new_admissions !== currentAdmCount) { document.getElementById('newAdmissionsCounter').textContent = data.new_admissions.toLocaleString(); const sideBadge = document.getElementById('admissionsBadge'); if (sideBadge) sideBadge.textContent = data.new_admissions; admSecondsElapsed = 0; }
                        if (data.active_subjects !== currentSubCount) { document.getElementById('activeSubjectsCounter').textContent = data.active_subjects.toLocaleString(); subSecondsElapsed = 0; }
                        if (data.active_sections !== currentSectCount) { document.getElementById('activeSectionsCounter').textContent = data.active_sections.toLocaleString(); sectSecondsElapsed = 0; }

                        lastLogFetchTimestamp = data.server_timestamp;
                    }
                }).catch(err => console.log('Metrics polling engine fault baseline loop:', err));
        }

        setInterval(runLiveDashboardClock, 1000);
        setInterval(incrementUpdateTimers, 1000);
        setInterval(pollLiveDatabaseMetrics, 4000);

        window.onload = function () { runLiveDashboardClock(); refreshSavedLogsTimestamps(); pollLiveDatabaseMetrics(); };
    </script>
</body>

</html>