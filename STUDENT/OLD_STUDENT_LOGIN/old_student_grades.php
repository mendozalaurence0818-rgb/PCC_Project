<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Grades & Transcripts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link class="rtl_container" rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-blue-dark: #001d3d;
            --pcc-gray: #6c757d;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f4f6f9 !important;
        }

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
            background-color: #001d3d;
        }

        .user-info .username {
            color: #ffffff;
            font-weight: 600;
        }

        .user-info .status-text {
            color: #ffffff;
        }

        .content-card {
            border: none;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .btn-pcc-primary {
            background-color: var(--pcc-blue);
            color: #fff;
        }

        .btn-pcc-primary:hover {
            background-color: var(--pcc-blue-dark);
            color: #fff;
        }

        .btn-outline-pcc {
            border-color: var(--pcc-blue);
            color: var(--pcc-blue);
        }

        .btn-outline-pcc:hover {
            background-color: var(--pcc-blue);
            color: #fff;
        }

        .grade-excellent {
            color: #198754;
            font-weight: 800;
        }

        .grade-good {
            color: #0d6efd;
            font-weight: 700;
        }

        .grade-average {
            color: #fd7e14;
            font-weight: 700;
        }

        .grade-failed {
            color: #dc3545;
            font-weight: 800;
        }

        .status-badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
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
                            <div class="avatar-placeholder shadow-sm">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="user-info avatar-wrapper">
                            <div class="username">Juan Dela Cruz</div>
                            <div class="status-text" style="color: #f1b813; font-size: 0.80rem; margin-top: -3px">ID:
                                2024-001234</div>
                            <div class="status-text" style="color: #f1b813; font-size: 0.80rem; margin-top: -3px">BSIT -
                                3rd Year</div>
                            <div class="status-text" style="color: #4ade80; font-size: 0.75rem; margin-top: 2px"><i
                                    class="bi bi-circle-fill" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                Regular Standing</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column" id="navigation">
                        <li class="nav-header">ACADEMIC HUB</li>
                        <li class="nav-item">
                            <a href="old_student_dashboard.php" class="nav-link">
                                <i class="nav-icon bi bi-house-door-fill"></i>
                                <p>
                                    Dashboard
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_profile.php" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-person-fill"></i>
                                <p>
                                    Student Information
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_enrollment.php" class="nav-link">
                                <i class="nav-icon bi bi-laptop"></i>
                                <p>
                                    Online Enrollment
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_schedule.php" class="nav-link">
                                <i class="nav-icon bi bi-calendar-week-fill"></i>
                                <p>
                                    Schedule
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <!-- Active Link Moved Here for Grades Page -->
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-journal-check"></i>
                                <p>
                                    Grades & Transcripts
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_drop.php" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>
                                    Dropping of Subject
                                    <i class="nav-arrow bi bi-chevron-left"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_login.php" class="nav-link text-danger-emphasis"
                                onclick="return confirm('Are you sure you want to end your session?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main p-4">
            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h3 class="mb-3 fw-bold">Grades & Transcripts</h3>
                        </div>
                        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                            <button class="btn btn-pcc-primary btn-sm fw-semibold rounded-pill px-3"
                                onclick="window.print();">
                                <i class="bi bi-printer-fill me-1"></i> Print Grade Slip
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <div
                                class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded content-card h-100">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 55px; height: 55px; font-size: 24px;">
                                    <i class="bi bi-trophy-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Cumulative GPA</span>
                                    <h3 class="fw-bold mb-0 text-dark">1.47</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div
                                class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded content-card h-100">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 55px; height: 55px; font-size: 24px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Academic
                                        Standing</span>
                                    <h3 class="fw-bold mb-0 text-success">Regular</h3>
                                    <small class="text-muted fw-medium">Eligible for normal load</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-5">
                            <div class="card content-card h-100">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-file-text-fill me-2 text-primary"></i>Semester Grades
                                    </h5>

                                    <div class="d-flex align-items-center">
                                        <label for="semesterSelect"
                                            class="me-2 small fw-bold text-secondary text-nowrap">Filter By:</label>
                                        <select class="form-select form-select-sm fw-medium shadow-none"
                                            id="semesterSelect" style="width: 250px; border-color: #dee2e6;">
                                            <option value="2025-2">2025-2026 | 2nd Semester</option>
                                            <option value="2025-1">2025-2026 | 1st Semester</option>
                                            <option value="2024-2">2024-2025 | 2nd Semester</option>
                                            <option value="2024-1">2024-2025 | 1st Semester</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary small text-uppercase">
                                                <tr>
                                                    <th class="ps-4 py-3">Course Code</th>
                                                    <th class="py-3">Descriptive Title</th>
                                                    <th class="text-center py-3">Units</th>
                                                    <th class="text-center py-3">Midterm</th>
                                                    <th class="text-center py-3">Finals</th>
                                                    <th class="text-center py-3">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark small">
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">IT411</td>
                                                    <td class="fw-medium">Capstone Project 1 (Proposal & Prototyping)
                                                    </td>
                                                    <td class="text-center fw-semibold text-muted">3.0</td>
                                                    <td class="text-center fw-semibold">1.50</td>
                                                    <td class="text-center grade-good">1.50</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-success-subtle text-success status-badge">Passed</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">IT412</td>
                                                    <td class="fw-medium">Information Assurance and Security 2</td>
                                                    <td class="text-center fw-semibold text-muted">3.0</td>
                                                    <td class="text-center fw-semibold">1.75</td>
                                                    <td class="text-center grade-good">1.75</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-success-subtle text-success status-badge">Passed</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">PE400</td>
                                                    <td class="fw-medium">Advanced Team Sports Elective</td>
                                                    <td class="text-center fw-semibold text-muted">2.0</td>
                                                    <td class="text-center fw-semibold">1.00</td>
                                                    <td class="text-center grade-excellent">1.00</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-success-subtle text-success status-badge">Passed</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-light border-top">
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold text-secondary py-3">Term
                                                        Summary:</td>
                                                    <td class="text-center fw-bold text-dark py-3">8.0</td>
                                                    <td colspan="1" class="text-end fw-bold text-secondary py-3">Term
                                                        GPA:</td>
                                                    <td class="text-center fw-bold fs-6 text-dark py-3">1.47</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
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
            <strong>
                <span>&nbsp;All rights reserved.</span>
            </strong>
        </footer>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="../../js/adminlte.js"></script>
</body>

</html>