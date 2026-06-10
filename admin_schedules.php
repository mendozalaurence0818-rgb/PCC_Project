<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Schedules Management</title>
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

        .status-pill {
            font-weight: 600;
            padding: 6px 16px !important;
            border-radius: 20px !important;
            font-size: 0.82rem;
            display: inline-block;
            width: 100px;
            text-align: center;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php
    $schedule_list = [
        ['id' => 'SCH-101', 'subject' => 'IT211 - Integrative Programming', 'section' => 'BSIT-201', 'room' => 'Computer Laboratory 1', 'day' => 'Mon / Wed', 'time' => '09:00 AM - 11:30 AM', 'instructor' => 'Prof. M. Racho', 'status' => 'Confirmed'],
        ['id' => 'SCH-102', 'subject' => 'CS312 - Automata Theory', 'section' => 'BSCS-301', 'room' => 'Room 302', 'day' => 'Tue / Thu', 'time' => '01:00 PM - 03:00 PM', 'instructor' => 'Dr. J. Villarta', 'status' => 'Confirmed'],
        ['id' => 'SCH-103', 'subject' => 'IT224 - Database Systems', 'section' => 'BSIT-202', 'room' => 'Computer Laboratory 2', 'day' => 'Friday', 'time' => '07:30 AM - 11:30 AM', 'instructor' => 'Prof. R. Depollo', 'status' => 'Pending'],
        ['id' => 'SCH-104', 'subject' => 'GEC101 - Understanding the Self', 'section' => 'BSIS-101', 'room' => 'Room 405', 'day' => 'Mon / Wed', 'time' => '04:00 PM - 05:30 PM', 'instructor' => 'Prof. H. Francisco', 'status' => 'Confirmed']
    ];

    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        echo "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Schedule Removed</strong> Slot entry #" . htmlspecialchars($delete_id) . " was successfully deleted.</div>";
    }

    $edit_mode = false;
    $selected_schedule = null;

    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        foreach ($schedule_list as $sched) {
            if ($sched['id'] === $edit_id) {
                $selected_schedule = $sched;
                $edit_mode = true;
                break;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
        echo "<div class='alert alert-success position-fixed bottom-0 end-0 m-3 z-3 shadow'>Schedule entry updated successfully!</div>";
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
                <a href="admin_dashboard.php" class="brand-link">
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
                        <li class="nav-item"><a href="admin_dashboard.php" class="nav-link"><i
                                    class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_student.php" class="nav-link"><i
                                    class="nav-icon bi bi-people-fill"></i>
                                <p>Students <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_admissions.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-fill"></i>
                                <p>Admissions <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_programs.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard-data-fill"></i>
                                <p>Programs <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_subjects.php" class="nav-link"><i
                                    class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_schedules.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-calendar3"></i>
                                <p>Schedules <i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a></li>

                        <li class="nav-header">OTHERS</li>
                        <li class="nav-item"><a href="admin_notice.php" class="nav-link"><i
                                    class="nav-icon bi bi-exclamation-circle-fill"></i>
                                <p>Notice <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_users.php" class="nav-link"><i
                                    class="nav-icon bi bi-person-check-fill"></i>
                                <p>Users <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
                        <li class="nav-item"><a href="admin_settings.php" class="nav-link"><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings <i class="nav-arrow bi bi-chevron-left"></i></p>
                            </a></li>
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
                            <h3 class="mb-3 mt-3 fw-bold">Class Schedules</h3>
                        </div>
                        <div class="col-sm-6 text-end"><button class="btn btn-primary shadow-sm fw-semibold"><i
                                    class="bi bi-calendar-plus me-2"></i>Add New Entry</button></div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-calendar3"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block">Total Schedules</span>
                                    <h4 class="fw-bold mb-0">36 Slots</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-building-check"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block">Room Allocations</span>
                                    <h4 class="fw-bold mb-0 text-success">12 Active</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-person-badge-fill"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block">Total Instructors</span>
                                    <h4 class="fw-bold mb-0 text-warning">18 Assigned</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span
                                    class="info-box-icon bg-info text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 50px; height: 50px; font-size: 22px;"><i
                                        class="bi bi-calendar-check"></i></span>
                                <div class="info-box-content ms-3"><span
                                        class="text-muted small text-uppercase d-block">School Year</span>
                                    <h4 class="fw-bold mb-0 text-info">2026 - 2027</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php if ($edit_mode && $selected_schedule): ?>
                            <div class="col-12">
                                <div class="card border-warning shadow-sm mb-4">
                                    <div
                                        class="card-header bg-warning-subtle text-dark-emphasis py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit
                                            Schedule [<?php echo htmlspecialchars($selected_schedule['id']); ?>]</h5>
                                        <a href="?" class="btn-close" aria-label="Close"></a>
                                    </div>
                                    <form method="POST" action="?">
                                        <div class="card-body bg-white text-dark">
                                            <input type="hidden" name="schedule_id"
                                                value="<?php echo htmlspecialchars($selected_schedule['id']); ?>">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Subject</label>
                                                    <input type="text" name="subject" class="form-control form-control-sm"
                                                        value="<?php echo htmlspecialchars($selected_schedule['subject']); ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Section</label>
                                                    <select name="section" class="form-select form-select-sm" required>
                                                        <option value="BSIT-201" <?php echo $selected_schedule['section'] === 'BSIT-201' ? 'selected' : ''; ?>>BSIT-201</option>
                                                        <option value="BSIT-202" <?php echo $selected_schedule['section'] === 'BSIT-202' ? 'selected' : ''; ?>>BSIT-202</option>
                                                        <option value="BSCS-301" <?php echo $selected_schedule['section'] === 'BSCS-301' ? 'selected' : ''; ?>>BSCS-301</option>
                                                        <option value="BSIS-101" <?php echo $selected_schedule['section'] === 'BSIS-101' ? 'selected' : ''; ?>>BSIS-101</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Room / Location</label>
                                                    <select name="room" class="form-select form-select-sm" required>
                                                        <option value="Computer Laboratory 1" <?php echo $selected_schedule['room'] === 'Computer Laboratory 1' ? 'selected' : ''; ?>>Computer Laboratory 1</option>
                                                        <option value="Computer Laboratory 2" <?php echo $selected_schedule['room'] === 'Computer Laboratory 2' ? 'selected' : ''; ?>>Computer Laboratory 2</option>
                                                        <option value="Room 302" <?php echo $selected_schedule['room'] === 'Room 302' ? 'selected' : ''; ?>>
                                                            Room 302</option>
                                                        <option value="Room 405" <?php echo $selected_schedule['room'] === 'Room 405' ? 'selected' : ''; ?>>
                                                            Room 405</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Days</label>
                                                    <select name="day" class="form-select form-select-sm" required>
                                                        <option value="Mon / Wed" <?php echo $selected_schedule['day'] === 'Mon / Wed' ? 'selected' : ''; ?>>
                                                            Mon / Wed</option>
                                                        <option value="Tue / Thu" <?php echo $selected_schedule['day'] === 'Tue / Thu' ? 'selected' : ''; ?>>
                                                            Tue / Thu</option>
                                                        <option value="Friday" <?php echo $selected_schedule['day'] === 'Friday' ? 'selected' : ''; ?>>
                                                            Friday</option>
                                                        <option value="Saturday" <?php echo $selected_schedule['day'] === 'Saturday' ? 'selected' : ''; ?>>
                                                            Saturday</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Time Window</label>
                                                    <input type="text" name="time" class="form-control form-control-sm"
                                                        value="<?php echo htmlspecialchars($selected_schedule['time']); ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <label class="form-label small fw-bold">Instructor</label>
                                                    <select name="instructor" class="form-select form-select-sm" required>
                                                        <option value="Prof. M. Racho" <?php echo $selected_schedule['instructor'] === 'Prof. M. Racho' ? 'selected' : ''; ?>>Prof. M. Racho</option>
                                                        <option value="Dr. J. Villarta" <?php echo $selected_schedule['instructor'] === 'Dr. J. Villarta' ? 'selected' : ''; ?>>Dr. J. Villarta</option>
                                                        <option value="Prof. R. Depollo" <?php echo $selected_schedule['instructor'] === 'Prof. R. Depollo' ? 'selected' : ''; ?>>Prof. R. Depollo</option>
                                                        <option value="Prof. H. Francisco" <?php echo $selected_schedule['instructor'] === 'Prof. H. Francisco' ? 'selected' : ''; ?>>Prof. H. Francisco</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mt-2">
                                                    <label class="form-label small fw-bold">Status</label>
                                                    <select name="status" class="form-select form-select-sm">
                                                        <option <?php echo $selected_schedule['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option <?php echo $selected_schedule['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                                            <a href="?delete_id=<?php echo urlencode($selected_schedule['id']); ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this schedule entry?');">
                                                <i class="bi bi-trash3-fill me-1"></i>Delete Entry
                                            </a>
                                            <div class="ms-auto">
                                                <a href="?" class="btn btn-sm btn-secondary me-2">Cancel</a>
                                                <button type="submit" name="update_schedule"
                                                    class="btn btn-sm btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0 fw-bold text-dark"><i
                                            class="bi bi-calendar3 me-2 text-primary"></i>Schedules List</h5>
                                    <div class="card-tools">
                                        <form method="GET" action="" class="d-flex gap-2">
                                            <?php if (isset($_GET['edit_id'])): ?>
                                                <input type="hidden" name="edit_id"
                                                    value="<?php echo htmlspecialchars($_GET['edit_id']); ?>">
                                            <?php endif; ?>
                                            <div class="input-group input-group-sm" style="width: 16rem">
                                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                <input id="table-filter" type="search" name="search"
                                                    class="form-control"
                                                    placeholder="Search section, room, or instructor..."
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
                                                    <th class="ps-4" style="width: 10%;">ID</th>
                                                    <th style="width: 23%;">Subject</th>
                                                    <th style="width: 12%;">Section</th>
                                                    <th style="width: 18%;">Room</th>
                                                    <th style="width: 12%;">Day & Time</th>
                                                    <th style="width: 8%;">Instructor</th>
                                                    <th class="text-center" style="width: 6%;">Status</th>
                                                    <th class="pe-4 text-center" style="width: 22%;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($schedule_list as $sched): ?>
                                                    <tr
                                                        class="<?php echo ($edit_mode && $edit_id === $sched['id']) ? 'table-warning-subtle fw-semibold' : ''; ?>">
                                                        <td class="ps-4 font-monospace text-secondary small">
                                                            <?php echo htmlspecialchars($sched['id']); ?>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark">
                                                                <?php echo htmlspecialchars($sched['subject']); ?>
                                                            </div>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-primary-subtle text-primary tab-indicator"><?php echo htmlspecialchars($sched['section']); ?></span>
                                                        </td>
                                                        <td><span class="text-dark small"><i
                                                                    class="bi bi-geo-alt-fill me-1 text-muted"></i><?php echo htmlspecialchars($sched['room']); ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="text-dark small fw-semibold">
                                                                <?php echo htmlspecialchars($sched['day']); ?>
                                                            </div>
                                                            <div class="text-muted small" style="font-size: 0.75rem;"><i
                                                                    class="bi bi-clock me-1"></i><?php echo htmlspecialchars($sched['time']); ?>
                                                            </div>
                                                        </td>
                                                        <td><span
                                                                class="text-secondary small"><?php echo htmlspecialchars($sched['instructor']); ?></span>
                                                        </td>
                                                        <td class="text-center py-1">
                                                            <?php
                                                            $status = $sched['status'];
                                                            $badge_color = ($status === 'Confirmed') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis';
                                                            ?>
                                                            <span
                                                                class="status-pill <?php echo $badge_color; ?>"><?php echo htmlspecialchars($status); ?></span>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <a href="?edit_id=<?php echo urlencode($sched['id']); ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                                                                class="btn btn-xs btn-outline-primary border py-1 px-2"
                                                                style="font-size: 0.75rem;"><i
                                                                    class="bi bi-pencil-square me-1"></i>Edit /
                                                                Manage
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
                                        Updates made here apply immediately to student and faculty dashboard
                                        views.</small>
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
                        const subject = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        const section = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const room = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                        const instructor = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                        if (subject.includes(query) || section.includes(query) || room.includes(query) || instructor.includes(query)) {
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