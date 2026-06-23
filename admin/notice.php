<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";

$upload_directory = "../uploads/notices/";
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true);
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    try {
        $conn->beginTransaction();
        $info_stmt = $conn->prepare("SELECT title, target_program, image_path FROM notices WHERE id = :id");
        $info_stmt->execute([':id' => $delete_id]);
        $notice_info = $info_stmt->fetch(PDO::FETCH_ASSOC);

        if ($notice_info) {
            if (!empty($notice_info['image_path']) && file_exists("../" . $notice_info['image_path'])) {
                unlink("../" . $notice_info['image_path']);
            }
            $del_stmt = $conn->prepare("DELETE FROM notices WHERE id = :id");
            $del_stmt->execute([':id' => $delete_id]);
            $log_msg = "Deleted a Notice, \"" . $notice_info['title'] . "\" for \"" . $notice_info['target_program'] . "\" .";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'USERS', :msg)");
            $log_stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':msg' => $log_msg
            ]);
        }

        $conn->commit();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-danger text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-trash3-fill me-2'></i>Notice/Announcement Deleted Successfully.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='alert alert-danger m-3'>Deletion Intercepted: " . $e->getMessage() . "</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $audience = "Students";
    $target_program = trim($_POST['target_program']);
    $content = trim($_POST['content']);
    $uploaded_image_path = $_POST['existing_image'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._]/', '', $_FILES['image']['name']);
        $destination_target = $upload_directory . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination_target)) {
            if (!empty($_POST['existing_image']) && file_exists("../" . $_POST['existing_image'])) {
                unlink("../" . $_POST['existing_image']);
            }
            $uploaded_image_path = "uploads/notices/" . $file_name;
        }
    }

    try {
        $conn->beginTransaction();

        if (isset($_POST['create_notice'])) {
            $ins_stmt = $conn->prepare("INSERT INTO notices (title, audience, target_program, content, image_path, status) VALUES (:title, :audience, :program, :content, :img, 'Published')");
            $ins_stmt->execute([
                ':title' => $title,
                ':audience' => $audience,
                ':program' => $target_program,
                ':content' => $content,
                ':img' => $uploaded_image_path
            ]);

            $log_msg = "Published a new Notice, \"" . $title . "\" for \"" . $target_program . "\" .";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'USERS', :msg)");
            $log_stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':msg' => $log_msg
            ]);

            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>New notice compiled and pushed into student portals.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";

        } elseif (isset($_POST['update_notice'])) {
            $notice_id = intval($_POST['notice_id']);

            $upd_stmt = $conn->prepare("UPDATE notices SET title = :title, audience = :audience, target_program = :program, content = :content, image_path = :img WHERE id = :id");
            $upd_stmt->execute([
                ':title' => $title,
                ':audience' => $audience,
                ':program' => $target_program,
                ':content' => $content,
                ':img' => $uploaded_image_path,
                ':id' => $notice_id
            ]);

            $log_msg = "Modified system announcement Advisory profile properties for \"" . $title . "\" .";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'USERS', :msg)");
            $log_stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':msg' => $log_msg
            ]);

            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i>Bulletin modifications stored cleanly.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
        }

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $toast_notification = "<div class='alert alert-danger m-3'>Database Error Fault: " . $e->getMessage() . "</div>";
    }
}
$new_admissions = 0;
$notice_list = [];
$search_param = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : '%';

try {
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();

    $query_stmt = $conn->prepare("SELECT * FROM notices WHERE (title LIKE :search OR content LIKE :search OR target_program LIKE :search) ORDER BY id DESC");
    $query_stmt->execute([':search' => $search_param]);
    $notice_list = $query_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $notice_list = [];
}

// View state handlers evaluation blocks
$edit_mode = false;
$add_mode = isset($_GET['action']) && $_GET['action'] === 'new';
$selected_notice = null;

if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    foreach ($notice_list as $notice) {
        if (intval($notice['id']) === $edit_id) {
            $selected_notice = $notice;
            $edit_mode = true;
            break;
        }
    }
}

$current_semester = "1st Semester, AY 2026-2027";

$pending_enrollment_count = 0;
$pending_drop_count = 0;

