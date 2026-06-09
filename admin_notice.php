<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Notice Management</title>
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

        .notice-thumb {
            width: 50px;
            height: 35px;
            object-fit: cover;
            border-radius: 4px;
            background-color: #e9ecef;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php
    // --- BASIC SERVER-SIDE DATA (MOCK) ---
    $notice_list = [
        ['id' => '1', 'title' => 'Enrollment Extended for First Semester', 'audience' => 'All Students', 'target_program' => 'General / Global', 'date' => 'June 8, 2026', 'content' => 'Please be informed that regular enrollment has been extended until next Friday.', 'image' => 'enrollment.jpg', 'status' => 'Published'],
        ['id' => '2', 'title' => 'Compulsory Capstone Orientation', 'audience' => 'Students Only', 'target_program' => 'BSIT', 'date' => 'June 7, 2026', 'content' => 'All 3rd and 4th-year IT students are required to attend the platform briefing.', 'image' => '', 'status' => 'Published'],
        ['id' => '3', 'title' => 'System Maintenance: Grading Module', 'audience' => 'Faculty Only', 'target_program' => 'All Programs', 'date' => 'June 5, 2026', 'content' => 'The grading sheet submission platform will be offline for routine updates on Sunday from 2:00 AM to 5:00 AM.', 'image' => 'maintenance.jpg', 'status' => 'Published']
    ];

    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        echo "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Record Deleted!</strong> Notice Bulletin entry #" . htmlspecialchars($delete_id) . " dropped from memory indices.</div>";
    }

    $edit_mode = false;
    $add_mode = isset($_GET['action']) && $_GET['action'] === 'new';
    $selected_notice = null;

    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        foreach ($notice_list as $notice) {
            if ($notice['id'] === $edit_id) {
                $selected_notice = $notice;
                $edit_mode = true;
                break;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_notice'])) {
            echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Record updated successfully!</div>";
            $edit_mode = false;
        } elseif (isset($_POST['create_notice'])) {
            echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>New notice bulletin published successfully!</div>";
            $add_mode = false;
        }
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
                            <a href="admin_reports.php" class="nav-link">
                                <i class="nav-icon bi bi-flag-fill"></i>
                                <p>Reports <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_notice.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 mt-3 fw-bold">Notice Management</h3>
                        </div>
                        <div class="col-sm-6 text-end mt-3">
                            <a href="?action=new" class="btn btn-primary shadow-sm fw-semibold"><i
                                    class="bi bi-megaphone-fill me-2"></i>Add New Notice/Announcement</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row g-4">
                        <?php if ($edit_mode && $selected_notice): ?>
                            <div class="col-12">
                                <div class="card border-warning shadow-sm mb-4">
                                    <div
                                        class="card-header bg-warning-subtle text-dark-emphasis py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit
                                            Notice Profile: #<?php echo htmlspecialchars($selected_notice['id']); ?></h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?" enctype="multipart/form-data">
                                        <div class="card-body bg-white text-dark">
                                            <input type="hidden" name="notice_id"
                                                value="<?php echo htmlspecialchars($selected_notice['id']); ?>">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Notice Title</label>
                                                    <input type="text" name="title" class="form-control form-control-sm"
                                                        value="<?php echo htmlspecialchars($selected_notice['title']); ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Audience Scope</label>
                                                    <select name="audience" class="form-select form-select-sm" required>
                                                        <option value="All Students" <?php echo $selected_notice['audience'] === 'All Students' ? 'selected' : ''; ?>>All Students</option>
                                                        <option value="Students Only" <?php echo $selected_notice['audience'] === 'Students Only' ? 'selected' : ''; ?>>Students Only</option>
                                                        <option value="Faculty Only" <?php echo $selected_notice['audience'] === 'Faculty Only' ? 'selected' : ''; ?>>Faculty Only</option>
                                                        <option value="Staff Only" <?php echo $selected_notice['audience'] === 'Staff Only' ? 'selected' : ''; ?>>Staff Only</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Program Focus</label>
                                                    <select name="target_program" class="form-select form-select-sm">
                                                        <option value="General / Global" <?php echo $selected_notice['target_program'] === 'General / Global' ? 'selected' : ''; ?>>General / Global</option>
                                                        <option value="All Programs" <?php echo $selected_notice['target_program'] === 'All Programs' ? 'selected' : ''; ?>>All Programs</option>
                                                        <option value="BSIT" <?php echo $selected_notice['target_program'] === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                                                        <option value="BSCS" <?php echo $selected_notice['target_program'] === 'BSCS' ? 'selected' : ''; ?>>BSCS</option>
                                                        <option value="BSIS" <?php echo $selected_notice['target_program'] === 'BSIS' ? 'selected' : ''; ?>>BSIS</option>
                                                        <option value="BSCpE" <?php echo $selected_notice['target_program'] === 'BSCpE' ? 'selected' : ''; ?>>BSCpE</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Banner Attachment File</label>
                                                    <input type="file" name="image" class="form-control form-control-sm"
                                                        accept="image/*">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Notice Content Text</label>
                                                    <textarea name="content" class="form-control form-control-sm" rows="3"
                                                        required><?php echo htmlspecialchars($selected_notice['content']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                                            <a href="?delete_id=<?php echo urlencode($selected_notice['id']); ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this notice?');"><i
                                                    class="bi bi-trash-fill me-1"></i>Delete Notice</a>
                                            <div class="ms-auto">
                                                <a href="?" class="btn btn-sm btn-secondary me-2">Cancel</a>
                                                <button type="submit" name="update_notice"
                                                    class="btn btn-sm btn-primary">Save Modifications</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($add_mode): ?>
                            <div class="col-12">
                                <div class="card border-primary shadow-sm mb-4">
                                    <div
                                        class="card-header bg-primary-subtle text-primary-emphasis py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-megaphone-fill me-2"></i>Compose
                                            New Bulletin Announcement</h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?" enctype="multipart/form-data">
                                        <div class="card-body bg-white text-dark">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Notice Title</label>
                                                    <input type="text" name="title" class="form-control form-control-sm"
                                                        placeholder="Enter headline summary..." required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Audience Scope</label>
                                                    <select name="audience" class="form-select form-select-sm" required>
                                                        <option value="All Students">All Students</option>
                                                        <option value="Students Only">Students Only</option>
                                                        <option value="Faculty Only">Faculty Only</option>
                                                        <option value="Staff Only">Staff Only</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Program Focus</label>
                                                    <select name="target_program" class="form-select form-select-sm">
                                                        <option value="General / Global">General / Global</option>
                                                        <option value="All Programs">All Programs</option>
                                                        <option value="BSIT">BSIT</option>
                                                        <option value="BSCS">BSCS</option>
                                                        <option value="BSIS">BSIS</option>
                                                        <option value="BSCpE">BSCpE</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Banner Attachment File</label>
                                                    <input type="file" name="image" class="form-control form-control-sm"
                                                        accept="image/*">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Notice Content Text</label>
                                                    <textarea name="content" class="form-control form-control-sm" rows="3"
                                                        placeholder="Type announcement details here..." required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light d-flex justify-content-end py-2">
                                            <a href="?" class="btn btn-sm btn-secondary me-2">Cancel</a>
                                            <button type="submit" name="create_notice"
                                                class="btn btn-sm btn-primary">Publish Announcement</button>
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
                                            class="bi bi-list-task me-2 text-primary"></i>Active Notices Matrix</h5>
                                    <div class="card-tools">
                                        <form method="GET" action="" class="d-flex gap-2">
                                            <?php if (isset($_GET['edit_id'])): ?>
                                                <input type="hidden" name="edit_id"
                                                    value="<?php echo htmlspecialchars($_GET['edit_id']); ?>">
                                            <?php endif; ?>
                                            <div class="input-group input-group-sm" style="width: 16rem">
                                                <span class="input-group-text"><i class="bi bi-search"
                                                        aria-hidden="true"></i></span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control" placeholder="Search headline or program..."
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
                                                    <th class="ps-4">Image</th>
                                                    <th>Title / Announcement</th>
                                                    <th>Audience Scope</th>
                                                    <th>Program Focus</th>
                                                    <th>Date Posted</th>
                                                    <th class="pe-4 text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($notice_list as $notice): ?>
                                                    <tr
                                                        class="<?php echo ($edit_mode && $edit_id === $notice['id']) ? 'table-warning-subtle' : ''; ?>">
                                                        <td class="ps-4">
                                                            <?php if (!empty($notice['image'])): ?>
                                                                <div class="notice-thumb d-flex align-items-center justify-content-center border"
                                                                    title="Has Attachment">
                                                                    <i class="bi bi-image text-muted small"></i>
                                                                </div>
                                                            <?php else: ?>
                                                                <div
                                                                    class="notice-thumb d-flex align-items-center justify-content-center text-muted small border bg-light">
                                                                    <i class="bi bi-file-text"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark">
                                                                <?php echo htmlspecialchars($notice['title']); ?>
                                                            </div>
                                                            <div class="text-muted small text-truncate"
                                                                style="max-width: 400px;">
                                                                <?php echo htmlspecialchars($notice['content']); ?>
                                                            </div>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-primary-subtle text-primary fw-medium px-2 py-1"><?php echo htmlspecialchars($notice['audience']); ?></span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $is_global = ($notice['target_program'] === 'General / Global' || $notice['target_program'] === 'All Programs');
                                                            $badge_theme = $is_global ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-warning-subtle text-dark-emphasis';
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $badge_theme; ?> tab-indicator d-inline-block text-center"><?php echo htmlspecialchars($notice['target_program']); ?></span>
                                                        </td>
                                                        <td class="text-secondary small">
                                                            <?php echo htmlspecialchars($notice['date']); ?>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_id=<?php echo urlencode($notice['id']); ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('table-filter');
            const tableRows = document.querySelectorAll('table tbody tr');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    tableRows.forEach(row => {
                        const titleText = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        const programText = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

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