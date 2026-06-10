<?php
date_default_timezone_set('Asia/Manila');

$student_list = [
    [
        'student_no' => '2024-00981',
        'name' => 'Villarta, Joeshua Louis',
        'program' => 'BSIT',
        'year' => '2nd Year',
        'classification' => 'Regular',
        'status' => 'Enrolled',
        'email' => 'jlvillarta@pcc.edu.ph',
        'contact' => '+63 912 345 6789',
        'gender' => 'Male',
        'birthdate' => '2005-04-12',
        'address' => 'Tondo, Manila, Philippines',
        'guardian' => 'Maria Clara Villarta',
        'emergency_contact' => '+63 912 345 6780',
        'date_enrolled' => '2024-06-03'
    ],
    [
        'student_no' => '2025-10432',
        'name' => 'Agudon, Miguelito M.',
        'program' => 'BSDS',
        'year' => '1st Year',
        'classification' => 'Transferee',
        'status' => 'Pending',
        'email' => 'mmagudon@pcc.edu.ph',
        'contact' => '+63 923 456 7890',
        'gender' => 'Male',
        'birthdate' => '2006-09-21',
        'address' => 'Sampaloc, Manila, Philippines',
        'guardian' => 'Juan Agudon',
        'emergency_contact' => '+63 923 456 7891',
        'date_enrolled' => '2025-05-28'
    ],
    [
        'student_no' => '2023-00214',
        'name' => 'Depollo, Ralph Geofrey',
        'program' => 'BSCS',
        'year' => '3rd Year',
        'classification' => 'Regular',
        'status' => 'Pending',
        'email' => 'rgdepollo@pcc.edu.ph',
        'contact' => '+63 934 567 8901',
        'gender' => 'Male',
        'birthdate' => '2004-11-05',
        'address' => 'Binondo, Manila, Philippines',
        'guardian' => 'Roberto Depollo',
        'emergency_contact' => '+63 934 567 8902',
        'date_enrolled' => '2023-06-11'
    ]
];

$toast_notification = "";

if (isset($_GET['delete_id'])) {
    $target_id = $_GET['delete_id'];
    $student_list = array_filter($student_list, fn($record) => $record['student_no'] !== $target_id);
    $toast_notification = "
    <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
        <div class='toast show align-items-center text-white bg-danger border-0 shadow' role='alert' aria-live='assertive' aria-atomic='true'>
            <div class='d-flex'>
                <div class='toast-body'><i class='bi bi-trash3-fill me-2'></i>Record <strong>#{$target_id}</strong> successfully dropped from database arrays.</div>
                <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast' aria-label='Close'></button>
            </div>
        </div>
    </div>";
}

