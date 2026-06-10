<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="css/adminlte.css" />
    <link rel="icon" href="images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .sidebar-bg {
            background-color: #002c5e !important;
        }

        .sidebar-bg .nav-link,
        .sidebar-bg .brand-link,
        .sidebar-bg .nav-header {
            color: #ffffff !important;
        }

        .sidebar-bg-active {
            color: #002c5e !important;
            background-color: #f1b813 !important;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
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
            background-color: #001d3d;
        }

        .user-info .username {
            color: #ffffff;
            font-weight: 600;
        }

        .user-info .status-text {
            color: #ffffff;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php
    $current_school_year = '2026 - 2027';
    $current_semester = '1st Semester';
    $enrollment_status = 'Open';
    $grading_status = 'Closed';
    $system_maintenance = 'Disabled';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['save_settings'])) {
            echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Settings Saved!</strong> System parameters updated successfully.</div>";
        } elseif (isset($_POST['change_access_code'])) {
            echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Code Changed!</strong> New Admin Access Code has been updated.</div>";
        } elseif (isset($_POST['trigger_backup'])) {
            echo "<div class='alert alert-info position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Backup Created!</strong> pcc_db_backup.sql generated.</div>";
        }
    }
    ?>

    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date" style="margin-top:6px; margin-bottom: 9px;">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <?php echo date('F j, Y') . " - " . date("h:iA"); ?>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand" style="border-right: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link">
                    <img src="images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
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
                            <div class="username">Admin 1</div>
                            <div class="status-text" style="color: #35e400; margin-top: -5px">Online</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column" id="navigation">
                        <li class="nav-header">MAIN MENU</li>
                        <li class="nav-item">
                            <a href="admin_dashboard.php" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_student.php" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Students <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_admissions.php" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_programs.php" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_subjects.php" class="nav-link">
                                <i class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_schedules.php" class="nav-link">
                                <i class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>

                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item">
                            <a href="admin_notice.php" class="nav-link">
                                <i class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_users.php" class="nav-link">
                                <i class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_settings.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings <i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_login.php" class="nav-link text-danger-emphasis"
                                onclick="return confirm('Are you sure you want to end your session?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p>Logout</p>
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
                            <h3 class="mb-0 mt-3 fw-bold">Portal Settings</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-sliders me-2 text-primary"></i>Global Academic Controls
                                    </h5>
                                </div>
                                <form method="POST" action="?">
                                    <div class="card-body bg-white text-dark">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Active School Year</label>
                                                <select name="school_year" class="form-select form-select-sm" required>
                                                    <option value="2025 - 2026" <?php echo $current_school_year === '2025 - 2026' ? 'selected' : ''; ?>>2025 - 2026</option>
                                                    <option value="2026 - 2027" <?php echo $current_school_year === '2026 - 2027' ? 'selected' : ''; ?>>2026 - 2027</option>
                                                    <option value="2027 - 2028" <?php echo $current_school_year === '2027 - 2028' ? 'selected' : ''; ?>>2027 - 2028</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Current Academic
                                                    Semester</label>
                                                <select name="semester" class="form-select form-select-sm" required>
                                                    <option value="1st Semester" <?php echo $current_semester === '1st Semester' ? 'selected' : ''; ?>>1st Semester</option>
                                                    <option value="2nd Semester" <?php echo $current_semester === '2nd Semester' ? 'selected' : ''; ?>>2nd Semester</option>
                                                    <option value="Summer" <?php echo $current_semester === 'Summer' ? 'selected' : ''; ?>>Summer</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Enrollment State</label>
                                                <select name="enrollment_status" class="form-select form-select-sm">
                                                    <option value="Open" <?php echo $enrollment_status === 'Open' ? 'selected' : ''; ?>>Open / Active</option>
                                                    <option value="Closed" <?php echo $enrollment_status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Grading Module</label>
                                                <select name="grading_status" class="form-select form-select-sm">
                                                    <option value="Open" <?php echo $grading_status === 'Open' ? 'selected' : ''; ?>>Open / Accept Inputs</option>
                                                    <option value="Closed" <?php echo $grading_status === 'Closed' ? 'selected' : ''; ?>>Closed / Locked</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Maintenance Mode</label>
                                                <select name="system_maintenance" class="form-select form-select-sm">
                                                    <option value="Disabled" <?php echo $system_maintenance === 'Disabled' ? 'selected' : ''; ?>>Disabled</option>
                                                    <option value="Enabled" <?php echo $system_maintenance === 'Enabled' ? 'selected' : ''; ?>>Enabled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-end py-2">
                                        <button type="submit" name="save_settings" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-circle-fill me-1"></i>Save Configurations
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-key-fill me-2 text-primary"></i>Change Admin Access Code
                                    </h5>
                                </div>
                                <form method="POST" action="?">
                                    <div class="card-body bg-white text-dark">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Current Access Code</label>
                                                <input type="password" name="current_code"
                                                    class="form-control form-control-sm" placeholder="••••••••"
                                                    required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">New Access Code</label>
                                                <input type="password" name="new_code"
                                                    class="form-control form-control-sm"
                                                    placeholder="Enter new access key..." required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Confirm New Access Code</label>
                                                <input type="password" name="confirm_code"
                                                    class="form-control form-control-sm"
                                                    placeholder="Confirm new access key..." required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-end py-2">
                                        <button type="submit" name="change_access_code" class="btn btn-sm btn-primary">
                                            <i class="bi bi-shield-lock-fill me-1"></i>Update Access Key
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-database-fill-gear me-2 text-primary"></i>System Maintenance &
                                        Database Utilities
                                    </h5>
                                </div>
                                <div class="card-body bg-white text-dark">
                                    <p class="small text-secondary mb-3">Maintain database indexing health by generating
                                        regular data snapshots and cleanups manually below.</p>
                                    <form method="POST" action="" class="d-inline-block">
                                        <button type="submit" name="trigger_backup"
                                            class="btn btn-sm btn-outline-primary fw-semibold me-2">
                                            <i class="bi bi-cloud-arrow-down-fill me-1"></i>Backup Database (.SQL)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <footer class="app-footer">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - </div>
            <strong><span>&nbsp;All rights reserved.</span></strong>
        </footer>
    </div>
</body>

</html>