<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Programs Management</title>
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
    $program_list = [
        ['program_code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology', 'major' => 'Network & Web Systems', 'status' => 'Active'],
        ['program_code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science', 'major' => 'Core Software Engineering', 'status' => 'Active'],
        ['program_code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems', 'major' => 'Enterprise Resource Planning', 'status' => 'Active'],
        ['program_code' => 'BSCpE', 'name' => 'Bachelor of Science in Computer Engineering', 'major' => 'Embedded Systems Architecture', 'status' => 'Active'],
        ['program_code' => 'ACT', 'name' => 'Associate in Computer Technology', 'major' => 'Application Development Tier', 'status' => 'Archived']
    ];

    if (isset($_GET['delete_code'])) {
        $delete_code = $_GET['delete_code'];
        echo "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Record Deleted!</strong> Program code #" . ($delete_code) . " dropped from configuration mappings.</div>";
    }

    $edit_mode = false;
    $selected_program = null;

    if (isset($_GET['edit_code'])) {
        $edit_code = $_GET['edit_code'];
        foreach ($program_list as $program) {
            if ($program['program_code'] === $edit_code) {
                $selected_program = $program;
                $edit_mode = true;
                break;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_program'])) {
        echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Program record updated successfully! (Database sync mock triggered)</div>";
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
                            <a href="admin_student.php" class="nav-link ">
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
                            <a href="admin_programs.php" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-3 mt-3 fw-bold">Academic Programs Management</h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <button class="btn btn-primary shadow-sm fw-semibold"><i class="bi bi-plus-lg me-2"></i>Add
                                New Program</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-clipboard-data-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Total Programs</span>
                                    <h4 class="fw-bold mb-0">5</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">IT/CS Enrollees</span>
                                    <h4 class="fw-bold mb-0 text-success">842</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-person-badge-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Faculty Members</span>
                                    <h4 class="fw-bold mb-0 text-warning">28</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Status</span>
                                    <h4 class="fw-bold mb-0 text-info">Active</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">

                        <?php if ($edit_mode && $selected_program): ?>
                            <div class="col-12">
                                <div class="card border-warning shadow-sm mb-0">
                                    <div class="card-header bg-warning-subtle text-dark-emphasis py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fw-bold">
                                            <i class="bi bi-pencil-square me-2"></i>Edit Profile / Program:
                                            <?php echo ($selected_program['program_code']); ?>
                                        </h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?">
                                        <div class="card-body bg-white text-dark">
                                            <input type="hidden" name="program_code"
                                                value="<?php echo ($selected_program['program_code']); ?>">
                                            <div class="row g-3">
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Program Code</label>
                                                    <input type="text"
                                                        class="form-control form-control-sm font-monospace fw-bold"
                                                        value="<?php echo ($selected_program['program_code']); ?>" disabled>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Description / Program
                                                        Name</label>
                                                    <input type="text" name="name" class="form-control form-control-sm"
                                                        value="<?php echo ($selected_program['name']); ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Major / Track
                                                        Specialization</label>
                                                    <input type="text" name="major" class="form-control form-control-sm"
                                                        value="<?php echo ($selected_program['major']); ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Status</label>
                                                    <select name="status" class="form-select form-select-sm">
                                                        <option <?php echo $selected_program['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                        <option <?php echo $selected_program['status'] === 'Archived' ? 'selected' : ''; ?>>Archived</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                                            <a href="?delete_code=<?php echo urlencode($selected_program['program_code']); ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('CRITICAL ACCESSIBILITY ALERT:\n\nAre you sure you want to permanently delete this academic program architecture? Connected tracks and subject mappings will fall out of system indexing.');">
                                                <i class="bi bi-trash-fill me-1"></i>Delete Program
                                            </a>
                                            <div class="ms-auto">
                                                <a href="?" class="btn btn-sm btn-secondary me-2">Cancel</a>
                                                <button type="submit" name="update_program"
                                                    class="btn btn-sm btn-primary">Save Modifications</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-mortarboard-fill me-2 text-primary"></i>Active Program
                                        Catalogues</h5>
                                    <div class="card-tools">
                                        <form method="GET" action="" class="d-flex gap-2">
                                            <?php if (isset($_GET['edit_code'])): ?>
                                                <input type="hidden" name="edit_code"
                                                    value="<?php echo ($_GET['edit_code']); ?>">
                                            <?php endif; ?>
                                            <div class="input-group input-group-sm" style="width: 16rem">
                                                <span class="input-group-text"><i class="bi bi-search"
                                                        aria-hidden="true"></i></span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control" placeholder="Search program name or code..."
                                                    aria-label="Search rows"
                                                    value="<?php echo isset($_GET['search']) ? ($_GET['search']) : ''; ?>" />
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
                                                    <th class="ps-4" style="width: 15%;">Program Code</th>
                                                    <th style="width: 45%;">Description / Program Name</th>
                                                    <th style="width: 18%;">Major / Track</th>
                                                    <th class="text-center" style="width: 12%;">Status</th>
                                                    <th class="pe-4 text-end" style="width: 10%;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($program_list as $program): ?>
                                                    <tr
                                                        class="<?php echo ($edit_mode && $edit_code === $program['program_code']) ? 'table-warning-subtle fw-semibold' : ''; ?>">
                                                        <td class="ps-4 font-monospace fw-bold text-secondary">
                                                            <span
                                                                class="badge bg-primary-subtle text-primary tab-indicator"><?php echo ($program['program_code']); ?></span>
                                                        </td>
                                                        <td class="text-dark">
                                                            <?php echo ($program['name']); ?>
                                                        </td>
                                                        <td><span
                                                                class="text-muted small"><?php echo ($program['major']); ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $status = $program['status'];
                                                            $badge_color = ($status === 'Active') ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary-emphasis';
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $badge_color; ?> tab-indicator d-inline-block w-75 text-center"><?php echo ($status); ?></span>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_code=<?php echo urlencode($program['program_code']); ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
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
                                <div class="card-footer bg-white border-top py-3 text-center">
                                    <small class="text-muted font-semibold"><i class="bi bi-info-circle me-1"></i>
                                        Changes made here will instantly reconfigure tracking maps across active
                                        registration desks and user access matrices.</small>
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
                        const programCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                        const programName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        const majorTrack = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

                        if (programCode.includes(query) || programName.includes(query) || majorTrack.includes(query)) {
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