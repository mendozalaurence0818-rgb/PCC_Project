<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admissions</title>
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
                <a href="admin_dashboard.php" class="brand-link">
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
                            <a href="reports.php" class="nav-link">
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
                            <a href="users.php" class="nav-link">
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
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-3 mt-3 fw-bold">Admissions Management</h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <button class="btn btn-primary" style="background-color: #002c5e; border-color: #002c5e;">
                                <i class="bi bi-person-plus-fill me-2"></i>Add New Applicant
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-hourglass-split"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Pending Applications</span>
                                    <h4 class="fw-bold mb-0">28</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Approved Today</span>
                                    <h4 class="fw-bold mb-0 text-success">14</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-clipboard-check-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Applicants</span>
                                    <h4 class="fw-bold mb-0 text-primary">142</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <ul class="nav nav-pills card-header-pills">
                                        <li class="nav-item"><a class="nav-link active bg-warning text-dark fw-semibold"
                                                href="#">Pending Evaluation</a></li>
                                        <li class="nav-item"><a class="nav-link text-secondary" href="#">Approved</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link text-secondary" href="#">Rejected</a>
                                        </li>
                                    </ul>
                                    <div class="input-group style" style="width: 250px;">
                                        <input type="text" class="form-control form-control-sm"
                                            placeholder="Search applicant...">
                                        <button class="btn btn-outline-secondary btn-sm"><i
                                                class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-uppercase text-secondary">
                                                <tr>
                                                    <th class="ps-4">Application No.</th>
                                                    <th>Full Name</th>
                                                    <th>Course Applied</th>
                                                    <th>Date Submitted</th>
                                                    <th>Status</th>
                                                    <th class="pe-4 text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-4 font-monospace fw-bold text-secondary">PCC-2026-0089
                                                    </td>
                                                    <td class="fw-semibold text-dark">Maria Clara Santos</td>
                                                    <td>BSIT</td>
                                                    <td>June 05, 2026</td>
                                                    <td><span
                                                            class="badge bg-warning text-dark px-2 py-1 rounded-pill">Pending
                                                            Review</span></td>
                                                    <td class="pe-4 text-end">
                                                        <button class="btn btn-sm btn-success me-1"><i
                                                                class="bi bi-check-lg"></i></button>
                                                        <button class="btn btn-sm btn-danger me-1"><i
                                                                class="bi bi-x-lg"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary"><i
                                                                class="bi bi-eye"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 font-monospace fw-bold text-secondary">PCC-2026-0090
                                                    </td>
                                                    <td class="fw-semibold text-dark">Crisostomo Ibarra Jr.</td>
                                                    <td>BSIT</td>
                                                    <td>June 07, 2026</td>
                                                    <td><span
                                                            class="badge bg-warning text-dark px-2 py-1 rounded-pill">Pending
                                                            Review</span></td>
                                                    <td class="pe-4 text-end">
                                                        <button class="btn btn-sm btn-success me-1"><i
                                                                class="bi bi-check-lg"></i></button>
                                                        <button class="btn btn-sm btn-danger me-1"><i
                                                                class="bi bi-x-lg"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary"><i
                                                                class="bi bi-eye"></i></button>
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

        <footer class="app-footer font-medium">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - </div>
            <strong><span>&nbsp;All rights reserved.</span></strong>
        </footer>
    </div>
</body>

</html>