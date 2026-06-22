<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_section'])) {
    $section_name = trim($_POST['section_name']);
    $program = trim($_POST['program']);
    $target_year = trim($_POST['target_year']);
    $status = trim($_POST['status']);
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 40;

    $is_block_section = isset($_POST['is_block_section']) ? 1 : 0;

    try {
        $conn->beginTransaction();
        if (empty($_POST['section_id'])) {
            $stmt = $conn->prepare("INSERT INTO sections (section_name, program, target_year, status, capacity, is_block_section) VALUES (:name, :program, :year, :status, :capacity, :is_block)");
            $stmt->execute([':name' => $section_name, ':program' => $program, ':year' => $target_year, ':status' => $status, ':capacity' => $capacity, ':is_block' => $is_block_section]);
            $section_id = $conn->lastInsertId();
            $log_msg = "Added a new Section \"" . $section_name . "\" under program " . $program . ".";
        } else {
            $section_id = intval($_POST['section_id']);
            $stmt = $conn->prepare("UPDATE sections SET section_name = :name, program = :program, target_year = :year, status = :status, capacity = :capacity, is_block_section = :is_block WHERE id = :id");
            $stmt->execute([':name' => $section_name, ':program' => $program, ':year' => $target_year, ':status' => $status, ':capacity' => $capacity, ':is_block' => $is_block_section, ':id' => $section_id]);
            $log_msg = "Updated Section profile configurations for \"" . $section_name . "\".";
        }

        if (isset($_POST['prebuilt_subjects'])) {
            $clear_stmt = $conn->prepare("DELETE FROM section_subjects WHERE section_id = :sid");
            $clear_stmt->execute([':sid' => $section_id]);

            if (is_array($_POST['prebuilt_subjects'])) {
                $map_stmt = $conn->prepare("INSERT INTO section_subjects (section_id, subject_id) VALUES (:sid, :subid)");
                foreach ($_POST['prebuilt_subjects'] as $sub_id) {
                    $map_stmt->execute([':sid' => $section_id, ':subid' => intval($sub_id)]);
                }
            }
        }

        $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'SCHEDULES', :msg)");
        $log_stmt->execute([':admin_id' => $_SESSION['admin_id'], ':msg' => $log_msg]);
        $conn->commit();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Section & block map logs updated successfully.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-danger text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-exclamation-triangle-fill me-2'></i>Section Processing Fault.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reallocate_students'])) {
    $source_section = trim($_POST['source_section_name']);
    $target_section_id = intval($_POST['target_section_id']);

    try {
        $conn->beginTransaction();

        $tgt_stmt = $conn->prepare("SELECT section_name FROM sections WHERE id = :id LIMIT 1");
        $tgt_stmt->execute([':id' => $target_section_id]);
        $target_section_name = $tgt_stmt->fetchColumn();

        if ($target_section_name) {
            $update_students = $conn->prepare("UPDATE students SET section = :tgt WHERE section = :src");
            $update_students->execute([':tgt' => $target_section_name, ':src' => $source_section]);

            $log_msg = "Relocated student roster matrix from Section \"" . $source_section . "\" over to \"" . $target_section_name . "\" due to minimum load constraints.";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'SCHEDULES', :msg)");
            $log_stmt->execute([':admin_id' => $_SESSION['admin_id'], ':msg' => $log_msg]);

            $conn->commit();
            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-shuffle me-2'></i>Roster relocated safely to <strong>{$target_section_name}</strong>.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
        } else {
            $conn->rollBack();
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<script>alert('Relocation Engine Fault: " . addslashes($e->getMessage()) . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_prebuilt_block'])) {
    $section_id = intval($_POST['block_section_id']);
    try {
        $conn->beginTransaction();
        $clear_stmt = $conn->prepare("DELETE FROM section_subjects WHERE section_id = :sid");
        $clear_stmt->execute([':sid' => $section_id]);

        if (isset($_POST['prebuilt_subjects']) && is_array($_POST['prebuilt_subjects'])) {
            $map_stmt = $conn->prepare("INSERT INTO section_subjects (section_id, subject_id) VALUES (:sid, :subid)");
            foreach ($_POST['prebuilt_subjects'] as $sub_id) {
                $map_stmt->execute([':sid' => $section_id, ':subid' => intval($sub_id)]);
            }
        }
        $conn->commit();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Course Block mapping matrix loaded cleanly.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } catch (PDOException $e) {
        $conn->rollBack();
    }
}

if (isset($_GET['activate_section_id'])) {
    $act_sec_id = intval($_GET['activate_section_id']);
    try {
        $stmt = $conn->prepare("UPDATE sections SET status = 'Available' WHERE id = :id");
        $stmt->execute([':id' => $act_sec_id]);
    } catch (PDOException $e) {
    }
}

