<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Subjects & Sections</title>
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
                            <a href="admin_subjects.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                    <div class="d-flex justify-content-between align-items-center my-3">
                        <h3 class="fw-bold m-0">Subject & Section Configurations</h3>
                        <div>
                            <button class="btn btn-success btn-sm me-2"><i class="bi bi-plus-circle me-1"></i> Add New
                                Section</button>
                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Add New
                                Subject</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bi bi-grid-3x3-gap-fill me-2 text-success"></i>Available BSIT Sections</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase text-secondary">
                                    <tr>
                                        <th class="ps-4">Section Name</th>
                                        <th>Target Year</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Section Operations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-semibold">BSIT-101</td>
                                        <td>1st Year</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i>
                                                Edit</button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold">BSIT-102</td>
                                        <td>1st Year</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i>
                                                Edit</button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold">BSIT-201</td>
                                        <td>2nd Year</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i>
                                                Edit</button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold">BSIT-202</td>
                                        <td>2nd Year</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i>
                                                Edit</button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bi bi-book-fill me-2 text-primary"></i>Subject Management & Enrollment
                                Controls</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase text-secondary">
                                    <tr>
                                        <th class="ps-4">Subject Code</th>
                                        <th>Descriptive Title</th>
                                        <th>Units</th>
                                        <th>Admin controls</th>
                                        <th class="pe-4 text-end">Student User Access (Add/Drop/Withdraw)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-semibold">IT-IC101</td>
                                        <td>Introduction to Computing</td>
                                        <td>3</td>
                                        <td>
                                            <button class="btn btn-xs btn-warning text-white"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-xs btn-outline-success"><i
                                                    class="bi bi-plus-circle"></i> Add</button>
                                            <button class="btn btn-xs btn-outline-danger"><i
                                                    class="bi bi-dash-circle"></i> Drop</button>
                                            <button class="btn btn-xs btn-outline-secondary"><i
                                                    class="bi bi-exclamation-octagon"></i> Withdraw Course</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold">IT-IPT201</td>
                                        <td>Integrative Programming & Technologies</td>
                                        <td>3</td>
                                        <td>
                                            <button class="btn btn-xs btn-warning text-white"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-xs btn-outline-success"><i
                                                    class="bi bi-plus-circle"></i> Add</button>
                                            <button class="btn btn-xs btn-outline-danger"><i
                                                    class="bi bi-dash-circle"></i> Drop</button>
                                            <button class="btn btn-xs btn-outline-secondary"><i
                                                    class="bi bi-exclamation-octagon"></i> Withdraw Course</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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