<!DOCTYPE html>
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
                                <p>Dashboard <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_profile.php" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-person-fill"></i>
                                <p>Student Information <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_enrollment.php" class="nav-link">
                                <i class="nav-icon bi bi-laptop"></i>
                                <p>Online Enrollment <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_schedule.php" class="nav-link">
                                <i class="nav-icon bi bi-calendar-week-fill"></i>
                                <p>Schedule <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="old_student_grades.php" class="nav-link">
                                <i class="nav-icon bi bi-journal-check"></i>
                                <p>Grades & Transcripts <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link sidebar-bg-active shadow-sm fw-semibold">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Dropping of Subject <i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main p-4" style="background-color: #f8fafc; margin-top: 60px;">
            <div class="container-fluid">

                <div id="dropping-view">
                    <div class="row mb-4">
                        <div class="col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1 text-muted small">
                                    <li class="breadcrumb-item">Academic Hub</li>
                                    <li class="breadcrumb-item active text-secondary" aria-current="page">Dropping of
                                        Subject</li>
                                </ol>
                            </nav>
                            <h3 class="fw-bold m-0" style="color: #002c5e; letter-spacing: -0.5px;">Subject Dropping
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card border-0 shadow-sm p-4 bg-white mb-4" style="border-radius: 16px;">
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                        <i class="fa-solid fa-circle-info fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold m-0 text-uppercase"
                                        style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Institutional
                                        Policy</h5>
                                </div>

                                <p class="text-secondary small lh-base">
                                    Dropping of subjects must be executed within the allowable period set by the
                                    Registrar's Office. Unauthorized absences do not constitute an official drop.
                                </p>

                                <div class="mb-3 p-3 bg-body-tertiary border border-light-subtle rounded-3">
                                    <div class="d-flex justify-content-between mb-1 small fw-bold text-secondary">
                                        <span>Dropping Deadline:</span>
                                        <span class="text-danger">Midterms Week</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 75%;"
                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2 font-monospace" style="font-size: 11px;">*
                                        Applications past the deadline will not be processed.</small>
                                </div>

                                <hr class="opacity-25 my-3">

                                <label class="text-uppercase text-muted fw-bold d-block mb-2"
                                    style="font-size: 10px; letter-spacing: 0.5px;">Steps to Drop a Subject</label>
                                <div class="position-relative ps-3 border-start border-2 border-primary-subtle ms-1">
                                    <div class="mb-3 position-relative">
                                        <i class="bi bi-circle-fill text-primary position-absolute"
                                            style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                        <h6 class="fw-bold text-secondary small mb-0">1. Adviser Consultation</h6>
                                        <p class="text-muted small m-0">Discuss academic impact with your Program
                                            Chair/Adviser.</p>
                                    </div>
                                    <div class="mb-3 position-relative">
                                        <i class="bi bi-circle-fill text-primary position-absolute"
                                            style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                        <h6 class="fw-bold text-secondary small mb-0">2. Submit Request</h6>
                                        <p class="text-muted small m-0">Fill out and file the drop application in this
                                            terminal module.</p>
                                    </div>
                                    <div class="position-relative">
                                        <i class="bi bi-circle-fill text-primary position-absolute"
                                            style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                        <h6 class="fw-bold text-secondary small mb-0">3. Registrar Clearance</h6>
                                        <p class="text-muted small m-0">Wait for final documentation updates and dynamic
                                            roster changes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                        <i class="fa-solid fa-file-signature fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold m-0 text-uppercase"
                                        style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">File Drop
                                        Application</h5>
                                </div>

                                <form action="" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label text-muted fw-semibold small mb-1">Select Enrolled
                                                Subject to Drop</label>
                                            <select
                                                class="form-select border-light-subtle rounded-3 bg-body-tertiary text-secondary fw-medium p-2.5 px-3">
                                                <option selected disabled>-- Select Course --</option>
                                                <option value="1">IT411 - Capstone Project 1</option>
                                                <option value="2">IT412 - Information Assurance 2</option>
                                                <option value="3">PE400 - Advanced Team Sports</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted fw-semibold small mb-1">Reason for
                                                Dropping</label>
                                            <textarea
                                                class="form-control border-light-subtle rounded-3 bg-body-tertiary text-secondary p-2.5 px-3"
                                                rows="3"
                                                placeholder="State your valid or academic reason briefly..."></textarea>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="button"
                                                class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm"
                                                style="background-color: #002c5e; border-color: #002c5e;">
                                                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>Submit
                                                Application
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                        <i class="fa-solid fa-clock-rotate-left fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold m-0 text-uppercase"
                                        style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Dropping
                                        History & Logs</h5>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle border-0 m-0">
                                        <thead>
                                            <tr class="text-uppercase text-muted small border-bottom"
                                                style="font-size: 11px; letter-spacing: 0.5px;">
                                                <th class="ps-0 py-3">Subject & Code</th>
                                                <th class="py-3">Units</th>
                                                <th class="py-3">Date Filed</th>
                                                <th class="py-3">Status Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-secondary small">
                                            <tr class="border-bottom border-light-subtle">
                                                <td class="ps-0 py-3">
                                                    <span class="fw-bold text-dark d-block">IT315</span>
                                                    <span class="text-muted font-monospace"
                                                        style="font-size: 12px;">Information Assurance and
                                                        Security</span>
                                                </td>
                                                <td class="py-3 fw-medium">3.0</td>
                                                <td class="py-3 text-muted font-monospace">June 02, 2026</td>
                                                <td class="py-3">
                                                    <span
                                                        class="badge rounded-pill px-3 py-1.5 bg-success-subtle text-success fw-semibold border border-success-subtle">Approved</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 py-3">
                                                    <span class="fw-bold text-dark d-block">GE102</span>
                                                    <span class="text-muted font-monospace"
                                                        style="font-size: 12px;">Readings in Philippine History</span>
                                                </td>
                                                <td class="py-3 fw-medium">3.0</td>
                                                <td class="py-3 text-muted font-monospace">May 28, 2026</td>
                                                <td class="py-3">
                                                    <span
                                                        class="badge rounded-pill px-3 py-1.5 bg-warning-subtle text-warning fw-semibold border border-warning-subtle">Pending
                                                        Review</span>
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
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>