$edit_mode = false;
$add_mode = isset($_GET['add_student']);
$selected_student = null;

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    foreach ($student_list as $student) {
        if ($student['student_no'] === $edit_id) {
            $selected_student = $student;
            $edit_mode = true;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_new_student'])) {
    $new_student = [
        'student_no' => $_POST['student_no'],
        'name' => $_POST['name'],
        'program' => $_POST['program'],
        'year' => $_POST['year'],
        'classification' => $_POST['classification'],
        'status' => $_POST['status'],
        'email' => $_POST['email'],
        'contact' => $_POST['contact'],
        'gender' => $_POST['gender'],
        'birthdate' => $_POST['birthdate'],
        'address' => $_POST['address'],
        'guardian' => 'N/A',
        'emergency_contact' => 'N/A',
        'date_enrolled' => date('Y-m-d')
    ];
    $student_list[] = $new_student;
    $toast_notification = "
    <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
        <div class='toast show align-items-center text-white bg-success border-0 shadow' role='alert' aria-live='assertive' aria-atomic='true'>
            <div class='d-flex'>
                <div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>New record successfully created for <strong>{$_POST['student_no']}</strong>.</div>
                <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast' aria-label='Close'></button>
            </div>
        </div>
    </div>";
    $add_mode = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    foreach ($student_list as &$student) {
        if ($student['student_no'] === $_POST['student_no']) {
            $student['name'] = $_POST['name'];
            $student['program'] = $_POST['program'];
            $student['year'] = $_POST['year'];
            $student['classification'] = $_POST['classification'];
            $student['status'] = $_POST['status'];
            $student['email'] = $_POST['email'];
            $student['contact'] = $_POST['contact'];
            $student['gender'] = $_POST['gender'];
            $student['birthdate'] = $_POST['birthdate'];
            $student['address'] = $_POST['address'];
            break;
        }
    }
    unset($student);
    $toast_notification = "
    <div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'>
        <div class='toast show align-items-center text-white bg-success border-0 shadow' role='alert' aria-live='assertive' aria-atomic='true'>
            <div class='d-flex'>
                <div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Modifications cleanly compiled for <strong>{$_POST['student_no']}</strong>.</div>
                <button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast' aria-label='Close'></button>
            </div>
        </div>
    </div>";
    $edit_mode = false;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Directory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
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

        .tab-indicator {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #002c5e;
        }

        .clickable-header {
            cursor: pointer;
            user-select: none;
        }

        .clickable-header:hover {
            background-color: #f1f3f5;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php echo $toast_notification; ?>

    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date" style="margin-top:6px; margin-bottom: 9px;">
                                <?php echo date('F j, Y') . " - " . date("h:iA"); ?>
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
                            <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
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
                                <p>Dashboard <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_student.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Students <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-3 mt-3 fw-bold">Student Management</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Students</span>
                                    <h4 class="fw-bold mb-0">3</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Enrolled</span>
                                    <h4 class="fw-bold mb-0 text-warning">1</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Pending</span>
                                    <h4 class="fw-bold mb-0 text-success">2</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Current School Year</span>
                                    <h4 class="fw-bold mb-0 text-info">2026 - 2027</h4>
                                </div>
                            </div>
                        </div>

                        <?php if ($add_mode): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4 bg-white">
                                    <div
                                        class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                                class="bi bi-person-plus-fill me-2 text-success"></i>Register New Student
                                            Profile</h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?">
                                        <div class="card-body text-dark">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Student
                                                        No.</label>
                                                    <input type="text" name="student_no"
                                                        class="form-control form-control-sm border shadow-sm font-monospace"
                                                        placeholder="e.g., 2026-00001" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Full Name</label>
                                                    <input type="text" name="name"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="Full Name" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Institutional
                                                        Email</label>
                                                    <input type="email" name="email"
                                                        class="form-control form-control-sm border shadow-sm font-monospace"
                                                        placeholder="username@pcc.edu.ph" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Academic
                                                        Program</label>
                                                    <select name="program"
                                                        class="form-select form-select-sm border shadow-sm" required>
                                                        <option value="BSIT">BS in Information Technology</option>
                                                        <option value="BSCS">BS in Computer Science</option>
                                                        <option value="BSDS">BS in Data Science</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Year
                                                        Level</label>
                                                    <select name="year" class="form-select form-select-sm border shadow-sm">
                                                        <option>1st Year</option>
                                                        <option>2nd Year</option>
                                                        <option>3rd Year</option>
                                                        <option>4th Year</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Mobile Contact
                                                        Number</label>
                                                    <input type="text" name="contact"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="+63 9XX XXX XXXX">
                                                </div>
                                                <div class="col-md-4">
                                                    <label
                                                        class="form-label small fw-bold text-secondary">Classification</label>
                                                    <select name="classification"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option>Regular</option>
                                                        <option>Transferee</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Status</label>
                                                    <select name="status"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option>Enrolled</option>
                                                        <option>Pending</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Gender
                                                        Expression</label>
                                                    <select name="gender"
                                                        class="form-select form-select-sm border shadow-sm">
                                                        <option>Male</option>
                                                        <option>Female</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-secondary">Date of
                                                        Birth</label>
                                                    <input type="date" name="birthdate"
                                                        class="form-control form-control-sm border shadow-sm">
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold text-secondary">Residential
                                                        Mailing Address</label>
                                                    <input type="text" name="address"
                                                        class="form-control form-control-sm border shadow-sm"
                                                        placeholder="House No., Street, Barangay, City">
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-end align-items-center py-3 border-top">
                                            <a href="?" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                            <button type="submit" name="submit_new_student"
                                                class="btn btn-sm btn-success px-3 shadow-sm">Save New Entry</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($edit_mode && $selected_student): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4 bg-white">
                                    <div
                                        class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                                class="bi bi-pencil-square me-2 text-warning"></i>Modify Student Profile
                                            Workspace</h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?">
                                        <div class="card-body text-dark">
                                            <input type="hidden" name="student_no" id="active-student-no"
                                                value="<?= htmlspecialchars($selected_student['student_no']) ?>">
                                            <div class="row g-4">

                                                <div class="col-xl-13 col-lg-13">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-secondary">Full
                                                                Name</label>
                                                            <input type="text" name="name"
                                                                class="form-control form-control-sm border shadow-sm"
                                                                value="<?= htmlspecialchars($selected_student['name']) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Institutional
                                                                Email</label>
                                                            <input type="email" name="email"
                                                                class="form-control form-control-sm border shadow-sm font-monospace"
                                                                value="<?= htmlspecialchars($selected_student['email']) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Academic
                                                                Program Option</label>
                                                            <select name="program"
                                                                class="form-select form-select-sm border shadow-sm"
                                                                required>
                                                                <option value="BSIT"
                                                                    <?= $selected_student['program'] === 'BSIT' ? 'selected' : '' ?>>BS in Information Technology</option>
                                                                <option value="BSCS"
                                                                    <?= $selected_student['program'] === 'BSCS' ? 'selected' : '' ?>>BS in Computer Science</option>
                                                                <option value="BSDS"
                                                                    <?= $selected_student['program'] === 'BSDS' ? 'selected' : '' ?>>BS in Data Science</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Year
                                                                Level Status</label>
                                                            <select name="year"
                                                                class="form-select form-select-sm border shadow-sm">
                                                                <option <?= $selected_student['year'] === '1st Year' ? 'selected' : '' ?>>1st Year</option>
                                                                <option <?= $selected_student['year'] === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                                                                <option <?= $selected_student['year'] === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                                                                <option <?= $selected_student['year'] === '4th Year' ? 'selected' : '' ?>>4th Year</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Mobile
                                                                Contact Number</label>
                                                            <input type="text" name="contact"
                                                                class="form-control form-control-sm border shadow-sm"
                                                                value="<?= htmlspecialchars($selected_student['contact']) ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Classification</label>
                                                            <select name="classification"
                                                                class="form-select form-select-sm border shadow-sm">
                                                                <option <?= $selected_student['classification'] === 'Regular' ? 'selected' : '' ?>>Regular</option>
                                                                <option
                                                                    <?= $selected_student['classification'] === 'Transferee' ? 'selected' : '' ?>>Transferee</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Status
                                                                State</label>
                                                            <select name="status"
                                                                class="form-select form-select-sm border shadow-sm">
                                                                <option <?= $selected_student['status'] === 'Enrolled' ? 'selected' : '' ?>>Enrolled</option>
                                                                <option <?= $selected_student['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Gender</label>
                                                            <select name="gender"
                                                                class="form-select form-select-sm border shadow-sm">
                                                                <option <?= $selected_student['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                                                <option <?= $selected_student['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Date of
                                                                Birth</label>
                                                            <input type="date" name="birthdate"
                                                                class="form-control form-control-sm border shadow-sm"
                                                                value="<?= htmlspecialchars($selected_student['birthdate']) ?>">
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Residential
                                                                Address</label>
                                                            <input type="text" name="address"
                                                                class="form-control form-control-sm border shadow-sm"
                                                                value="<?= htmlspecialchars($selected_student['address']) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-between align-items-center py-3 border-top">
                                            <button type="button" class="btn btn-sm btn-danger px-3 shadow-sm"
                                                onclick="triggerRecordDeletion()">
                                                <i class="bi bi-trash-fill me-2"></i>Drop Record Stack
                                            </button>
                                            <div class="ms-auto">
                                                <a href="?" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                                <button type="submit" name="update_student"
                                                    class="btn btn-sm btn-primary px-3 shadow-sm">Save Profile
                                                    Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="card shadow-sm border-0 bg-white" style="border-radius: 10px;">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-people-fill me-2 text-primary"></i>PCC Student List Directory
                                    </h5>

                                    <div class="card-tools d-flex flex-wrap align-items-center gap-2">
                                        <div
                                            class="d-flex align-items-center gap-1 border-end pe-2 me-1 border-light-subtle flex-wrap">
                                            <select id="filter-program"
                                                class="form-select form-select-sm border shadow-sm text-muted"
                                                style="width: 9rem;" aria-label="Filter Academic Programs">
                                                <option selected>All Programs</option>
                                                <option>BSIT</option>
                                                <option>BSCS</option>
                                                <option>BSDS</option>
                                            </select>
                                            <select id="filter-year"
                                                class="form-select form-select-sm border shadow-sm text-muted"
                                                style="width: 8rem;" aria-label="Filter Year Levels">
                                                <option selected>All Year Levels</option>
                                                <option>1st Year</option>
                                                <option>2nd Year</option>
                                                <option>3rd Year</option>
                                                <option>4th Year</option>
                                            </select>
                                        </div>

                                        <form method="GET" action="" class="d-flex gap-2">
                                            <?php if (isset($_GET['edit_id'])): ?><input type="hidden" name="edit_id"
                                                    value="<?= htmlspecialchars($_GET['edit_id']) ?>"><?php endif; ?>
                                            <div class="input-group input-group-sm border shadow-sm rounded"
                                                style="width: 14rem">
                                                <span class="input-group-text bg-light border-0 text-muted"><i
                                                        class="bi bi-search"></i></span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control border-0 bg-light" placeholder="Search"
                                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                            </div>
                                        </form>

                                        <div
                                            class="d-flex align-items-center gap-1 border-start ps-2 ms-1 border-light-subtle">
                                            <a href="?add_student=true"
                                                class="btn btn-success btn-sm font-weight-bold px-2 shadow-sm"><i
                                                    class="bi bi-person-plus-fill me-1"></i> Add Student</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead
                                                class="table-light small text-uppercase text-secondary border-bottom">
                                                <tr>
                                                    <th class="clickable-header ps-4 py-3 font-weight-bold"
                                                        onclick="alert('Sorting routine triggered for: Student No.')">
                                                        Student No. <i
                                                            class="bi bi-arrow-down-up text-muted ms-1 small"></i></th>
                                                    <th class="clickable-header py-3 font-weight-bold"
                                                        onclick="alert('Sorting routine triggered for: Name')">Student
                                                        Name <i class="bi bi-arrow-down-up text-muted ms-1 small"></i>
                                                    </th>
                                                    <th>Program</th>
                                                    <th>Year Level</th>
                                                    <th>Classification</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="pe-4 text-end" style="width: 240px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($student_list)): ?>
                                                    <tr>
                                                        <td colspan="7" class="py-0">
                                                            <div class="text-center py-5 bg-white">
                                                                <i
                                                                    class="bi bi-people fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                                                <h5 class="fw-bold text-dark">No Active Records Located</h5>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($student_list as $student): ?>
                                                        <tr
                                                            class="<?php echo ($edit_mode && $edit_id === $student['student_no']) ? 'table-warning-subtle' : ''; ?>">
                                                            <td class="ps-4 font-monospace fw-bold text-secondary small">
                                                                <?= htmlspecialchars($student['student_no']) ?>
                                                            </td>
                                                            <td class="fw-semibold text-dark">
                                                                <?= htmlspecialchars($student['name']) ?>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-primary-subtle text-primary fw-medium px-2 py-1"><?= htmlspecialchars($student['program']) ?></span>
                                                            </td>
                                                            <td class="text-secondary small">
                                                                <?= htmlspecialchars($student['year']) ?>
                                                            </td>
                                                            <td class="text-dark small">
                                                                <?= htmlspecialchars($student['classification']) ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                $status = $student['status'];
                                                                $badge_color = ($status === 'Enrolled') ? 'bg-success-subtle text-success border border-success-subtle' : ($status === 'Pending' ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle');
                                                                ?>
                                                                <span
                                                                    class="badge <?php echo $badge_color; ?> tab-indicator d-inline-block w-75 text-center"><?= htmlspecialchars($status) ?></span>
                                                            </td>
                                                            <td class="pe-4 text-end">
                                                                <a href="?edit_id=<?= urlencode($student['student_no']) ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                                                                    class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                                    style="font-size: 0.75rem;"><i
                                                                        class="bi bi-pencil-square me-1"></i>Edit /
                                                                    Manage
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div
                                    class="card-footer bg-white border-top py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div class="small text-muted font-monospace">
                                        Showing <?= count($student_list) ?> entries tracked in system scope.
                                    </div>
                                    <nav aria-label="Index Navigation">
                                        <ul class="pagination pagination-sm justify-content-end mb-0 shadow-sm">
                                            <li class="page-item disabled"><a class="page-link text-muted" href="#"
                                                    tabindex="-1">Previous</a></li>
                                            <li class="page-item active" aria-current="page"><a
                                                    class="page-link border-primary bg-primary" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                        </ul>
                                    </nav>
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
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/adminlte.js"></script>

    <script>
        function triggerRecordDeletion() {
            const studentId = document.getElementById('active-student-no').value;
            if (confirm("Are you sure you want to drop student profile target #" + studentId + " from system scope?")) {
                window.location.href = "?delete_id=" + encodeURIComponent(studentId);
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("table-filter");
            const programFilter = document.getElementById("filter-program");
            const yearFilter = document.getElementById("filter-year");
            const tableBody = document.querySelector("table tbody");
            const tableRows = tableBody ? tableBody.querySelectorAll("tr") : [];

            const noResultsRow = document.createElement("tr");
            noResultsRow.style.display = "none";
            noResultsRow.innerHTML = `
                <td colspan="7" class="text-center text-muted py-5 bg-white">
                    <i class="bi bi-search fs-2 opacity-50 mb-2 d-block"></i>
                    <p class="mb-0 fw-bold">No Records Met Search Criteria</p>
                </td>
            `;
            if (tableBody) tableBody.appendChild(noResultsRow);

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
                const selectedProgram = programFilter ? programFilter.value : "All Programs";
                const selectedYear = yearFilter ? yearFilter.value : "All Year Levels";
                let visibleCount = 0;

                tableRows.forEach(row => {
                    if (row === noResultsRow) return;

                    const studentNo = row.cells[0]?.textContent.toLowerCase().trim() || "";
                    const studentName = row.cells[1]?.textContent.toLowerCase().trim() || "";
                    const studentProgram = row.cells[2]?.textContent.trim() || "";
                    const studentYear = row.cells[3]?.textContent.trim() || "";

                    const matchesSearch = studentNo.includes(query) || studentName.includes(query);
                    const matchesProgram = selectedProgram === "All Programs" || studentProgram === selectedProgram;
                    const matchesYear = selectedYear === "All Year Levels" || studentYear === selectedYear;

                    if (matchesSearch && matchesProgram && matchesYear) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                });

                noResultsRow.style.display = (visibleCount === 0) ? "" : "none";
            }

            if (searchInput) searchInput.addEventListener("input", filterTable);
            if (programFilter) programFilter.addEventListener("change", filterTable);
            if (yearFilter) yearFilter.addEventListener("change", filterTable);
        });
    </script>
</body>

</html>