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
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date text-muted fw-semibold small px-3"
                                style="margin-top:6px; margin-bottom: 9px;">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <i class="bi bi-clock me-1 text-primary"></i>
                                <?php echo date('F j, Y') . " — " . date("h:i A"); ?>
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
                            <a href="#" class="nav-link sidebar-bg-active shadow-sm fw-semibold">
                                <i class="nav-icon bi bi-file-earmark-person-fill"></i>
                                <p>Student Information <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                            <a href="old_student_subject.php" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Dropping of Subject <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="app-main p-4" style="background-color: #f8fafc;">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-4">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 text-muted small">
                                <li class="breadcrumb-item">Academic Hub</li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page">Student
                                    Information</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold m-0" style="color: #002c5e; letter-spacing: -0.5px;">Profile Overview</h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Quick Profile Summary Card -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm text-center p-4 h-100 bg-white"
                            style="border-radius: 16px;">
                            <div class="position-relative d-inline-block mx-auto my-3">
                                <div class="avatar-placeholder d-flex align-items-center justify-content-center text-white shadow"
                                    style="width: 110px; height: 110px; font-size: 44px; background: linear-gradient(135deg, #002c5e, #001d3d); border-radius: 50%;">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <span
                                    class="position-absolute bottom-0 end-0 bg-success border border-4 border-white rounded-circle p-2"
                                    title="Active Account"></span>
                            </div>

                            <h4 class="fw-bold mb-1" style="color: #002c5e;">Juan Dela Cruz</h4>
                            <p class="text-muted small mb-4 fw-medium" style="letter-spacing: 0.5px;">ID: 2024-001234
                            </p>

                            <hr class="my-3 opacity-25">

                            <div class="text-start px-2 mt-2">
                                <div class="mb-3">
                                    <label class="text-uppercase text-muted fw-bold d-block mb-1"
                                        style="font-size: 10px; letter-spacing: 0.5px;">College</label>
                                    <span class="fw-semibold text-secondary" style="font-size: 0.95rem;">College of
                                        Computer Studies and Systems</span>
                                </div>
                                <div>
                                    <label class="text-uppercase text-muted fw-bold d-block mb-1"
                                        style="font-size: 10px; letter-spacing: 0.5px;">Institutional Email</label>
                                    <span class="text-break text-primary fw-medium"
                                        style="font-size: 0.95rem;">delacruzjuan@pcc.edu.ph</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Detailed Categorized Profile Cards -->
                    <div class="col-lg-8">

                        <!-- Academic Status Details -->
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                    <i class="fa-solid fa-graduation-cap fa-lg"></i>
                                </div>
                                <h5 class="fw-bold m-0 text-uppercase"
                                    style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Academic
                                    Information</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Registration
                                        Status</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary d-flex align-items-center justify-content-between">
                                        <span class="fw-bold text-success"><i
                                                class="bi bi-check-circle-fill me-2"></i>Officially Enrolled</span>
                                        <small class="text-muted font-monospace" style="font-size: 11px;">1st Sem
                                            2026-2027</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Curriculum Year</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-semibold text-secondary">
                                        Third Year (3)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Contact Details -->
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                    <i class="fa-solid fa-address-book fa-lg"></i>
                                </div>
                                <h5 class="fw-bold m-0 text-uppercase"
                                    style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Contact
                                    Information</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Student Full
                                        Name</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary">
                                        Juan Dela Cruz</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Mobile Number</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary font-monospace">
                                        <i class="bi bi-telephone me-2 text-muted"></i>+63 912 345 6789</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Personal Email</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-envelope me-2 text-muted"></i>delacruzjuan@pcc.edu.ph</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Permanent Residential
                                        Address</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>123 Clear Ave, Manila, Philippines
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Details (Custom Minimal Dark Red Minimal Theme) -->
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 rounded-3 me-3" style="background-color: #fde8e8; color: #8b0000;">
                                    <i class="fa-solid fa-phone-flip fa-lg"></i>
                                </div>
                                <h5 class="fw-bold m-0 text-uppercase"
                                    style="color: #8b0000; font-size: 0.95rem; letter-spacing: 0.5px;">Emergency Details
                                </h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Emergency Contact
                                        Person</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary">
                                        Jane Dela Cruz</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Contact Number</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary font-monospace">
                                        <i class="bi bi-telephone me-2 text-muted"></i>+63 998 765 4321</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Contact Address</label>
                                    <div
                                        class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>123 Clear Ave, Manila, Philippines
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Required Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>