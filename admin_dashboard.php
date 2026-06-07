<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
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

        .status-dot {
            width: 12px;
            height: 12px;
            background-color: var(--bs-success);
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid #002c5e;
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
        
        .activity-card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        
        .tab-indicator {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date" style="margin-top:6px; margin-bottom: 9px;">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <?php echo date('F j, Y') . " -"; ?>
                                <?php echo date("h:iA"); ?>
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
                            <div class="avatar-placeholder">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        </div>
                        <div class="user-info avatar-wrapper">
                            <div class="username">Admin 1</div>
                            <div class="status-text" style="color: #35e400; margin-top: -5px">Online</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column" id="navigation">
                        <li class="nav-header">MAIN MENU</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>
                                    Dashboard
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>
                                    Students
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-fill"></i>
                                <p>
                                    Admissions
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>
                                    Programs
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>
                                    Subjects
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-calendar3"></i>
                                <p>
                                    Schedules
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-flag-fill"></i>
                                <p>
                                    Reports
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>
                                    Notice
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-person-check-fill"></i>
                                <p>
                                    Users
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>
                                    Settings
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
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
                            <h3 class="mb-3 mt-3 fw-bold">Dashboard</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Students</span>
                                    <h4 class="fw-bold mb-0">1,248</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-clipboard-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">New Admissions</span>
                                    <h4 class="fw-bold mb-0 text-warning">42</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-clipboard2-minus-fill"></i>
                                tap</span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Active Subjects</span>
                                    <h4 class="fw-bold mb-0 text-success">185</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Schedules Block</span>
                                    <h4 class="fw-bold mb-0 text-info">36</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card activity-card">
                                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-arrow-repeat me-2 text-primary"></i>Recent System & Module Updates
                                    </h5>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">Live Monitoring Feed</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-uppercase text-secondary">
                                                <tr>
                                                    <th class="ps-4" style="width: 18%;">Source Tab</th>
                                                    <th style="width: 57%;">Activity Log / Action Taken</th>
                                                    <th class="pe-4 text-end" style="width: 25%;">Time Elapsed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-primary-subtle text-primary tab-indicator">
                                                            <i class="bi bi-people-fill me-1"></i> Students
                                                        </span>
                                                    </td>
                                                    <td>
                                                        Profile status of student <span class="fw-bold text-dark">Yoyenk D. Creator</span> (ID: 2026-0412) was successfully changed to <span class="text-success fw-semibold">Officially Enrolled</span>.
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> Just now</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-warning-subtle text-warning-emphasis tab-indicator">
                                                            <i class="bi bi-clipboard-fill me-1"></i> Admissions
                                                        </span>
                                                    </td>
                                                    <td>
                                                        New enrollment application received from transferee applicant <span class="fw-bold text-dark">Miguelito Agudon</span> (Pending Requirements Review).
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 5 mins ago</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-danger-subtle text-danger tab-indicator">
                                                            <i class="bi bi-clipboard-data-fill me-1"></i> Programs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        Curriculum structure modifications were applied to the <span class="fw-bold text-dark">Bachelor of Science in Information Technology (BSIT)</span> program.
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 24 mins ago</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-success-subtle text-success tab-indicator">
                                                            <i class="bi bi-clipboard2-minus-fill me-1"></i> Subjects
                                                        </span>
                                                    </td>
                                                    <td>
                                                        A new curriculum subject course entry <span class="font-monospace fw-bold text-dark">IT211 - Integrative Programming</span> was added by the admin system.
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 42 mins ago</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-info-subtle text-info-emphasis tab-indicator">
                                                            <i class="bi bi-calendar3 me-1"></i> Schedules
                                                        </span>
                                                    </td>
                                                    <td>
                                                        Room configuration for <span class="fw-bold text-dark">BSIT - Section 201</span> was updated from Room 302 to Computer Laboratory Room 1.
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 1 hour ago</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis tab-indicator">
                                                            <i class="bi bi-person-check-fill me-1"></i> Users
                                                        </span>
                                                    </td>
                                                    <td>
                                                        Password recovery access credentials token generated for user account <span class="font-monospace text-dark">registrar_staff_02</span>.
                                                    </td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 2 hours ago</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top py-3 text-center">
                                    <small class="text-muted font-semibold"><i class="bi bi-info-circle me-1"></i> This interface acts as the secondary monitoring layer showing real-time feedback loop iterations from connected workspace tabs.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </main>

        <footer class="app-footer">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - </div>
            <strong>
                <span>&nbsp;All rights reserved.</span>
            </strong>
        </footer>
    </div>
</body>

</html>