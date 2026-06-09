<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Schedule</title>
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

        /* Sidebar Styles (Matched exactly to Dashboard) */
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

        /* Card Styles */
        .schedule-card {
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

        /* Schedule Grid Styles */
        .schedule-grid-table th,
        .schedule-grid-table td {
            border: 1px solid #edeff1;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            font-size: 0.85rem;
            height: 60px;
        }

        .schedule-grid-table thead th {
            background-color: #f8f9fa !important;
            color: var(--pcc-blue) !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .time-col {
            font-weight: 600;
            background-color: #f8f9fa;
            width: 130px;
            color: #495057;
        }

        /* Confirmed Subject Slot */
        .confirmed-slot {
            background-color: #e6f2ff;
            color: #002c5e;
            border: 2px solid #b3d7ff;
            border-radius: 6px;
            transition: transform 0.2s;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.5);
        }

        .confirmed-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 44, 94, 0.1);
            cursor: pointer;
        }

        .course-code {
            font-weight: 800;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 2px;
        }

        .room-desc {
            font-weight: 500;
            font-size: 0.75rem;
            color: #495057;
            display: block;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Top Navigation -->
        <nav class="app-header navbar navbar-expand bg-body shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i
                                class="bi bi-list"></i></a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date fw-semibold text-secondary"
                                style="margin-top:6px; margin-bottom: 9px; font-size: 0.9rem;">
                                <i class="bi bi-clock-history me-2"></i>
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <?php echo date('F j, Y') . " - " . date("h:iA"); ?>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar (Updated layout struct to completely match Dashboard) -->
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
                            <!-- Set Active Page indicators here to Schedule -->
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-calendar-week-fill"></i>
                                <p>
                                    Schedule
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_grades.php" class="nav-link">
                                <i class="nav-icon bi bi-journal-check"></i>
                                <p>
                                    Grades & Transcripts
                                    <i class="nav-arrow bi bi-chevron-left"></i>
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
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main py-4">
            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h3 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">My Class Schedule</h3>
                            <p class="text-muted small mb-0 fw-medium">Academic Year 2026-2027 | 1st Semester</p>
                        </div>
                        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                            <button class="btn btn-outline-pcc btn-sm fw-semibold rounded-pill px-3 me-2"
                                onclick="window.print();">
                                <i class="bi bi-printer-fill me-1"></i> Print
                            </button>
                            <button class="btn btn-pcc-primary btn-sm fw-semibold rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">

                        <!-- Visual Schedule Grid Card -->
                        <div class="col-12">
                            <div class="card schedule-card">
                                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Visual Timetable</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table schedule-grid-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="time-col border-0 rounded-start">Time</th>
                                                    <th class="border-0">Monday</th>
                                                    <th class="border-0">Tuesday</th>
                                                    <th class="border-0">Wednesday</th>
                                                    <th class="border-0">Thursday</th>
                                                    <th class="border-0">Friday</th>
                                                    <th class="border-0 rounded-end">Saturday</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="time-col">07:00 AM - 09:00 AM</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">PE400</span>
                                                        <span class="room-desc">Gymnasium</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="time-col">09:00 AM - 10:30 AM</td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT411</span>
                                                        <span class="room-desc">Laboratory 1</span>
                                                    </td>
                                                    <td></td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT411</span>
                                                        <span class="room-desc">Laboratory 1</span>
                                                    </td>
                                                    <td></td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT411</span>
                                                        <span class="room-desc">Laboratory 1</span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="time-col">11:00 AM - 12:00 PM</td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT412</span>
                                                        <span class="room-desc">Lecture 304</span>
                                                    </td>
                                                    <td></td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT412</span>
                                                        <span class="room-desc">Lecture 304</span>
                                                    </td>
                                                    <td></td>
                                                    <td class="confirmed-slot">
                                                        <span class="course-code">IT412</span>
                                                        <span class="room-desc">Lecture 304</span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="time-col">12:00 PM - 01:00 PM</td>
                                                    <td colspan="6" class="text-center text-muted fw-semibold bg-light"
                                                        style="letter-spacing: 2px;">L U N C H &nbsp; B R E A K</td>
                                                </tr>
                                                <tr>
                                                    <td class="time-col">01:00 PM - 03:30 PM</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Roster Details Card -->
                        <div class="col-12 mb-5">
                            <div class="card schedule-card">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-card-list me-2 text-primary"></i>Enrolled Subjects List</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary small text-uppercase">
                                                <tr>
                                                    <th class="ps-4">Course Code</th>
                                                    <th>Descriptive Title</th>
                                                    <th class="text-center">Units</th>
                                                    <th>Days</th>
                                                    <th>Time</th>
                                                    <th>Room</th>
                                                    <th>Instructor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark small">
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">IT411</td>
                                                    <td class="fw-medium">Capstone Project 1 (Proposal & Prototyping)
                                                    </td>
                                                    <td class="text-center fw-semibold">3</td>
                                                    <td>M - W - F</td>
                                                    <td>09:00 AM - 10:30 AM</td>
                                                    <td>Laboratory Station 1</td>
                                                    <td class="text-muted">Prof. A. Santos</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">IT412</td>
                                                    <td class="fw-medium">Information Assurance and Security 2</td>
                                                    <td class="text-center fw-semibold">3</td>
                                                    <td>M - W - F</td>
                                                    <td>11:00 AM - 12:00 PM</td>
                                                    <td>Lecture Hall 304</td>
                                                    <td class="text-muted">Prof. M. Torres</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">PE400</td>
                                                    <td class="fw-medium">Advanced Team Sports Elective</td>
                                                    <td class="text-center fw-semibold">2</td>
                                                    <td>SAT</td>
                                                    <td>07:00 AM - 09:00 AM</td>
                                                    <td>Physical Campus Gym</td>
                                                    <td class="text-muted">Coach J. Perez</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-light border-top">
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold text-secondary">Total
                                                        Enrolled Units:</td>
                                                    <td class="text-center fw-bold fs-6 text-dark">8</td>
                                                    <td colspan="4"></td>
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
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="../../js/adminlte.js"></script>
</body>

</html>