<?php
session_start();

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true || !isset($_SESSION['student_number'])) {
    header("Location: old_student_login.php");
    exit();
}

require_once '../../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$student_number = $_SESSION['student_number'];
$student_id = $_SESSION['student_id'] ?? 0;

$toast_notification = "";

$is_admission_closed = false;
try {
    $admission_check = $conn->query("SELECT LOWER(TRIM(enrollment_status)) FROM system_settings LIMIT 1");
    $admission_state = $admission_check->fetchColumn();
    
    if ($admission_state === 'closed') {
        $is_admission_closed = true;
    }
} catch (PDOException $e) {
    $is_admission_closed = true;
    error_log("Dropping access gate reading error: " . $e->getMessage());
}

$student_data = null;
$applicant_data = null;
$app_id = null;

try {
    $s_stmt = $conn->prepare("SELECT * FROM students WHERE student_number = :sn LIMIT 1");
    $s_stmt->execute([':sn' => $student_number]);
    $student_data = $s_stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_data) {
        $student_id = (int) $student_data['student_id'];
        $_SESSION['student_id'] = $student_id;
        $app_id = $student_data['application_id'];

        if (empty($app_id)) {
            $find_app = $conn->prepare("SELECT application_id FROM applicants WHERE student_number = :sn LIMIT 1");
            $find_app->execute([':sn' => $student_number]);
            $app_id = $find_app->fetchColumn();

            if (empty($app_id)) {
                $find_name = $conn->prepare("SELECT application_id FROM applicants WHERE LOWER(first_name) = LOWER(:fn) AND LOWER(last_name) = LOWER(:ln) LIMIT 1");
                $find_name->execute([
                    ':fn' => $student_data['first_name'],
                    ':ln' => $student_data['last_name']
                ]);
                $app_id = $find_name->fetchColumn();
            }

            if (!empty($app_id)) {
                $heal_stmt = $conn->prepare("UPDATE students SET application_id = :aid WHERE student_id = :sid");
                $heal_stmt->execute([':aid' => $app_id, ':sid' => $student_id]);
            }
        }

        if (!empty($app_id)) {
            $a_stmt = $conn->prepare("SELECT * FROM applicants WHERE application_id = :aid LIMIT 1");
            $a_stmt->execute([':aid' => $app_id]);
            $applicant_data = $a_stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    error_log("Profile Fetch Failure: " . $e->getMessage());
}

$current_school_year = '2026 - 2027';
$current_semester = '1st Semester';
try {
    $settings_stmt = $conn->query("SELECT school_year, semester FROM system_settings LIMIT 1");
    $settings_data = $settings_stmt->fetch(PDO::FETCH_ASSOC);
    if ($settings_data) {
        $current_school_year = $settings_data['school_year'];
        $current_semester = $settings_data['semester'];
    }
} catch (PDOException $e) {
}

$display_semester_year = $current_semester . ", AY " . $current_school_year;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_submit_drop'])) {
    $target_enrollment_id = isset($_POST['target_enrollment_id']) ? intval($_POST['target_enrollment_id']) : 0;
    $drop_reason = isset($_POST['drop_reason']) ? trim($_POST['drop_reason']) : '';

    if ($is_admission_closed) {
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show align-items-center text-white bg-danger border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-shield-lock-fill me-2'></i> Action Blocked: Subject dropping period is currently closed.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } elseif ($target_enrollment_id <= 0 || empty($drop_reason)) {
        $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show align-items-center text-white bg-danger border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-exclamation-triangle-fill me-2'></i> Please select a subject and provide a valid reason.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
    } else {
        try {
            $conn->beginTransaction();

            $dup_stmt = $conn->prepare("SELECT id FROM drop_requests WHERE enrollment_id = :eid AND status = 'Pending Review' LIMIT 1");
            $dup_stmt->execute([':eid' => $target_enrollment_id]);

            if ($dup_stmt->fetch()) {
                $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show align-items-center text-white bg-warning border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-exclamation-circle-fill me-2'></i> You already have a pending drop request for this subject.</div><button type='button' class='btn-close m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
                $conn->rollBack();
            } else {
                $ins_req = $conn->prepare("INSERT INTO drop_requests (student_id, enrollment_id, reason) VALUES (:sid, :eid, :reason)");
                $ins_req->execute([
                    ':sid' => $student_id,
                    ':eid' => $target_enrollment_id,
                    ':reason' => $drop_reason
                ]);

                $conn->commit();
                $toast_notification = "<div class='toast-container position-fixed bottom-0 end-0 p-3 z-3'><div class='toast show align-items-center text-white bg-success border-0 shadow rounded-3' role='alert'><div class='d-flex'><div class='toast-body'><i class='bi bi-check-circle-fill me-2'></i> Drop request submitted successfully to Admin Workspace.</div><button type='button' class='btn-close btn-close-white m-auto me-2' data-bs-dismiss='toast'></button></div></div></div>";
            }
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Drop Processing Error: " . $e->getMessage());
        }
    }
}

