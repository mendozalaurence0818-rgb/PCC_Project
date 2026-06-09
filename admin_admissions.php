<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admissions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/adminlte.css" />
    <link rel="icon" href="images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .sidebar-bg { background-color: #002c5e !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: #002c5e !important; background-color: #f1b813 !important; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: #001d3d; }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .user-info .status-text { color: #ffffff; }
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
                                <?php date_default_timezone_set('Asia/Manila'); echo date('F j, Y') . " - " . date("h:iA"); ?>
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
                        <div class="user-info avatar-wrapper">
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
                            <a href="admin_admissions.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                            <a href="admin_reports.php" class="nav-link">
                                <i class="nav-icon bi bi-flag-fill"></i>
                                <p>Reports <i class="nav-arrow bi bi-chevron-left"></i></p>
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
                            <a href="admin_login.php" class="nav-link text-danger-emphasis" onclick="return confirm('Are you sure you want to end your session?');">
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="fw-bold my-3">Admission Management</h3>
                        <button class="btn btn-primary" style="background-color: #002c5e; border: none;"><i class="bi bi-person-plus-fill me-2"></i>Add New Applicant</button>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <span class="text-muted small text-uppercase fw-bold">Total Applications</span>
                                <h3 class="fw-bold text-dark my-1">4</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <span class="text-muted small text-uppercase fw-bold">New Students (1st Year)</span>
                                <h3 class="fw-bold text-primary my-1">2</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <span class="text-muted small text-uppercase fw-bold">Old Students (2nd Year)</span>
                                <h3 class="fw-bold text-warning my-1">2</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <span class="text-muted small text-uppercase fw-bold">Pending Review</span>
                                <h3 class="fw-bold text-danger my-1">0</h3>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm bg-white text-dark mb-4" role="alert" style="border-left: 5px solid #ffc107 !important;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                        <strong>System Alert:</strong> A student account has an unresolved balance payment issue. Action required within the records department.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-journal-text me-2 text-secondary"></i>Applicant Classification Records</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase text-secondary">
                                        <tr>
                                            <th class="ps-4">Applicant ID</th>
                                            <th>Full Name</th>
                                            <th>Classification</th>
                                            <th>Course / Program</th>
                                            <th>Year Level</th>
                                            <th class="pe-4 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-4 fw-semibold">APP-2026-001</td>
                                            <td>Agudon, Miguelito M.</td>
                                            <td><span class="badge bg-success-subtle text-success">New Student</span></td>
                                            <td>BS Information Technology</td>
                                            <td>1st Year</td>
                                            <td class="pe-4 text-end"><button class="btn btn-sm btn-outline-secondary">Manage</button></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-semibold">APP-2026-002</td>
                                            <td>Depollo, Ralph Geofrey G.</td>
                                            <td><span class="badge bg-success-subtle text-success">New Student</span></td>
                                            <td>BS Information Technology</td>
                                            <td>1st Year</td>
                                            <td class="pe-4 text-end"><button class="btn btn-sm btn-outline-secondary">Manage</button></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-semibold">APP-2024-001</td>
                                            <td>Mendoza, Laurence C.</td>
                                            <td><span class="badge bg-primary-subtle text-primary">Old Student</span></td>
                                            <td>BS Information Technology</td>
                                            <td>2nd Year</td>
                                            <td class="pe-4 text-end"><button class="btn btn-sm btn-outline-secondary">Manage</button></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-semibold">APP-2024-002</td>
                                            <td>Villarta, Joeshua Louis</td>
                                            <td><span class="badge bg-primary-subtle text-primary">Old Student</span></td>
                                            <td>BS Information Technology</td>
                                            <td>2nd Year</td>
                                            <td class="pe-4 text-end"><button class="btn btn-sm btn-outline-secondary">Manage</button></td>
                                        </tr>
                                    </tbody>
                                </table>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>