if (isset($_GET['dissolve_section_id'])) {
    $diss_sec_id = intval($_GET['dissolve_section_id']);
    try {
        $stmt = $conn->prepare("UPDATE sections SET status = 'Inactive' WHERE id = :id");
        $stmt->execute([':id' => $diss_sec_id]);
    } catch (PDOException $e) {
    }
}

if (isset($_GET['delete_section_id'])) {
    $del_id = intval($_GET['delete_section_id']);
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("DELETE FROM sections WHERE id = :id");
        $stmt->execute([':id' => $del_id]);
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_subject'])) {
    $subject_code = trim($_POST['subject_code']);
    $title = trim($_POST['descriptive_title']);
    $program = trim($_POST['program']);
    $target_year = trim($_POST['target_year']);
    $units = intval($_POST['units']);
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 40;

    try {
        $conn->beginTransaction();
        if (empty($_POST['subject_id'])) {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_code, program, descriptive_title, target_year, units, capacity, status) VALUES (:code, :program, :title, :year, :units, :capacity, 'Inactive')");
            $stmt->execute([':code' => $subject_code, ':program' => $program, ':title' => $title, ':year' => $target_year, ':units' => $units, ':capacity' => $capacity]);
        } else {
            $subject_id = intval($_POST['subject_id']);
            $stmt = $conn->prepare("UPDATE subjects SET subject_code = :code, program = :program, descriptive_title = :title, target_year = :year, units = :units, capacity = :capacity WHERE id = :id");
            $stmt->execute([':code' => $subject_code, ':program' => $program, ':title' => $title, ':year' => $target_year, ':units' => $units, ':capacity' => $capacity, ':id' => $subject_id]);
        }
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
    }
}

if (isset($_GET['activate_subject_id'])) {
    $act_id = intval($_GET['activate_subject_id']);
    try {
        $stmt = $conn->prepare("UPDATE subjects SET status = 'Active' WHERE id = :id");
        $stmt->execute([':id' => $act_id]);
    } catch (PDOException $e) {
    }
}

if (isset($_GET['dissolve_subject_id'])) {
    $dissolve_id = intval($_GET['dissolve_subject_id']);
    try {
        $stmt = $conn->prepare("UPDATE subjects SET status = 'Inactive' WHERE id = :id");
        $stmt->execute([':id' => $dissolve_id]);
    } catch (PDOException $e) {
    }
}

if (isset($_GET['hard_delete_subject_id'])) {
    $hd_id = intval($_GET['hard_delete_subject_id']);
    try {
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->execute([':id' => $hd_id]);
    } catch (PDOException $e) {
    }
}

$form_subject_filter_year = isset($_GET['form_sub_filter']) ? trim($_GET['form_sub_filter']) : 'All';
$table_section_filter_year = isset($_GET['sec_filter']) ? trim($_GET['sec_filter']) : 'All';
$table_section_filter_program = isset($_GET['sec_prog_filter']) ? trim($_GET['sec_prog_filter']) : 'All';

$table_subject_filter_year = isset($_GET['sub_filter']) ? trim($_GET['sub_filter']) : 'All';
$table_subject_filter_program = isset($_GET['sub_prog_filter']) ? trim($_GET['sub_prog_filter']) : 'All';

try {
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();
    $all_master_subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_code ASC")->fetchAll(PDO::FETCH_ASSOC);

    $sec_conditions = [];
    $sec_params = [];
    if ($table_section_filter_year !== 'All') {
        $sec_conditions[] = "target_year = :ty";
        $sec_params[':ty'] = $table_section_filter_year;
    }
    if ($table_section_filter_program !== 'All') {
        $sec_conditions[] = "program = :prog";
        $sec_params[':prog'] = $table_section_filter_program;
    }
    $sec_sql = "SELECT * FROM sections" . (!empty($sec_conditions) ? " WHERE " . implode(" AND ", $sec_conditions) : "") . " ORDER BY section_name ASC";
    $s_stmt = $conn->prepare($sec_sql);
    $s_stmt->execute($sec_params);
    $sections_list = $s_stmt->fetchAll(PDO::FETCH_ASSOC);

    $sub_conditions = [];
    $sub_params = [];
    if ($table_subject_filter_year !== 'All') {
        $sub_conditions[] = "target_year = :ty";
        $sub_params[':ty'] = $table_subject_filter_year;
    }
    if ($table_subject_filter_program !== 'All') {
        $sub_conditions[] = "program = :prog";
        $sub_params[':prog'] = $table_subject_filter_program;
    }
    $sub_sql = "SELECT * FROM subjects" . (!empty($sub_conditions) ? " WHERE " . implode(" AND ", $sub_conditions) : "") . " ORDER BY subject_code ASC";
    $sub_stmt = $conn->prepare($sub_sql);
    $sub_stmt->execute($sub_params);
    $subjects_list = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
}