try {
    $enroll_count_stmt = $conn->query("SELECT COUNT(*) FROM students WHERE enrollment_status = 'Pending Approval'");
    $pending_enrollment_count = $enroll_count_stmt->fetchColumn();

    $drop_count_stmt = $conn->query("SELECT COUNT(*) FROM drop_requests WHERE status = 'Pending Review'");
    $pending_drop_count = $drop_count_stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Sidebar Badges Fetch Error: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Notice Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-dark-blue: #001d3d;
        }

        .sidebar-bg {
            background-color: var(--pcc-blue) !important;
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
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

        .tab-indicator {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
        }

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .notice-thumb {
            width: 50px;
            height: 35px;
            object-fit: cover;
            border-radius: 4px;
            background-color: #e9ecef;
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
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)" role="button"><i
                                class="bi bi-list fs-5"></i></a>
                    </li>
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
                    <img src="../assets/images/PCC_logo.png" alt="PCC Logo" class="brand-image" />
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
                        <li class="nav-item"><a href="students.php" class="nav-link "><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students</p>
                            </a></li>
                        <li class="nav-item"><a href="admissions.php" class="nav-link "><i
                                    class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions
                                    <?php if ($new_admissions > 0): ?>
                                        <span id="admissionsBadge"
                                            class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $new_admissions; ?></span>
                                    <?php endif; ?>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="verify_enrollment.php" class="nav-link"><i
                                    class="nav-icon bi bi-shield-check"></i>
                                <p>Enrollment
                                    <?php if ($pending_enrollment_count > 0): ?>
                                        <span class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $pending_enrollment_count; ?></span>
                                    <?php endif; ?>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs</p>
                            </a></li>
                        <li class="nav-item"><a href="subjects.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="drop_requests.php" class="nav-link"><i
                                    class="nav-icon bi bi-file-earmark-minus-fill"></i>
                                <p>Drop Requests
                                    <?php if ($pending_drop_count > 0): ?>
                                        <span class="badge bg-warning text-dark float-end small font-bold rounded-pill"
                                            style="background-color: white"><?php echo $pending_drop_count; ?></span>
                                    <?php endif; ?>
                                </p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules</p>
                            </a></li>
                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item"><a href="notice.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice</p>
                            </a></li>
                        <li class="nav-item"><a href="users.php" class="nav-link"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users</p>
                            </a></li>
                        <li class="nav-item"><a href="settings.php" class="nav-link "><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings</p>
                            </a></li>
                        <li class="nav-item">
                            <a href="../index.php" class="nav-link text-danger-emphasis"
                                onclick="return confirm('Are you sure you want to end your snapshot session?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p class="text-danger font-bold">Logout</p>
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
                            <h3 class="mb-0 mt-3 fw-bold text-dark">Notice Management</h3>
                        </div>
                        <div class="col-sm-6 text-end mt-3">
                            <a href="?action=new" class="btn btn-primary shadow-sm fw-semibold btn-sm"><i
                                    class="bi bi-megaphone-fill me-2"></i>Add New Notice / Announcement</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content mt-3">
                <div class="container-fluid">

                    <?php if ($edit_mode && $selected_notice): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div
                                class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-pencil-square me-2 text-warning"></i>Edit Notice Profile Workspace:
                                    #<?= $selected_notice['id'] ?></h5>
                                <a href="notice.php" class="btn-close" aria-label="Close"></a>
                            </div>
                            <form method="POST" action="notice.php" enctype="multipart/form-data">
                                <div class="card-body bg-white text-dark">
                                    <input type="hidden" name="notice_id"
                                        value="<?= htmlspecialchars($selected_notice['id']) ?>">
                                    <input type="hidden" name="existing_image"
                                        value="<?= htmlspecialchars($selected_notice['image_path'] ?? '') ?>">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary">Notice Title
                                                Summary</label>
                                            <input type="text" name="title"
                                                class="form-control form-control-sm border shadow-sm"
                                                value="<?= htmlspecialchars($selected_notice['title']) ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Audience Scope
                                                Mapping</label>
                                            <input type="text"
                                                class="form-control form-control-sm border shadow-sm bg-light"
                                                name="audience" value="Students" readonly required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Program Focus
                                                Category</label>
                                            <select name="target_program"
                                                class="form-select form-select-sm border shadow-sm" required>
                                                <option value="All Programs" <?= $selected_notice['target_program'] === 'All Programs' ? 'selected' : '' ?>>All Programs</option>
                                                <option value="BSIT" <?= $selected_notice['target_program'] === 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                                                <option value="BSCS" <?= $selected_notice['target_program'] === 'BSCS' ? 'selected' : '' ?>>BSCS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Update Banner Attachment
                                                File</label>
                                            <input type="file" name="image"
                                                class="form-control form-control-sm border shadow-sm" accept="image/*">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Notice Content
                                                Text</label>
                                            <textarea name="content" class="form-control form-control-sm border shadow-sm"
                                                rows="3"
                                                required><?= htmlspecialchars($selected_notice['content']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="card-header bg-light border-top d-flex justify-content-between align-items-center py-3">
                                    <a href="notice.php?delete_id=<?= $selected_notice['id'] ?>"
                                        class="btn btn-sm btn-danger shadow-sm px-3"
                                        onclick="return confirm('Drop notice row content stack permanently?');"><i
                                            class="bi bi-trash-fill me-2"></i>Remove Notice/Announcement</a>
                                    <div class="ms-auto">
                                        <a href="notice.php" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                        <button type="submit" name="update_notice"
                                            class="btn btn-sm btn-primary px-3 shadow-sm">Save Modifications</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($add_mode): ?>
                        <div class="card border-0 shadow-sm mb-4 bg-white">
                            <div
                                class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-megaphone-fill me-2 text-primary"></i>Compose New Bulletin Announcement
                                </h5>
                                <a href="notice.php" class="btn-close" aria-label="Close"></a>
                            </div>
                            <form method="POST" action="notice.php" enctype="multipart/form-data">
                                <div class="card-body bg-white text-dark">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary">Notice Title</label>
                                            <input type="text" name="title"
                                                class="form-control form-control-sm border shadow-sm"
                                                placeholder="Enter headline summary..." required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Audience Scope</label>
                                            <input type="text"
                                                class="form-control form-control-sm border shadow-sm bg-light"
                                                name="audience" value="Students" readonly required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary">Program Focus</label>
                                            <select name="target_program"
                                                class="form-select form-select-sm border shadow-sm" required>
                                                <option value="All Programs">All Programs</option>
                                                <option value="BSIT">BSIT</option>
                                                <option value="BSCS">BSCS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary">Banner Attachment
                                                File</label>
                                            <input type="file" name="image"
                                                class="form-control form-control-sm border shadow-sm" accept="image/*">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Notice Content
                                                Text</label>
                                            <textarea name="content" class="form-control form-control-sm border shadow-sm"
                                                rows="3" placeholder="Type announcement details here..."
                                                required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-end py-3 border-top">
                                    <a href="notice.php" class="btn btn-sm btn-outline-secondary px-3 me-2">Cancel</a>
                                    <button type="submit" name="create_notice"
                                        class="btn btn-sm btn-primary px-3 shadow-sm">Publish Announcement</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <div class="card shadow-sm border border-light-subtle bg-white" style="border-radius: 10px;">
                            <div
                                class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="card-title mb-0 fw-bold text-dark"><i
                                        class="bi bi-list-task me-2 text-primary"></i>Active Notices Matrix</h5>
                                <div class="card-tools">
                                    <form method="GET" action="notice.php" class="d-flex gap-2">
                                        <div class="input-group input-group-sm border rounded shadow-sm"
                                            style="width: 16rem">
                                            <span class="input-group-text bg-light border-0 text-muted"><i
                                                    class="bi bi-search"></i></span>
                                            <input id="table-filter" type="search" name="search"
                                                class="form-control border-0 bg-light"
                                                placeholder="Search headline or program..."
                                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 text-dark">
                                        <thead class="table-light small text-uppercase text-secondary border-bottom">
                                            <tr>
                                                <th>Image</th>
                                                <th>Title / Announcement</th>
                                                <th>Audience Scope</th>
                                                <th>Program Focus</th>
                                                <th>Date Posted</th>
                                                <th class="pe-4 text-end" style="width: 180px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($notice_list)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted"><i
                                                            class="bi bi-megaphone fs-1 opacity-50 mb-3 d-block"></i>No Live
                                                        Bulletins Broadcasted</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($notice_list as $notice): ?>
                                                    <tr
                                                        class="<?= ($edit_mode && $edit_id === intval($notice['id'])) ? 'table-warning-subtle' : ''; ?>">
                                                        <td class="ps-4">
                                                            <?php if (!empty($notice['image_path']) && file_exists("../" . $notice['image_path'])): ?>
                                                                <img src="../<?= htmlspecialchars($notice['image_path']) ?>"
                                                                    class="notice-thumb border shadow-sm" alt="Banner" />
                                                                <?php
                                                            else: ?>
                                                                <div
                                                                    class="notice-thumb d-flex align-items-center justify-content-center text-muted small border bg-light shadow-sm">
                                                                    <i class="bi bi-file-text fs-5"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark">
                                                                <?= htmlspecialchars($notice['title']); ?>
                                                            </div>
                                                            <div class="text-muted small text-truncate"
                                                                style="max-width: 380px;">
                                                                <?= htmlspecialchars($notice['content']); ?>
                                                            </div>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-primary-subtle text-primary fw-medium px-2 py-1"><?= htmlspecialchars($notice['audience']); ?></span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $badge_theme = 'bg-secondary-subtle text-secondary-emphasis';
                                                            if ($notice['target_program'] === 'BSIT') {
                                                                $badge_theme = 'bg-success-subtle text-success';
                                                            } elseif ($notice['target_program'] === 'BSCS') {
                                                                $badge_theme = 'bg-info-subtle text-info-emphasis';
                                                            }
                                                            ?>
                                                            <span
                                                                class="badge <?= $badge_theme; ?> px-2 py-1"><?= htmlspecialchars($notice['target_program']); ?></span>
                                                        </td>
                                                        <td class="text-secondary small">
                                                            <?= date('M j, Y', strtotime($notice['created_at'])); ?>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_id=<?= urlencode($notice['id']); ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                                                                class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-pencil-square me-1"></i>Edit / Manage</a>
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
            </div>
        </main>

        <footer class="app-footer px-4 py-3 border-top bg-white small text-muted">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - &copy; 2026</div>
            <strong><span class="float-end">&nbsp;All rights reserved.</span></strong>
            <div class="clearfix"></div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            runLiveDashboardClock();
            setInterval(runLiveDashboardClock, 1000);

            const searchInput = document.getElementById('table-filter');
            const tableRows = document.querySelectorAll('table tbody tr');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    tableRows.forEach(row => {
                        const titleText = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || "";
                        const programText = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || "";

                        if (titleText.includes(query) || programText.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>