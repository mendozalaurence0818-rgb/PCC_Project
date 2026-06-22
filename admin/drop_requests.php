<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";
$current_semester = "1st Semester, AY 2026-2027";

if (isset($_GET['approve_id'])) {
    $request_id = intval($_GET['approve_id']);
    try {
        $conn->beginTransaction();

        $get_req = $conn->prepare("SELECT student_id, enrollment_id FROM drop_requests WHERE id = :id LIMIT 1");
        $get_req->execute([':id' => $request_id]);
        $req_data = $get_req->fetch(PDO::FETCH_ASSOC);

        if ($req_data) {
            $upd_enroll = $conn->prepare("UPDATE enrollments SET remarks = 'Dropped' WHERE enrollment_id = :eid");
            $upd_enroll->execute([':eid' => $req_data['enrollment_id']]);

            $upd_req = $conn->prepare("UPDATE drop_requests SET status = 'Approved' WHERE id = :id");
            $upd_req->execute([':id' => $request_id]);

            $conn->commit();
            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle me-2'></i> Subject request marked as officially dropped.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
        }
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

if (isset($_GET['reject_id'])) {
    $request_id = intval($_GET['reject_id']);
    try {
        $conn->beginTransaction();

        $get_req = $conn->prepare("SELECT enrollment_id FROM drop_requests WHERE id = :id LIMIT 1");
        $get_req->execute([':id' => $request_id]);
        $eid = $get_req->fetchColumn();

        if ($eid) {
            $upd_enroll = $conn->prepare("UPDATE enrollments SET remarks = NULL WHERE enrollment_id = :eid");
            $upd_enroll->execute([':eid' => $eid]);
        }

        $upd_req = $conn->prepare("UPDATE drop_requests SET status = 'Rejected' WHERE id = :id");
        $upd_req->execute([':id' => $request_id]);

        $conn->commit();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-warning text-dark border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-arrow-counterclockwise me-2'></i> Dropping application turned down cleanly.</div><button type='button' class='btn-close m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

try {
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();

    $drop_queue = $conn->query("
        SELECT dr.id as req_id, dr.reason, DATE(dr.created_at) as date_filed,
               st.student_number, st.first_name, st.last_name, st.current_course,
               sub.subject_code, sub.descriptive_title, sub.units
        FROM drop_requests dr
        JOIN students st ON dr.student_id = st.student_id
        JOIN enrollments e ON dr.enrollment_id = e.enrollment_id
        JOIN subjects sub ON e.subject_id = sub.id
        WHERE dr.status = 'Pending Review'
        ORDER BY dr.created_at ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $drop_queue = [];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Subject Drop Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="icon" href="../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --pcc-blue: #002c5e; --pcc-gold: #f1b813; --pcc-dark-blue: #001d3d; }
        body { background-color: #f4f6f9 !important; }
        .sidebar-bg { background-color: var(--pcc-blue) !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: var(--pcc-blue) !important; background-color: var(--pcc-gold) !important; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: var(--pcc-dark-blue); }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .nav-date { font-weight: 600; color: var(--pcc-blue); }
        .toast {
            border-radius: 8px !important;
        }
        .modal-clean-layout { border-radius: 8px; border: none; box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12) !important; }
        .modal-backdrop.show { background-color: #ffffff !important; opacity: 0.75 !important; filter: blur(4px); }
        .sidebar-semester-text {
            color: #adb5bd;
            font-size: 11px;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }
        
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <?php echo $toast_notification; ?>
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body px-1">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)" role="button"><i class="bi bi-list fs-5"></i></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu"><span class="d-md-inline"><div class="nav-date" id="liveClockDisplay">Loading Time...</div></span></li>
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
                        <li class="nav-item"><a href="subjects.php" class="nav-link "><i
                                    class="nav-icon bi bi-clipboard2-minus-fill"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="drop_requests.php" class="nav-link sidebar-bg-active"><i
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
                <div class="container-fluid">
                    <h3 class="mb-0 mt-3 fw-bold text-dark">Subject Drop Applications</h3>
                    <p class="text-muted small">Evaluate and approve course drop processing tokens submitted by active term portal users.</p>
                </div>
            </div>

            <div class="app-content mt-2">
                <div class="container-fluid">
                    <div class="card border border-light-subtle shadow-sm mb-4 bg-white" style="border-radius: 10px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-warning me-2"></i>Pending Drop Queue</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-dark">
                                    <thead class="table-light small text-uppercase text-secondary">
                                        <tr>
                                            <th class="ps-4">Student Info</th>
                                            <th>Target Subject</th>
                                            <th>Date Filed</th>
                                            <th class="text-end pe-4" style="width: 200px;">Review Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($drop_queue)): ?>
                                            <tr><td colspan="4" class="text-center text-muted py-5 small"><i class="bi bi-folder-check text-success display-6 mb-2 d-block"></i>No pending dropping applications found.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($drop_queue as $row): ?>
                                                <tr>
                                                    <td class="ps-4 py-3">
                                                        <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?></div>
                                                        <span class="font-monospace text-secondary text-xs"><?= htmlspecialchars($row['student_number'] . " | " . $row['current_course']) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-danger mb-0"><?= htmlspecialchars($row['subject_code']) ?></div>
                                                        <span class="text-muted text-xs"><?= htmlspecialchars($row['descriptive_title']) ?> (<?= number_format($row['units'], 1) ?> Units)</span>
                                                    </td>
                                                    <td class="font-monospace small text-muted"><?= date('M d, Y', strtotime($row['date_filed'])) ?></td>
                                                    <td class="pe-4 text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3" 
                                                                onclick="triggerDropInspectorModal(
                                                                    '<?= $row['req_id'] ?>', 
                                                                    '<?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?>', 
                                                                    '<?= htmlspecialchars($row['subject_code'] . ' - ' . $row['descriptive_title']) ?>', 
                                                                    '<?= urlencode($row['reason']) ?>'
                                                                )">
                                                            <i class="bi bi-eye-fill me-1"></i> View Details
                                                        </button>
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

    <div class="modal fade" id="dropInspectorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
            <div class="modal-content modal-clean-layout text-dark">
                <div class="modal-header py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center"><i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i> Audit Drop Application</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-uppercase text-muted font-bold m-0" style="font-size: 11px;">Student Name</label>
                        <div class="fw-bold text-dark fs-6" id="lblModalStudentName">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-uppercase text-muted font-bold m-0" style="font-size: 11px;">Subject Requested</label>
                        <div class="fw-semibold text-danger" id="lblModalSubjectName">-</div>
                    </div>
                    <div class="bg-light p-3 border border-light-subtle rounded mb-4">
                        <label class="form-label text-uppercase font-bold text-muted mb-1" style="font-size: 11px;">Stated Reason</label>
                        <div class="text-dark small bg-white border p-2.5 rounded text-start lh-base" id="lblModalDropReason" style="min-height: 60px;">-</div>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-secondary fw-semibold px-3" data-bs-dismiss="modal">Close</button>
                        <a href="" id="btnModalReject" class="btn btn-sm btn-danger fw-bold text-white px-3" onclick="return confirm('Reject request?');">Deny</a>
                        <a href="" id="btnModalAccept" class="btn btn-sm btn-success fw-bold text-white px-3">Approve Drop</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }
        function triggerDropInspectorModal(reqId, name, subject, rawReason) {
            document.getElementById('lblModalStudentName').textContent = name;
            document.getElementById('lblModalSubjectName').textContent = subject;
            document.getElementById('lblModalDropReason').textContent = decodeURIComponent(rawReason.replace(/\+/g, ' '));
            
            document.getElementById('btnModalAccept').href = `?approve_id=${reqId}`;
            document.getElementById('btnModalReject').href = `?reject_id=${reqId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('dropInspectorModal'));
            modal.show();
        }
        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }
        document.addEventListener("DOMContentLoaded", function () { runLiveDashboardClock(); setInterval(runLiveDashboardClock, 1000); });
    </script>
</body>
</html>