$edit_section_mode = false;
$selected_section = null;
$assigned_subject_ids = [];

if (isset($_GET['edit_section_id'])) {
    $edit_section_mode = true;
    foreach ($sections_list as $s) {
        if ($s['id'] == $_GET['edit_section_id']) {
            $selected_section = $s;
            break;
        }
    }
    if ($selected_section) {
        try {
            $assigned_stmt = $conn->prepare("SELECT subject_id FROM section_subjects WHERE section_id = :sid");
            $assigned_stmt->execute([':sid' => $selected_section['id']]);
            $assigned_subject_ids = $assigned_stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
        }
    }
}

$edit_subject_mode = false;
$selected_subject = null;
if (isset($_GET['edit_subject_id'])) {
    $edit_subject_mode = true;
    foreach ($subjects_list as $sub) {
        if ($sub['id'] == $_GET['edit_subject_id']) {
            $selected_subject = $sub;
            break;
        }
    }
}

$add_block_mode = isset($_GET['add_block']);
$add_section_mode = isset($_GET['add_section']);
$add_subject_mode = isset($_GET['add_subject']);
$current_semester = "1st Semester, AY 2026-2027";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Subjects & Sections</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-dark-blue: #001d3d;
        }

        body {
            background-color: #f4f6f9 !important;
        }

        .sidebar-bg {
            background-color: var(--pcc-blue) !important;
        }

        .sidebar-bg .nav-link,
        .sidebar-bg .brand-link,
        .sidebar-bg .nav-header {
            color: #ffffff !important;
        }

        .sidebar-bg-active {
            color: var(--pcc-blue) !important;
            background-color: var(--pcc-gold) !important;
            font-weight: 600;
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
            background-color: var(--pcc-dark-blue);
        }

        .user-info .username {
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-semester-text {
            color: #adb5bd;
            font-size: 11px;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .block-subjects-box {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            padding: 12px;
            border-radius: 8px;
            background: #fff;
        }

        @media (min-width: 992px) {
            .sidebar-collapse .app-sidebar {
                margin-left: -250px !important;
            }

            .sidebar-collapse .app-main,
            .sidebar-collapse .app-footer,
            .sidebar-collapse .app-header {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php echo $toast_notification; ?>
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body px-1">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)"
                            role="button"><i class="bi bi-list fs-5"></i></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-md-inline">
                            <div class="nav-date" id="liveClockDisplay">Loading Central Server Time...</div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand"
                style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="dashboard.php" class="brand-link">
                    <img src="../assets/images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
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
                            <div class="username">
                                <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin Account'); ?>
                            </div>
                            <div class="status-text small" style="color: #35e400;">Online</div>
                            <span class="sidebar-semester-text"><?php echo $current_semester; ?></span>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header">MAIN MENU</li>
                        <li class="nav-item"><a href="dashboard.php" class="nav-link"><i
                                    class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="students.php" class="nav-link"><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students</p>
                            </a></li>
                        <li class="nav-item"><a href="admissions.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions <span
                                        class="badge bg-warning text-dark float-end small font-bold rounded-pill"><?= $new_admissions; ?></span>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="verify_enrollment.php" class="nav-link"><i
                                    class="nav-icon bi bi-shield-check"></i>
                                <p>Enrollment</p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs</p>
                            </a></li>
                        <li class="nav-item"><a href="subjects.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="drop_requests.php" class="nav-link"><i
                                    class="nav-icon bi bi-file-earmark-minus-fill"></i>
                                <p>Drop Requests</p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules</p>
                            </a></li>
                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item"><a href="notice.php" class="nav-link"><i
                                    class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice</p>
                            </a></li>
                        <li class="nav-item"><a href="users.php" class="nav-link"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users</p>
                            </a></li>
                        <li class="nav-item"><a href="settings.php" class="nav-link"><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings</p>
                            </a></li>
                        <li class="nav-item"><a href="../index.php" class="nav-link text-danger-emphasis"
                                onclick="return confirm('Exit Snapshot?');"><i
                                    class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p class="text-danger font-bold">Logout</p>
                            </a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="px-3 py-3">
                <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h3 class="mb-0 mt-3 fw-bold text-dark">Subject & Section Configurations</h3>
                    <div>
                        <a href="?add_block=true" class="btn btn-success btn-sm me-2 shadow-sm"><i
                                class="bi bi-grid-3x3-gap-fill me-1"></i> Create New Block</a>
                        <a href="?add_section=true" class="btn btn-outline-success btn-sm me-2 shadow-sm"><i
                                class="bi bi-plus-circle me-1"></i> Add Section</a>
                        <a href="?add_subject=true" class="btn btn-primary btn-sm shadow-sm"><i
                                class="bi bi-plus-circle me-1"></i> Add Subject</a>
                    </div>
                </div>
            </div>

            <div class="app-content mt-2">
                <div class="container-fluid">

                    <?php if (($add_section_mode) || ($edit_section_mode && $selected_section)): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div
                                class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-sliders me-2 text-success"></i><?= $add_section_mode ? 'Register New Section' : 'Edit Section & Block Map Configuration' ?>
                                </h5>
                                <a href="subjects.php" class="btn-close"></a>
                            </div>
                            <form method="POST" action="subjects.php">
                                <div class="card-body text-dark">
                                    <input type="hidden" name="section_id" value="<?= $selected_section['id'] ?? '' ?>">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Section Name</label>
                                            <input type="text" name="section_name"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= htmlspecialchars($selected_section['section_name'] ?? '') ?>"
                                                placeholder="" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Program</label>
                                            <select name="program" class="form-select form-select-sm border shadow-sm">
                                                <option value="BSIT" <?= isset($selected_section['program']) && $selected_section['program'] === 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                                                <option value="BSCS" <?= isset($selected_section['program']) && $selected_section['program'] === 'BSCS' ? 'selected' : '' ?>>BSCS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Target Year</label>
                                            <select name="target_year" class="form-select form-select-sm border shadow-sm">
                                                <option <?= isset($selected_section['target_year']) && $selected_section['target_year'] === '1st Year' ? 'selected' : '' ?>>1st
                                                    Year</option>
                                                <option <?= isset($selected_section['target_year']) && $selected_section['target_year'] === '2nd Year' ? 'selected' : '' ?>>2nd
                                                    Year</option>
                                                <option <?= isset($selected_section['target_year']) && $selected_section['target_year'] === '3rd Year' ? 'selected' : '' ?>>3rd
                                                    Year</option>
                                                <option <?= isset($selected_section['target_year']) && $selected_section['target_year'] === '4th Year' ? 'selected' : '' ?>>4th
                                                    Year</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Capacity Status</label>
                                            <select name="status" class="form-select form-select-sm border shadow-sm">
                                                <option value="Available" <?= isset($selected_section['status']) && $selected_section['status'] === 'Available' ? 'selected' : '' ?>>Available
                                                </option>
                                                <option value="Full" <?= isset($selected_section['status']) && $selected_section['status'] === 'Full' ? 'selected' : '' ?>>Full</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Max Roster
                                                Capacity</label>
                                            <input type="number" name="capacity"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= $selected_section['capacity'] ?? 40 ?>" required>
                                        </div>

                                        <div class="col-md-2 pb-1 text-center">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input border shadow-sm" type="checkbox"
                                                    name="is_block_section" value="1" id="chkIsBlockSec"
                                                    <?= (!isset($selected_section['is_block_section']) || $selected_section['is_block_section'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label small fw-bold text-dark"
                                                    for="chkIsBlockSec">Is Block Section</label>
                                            </div>
                                        </div>

                                        <?php if ($edit_section_mode): ?>
                                            <div class="col-12 mt-4">
                                                <div
                                                    class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                                    <label class="form-label small fw-bold text-secondary m-0"><i
                                                            class="bi bi-box-fill text-primary me-1"></i> Prebuilt Block
                                                        Subjects Matrix Assignment</label>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="text-xs text-muted fw-semibold">Prog Filter:</span>
                                                        <select
                                                            class="form-select form-select-sm p-1 fs-8 text-secondary border shadow-sm"
                                                            style="width:100px;" id="formProgFilter"
                                                            onchange="filterFormSubjects()">
                                                            <option value="All">All Programs</option>
                                                            <option value="BSIT" <?= (isset($selected_section['program']) && $selected_section['program'] === 'BSIT') ? 'selected' : ''; ?>>
                                                                BSIT Only</option>
                                                            <option value="BSCS" <?= (isset($selected_section['program']) && $selected_section['program'] === 'BSCS') ? 'selected' : ''; ?>>
                                                                BSCS Only</option>
                                                        </select>

                                                        <span class="text-xs text-muted fw-semibold ms-2">Yr Level
                                                            Filter:</span>
                                                        <select
                                                            class="form-select form-select-sm p-1 fs-8 text-secondary border shadow-sm"
                                                            style="width:120px;" id="formYearFilter"
                                                            onchange="filterFormSubjects()">
                                                            <option value="All">All Levels</option>
                                                            <option value="1st Year">1st Year Only</option>
                                                            <option value="2nd Year">2nd Year Only</option>
                                                            <option value="3rd Year">3rd Year Only</option>
                                                            <option value="4th Year">4th Year Only</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="block-subjects-box shadow-sm">
                                                    <div class="row row-cols-1 row-cols-md-2 g-2" id="formSubjectsWrapperRow">
                                                        <?php
                                                        foreach ($all_master_subjects as $sb) {
                                                            $is_checked = in_array($sb['id'], $assigned_subject_ids) ? 'checked' : '';
                                                            echo "
                                                            <div class='col subject-form-item' data-program='{$sb['program']}' data-year='{$sb['target_year']}'>
                                                                <div class='form-check'>
                                                                    <input class='form-check-input' type='checkbox' name='prebuilt_subjects[]' value='{$sb['id']}' id='editFormBlockSub_{$sb['id']}' {$is_checked}>
                                                                    <label class='form-check-label small text-dark' for='editFormBlockSub_{$sb['id']}'>
                                                                        <strong>[{$sb['program']}] {$sb['subject_code']}</strong> - " . htmlspecialchars($sb['descriptive_title']) . " <span class='text-xs text-muted'>({$sb['target_year']})</span>
                                                                    </label>
                                                                </div>
                                                            </div>";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-end py-3 border-top">
                                    <a href="subjects.php" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                    <button type="submit" name="save_section"
                                        class="btn btn-sm btn-success px-3 shadow-sm">Save Complete Adjustments</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($add_block_mode): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div
                                class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-grid-3x3-gap-fill me-2 text-success"></i>Configure New Prebuilt Course
                                    Block</h5>
                                <a href="subjects.php" class="btn-close"></a>
                            </div>
                            <form method="POST" action="subjects.php">
                                <div class="card-body text-dark">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold text-secondary">Target Section
                                                Container</label>
                                            <select name="block_section_id"
                                                class="form-select form-select-sm border shadow-sm" required>
                                                <option value="" disabled selected>-- Select a target section matrix --
                                                </option>
                                                <?php foreach ($sections_list as $sc): ?>
                                                    <option value="<?php echo $sc['id']; ?>">
                                                        <?php echo htmlspecialchars($sc['section_name'] . ' [' . $sc['program'] . ' - ' . $sc['target_year'] . ']'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <label class="form-label small fw-bold text-secondary">Assign Subjects Into
                                                Staged Block</label>
                                            <div class="block-subjects-box shadow-sm">
                                                <div class="row row-cols-1 row-cols-md-2 g-2">
                                                    <?php foreach ($all_master_subjects as $sb): ?>
                                                        <div class="col">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="prebuilt_subjects[]" value="<?php echo $sb['id']; ?>"
                                                                    id="blockSub_<?php echo $sb['id']; ?>">
                                                                <label class="form-check-label small text-dark"
                                                                    for="blockSub_<?php echo $sb['id']; ?>">
                                                                    <strong>[<?= $sb['program']; ?>]
                                                                        <?php echo htmlspecialchars($sb['subject_code']); ?></strong>
                                                                    - <?php echo htmlspecialchars($sb['descriptive_title']); ?>
                                                                    <span
                                                                        class="text-xs text-muted">(<?php echo $sb['target_year']; ?>)</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-end py-3 border-top">
                                    <a href="subjects.php" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                    <button type="submit" name="save_prebuilt_block"
                                        class="btn btn-sm btn-success px-3 shadow-sm">Save Prebuilt Block Map</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if (($add_subject_mode) || ($edit_subject_mode && $selected_subject)): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div
                                class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-sliders me-2 text-primary"></i><?= $add_subject_mode ? 'Initialize New Subject' : 'Edit Subject Profile' ?>
                                </h5>
                                <a href="subjects.php" class="btn-close"></a>
                            </div>
                            <form method="POST" action="subjects.php">
                                <div class="card-body text-dark">
                                    <input type="hidden" name="subject_id" value="<?= $selected_subject['id'] ?? '' ?>">
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Subject Code</label>
                                            <input type="text" name="subject_code"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= htmlspecialchars($selected_subject['subject_code'] ?? '') ?>"
                                                placeholder="" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Subject Name</label>
                                            <input type="text" name="descriptive_title"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= htmlspecialchars($selected_subject['descriptive_title'] ?? '') ?>"
                                                placeholder="" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Program</label>
                                            <select name="program" class="form-select form-select-sm border shadow-sm">
                                                <option value="BSIT" <?= isset($selected_subject['program']) && $selected_subject['program'] === 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                                                <option value="BSCS" <?= isset($selected_subject['program']) && $selected_subject['program'] === 'BSCS' ? 'selected' : '' ?>>BSCS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Year Level</label>
                                            <select name="target_year" class="form-select form-select-sm border shadow-sm">
                                                <option <?= isset($selected_subject['target_year']) && $selected_subject['target_year'] === '1st Year' ? 'selected' : '' ?>>1st
                                                    Year</option>
                                                <option <?= isset($selected_subject['target_year']) && $selected_subject['target_year'] === '2nd Year' ? 'selected' : '' ?>>2nd
                                                    Year</option>
                                                <option <?= isset($selected_subject['target_year']) && $selected_subject['target_year'] === '3rd Year' ? 'selected' : '' ?>>3rd
                                                    Year</option>
                                                <option <?= isset($selected_subject['target_year']) && $selected_subject['target_year'] === '4th Year' ? 'selected' : '' ?>>4th
                                                    Year</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label small fw-bold text-secondary">Units</label>
                                            <input type="number" name="units"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= $selected_subject['units'] ?? 3 ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Capacity</label>
                                            <input type="number" name="capacity"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= $selected_subject['capacity'] ?? 40 ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-end py-3 border-top">
                                    <a href="subjects.php" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                    <button type="submit" name="save_subject"
                                        class="btn btn-sm btn-primary px-3 shadow-sm">Save Subject Changes</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="card border border-light-subtle shadow-sm mb-4 bg-white" style="border-radius: 10px;">
                        <div
                            class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bi bi-grid-3x3-gap-fill me-2 text-success"></i>Sections Directory</h5>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary fw-bold">Program:</span>
                                    <select class="form-select form-select-sm text-secondary fw-medium shadow-sm py-1"
                                        style="width: 110px; font-size:0.85rem;"
                                        onchange="location.href='?sec_prog_filter=' + this.value + '&sec_filter=<?= $table_section_filter_year; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>'">
                                        <option value="All" <?= ($table_section_filter_program === 'All') ? 'selected' : ''; ?>>All</option>
                                        <option value="BSIT" <?= ($table_section_filter_program === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                                        <option value="BSCS" <?= ($table_section_filter_program === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary fw-bold">Filter By Level:</span>
                                    <select class="form-select form-select-sm text-secondary fw-medium shadow-sm py-1"
                                        style="width: 130px; font-size:0.85rem;"
                                        onchange="location.href='?sec_filter=' + this.value + '&sec_prog_filter=<?= $table_section_filter_program; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>'">
                                        <option value="All" <?= ($table_section_filter_year === 'All') ? 'selected' : ''; ?>>All Year Levels</option>
                                        <option value="1st Year" <?= ($table_section_filter_year === '1st Year') ? 'selected' : ''; ?>>1st Year </option>
                                        <option value="2nd Year" <?= ($table_section_filter_year === '2nd Year') ? 'selected' : ''; ?>>2nd Year </option>
                                        <option value="3rd Year" <?= ($table_section_filter_year === '3rd Year') ? 'selected' : ''; ?>>3rd Year </option>
                                        <option value="4th Year" <?= ($table_section_filter_year === '4th Year') ? 'selected' : ''; ?>>4th Year </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-dark">
                                    <thead class="table-light small text-uppercase text-secondary">
                                        <tr>
                                            <th>Section Name</th>
                                            <th>Program</th>
                                            <th>Target Year</th>
                                            <th>Classification</th>
                                            <th>Block Status</th>
                                            <th>Enrollment Headcount</th>
                                            <th>Capacity Status</th>
                                            <th class="text-end pe-4" style="width: 320px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($sections_list)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 small">No sections found
                                                    matching selected criteria.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($sections_list as $row): ?>
                                                <?php
                                                $is_sec_hidden = ($row['status'] === 'Inactive');
                                                $max_capacity = isset($row['capacity']) ? intval($row['capacity']) : 40;

                                                $count_stmt = $conn->prepare("SELECT COUNT(*) FROM section_subjects WHERE section_id = :sid");
                                                $count_stmt->execute([':sid' => $row['id']]);
                                                $total_mapped = $count_stmt->fetchColumn();

                                                $enrolled_query = $conn->prepare("SELECT COUNT(*) FROM students WHERE section = :secName");
                                                $enrolled_query->execute([':secName' => $row['section_name']]);
                                                $enrolled_count = intval($enrolled_query->fetchColumn());

                                                $under_populated = ($enrolled_count < 10);
                                                ?>
                                                <tr>
                                                    <td class="ps-4 fw-semibold text-dark">
                                                        <?= htmlspecialchars($row['section_name']) ?>
                                                    </td>
                                                    <td class="fw-bold text-secondary">
                                                        <?= htmlspecialchars($row['program'] ?? 'BSIT') ?>
                                                    </td>
                                                    <td class="text-secondary"><?= htmlspecialchars($row['target_year']) ?></td>

                                                    <td>
                                                        <span
                                                            class="badge <?= (!isset($row['is_block_section']) || $row['is_block_section'] == 1) ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info'; ?>">
                                                            <?= (!isset($row['is_block_section']) || $row['is_block_section'] == 1) ? 'Block Section' : 'Free Section'; ?>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span class="text-sm fw-medium text-primary">
                                                            <?= $total_mapped; ?> Subjects
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">
                                                            <?= $enrolled_count; ?> / <?= $max_capacity; ?> Students
                                                        </span>
                                                        <?php if ($under_populated && !$is_sec_hidden && $enrolled_count > 0): ?>
                                                            <small class="d-block text-muted fw-semibold"
                                                                style="font-size: 11px;"><i class="bi bi-exclamation-triangle"></i>
                                                                Below Min Load (10)</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($is_sec_hidden): ?>
                                                            <span
                                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle tab-indicator">Hidden</span>
                                                        <?php else: ?>
                                                            <span
                                                                class="badge <?= ($enrolled_count >= $max_capacity || strtolower($row['status']) === 'full') ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> tab-indicator">
                                                                <?= ($enrolled_count >= $max_capacity) ? 'Full' : htmlspecialchars($row['status']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <?php if ($under_populated && $enrolled_count > 0): ?>
                                                        <?php endif; ?>
                                                        <?php if ($is_sec_hidden): ?>
                                                            <a href="?activate_section_id=<?= $row['id'] ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>"
                                                                class="btn btn-xs btn-outline-success border py-1 px-2 me-1"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-plus-circle me-1"></i>Add</a>
                                                        <?php else: ?>
                                                            <a href="?dissolve_section_id=<?= $row['id'] ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>"
                                                                class="btn btn-xs btn-outline-warning border py-1 px-2 me-1"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-dash-circle me-1"></i>Dissolve</a>
                                                        <?php endif; ?>
                                                        <a href="?edit_section_id=<?= $row['id'] ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>"
                                                            class="btn btn-xs btn-outline-primary border py-1 px-2 me-1"
                                                            style="font-size: 0.75rem;"><i
                                                                class="bi bi-pencil-square me-1"></i>Edit</a>
                                                        <a href="?delete_section_id=<?= $row['id'] ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>"
                                                            class="btn btn-xs btn-outline-danger border py-1 px-2"
                                                            style="font-size: 0.75rem;"
                                                            onclick="return confirm('Drop section permanently?');"><i
                                                                class="bi bi-trash me-1"></i>Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-light-subtle shadow-sm mb-4 bg-white" style="border-radius: 10px;">
                        <div
                            class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bi bi-book-fill me-2 text-primary"></i>Subject Directory</h5>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary fw-bold">Program:</span>
                                    <select class="form-select form-select-sm text-secondary fw-medium shadow-sm py-1"
                                        style="width: 110px; font-size:0.85rem;"
                                        onchange="location.href='?sub_prog_filter=' + this.value + '&sub_filter=<?= $table_subject_filter_year; ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>'">
                                        <option value="All" <?= ($table_subject_filter_program === 'All') ? 'selected' : ''; ?>>All</option>
                                        <option value="BSIT" <?= ($table_subject_filter_program === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                                        <option value="BSCS" <?= ($table_subject_filter_program === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary fw-bold">Filter By Level:</span>
                                    <select class="form-select form-select-sm text-secondary fw-medium shadow-sm py-1"
                                        style="width: 150px; font-size:0.85rem;"
                                        onchange="location.href='?sub_filter=' + this.value + '&sub_prog_filter=<?= $table_subject_filter_program; ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>'">
                                        <option value="All" <?= ($table_subject_filter_year === 'All') ? 'selected' : ''; ?>>All Year Levels</option>
                                        <option value="1st Year" <?= ($table_subject_filter_year === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                                        <option value="2nd Year" <?= ($table_subject_filter_year === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                                        <option value="3rd Year" <?= ($table_subject_filter_year === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                                        <option value="4th Year" <?= ($table_subject_filter_year === '4th Year') ? 'selected' : ''; ?>>4th Year</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-dark">
                                    <thead class="table-light small text-uppercase text-secondary">
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Descriptive Title</th>
                                            <th>Program</th>
                                            <th>Target Year</th>
                                            <th>Units</th>
                                            <th>Portal View Visibility Status</th>
                                            <th class="text-end pe-4" style="width: 240px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($subjects_list)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4 small">No subjects found
                                                    matching selected criteria.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($subjects_list as $sub): ?>
                                                <?php $is_active = (isset($sub['status']) && $sub['status'] === 'Active'); ?>
                                                <tr>
                                                    <td class="ps-4 font-monospace fw-bold text-secondary small">
                                                        <?= htmlspecialchars($sub['subject_code']) ?>
                                                    </td>
                                                    <td class="fw-semibold text-dark">
                                                        <?= htmlspecialchars($sub['descriptive_title']) ?>
                                                    </td>
                                                    <td class="fw-bold text-secondary">
                                                        <?= htmlspecialchars($sub['program'] ?? 'BSIT') ?>
                                                    </td>
                                                    <td class="text-secondary small">
                                                        <?= htmlspecialchars($sub['target_year'] ?? '1st Year') ?>
                                                    </td>
                                                    <td class="text-secondary small"><?= htmlspecialchars($sub['units']) ?></td>
                                                    <td>
                                                        <span
                                                            class="badge <?= $is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?> tab-indicator">
                                                            <?= $is_active ? 'Visible (Active)' : 'Hidden (Inactive)' ?>
                                                        </span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <?php if (!$is_active): ?>
                                                            <a href="?activate_subject_id=<?= $sub['id'] ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>"
                                                                class="btn btn-xs btn-outline-success border py-1 px-2 me-1"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-plus-circle me-1"></i>Add to Portal</a>
                                                        <?php else: ?>
                                                            <a href="?dissolve_subject_id=<?= $sub['id'] ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>"
                                                                class="btn btn-xs btn-outline-warning border py-1 px-2 me-1"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-dash-circle me-1"></i>Dissolve</a>
                                                        <?php endif; ?>
                                                        <a href="?edit_subject_id=<?= $sub['id'] ?>&sub_filter=<?= $table_subject_filter_year; ?>&sub_prog_filter=<?= $table_subject_filter_program; ?>&sec_filter=<?= $table_section_filter_year; ?>&sec_prog_filter=<?= $table_section_filter_program; ?>"
                                                            class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                            style="font-size: 0.75rem;"><i
                                                                class="bi bi-pencil-square me-1"></i>Edit</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <div class="modal fade text-dark" id="studentRelocationModal" preg-index="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
                <div class="modal-header bg-warning text-dark border-bottom-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shuffle me-2"></i>Irregular Load Relocation Engine
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="subjects.php">
                    <div class="modal-body py-4">
                        <p class="small text-secondary mb-3">Roster headcount constraints require relocating legacy
                            entries. Select a target block container matching parameters below.</p>

                        <div class="mb-3 bg-light p-3 rounded border">
                            <div class="small mb-1"><strong>Source Section:</strong> <span id="lblSourceSection"
                                    class="font-monospace text-primary"></span></div>
                            <div class="small mb-1"><strong>Program Path:</strong> <span id="lblSourceProgram"
                                    class="fw-bold"></span></div>
                            <div class="small"><strong>Year Level:</strong> <span id="lblSourceYear"
                                    class="text-secondary"></span></div>
                        </div>

                        <input type="hidden" name="source_section_name" id="hidSourceSectionName">

                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Target Redirection Group
                                Destination</label>
                            <select name="target_section_id" id="optTargetSectionsDropdown"
                                class="form-select border shadow-sm" required>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3"
                            data-bs-dismiss="modal">Abort</button>
                        <button type="submit" name="reallocate_students"
                            class="btn btn-sm btn-warning fw-bold px-3 shadow-sm">Execute Relocation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }

        const masterSectionsMap = <?php echo json_encode($sections_list); ?>;

        function triggerRelocation(srcSection, program, yearLevel) {
            document.getElementById('lblSourceSection').textContent = srcSection;
            document.getElementById('lblSourceProgram').textContent = program;
            document.getElementById('lblSourceYear').textContent = yearLevel;
            document.getElementById('hidSourceSectionName').value = srcSection;

            const dropdown = document.getElementById('optTargetSectionsDropdown');
            dropdown.innerHTML = '<option value="" disabled selected>-- Choose valid destination block --</option>';

            const choices = masterSectionsMap.filter(sec => sec.program === program && sec.target_year === yearLevel && sec.section_name !== srcSection && sec.status !== 'Inactive');

            if (choices.length === 0) {
                dropdown.innerHTML = '<option value="" disabled>No compatible parallel paths active for this tier.</option>';
            } else {
                choices.forEach(sec => {
                    const opt = document.createElement('option');
                    opt.value = sec.id;
                    opt.textContent = `${sec.section_name} (${sec.status})`;
                    dropdown.appendChild(opt);
                });
            }

            const modalElement = new bootstrap.Modal(document.getElementById('studentRelocationModal'));
            modalElement.show();
        }

        function filterFormSubjects() {
            const selectedProg = document.getElementById('formProgFilter').value;
            const selectedYear = document.getElementById('formYearFilter').value;
            const items = document.querySelectorAll('.subject-form-item');

            items.forEach(item => {
                const progMatch = (selectedProg === 'All' || item.getAttribute('data-program') === selectedProg);
                const yearMatch = (selectedYear === 'All' || item.getAttribute('data-year') === selectedYear);

                if (progMatch && yearMatch) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        }

        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        document.addEventListener("DOMContentLoaded", function () {
            runLiveDashboardClock();
            setInterval(runLiveDashboardClock, 1000);

            if (document.getElementById('formProgFilter')) {
                filterFormSubjects();
            }
        });
    </script>
</body>

</html>