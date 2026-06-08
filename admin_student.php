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
    <?php
    // --- SERVER-SIDE OPERATION PROCESSOR ---
    // require_once 'config.php';
    
    $student_list = [
        ['student_no' => '2024-00142', 'name' => 'Mendoza, Laurence C.', 'program' => 'BSIT', 'year' => '2nd Year', 'classification' => 'Regular', 'status' => 'Enrolled'],
        ['student_no' => '2024-00981', 'name' => 'Villarta, Joeshua Louis', 'program' => 'BSIT', 'year' => '2nd Year', 'classification' => 'Regular', 'status' => 'Enrolled'],
        ['student_no' => '2025-10432', 'name' => 'Agudon, Miguelito M.', 'program' => 'BSBA', 'year' => '1st Year', 'classification' => 'Transferee', 'status' => 'Pending'],
        ['student_no' => '2023-00214', 'name' => 'Depollo, Ralph Geofrey', 'program' => 'BSCS', 'year' => '3rd Year', 'classification' => 'Regular', 'status' => 'Enrolled']
    ];

    // Intercept Delete Action Parameters Early
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];

        /* Production PDO SQL Database Target:
        $stmt = $pdo->prepare("DELETE FROM students WHERE student_no = ?");
        $stmt->execute([$delete_id]);
        header("Location: students.php");
        exit;
        */
        echo "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Record Deleted!</strong> Student Profile code #" . htmlspecialchars($delete_id) . " dropped from memory indices.</div>";
    }

    $edit_mode = false;
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

    // Process Updates Sent Via POST Execution
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
        /* Production PDO SQL Database Target:
        $stmt = $pdo->prepare("UPDATE students SET name = ?, program = ?, year = ?, classification = ?, status = ? WHERE student_no = ?");
        $stmt->execute([$_POST['name'], $_POST['program'], $_POST['year'], $_POST['classification'], $_POST['status'], $_POST['student_no']]);
        header("Location: students.php");
        exit;
        */
        echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Record updated successfully! (Database sync mock triggered)</div>";
        $edit_mode = false;
    }
    ?>

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
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Students <i class="nav-arrow bi bi-chevron-right"></i></p>
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

        <img src="images/PCC_Logo.png" alt="PCC Logo" class="brand-image" style="display: none;" />

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
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
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
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Enrolled</span>
                                    <h4 class="fw-bold mb-0 text-warning">42</h4>
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
                                    <h4 class="fw-bold mb-0 text-success">185</h4>
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
                    </div>

                    <div class="row g-4">
                        <?php if ($edit_mode && $selected_student): ?>
                            <div class="col-12">
                                <div class="card border-warning shadow-sm mb-4">
                                    <div
                                        class="card-header bg-warning-subtle text-dark-emphasis py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit
                                            Student Profile:
                                            <?php echo htmlspecialchars($selected_student['student_no']); ?>
                                        </h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?">
                                        <div class="card-body bg-white text-dark">
                                            <input type="hidden" name="student_no"
                                                value="<?php echo htmlspecialchars($selected_student['student_no']); ?>">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Full Name</label>
                                                    <input type="text" name="name" class="form-control form-control-sm"
                                                        value="<?php echo htmlspecialchars($selected_student['name']); ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Program</label>
                                                    <select name="program" class="form-select form-select-sm" required>
                                                        <option value="BSIT" <?php echo $selected_student['program'] === 'BSIT' ? 'selected' : ''; ?>>
                                                            Bachelor of Science in Information Technology</option>
                                                        <option value="BSCS" <?php echo $selected_student['program'] === 'BSCS' ? 'selected' : ''; ?>>
                                                            Bachelor of Science in Computer Science</option>
                                                        <option value="BSEMC" <?php echo $selected_student['program'] === 'BSEMC' ? 'selected' : ''; ?>>
                                                            Bachelor of Science in Entertainment and Multimedia Computing
                                                        </option>
                                                        <option value="BSDS" <?php echo $selected_student['program'] === 'BSDS' ? 'selected' : ''; ?>>
                                                            Bachelor of Science in Data Science</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Year Level</label>
                                                    <select name="year" class="form-select form-select-sm">
                                                        <option <?php echo $selected_student['year'] === '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                                        <option <?php echo $selected_student['year'] === '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                                        <option <?php echo $selected_student['year'] === '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                                        <option <?php echo $selected_student['year'] === '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Classification</label>
                                                    <select name="classification" class="form-select form-select-sm">
                                                        <option <?php echo $selected_student['classification'] === 'Regular' ? 'selected' : ''; ?>>Regular</option>
                                                        <option <?php echo $selected_student['classification'] === 'Transferee' ? 'selected' : ''; ?>>Transferee</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Status</label>
                                                    <select name="status" class="form-select form-select-sm">
                                                        <option <?php echo $selected_student['status'] === 'Enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                                                        <option <?php echo $selected_student['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option <?php echo $selected_student['status'] === 'Unenrolled' ? 'selected' : ''; ?>>Unenrolled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                                            <a href="?delete_id=<?php echo urlencode($selected_student['student_no']); ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('CRITICAL ACCESSIBILITY ALERT:\n\nAre you sure you want to permanently drop this student from records index? This operation cannot be rolled back.');">
                                                <i class="bi bi-trash-fill me-1"></i>Delete Student
                                            </a>

                                            <div class="ms-auto">
                                                <a href="?" class="btn btn-sm btn-secondary me-2">Cancel</a>
                                                <button type="submit" name="update_student"
                                                    class="btn btn-sm btn-primary">Save Modifications</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-people-fill me-2 text-primary"></i>PCC Student List</h5>

                                    <div class="card-tools">
                                        <form method="GET" action="" class="d-flex gap-2">
                                            <?php if (isset($_GET['edit_id'])): ?>
                                                <input type="hidden" name="edit_id"
                                                    value="<?php echo htmlspecialchars($_GET['edit_id']); ?>">
                                            <?php endif; ?>

                                            <div class="input-group input-group-sm" style="width: 16rem">
                                                <span class="input-group-text">
                                                    <i class="bi bi-search" aria-hidden="true"></i>
                                                </span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control" placeholder="Search code or name..."
                                                    aria-label="Search rows"
                                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary px-3">Search</button>
                                            <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                                                <a href="?" class="btn btn-sm btn-outline-secondary">Clear</a>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-uppercase text-secondary">
                                                <tr>
                                                    <th class="ps-4">Student No.</th>
                                                    <th>Student Name</th>
                                                    <th>Program</th>
                                                    <th>Year Level</th>
                                                    <th>Classification</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="pe-4 text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($student_list as $student): ?>
                                                    <tr
                                                        class="<?php echo ($edit_mode && $edit_id === $student['student_no']) ? 'table-warning-subtle' : ''; ?>">
                                                        <td class="ps-4 font-monospace fw-bold text-secondary">
                                                            <?php echo htmlspecialchars($student['student_no']); ?>
                                                        </td>
                                                        <td class="fw-semibold text-dark">
                                                            <?php echo htmlspecialchars($student['name']); ?>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-primary-subtle text-primary fw-medium px-2 py-1"><?php echo htmlspecialchars($student['program']); ?></span>
                                                        </td>
                                                        <td class="text-secondary small">
                                                            <?php echo htmlspecialchars($student['year']); ?>
                                                        </td>
                                                        <td><span
                                                                class="text-dark small"><?php echo htmlspecialchars($student['classification']); ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $status = $student['status'];
                                                            $badge_color = ($status === 'Enrolled') ? 'bg-success-subtle text-success' : (($status === 'Pending') ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger-subtle text-danger');
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $badge_color; ?> tab-indicator d-inline-block w-75 text-center"><?php echo htmlspecialchars($status); ?></span>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_id=<?php echo urlencode($student['student_no']); ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                                                                class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                                style="font-size: 0.75rem;">
                                                                <i class="bi bi-pencil-square me-1"></i>Edit / Manage
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
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
        <footer class="app-footer">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - </div>
            <strong><span>&nbsp;All rights reserved.</span></strong>
        </footer>
    </div>
</body>

</html>