$eligible_subjects = [];
if (!$is_admission_closed) {
    try {
        $sub_stmt = $conn->prepare("
            SELECT e.enrollment_id, s.subject_code, s.descriptive_title 
            FROM enrollments e
            JOIN subjects s ON e.subject_id = s.id
            WHERE e.student_id = :sid 
              AND e.school_year = :sy 
              AND e.semester = :sem 
              AND e.enrollment_id NOT IN (SELECT enrollment_id FROM drop_requests WHERE status = 'Approved')
            ORDER BY s.subject_code ASC
        ");
        $sub_stmt->execute([
            ':sid' => $student_id,
            ':sy' => $current_school_year,
            ':sem' => $current_semester
        ]);
        $eligible_subjects = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Eligible subjects lookup error: " . $e->getMessage());
    }
}

$first_name = $student_data['first_name'] ?: ($applicant_data['first_name'] ?? 'Not Provided');
$middle_name = $student_data['middle_name'] ?: ($applicant_data['middle_name'] ?? '');
$last_name = $student_data['last_name'] ?: ($applicant_data['last_name'] ?? 'Not Provided');
$suffix = $student_data['suffix'] ?: ($applicant_data['suffix'] ?? '');

$display_name = trim(preg_replace('/\s+/', ' ', "$first_name $middle_name $last_name $suffix"));

$course_code = $student_data['current_course'] ?? 'BSIT';
$year_level_raw = intval($student_data['year_level'] ?? 3);
$suffix_str = ($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : 'th'));
$formatted_rank = "{$course_code} - {$year_level_raw}{$suffix_str} Year";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Drop Subject</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-blue-dark: #001d3d;
            --pcc-gray: #6c757d;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f4f6f9 !important;
            color: #212529;
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
            background-color: var(--pcc-blue-dark);
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
            margin-top: 4px;
        }

        .nav-date {
            font-weight: 600;
            color: var(--pcc-blue);
        }

        .btn,
        .badge {
            border-radius: 0px !important;
        }

        .toast {
            border-radius: 8px !important;
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
                        <span class="d-none d-md-inline">
                            <div class="nav-date" id="liveClockDisplay">Loading Server Time...</div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand"
                style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link">
                    <img src="../../assets/images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Student</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div>
                            <div class="avatar-placeholder shadow-sm"><i class="fa-solid fa-user"></i></div>
                        </div>
                        <div class="user-info">
                            <div class="username"><?php echo htmlspecialchars($display_name); ?></div>
                            <div class="status-text small" style="color: #f1b813; margin-top: -1px;">ID:
                                <?php echo htmlspecialchars($student_number); ?>
                            </div>
                            <div class="status-text small" style="color: #f1b813; margin-top: -3px;">
                                <?php echo htmlspecialchars($formatted_rank); ?>
                            </div>
                            <span class="sidebar-semester-text"><?php echo $display_semester_year; ?></span>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header">ACADEMIC HUB</li>
                        <li class="nav-item"><a href="old_student_dashboard.php" class="nav-link"><i
                                    class="nav-icon bi bi-house-door-fill"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_profile.php" class="nav-link"><i
                                    class="nav-icon bi bi-file-earmark-person-fill"></i>
                                <p>Profile</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_enrollment.php" class="nav-link"><i
                                    class="nav-icon bi bi-laptop"></i>
                                <p>Online Enrollment</p>
                            </a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i
                                    class="nav-icon bi bi-calendar-week-fill"></i>
                                <p>Schedule</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_grades.php" class="nav-link"><i
                                    class="nav-icon bi bi-journal-check"></i>
                                <p>Grades</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_drop.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Dropping of Subject</p>
                            </a></li>
                        <li class="nav-item">
                            <a href="old_student_login.php" class="nav-link text-danger"
                                onclick="return confirm('Are you sure you want to end your session?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p class="text-danger fw-bold">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main p-4">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-3 fw-bold">Subject Dropping</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <?php if ($is_admission_closed): ?>
                    <div class="col-12">
                        <div class="alert alert-danger border-0 shadow-sm p-4 mb-4 d-flex align-items-center rounded-3 text-dark" style="background-color: #f8d7da;">
                            <i class="bi bi-shield-lock-fill fs-1 me-4 text-danger"></i>
                            <div>
                                <h5 class="fw-bold text-danger mb-1">Subject Dropping Period Closed</h5>
                                <span class="small fw-medium">The school administration has officially deactivated the processing system for course load adjustments. New drop applications are frozen at this time.</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm p-4 bg-white mb-4" style="border-radius: 16px;">
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                <i class="fa-solid fa-circle-info fa-lg"></i>
                            </div>
                            <h5 class="fw-bold m-0 text-uppercase"
                                style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Institutional Policy
                            </h5>
                        </div>
                        <p class="text-secondary small lh-base">
                            Dropping of subjects must be executed within the allowable period set by the Registrar's
                            Office. Unauthorized absences do not constitute an official drop.
                        </p>
                        <hr class="opacity-25 my-3">
                        <label class="text-uppercase text-muted fw-bold d-block mb-2"
                            style="font-size: 10px; letter-spacing: 0.5px;">Steps to Drop a Subject</label>
                        <div class="position-relative ps-3 border-start border-2 border-primary-subtle ms-1">
                            <div class="mb-3 position-relative">
                                <i class="bi bi-circle-fill text-primary position-absolute"
                                    style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                <h6 class="fw-bold text-secondary small mb-0">1. Adviser Consultation</h6>
                                <p class="text-muted small m-0">Discuss academic impact with your Program Chair/Adviser.
                                </p>
                            </div>
                            <div class="mb-3 position-relative">
                                <i class="bi bi-circle-fill text-primary position-absolute"
                                    style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                <h6 class="fw-bold text-secondary small mb-0">2. Submit Request</h6>
                                <p class="text-muted small m-0">Fill out and file the drop application in this terminal
                                    module.</p>
                            </div>
                            <div class="position-relative">
                                <i class="bi bi-circle-fill text-primary position-absolute"
                                    style="left: -21px; top: 3px; font-size: 10px; background: white;"></i>
                                <h6 class="fw-bold text-secondary small mb-0">3. Registrar Clearance</h6>
                                <p class="text-muted small m-0">Wait for final documentation updates and dynamic roster
                                    changes.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3">
                                <i class="fa-solid fa-file-signature fa-lg"></i>
                            </div>
                            <h5 class="fw-bold m-0 text-uppercase"
                                style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">File Drop Application
                            </h5>
                        </div>

                        <form action="" method="POST">
                            <input type="hidden" name="action_submit_drop" value="1">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Select Enrolled Subject
                                        to Drop</label>
                                    <select name="target_enrollment_id"
                                        class="form-select border-light-subtle rounded-3 bg-body-tertiary text-secondary fw-medium p-2.5 px-3"
                                        required <?php echo ($is_admission_closed) ? 'disabled' : ''; ?>>
                                        <option value="" selected disabled>
                                            <?php echo ($is_admission_closed) ? '-- Drop Period Inactive --' : '-- Select Course --'; ?>
                                        </option>
                                        <?php foreach ($eligible_subjects as $sub): ?>
                                            <option value="<?php echo $sub['enrollment_id']; ?>">
                                                <?php echo htmlspecialchars($sub['subject_code'] . " - " . $sub['descriptive_title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Reason for
                                        Dropping</label>
                                    <textarea name="drop_reason"
                                        class="form-control border-light-subtle rounded-3 bg-body-tertiary text-secondary p-2.5 px-3"
                                        rows="3" placeholder="State your valid or academic reason briefly..."
                                        required <?php echo ($is_admission_closed) ? 'disabled' : ''; ?>></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit"
                                        class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm"
                                        style="background-color: #002c5e; border-color: #002c5e;"
                                        <?php echo ($is_admission_closed) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
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
        document.addEventListener("DOMContentLoaded", function () { runLiveDashboardClock(); setInterval(runLiveDashboardClock, 1000); });
    </script>
</body>

</html>