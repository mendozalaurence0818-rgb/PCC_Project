<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
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
                    <img src="../../images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Student</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="user-info avatar-wrapper">
                            <div class="username">Juan Dela Cruz</div>
                            <div class="status-text" style="color: #f1b813; font-size: 0.80rem; margin-top: -3px">ID: 2024-001234</div>
                            <div class="status-text" style="color: #f1b813; font-size: 0.80rem; margin-top: -3px">BSIT - 3rd Year</div>
                            <div class="status-text" style="color: #4ade80; font-size: 0.75rem; margin-top: 2px"><i class="bi bi-circle-fill" style="font-size: 0.5rem; vertical-align: middle;"></i> Regular Standing</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column" id="navigation">
                        <li class="nav-header">ACADEMIC HUB</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-house-door-fill"></i>
                                <p>
                                    Dashboard
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-calendar-week-fill"></i>
                                <p>
                                    My Schedule
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-journal-check"></i>
                                <p>
                                    Grades & Transcripts
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-laptop"></i>
                                <p>
                                    LMS / Canvas
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-person-fill"></i>
                                <p>
                                    Clearance & Holds
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>
                                    Profile Settings
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
                            <h3 class="mb-3 mt-3 fw-bold">Student Dashboard</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-primary d-flex align-items-center shadow-sm border-0" role="alert" style="background-color: #e6f2ff; color: #002c5e;">
                                <i class="bi bi-clock-history fs-2 me-4"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1">Registration Window Opens In:</h5>
                                    <div class="fs-4 fw-bold font-monospace">03 Days : 14 Hrs : 45 Mins</div>
                                    <small class="text-muted fw-semibold">Batch 3 (3rd Year Students) - Schedule Builder unlocks at exactly 08:00 AM.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-award-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Current GPA</span>
                                    <h4 class="fw-bold mb-0">1.75</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-book-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Completed Units</span>
                                    <h4 class="fw-bold mb-0 text-warning">86</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-cash-stack"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Account Balance</span>
                                    <h4 class="fw-bold mb-0 text-success">₱0.00</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Active Holds</span>
                                    <h4 class="fw-bold mb-0 text-info">1 Pending</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8 col-12 mb-4">
                            <div class="card activity-card h-100">
                                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-bell-fill me-2 text-primary"></i>Recent Announcements
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-uppercase text-secondary">
                                                <tr>
                                                    <th class="ps-4" style="width: 20%;">Category</th>
                                                    <th style="width: 55%;">Details</th>
                                                    <th class="pe-4 text-end" style="width: 25%;">Received</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-primary-subtle text-primary tab-indicator">
                                                            <i class="bi bi-journal-text me-1"></i> Academics
                                                        </span>
                                                    </td>
                                                    <td>Your Midterm Grade for <span class="fw-bold text-dark">IT211 - Integrative Programming</span> has been officially posted.</td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> Just now</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-warning-subtle text-warning-emphasis tab-indicator">
                                                            <i class="bi bi-calendar-event-fill me-1"></i> Campus Life
                                                        </span>
                                                    </td>
                                                    <td>Reminder: Intramural Sports sign-ups close this Friday.</td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> 3 hours ago</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-danger-subtle text-danger tab-indicator">
                                                            <i class="bi bi-exclamation-circle-fill me-1"></i> Action Required
                                                        </span>
                                                    </td>
                                                    <td>Please submit your <span class="fw-bold text-dark">Annual Medical Clearance Form</span> to the campus clinic.</td>
                                                    <td class="pe-4 text-end text-muted small"><i class="bi bi-clock me-1"></i> Yesterday</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12 mb-4">
                            <div class="card activity-card h-100">
                                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-shield-lock-fill me-2 text-warning"></i>Clearance Status
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted mb-3">All units must be cleared before the Schedule Builder is unlocked for enrollment.</p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="bi bi-cash me-2 text-secondary"></i>Accounting</span>
                                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Cleared</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="bi bi-book me-2 text-secondary"></i>Library</span>
                                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Cleared</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="bi bi-person-hearts me-2 text-secondary"></i>Guidance</span>
                                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Cleared</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="bi bi-building me-2 text-secondary"></i>Dean's Office</span>
                                            <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i> Hold</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-light border-top py-3 text-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary w-100 fw-bold">Resolve Holds</a>
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