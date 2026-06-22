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

if (isset($_GET['approve_uid']) && isset($_GET['sec'])) {
    $target_student_id = intval($_GET['approve_uid']);
    $assigned_section = trim($_GET['sec']);

    try {
        $conn->beginTransaction();

        $settings_stmt = $conn->query("SELECT school_year, semester FROM system_settings LIMIT 1");
        $settings_data = $settings_stmt->fetch(PDO::FETCH_ASSOC);
        $active_sy = $settings_data['school_year'] ?? '2026 - 2027';
        $active_sem = $settings_data['semester'] ?? '1st Semester';

        $sub_stmt = $conn->prepare("
            SELECT ss.subject_id 
            FROM section_subjects ss
            JOIN sections s ON ss.section_id = s.id
            WHERE s.section_name = :sec_name
        ");
        $sub_stmt->execute([':sec_name' => $assigned_section]);
        $subject_ids = $sub_stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($subject_ids)) {
            $clear_stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = :sid AND school_year = :sy AND semester = :sem");
            $clear_stmt->execute([':sid' => $target_student_id, ':sy' => $active_sy, ':sem' => $active_sem]);

            $ins_stmt = $conn->prepare("
                INSERT INTO enrollments (student_id, subject_id, school_year, semester) 
                VALUES (:sid, :subid, :sy, :sem)
            ");

            foreach ($subject_ids as $sub_id) {
                $ins_stmt->execute([
                    ':sid' => $target_student_id,
                    ':subid' => intval($sub_id),
                    ':sy' => $active_sy,
                    ':sem' => $active_sem
                ]);
            }

            $upd_student = $conn->prepare("UPDATE students SET enrollment_status = 'Enrolled' WHERE student_id = :sid");
            $upd_student->execute([':sid' => $target_student_id]);

            $log_msg = "Formally confirmed enrollment for Student ID Token #{$target_student_id} into formal section block group row {$assigned_section}.";
            $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'SCHEDULES', :msg)");
            $log_stmt->execute([':admin_id' => $_SESSION['admin_id'], ':msg' => $log_msg]);

            $conn->commit();
            $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-success text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle me-2'></i> Compiled into formal enrollments successfully.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
        } else {
            throw new Exception("Configuration mismatch: Requested block container targets zero subjects.");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-danger text-white border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'>Fault: " . htmlspecialchars($e->getMessage()) . "</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    }
}

if (isset($_GET['reject_uid'])) {
    $reject_student_id = intval($_GET['reject_uid']);
    try {
        $conn->beginTransaction();
        $reject_stmt = $conn->prepare("UPDATE students SET section = NULL, enrollment_status = 'Not Enrolled' WHERE student_id = :sid");
        $reject_stmt->execute([':sid' => $reject_student_id]);

        $log_msg = "Rejected load configuration setup variables for student record context entry key #{$reject_student_id}. Session dropped back.";
        $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, module_tab, custom_message) VALUES (:admin_id, 'SCHEDULES', :msg)");
        $log_stmt->execute([':admin_id' => $_SESSION['admin_id'], ':msg' => $log_msg]);

        $conn->commit();
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show bg-warning text-dark border-0 shadow' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-arrow-counterclockwise me-2'></i> Enrollment mapping rejected and reverted back to student dashboard control.</div><button type='button' class='btn-close m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

try {
    $new_admissions = $conn->query("SELECT COUNT(*) FROM applicants WHERE application_status = 'Pending'")->fetchColumn();

    $pending_students = $conn->query("
        SELECT student_id, student_number, first_name, last_name, current_course, year_level, section, classification,
               payment_method, payment_scheme, gcash_ref_id, gcash_receipt_path 
        FROM students 
        WHERE enrollment_status = 'Pending Approval' AND section IS NOT NULL AND section != ''
        ORDER BY last_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pending_students = [];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Enrollment Verification Hub</title>
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

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .toast {
            border-radius: 8px !important;
        }

        .sidebar-semester-text {
            color: #adb5bd;
            font-size: 11px;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }

        .modal-clean-layout {
            border-radius: 8px;
            border: none;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12) !important;
        }

        .modal-clean-layout .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            color: #212529;
        }

        .modal-clean-layout .modal-body {
            background-color: #ffffff;
            color: #212529;
        }

        .image-preview-frame {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .modal-backdrop.show {
            background-color: #ffffff !important;
            opacity: 0.75 !important;
            filter: blur(4px);
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
                            <div class="nav-date" id="liveClockDisplay">Loading Time Parameters...</div>
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
                        <li class="nav-item"><a href="verify_enrollment.php" class="nav-link sidebar-bg-active"><i
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
                <div class="container-fluid">
                    <h3 class="mb-0 mt-3 fw-bold text-dark">Enrollment Verification</h3>
                </div>
            </div>

            <div class="app-content mt-2">
                <div class="container-fluid">
                    <div class="card border border-light-subtle shadow-sm mb-4 bg-white" style="border-radius: 10px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bi bi-hourglass-split text-warning me-2"></i>Pending Verification Queue</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-dark">
                                    <thead class="table-light small text-uppercase text-secondary">
                                        <tr>
                                            <th class="ps-4">Student Info</th>
                                            <th>Academic Track</th>
                                            <th>Staged Section</th>
                                            <th>Payment Method</th>
                                            <th>Payment Scheme</th>
                                            <th class="text-end pe-4" style="width: 240px;">Roster Verification</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pending_students)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-5 small">
                                                    <i class="bi bi-shield-check text-success display-6 mb-2 d-block"></i>
                                                    No pending course loads found waiting for system authentication checks.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($pending_students as $row): ?>
                                                <?php
                                                $payment_method = !empty($row['payment_method']) ? $row['payment_method'] : 'Cashier';
                                                $payment_scheme = !empty($row['payment_scheme']) ? $row['payment_scheme'] : 'Full Payment';
                                                $ref_id = !empty($row['gcash_ref_id']) ? $row['gcash_ref_id'] : 'N/A';
                                                $receipt_path = !empty($row['gcash_receipt_path']) ? $row['gcash_receipt_path'] : '';

                                                if ($payment_method === 'GCash') {
                                                    $badge_theme = 'bg-info-subtle text-info border border-info-subtle';
                                                } elseif ($payment_method === 'Bank Transfer') {
                                                    $badge_theme = 'bg-primary-subtle text-primary border border-primary-subtle';
                                                } else {
                                                    $badge_theme = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="ps-4 py-3">
                                                        <div class="fw-bold text-dark mb-0">
                                                            <?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?>
                                                        </div>
                                                        <span
                                                            class="font-monospace text-secondary text-xs"><?= htmlspecialchars($row['student_number']) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-secondary mb-0">
                                                            <?= htmlspecialchars($row['current_course']) ?>
                                                        </div>
                                                        <span
                                                            class="text-muted text-xs"><?= htmlspecialchars($row['year_level']) ?>
                                                            Year Level</span>
                                                    </td>
                                                    <td><span
                                                            class="badge bg-primary-subtle text-primary font-monospace fs-7 border border-primary-subtle px-2 py-1"><?= htmlspecialchars($row['section']) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $badge_theme ?> px-2 py-1 fw-bold">
                                                            <?= htmlspecialchars($payment_method) ?>
                                                        </span>
                                                    </td>
                                                    <td><span
                                                            class="text-dark small fw-medium"><?= htmlspecialchars($payment_scheme) ?></span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary fw-bold px-3" onclick="triggerAssetInspectorModal(
                                                                    '<?= $row['student_id'] ?>', 
                                                                    '<?= urlencode($row['section']) ?>', 
                                                                    '<?= htmlspecialchars($payment_method) ?>', 
                                                                    '<?= htmlspecialchars($ref_id) ?>', 
                                                                    '<?= htmlspecialchars($receipt_path) ?>'
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

    <div class="modal fade" id="receiptInspectorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
            <div class="modal-content modal-clean-layout text-dark">
                <div class="modal-header py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center" id="modalHeaderTitleText">
                    </h6>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">

                    <div id="modalReceiptAssetWrapper" class="d-none">
                        <div class="image-preview-frame mb-3 shadow-sm bg-light">
                            <a href="" id="lnkNativeReceiptViewer" target="_blank"
                                title="Click to view full image resolution">
                                <img src="" id="imgReceiptElementView" class="img-fluid border border-light shadow-2xs"
                                    style="max-height: 380px; object-fit: contain; cursor: zoom-in;"
                                    alt="Transaction Image File">
                            </a>
                        </div>

                        <div class="bg-light p-3 border border-light-subtle rounded-1 mb-4">
                            <label class="form-label text-uppercase font-bold text-muted mb-1" id="modalRefLabelText"
                                style="font-size: 11px; letter-spacing: 0.5px;">Reference ID</label>
                            <div class="font-monospace text-dark fw-bold bg-white border px-3 py-2 select-all text-center rounded"
                                id="lblGcashRefStrField" style="font-size:15px; letter-spacing:0.3px;">-</div>
                        </div>
                    </div>

                    <div id="modalOtcNoticeWrapper"
                        class="d-none alert alert-info border-0 p-3 mb-4 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                        <span class="small fw-medium"><strong>OTC Counter Strategy:</strong> This student has requested
                            to process their financial steps directly over-the-counter through the physical campus
                            cashier box terminal.</span>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-secondary fw-semibold px-3"
                            data-bs-dismiss="modal">Close</button>
                        <a href="" id="btnModalActionReject" class="btn btn-sm btn-danger fw-bold text-white px-3"
                            onclick="return confirm('Completely reject this load selection request?');"><i
                                class="bi bi-x-circle-fill me-1"></i> Reject Enrollment</a>
                        <a href="" id="btnModalActionAccept" class="btn btn-sm btn-success fw-bold text-white px-3"><i
                                class="bi bi-check-circle-fill me-1"></i> Accept Enrollment</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }

        function triggerAssetInspectorModal(studentId, sectionCode, method, referenceId, imagePath) {
            const headerTitle = document.getElementById('modalHeaderTitleText');
            const refLabel = document.getElementById('modalRefLabelText');
            const assetWrapper = document.getElementById('modalReceiptAssetWrapper');
            const otcWrapper = document.getElementById('modalOtcNoticeWrapper');

            document.getElementById('btnModalActionAccept').href = `?approve_uid=${studentId}&sec=${sectionCode}`;
            document.getElementById('btnModalActionReject').href = `?reject_uid=${studentId}`;

            if (method === 'GCash' || method === 'Bank Transfer') {
                otcWrapper.classList.add('d-none');
                assetWrapper.classList.remove('d-none');
                document.getElementById('lblGcashRefStrField').textContent = referenceId;

                if (method === 'GCash') {
                    headerTitle.innerHTML = '<i class="bi bi-shield-check text-info fs-5 me-2"></i> GCash Receipt';
                    refLabel.textContent = "GCash Reference ID String Number";
                } else {
                    headerTitle.innerHTML = '<i class="bi bi-bank2 text-primary fs-5 me-2"></i> Bank Transfer Receipt';
                    refLabel.textContent = "Bank Transfer Reference Validation Track";
                }

                const completePath = "../" + imagePath;
                document.getElementById('imgReceiptElementView').src = completePath;
                document.getElementById('lnkNativeReceiptViewer').href = completePath;
            } else {
                assetWrapper.classList.add('d-none');
                otcWrapper.classList.remove('d-none');
                headerTitle.innerHTML = '<i class="bi bi-cash-coin text-secondary fs-5 me-2"></i> Over-The-Counter Roster Check';
            }

            const modal = new bootstrap.Modal(document.getElementById('receiptInspectorModal'));
            modal.show();
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
        });
    </script>
</body>

</html>