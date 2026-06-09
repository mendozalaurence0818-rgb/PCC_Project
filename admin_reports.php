<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
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
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date" style="margin-top:6px; margin-bottom: 9px;">
                                <?php date_default_timezone_set('Asia/Manila');
                                echo date('F j, Y') . " - " . date("h:iA"); ?>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand" style="border-right: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="admin_dashboard.php" class="brand-link">
                    <img src="images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Admin</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder">
                                <i class="fa-solid fa-user"></i>
                            </div>
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
                                <p>
                                    Dashboard
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
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
                            <a href="admin_reports.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-flag-fill"></i>
                                <p>Reports <i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a>
                        </li>
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
                            <a href="admin_settings.php" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings <i class="nav-arrow bi bi-chevron-left"></i></p>
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
                    <h3 class="fw-bold my-3">Reports & Audit Logs</h3>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card p-3 border-0 shadow-sm bg-white text-center">
                                <i class="bi bi-file-earmark-pdf-fill text-danger display-6 mb-2"></i>
                                <h6 class="fw-bold mb-1">Enrollment Report</h6>
                                <p class="text-muted small mb-3">Download complete demographic summaries.</p>
                                <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-download me-1"></i>
                                    Export PDF</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 border-0 shadow-sm bg-white text-center">
                                <i class="bi bi-file-earmark-excel-fill text-success display-6 mb-2"></i>
                                <h6 class="fw-bold mb-1">Subject Load Sheets</h6>
                                <p class="text-muted small mb-3">Check section and subject assignment distributions.</p>
                                <button class="btn btn-sm btn-outline-success w-100"><i class="bi bi-download me-1"></i>
                                    Export Excel</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 border-0 shadow-sm bg-white text-center">
                                <i class="bi bi-printer-fill text-primary display-6 mb-2"></i>
                                <h6 class="fw-bold mb-1">Class Schedules Printable</h6>
                                <p class="text-muted small mb-3">Generate printable formats for campus bulletin boards.
                                </p>
                                <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-printer me-1"></i>
                                    Print Layout</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-shield-lock-fill text-secondary me-2"></i>System Security Audit
                                        Trail</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-uppercase text-secondary">
                                                <tr>
                                                    <th class="ps-4">User</th>
                                                    <th>Role</th>
                                                    <th>Action Conducted</th>
                                                    <th>Target Module</th>
                                                    <th class="pe-4 text-end">Timestamp</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-4 fw-semibold">admin_01</td>
                                                    <td><span class="badge bg-danger">Admin</span></td>
                                                    <td>Created new available sections for 1st/2nd year IT students</td>
                                                    <td>Subjects Module</td>
                                                    <td class="pe-4 text-end text-muted small">June 08, 2026 11:15 AM
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-semibold">admin_02</td>
                                                    <td><span class="badge bg-danger">Admin</span></td>
                                                    <td>Approved pending student application</td>
                                                    <td>Admissions Module</td>
                                                    <td class="pe-4 text-end text-muted small">June 08, 2026 11:15 AM
                                                    </td>
                                                </tr>
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

        <footer class="app-footer">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - </div>
            <strong><span>&nbsp;All rights reserved.</span></strong>
        </footer>
    </div>
</body>

</html>