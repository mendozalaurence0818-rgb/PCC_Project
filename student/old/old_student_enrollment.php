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

$is_admission_closed = false;
$raw_db_value_for_debugging = "Row not found/Query Error";

try {
    $debug_check = $conn->query("SELECT enrollment_status FROM system_settings LIMIT 1");
    $fetched_val = $debug_check->fetchColumn();
    
    if ($fetched_val !== false) {
        $raw_db_value_for_debugging = $fetched_val;
        
        if (strtolower(trim($fetched_val)) === 'closed') {
            $is_admission_closed = true;
        }
    }
} catch (PDOException $e) {
    $is_admission_closed = true;
    $raw_db_value_for_debugging = "SQL Crash: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_enrollment_db') {
    header('Content-Type: application/json');
    $chosen_sec = $_POST['section'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'Cashier';
    $payment_scheme = $_POST['payment_scheme'] ?? 'Full Payment';
    $gcash_ref_id = isset($_POST['gcash_ref_id']) ? trim($_POST['gcash_ref_id']) : null;
    $uploaded_receipt_path = null;
    
    if ($is_admission_closed) {
        echo json_encode(['success' => false, 'message' => 'Enrollment Blocked: Online registration period is currently closed.']);
        exit();
    }

    if (empty($chosen_sec)) {
        echo json_encode(['success' => false, 'message' => 'No section selected.']);
        exit();
    }

    try {
        $guard_stmt = $conn->prepare("SELECT year_level, enrollment_status FROM students WHERE student_number = :sn LIMIT 1");
        $guard_stmt->execute([':sn' => $student_number]);
        $guard_student = $guard_stmt->fetch(PDO::FETCH_ASSOC);
        
        $guard_year = intval($guard_student['year_level'] ?? 1);
        $guard_status = $guard_student['enrollment_status'] ?? 'Not Enrolled';

        if ($guard_year === 1 && ($guard_status === 'Enrolled' || $guard_status === 'Pending Approval')) {
            echo json_encode(['success' => false, 'message' => 'Modification Denied: 1st Year enrollment choices are locked once submitted.']);
            exit();
        }

        if (($payment_method === 'GCash' || $payment_method === 'Bank Transfer') && isset($_FILES['gcash_receipt_file'])) {
            $file = $_FILES['gcash_receipt_file'];
            $target_dir = "../../assets/uploads/payments/";
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $prefix = ($payment_method === 'GCash') ? 'PAY_GCASH_' : 'PAY_BANK_';
            $new_filename = $prefix . $student_number . "_" . time() . "." . $file_ext;
            $target_file_path = $target_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $target_file_path)) {
                $uploaded_receipt_path = "assets/uploads/payments/" . $new_filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'File Storage Loop Error: Failed to drop receipt binary onto central disk pathways.']);
                exit();
            }
        }

        $conn->beginTransaction();

        $update_student = $conn->prepare("
            UPDATE students 
            SET section = :section, 
                enrollment_status = 'Pending Approval',
                payment_method = :pm,
                payment_scheme = :ps,
                gcash_ref_id = :ref,
                gcash_receipt_path = :rpath
            WHERE student_number = :sn
        ");
        $update_student->execute([
            ':section' => $chosen_sec,
            ':pm'      => $payment_method,
            ':ps'      => $payment_scheme,
            ':ref'     => $gcash_ref_id,
            ':rpath'   => $uploaded_receipt_path,
            ':sn'      => $student_number
        ]);

        $find_app_id = $conn->prepare("SELECT application_id FROM students WHERE student_number = :sn LIMIT 1");
        $find_app_id->execute([':sn' => $student_number]);
        $linked_application_id = $find_app_id->fetchColumn();

        if (!empty($linked_application_id)) {
            $update_applicant = $conn->prepare("
                UPDATE applicants 
                SET application_status = 'Under Review' 
                WHERE application_id = :aid
            ");
            $update_applicant->execute([':aid' => $linked_application_id]);
        }

        $conn->commit();
        echo json_encode(['success' => true]);
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

$student_data = null;
$applicant_data = null;
$app_id = null;

try {
    $s_stmt = $conn->prepare("SELECT * FROM students WHERE student_number = :sn LIMIT 1");
    $s_stmt->execute([':sn' => $student_number]);
    $student_data = $s_stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_data) {
        $student_id = (int)$student_data['student_id'];
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
    error_log("Error getting student data: " . $e->getMessage());
}

$first_name = $student_data['first_name'] ?? ($applicant_data['first_name'] ?? '');
$middle_name = $student_data['middle_name'] ?? ($applicant_data['middle_name'] ?? '');
$last_name = $student_data['last_name'] ?? ($applicant_data['last_name'] ?? '');
$suffix = $student_data['suffix'] ?? ($applicant_data['suffix'] ?? '');
$display_name = trim("$first_name $middle_name $last_name $suffix");
if (empty($display_name)) $display_name = $_SESSION['student_name'] ?? "No Name Assigned";

$course_code = $student_data['current_course'] ?? ($applicant_data['preferred_program'] ?? 'TBA');
$year_level_raw = intval($student_data['year_level'] ?? 1);
$classification = !empty($student_data['classification']) ? $student_data['classification'] : 'Regular';

$db_enrollment_status = $student_data['enrollment_status'] ?? 'Not Enrolled';

if ($year_level_raw === 1 && ($db_enrollment_status === 'Enrolled' || $db_enrollment_status === 'Pending Approval')) {
    $initially_enrolled_flag = 'true';
} else {
    $initially_enrolled_flag = 'false';
}

$suffix_str = ($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : (($year_level_raw == 4) ? 'th' : '')));
$formatted_rank = ($year_level_raw > 0) ? "{$course_code} - {$year_level_raw}{$suffix_str} Year" : "Not Assigned";

$current_school_year = '2026 - 2027';
$current_semester = '1st Semester';
try {
    $settings_stmt = $conn->query("SELECT school_year, semester FROM system_settings LIMIT 1");
    $settings_data = $settings_stmt->fetch(PDO::FETCH_ASSOC);
    if ($settings_data) {
        $current_school_year = $settings_data['school_year'];
        $current_semester = $settings_data['semester'];
    }
} catch (PDOException $e) {}

$display_semester_year = $current_semester . ", AY " . $current_school_year;

$target_year_string = "{$year_level_raw}" . (($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : 'th'))) . " Year";

$sections_list = [];
try {
    $sec_query = $conn->prepare("SELECT * FROM sections WHERE (target_year = :ty OR target_year = 'All' OR target_year = :yr_num) AND program = :program AND status != 'Inactive' ORDER BY section_name ASC");
    $sec_query->execute([':ty' => $target_year_string, ':yr_num' => $year_level_raw, ':program' => $course_code]);
    $sections_list = $sec_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error getting sections: " . $e->getMessage());
}

$master_section_subjects = [];
try {
    $relationship_query = $conn->prepare("
        SELECT ss.section_id, s.section_name, sub.id AS subject_master_id, sub.subject_code, sub.descriptive_title, sub.units, sub.capacity
        FROM section_subjects ss
        JOIN sections s ON ss.section_id = s.id
        JOIN subjects sub ON ss.subject_id = sub.id
        WHERE sub.status = 'Active' 
          AND s.status != 'Inactive' 
          AND (sub.program = :program OR s.program = :program_alt)
    ");
    $relationship_query->execute([':program' => $course_code, ':program_alt' => $course_code]);
    $master_section_subjects = $relationship_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error getting section subjects: " . $e->getMessage());
}

$unmapped_free_subjects = [];
try {
    $free_sub_query = $conn->prepare("SELECT id, subject_code, descriptive_title, units FROM subjects WHERE status = 'Active' AND program = :program AND (target_year = :ty OR target_year = :yr_num) ORDER BY subject_code ASC");
    $free_sub_query->execute([':program' => $course_code, ':ty' => $target_year_string, ':yr_num' => $year_level_raw]);
    $unmapped_free_subjects = $free_sub_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

$officially_enrolled_subjects = [];
$saved_student_section = $student_data['section'] ?? '';

if ($db_enrollment_status === 'Enrolled') {
    try {
        $final_ledger_query = $conn->prepare("
            SELECT sub.subject_code, sub.descriptive_title, sub.units 
            FROM enrollments e
            JOIN subjects sub ON e.subject_id = sub.id
            WHERE e.student_id = :sid 
              AND e.school_year = :sy 
              AND e.semester = :sem
              AND (e.remarks IS NULL OR e.remarks != 'Dropped')
            ORDER BY sub.subject_code ASC
        ");
        $final_ledger_query->execute([
            ':sid' => $student_id,
            ':sy'  => $current_school_year,
            ':sem' => $current_semester
        ]);
        $officially_enrolled_subjects = $final_ledger_query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
} elseif ($db_enrollment_status === 'Pending Approval' && !empty($saved_student_section)) {
    try {
        $pending_subjects_query = $conn->prepare("
            SELECT sub.subject_code, sub.descriptive_title, sub.units 
            FROM section_subjects ss
            JOIN sections s ON ss.section_id = s.id
            JOIN subjects sub ON ss.subject_id = sub.id
            WHERE s.section_name = :sec_name 
              AND sub.status = 'Active'
              AND ss.subject_id NOT IN (
                  SELECT e.subject_id FROM enrollments e 
                  WHERE e.student_id = :sid AND e.remarks = 'Dropped'
              )
            ORDER BY sub.subject_code ASC
        ");
        $pending_subjects_query->execute([
            ':sec_name' => $saved_student_section,
            ':sid'      => $student_id
        ]);
        $officially_enrolled_subjects = $pending_subjects_query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Enrollment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --pcc-blue: #002c5e; --pcc-gold: #f1b813; --pcc-blue-dark: #001d3d; --pcc-gray: #6c757d; }
        body { font-family: 'Source Sans 3', sans-serif; background-color: #f4f6f9 !important; }
        .sidebar-bg { background-color: var(--pcc-blue) !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: var(--pcc-blue) !important; background-color: var(--pcc-gold) !important; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: var(--pcc-blue-dark); }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .sidebar-semester-text { color: #adb5bd; font-size: 11px; font-weight: 500; display: block; margin-top: 4px; }
        .nav-date { font-weight: 600; color: var(--pcc-blue); }

        .step-timeline-container { display: flex; justify-content: space-between; position: relative; margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 16px; border: 1px solid #e3e6f0; }
        .timeline-node { position: relative; z-index: 2; background: #fff; border: 3px solid #eaecf4; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--pcc-gray); transition: all 0.3s ease; cursor: pointer; }
        .timeline-item { text-align: center; width: 23%; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .timeline-item.active .timeline-node { border-color: var(--pcc-blue); background: var(--pcc-blue); color: #fff; box-shadow: 0 0 0 6px rgba(0, 44, 94, 0.15); }
        .timeline-item.completed .timeline-node { border-color: #28a745; background: #28a745; color: #fff; }
        .timeline-label { font-size: 0.85rem; font-weight: 700; color: #4e73df; }

        .enrollment-card { border: none; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); border-radius: 16px; background-color: #fff; overflow: hidden; }
        .subject-row-box { background: #f8f9fc; border: 1px solid #eaecf4; border-radius: 10px; padding: 14px; transition: all 0.2s ease-out; }
        .subject-row-box:hover { background: #fff; border-color: var(--pcc-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
        .staged-item-card { border-radius: 12px; transition: all 0.2s ease-out; background: #fff; border: 1px solid #e3e6f0; }
        .staged-item-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.06) !important; }

        .cor-watermark { border: 2px solid var(--pcc-blue); padding: 40px; background-color: #fff; border-radius: 12px; position: relative; }
        .qr-box { border: 2px dashed #ced4da; padding: 12px; text-align: center; background: #fafafa; border-radius: 8px; width: 130px; }
        .fs-7 { font-size: 0.9rem; }
        .fs-8 { font-size: 0.75rem; }
        
        .btn, .form-select, .btn-group { border-radius: 0px !important; }
        .btn-pcc-primary { background-color: var(--pcc-blue); color: #ffffff; border: 1px solid var(--pcc-blue); }
        .btn-pcc-primary:hover { background-color: var(--pcc-blue-dark); color: #ffffff; border-color: var(--pcc-blue-dark); }
        
        @media print {
            body { background-color: #fff !important; color: #000 !important; }
            .app-header, .app-sidebar, .step-timeline-container, .btn, footer, .back-btn-container { display: none !important; }
            .app-main { margin-left: 0 !important; padding: 0 !important; }
            .cor-watermark { border: none !important; padding: 0 !important; box-shadow: none !important; }
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body px-1">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-dark" href="#" onclick="toggleSidebarMenu(event)" role="button"><i class="bi bi-list fs-5"></i></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline"><div class="nav-date" id="liveClockDisplay">Loading...</div></span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand" style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link">
                    <img src="../../assets/images/PCC_logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Student</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div><div class="avatar-placeholder shadow-sm"><i class="fa-solid fa-user"></i></div></div>
                        <div class="user-info">
                            <div class="username"><?php echo htmlspecialchars($display_name); ?></div>
                            <div class="status-text small" style="color: #f1b813; margin-top: -1px;">ID: <?php echo htmlspecialchars($student_number); ?></div>
                            <div class="status-text small" style="color: #f1b813; margin-top: -3px;"><?php echo htmlspecialchars($formatted_rank); ?></div>
                            <span class="sidebar-semester-text"><?php echo $display_semester_year; ?></span>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header">ACADEMIC HUB</li>
                        <li class="nav-item"><a href="old_student_dashboard.php" class="nav-link"><i class="nav-icon bi bi-house-door-fill"></i><p>Dashboard</p></a></li>
                        <li class="nav-item"><a href="old_student_profile.php" class="nav-link"><i class="nav-icon bi bi-file-earmark-person-fill"></i><p>Profile</p></a></li>
                        <li class="nav-item"><a href="old_student_enrollment.php" class="nav-link sidebar-bg-active"><i class="nav-icon bi bi-laptop"></i><p>Online Enrollment</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar-week-fill"></i><p>Schedule</p></a></li>
                        <li class="nav-item"><a href="old_student_grades.php" class="nav-link"><i class="nav-icon bi bi-journal-check"></i><p>Grades</p></a></li>
                        <li class="nav-item"><a href="old_student_drop.php" class="nav-link"><i class="nav-icon bi bi-gear-fill"></i><p>Dropping of Subject</p></a></li>
                        <li class="nav-item">
                            <a href="old_student_login.php" class="nav-link text-danger" onclick="return confirm('Are you sure you want to log out?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i><p class="text-danger fw-bold">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main py-4">
            <div class="container-fluid">
                
                <?php if ($is_admission_closed && $db_enrollment_status !== 'Enrolled' && $db_enrollment_status !== 'Pending Approval'): ?>
                    <div class="alert alert-danger border-0 shadow-sm p-4 mb-4 d-flex align-items-center rounded-3 text-dark" style="background-color: #f8d7da;">
                        <i class="bi bi-shield-lock-fill fs-1 me-4 text-danger"></i>
                        <div>
                            <h5 class="fw-bold text-danger mb-1">Online Enrollment Portal Closed</h5>
                            <span class="small fw-medium d-block mb-2">The system administrator has closed the student registration process. Staging submissions, custom course section selections, and file uploads are deactivated.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="step-timeline-container">
                    <div class="timeline-item active" id="node1" onclick="switchEnrollmentTab('step1-builder', 0)">
                        <div class="timeline-node"><i class="bi bi-collection"></i></div>
                        <span class="timeline-label">1. Pick Subjects</span>
                    </div>
                    <div class="timeline-item" id="node2" onclick="switchEnrollmentTab('step2-payment', 33)">
                        <div class="timeline-node"><i class="bi bi-wallet2"></i></div>
                        <span class="timeline-label">2. Check Price</span>
                    </div>
                    <div class="timeline-item" id="node3" onclick="switchEnrollmentTab('step3-save', 66)">
                        <div class="timeline-node"><i class="bi bi-cloud-arrow-up"></i></div>
                        <span class="timeline-label">3. Save Selection</span>
                    </div>
                    <div class="timeline-item" id="node4" onclick="switchEnrollmentTab('step4-cor', 100)">
                        <div class="timeline-node"><i class="bi bi-file-check"></i></div>
                        <span class="timeline-label">4. Official Form</span>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="tab-content" id="enrollmentStepContent">

                    <div class="tab-pane fade show active" id="step1-builder" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-xl-5 col-lg-6">
                                <div class="card enrollment-card p-4">
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Choose Type of Section</label>
                                        <div class="btn-group w-100 shadow-sm" role="group">
                                            <input type="radio" class="btn-check" name="enrollment_mode" id="modeBlock" value="block" checked onchange="toggleEnrollmentTypeMode(this.value)">
                                            <label class="btn btn-outline-primary fw-bold py-2" for="modeBlock"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Block Section</label>

                                            <input type="radio" class="btn-check" name="enrollment_mode" id="modeFree" value="free" onchange="toggleEnrollmentTypeMode(this.value)" <?php echo ($year_level_raw === 1 || $is_admission_closed) ? 'disabled' : ''; ?>>
                                            <label class="btn btn-outline-primary fw-bold py-2 <?= ($year_level_raw === 1) ? 'opacity-50 text-decoration-line-through' : ''; ?>" for="modeFree" <?= ($year_level_raw === 1) ? 'data-bs-toggle="tooltip" title="Free Section Restricted for 1st Year Students"' : ''; ?>><i class="bi bi-list-check me-2"></i>Free Section</label>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <div class="p-2 bg-success-subtle text-success rounded-3 me-3"><i class="bi bi-grid-3x3-gap-fill fs-5"></i></div>
                                        <h5 class="fw-bold m-0 text-dark" id="step1FormTitleLabel">Step 1: Choose Your Section</h5>
                                    </div>
                                    
                                    <!-- FIXED: Removed the invalid nested <aside> element that was causing structural framework compilation conflicts -->
                                    <select class="form-select border shadow-sm mb-4 py-2.5 fw-semibold" id="sectionSelectorField" onchange="processSectionChoice(this.value)" <?php echo ($is_admission_closed) ? 'disabled' : ''; ?>>
                                        <option value="" disabled selected>-- Choose an open section --</option>
                                        <?php foreach ($sections_list as $sec): ?>
                                            <?php $is_block_column = isset($sec['is_block_section']) ? intval($sec['is_block_section']) : 1; ?>
                                            <option value="<?php echo htmlspecialchars($sec['section_name']); ?>" 
                                                    data-is-block-sec="<?php echo $is_block_column; ?>"
                                                    <?php echo ($sec['section_name'] === $saved_student_section) ? 'selected' : ''; ?>
                                                    <?php echo (strtolower($sec['status']) === 'full' && $sec['section_name'] !== $saved_student_section) ? 'disabled style="color:red;"' : ''; ?>>
                                                <?php echo htmlspecialchars($sec['section_name']); ?> (<?php echo htmlspecialchars($sec['status']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div class="d-none" id="subjectDirectoryWrapper">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="p-2 bg-primary-subtle text-primary rounded-3 me-3"><i class="bi bi-book fs-5"></i></div>
                                            <h5 class="fw-bold m-0 text-dark" id="step2FormTitleLabel">Step 2: Included Block Subjects</h5>
                                        </div>
                                        <div class="d-flex flex-column gap-3 mt-2" id="dynamicSubjectsListContainer"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-7 col-lg-6">
                                <div class="card enrollment-card p-4 d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-tags-fill text-primary me-2"></i>Your Chosen Subjects</h5>
                                            <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-monospace" id="stagedUnitsCounter">0.0 Total Units</span>
                                        </div>
                                        <div id="stagedContainerBucket" class="d-flex flex-column gap-3 py-2">
                                            <div class="text-center text-muted p-5 bg-light border border-dashed rounded-3" id="blankQueueAlert">
                                                <i class="bi bi-inboxes-fill fs-2 text-secondary mb-2 d-block"></i>
                                                Your list is empty. Please choose a section first and select your subjects.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-4 border-top mt-4">
                                        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 d-none" id="conflictIndicatorBox">
                                            <i class="bi bi-shield-check fs-4 me-3 text-success"></i>
                                            <span class="small fw-medium"><strong>Success:</strong> Everything looks good and ready to go.</span>
                                        </div>
                                        <div class="text-end">
                                            <button type="button" id="nextAssessmentBtn" disabled onclick="switchEnrollmentTab('step2-payment', 33)" class="btn btn-primary px-4 py-2.5 fw-semibold">Continue to Price Assessment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="step2-payment" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <div class="card enrollment-card p-4 border">
                                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Price Breakdown</h5>
                                    
                                    <div class="alert alert-info border-0 mb-4 py-2 small" style="border-radius:0px;">
                                        <i class="bi bi-info-circle-fill me-2"></i>Staged Base Container Selection Route: <strong id="summarySectionLabelField">None</strong>
                                    </div>

                                    <div class="table-responsive mb-4">
                                        <table class="table align-middle">
                                            <thead class="table-light text-secondary text-uppercase fs-8">
                                                <tr>
                                                    <th class="ps-4 py-3">Subject Details</th>
                                                    <th class="text-center">Units</th>
                                                    <th class="pe-4 text-end">Price Matrix</th>
                                                </tr>
                                            </thead>
                                            <tbody id="assessmentRowsContainer"></tbody>
                                            <tbody class="border-top-0 text-secondary small">
                                                <tr>
                                                    <td class="ps-4 py-2.5 text-muted">Laboratory and Equipment Fees</td>
                                                    <td class="text-center">-</td>
                                                    <td class="pe-4 text-end text-muted">₱2,100.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 py-2.5 text-muted">Other School Fees</td>
                                                    <td class="text-center">-</td>
                                                    <td class="pe-4 text-end text-muted">₱4,500.00</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="ps-4 fw-bold text-dark text-end py-3">Total Amount:</td>
                                                    <td class="pe-4 fw-bold text-primary text-end fs-5 py-3" id="grandFeeSumDisplay">₱6,600.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="row g-3 mb-4 text-dark">
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 border h-100">
                                                <label class="form-label small fw-bold text-dark text-uppercase tracking-wider mb-2 d-block"><i class="bi bi-credit-card-2-front text-secondary me-2"></i>Choose Settlement Strategy Channel</label>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="form-check border p-3 bg-white w-100 m-0">
                                                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_channel_choice" id="payGCash" value="GCash" checked onchange="evaluatePaymentChannelView(this.value)">
                                                        <label class="form-check-label small fw-semibold text-dark" for="payGCash">Online Settlement via GCash API</label>
                                                    </div>
                                                    <div class="form-check border p-3 bg-white w-100 m-0">
                                                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_channel_choice" id="payBank" value="Bank Transfer" onchange="evaluatePaymentChannelView(this.value)">
                                                        <label class="form-check-label small fw-semibold text-dark" for="payBank">Secure Bank Transfer Network (MOBI/Online)</label>
                                                    </div>
                                                    <div class="form-check border p-3 bg-white w-100 m-0">
                                                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_channel_choice" id="payCashier" value="Cashier" onchange="evaluatePaymentChannelView(this.value)">
                                                        <label class="form-check-label small fw-semibold text-dark" for="payCashier">Over-the-Counter Cash (Cashier)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="bg-light p-3 border h-100">
                                                <label class="form-label small fw-bold text-dark text-uppercase tracking-wider mb-2 d-block"><i class="bi bi-calendar-check text-secondary me-2"></i>Select Payment Terms Scheme</label>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="form-check border p-3 bg-white w-100 m-0">
                                                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_scheme_choice" id="schemeFull" value="Full Payment" checked>
                                                        <label class="form-check-label small fw-semibold text-dark" for="schemeFull">Full Payment (Clear Balance Total)</label>
                                                    </div>
                                                    <div class="form-check border p-3 bg-white w-100 m-0">
                                                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_scheme_choice" id="schemeInstallment" value="Installment">
                                                        <label class="form-check-label small fw-semibold text-dark" for="schemeInstallment">Installment Mode Plan Options</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between pt-4 border-top">
                                        <button type="button" id="assessmentGoBackBtn" onclick="switchEnrollmentTab('step1-builder', 0)" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">Go Back</button>
                                        <button type="button" onclick="switchEnrollmentTab('step3-save', 66)" class="btn btn-primary text-white px-4 py-2.5 fw-semibold">Continue to Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="step3-save" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-xl-6">
                                <div class="card enrollment-card p-5 border shadow-sm text-center text-dark">
                                    <i class="bi bi-cloud-arrow-up-fill text-primary display-2 mb-3"></i>
                                    <h3 class="fw-bold text-dark mb-2">Save Your Enrollment</h3>
                                    <p class="text-muted mb-4 small">Clicking the save button will secure your spot and lock in your chosen section and subjects in our system database.</p>
                                    
                                    <div class="text-start mb-4 bg-light p-4 border d-none" id="panelGcashUploadInputs">
                                        <h6 class="fw-bold text-primary mb-3" id="paymentPanelHeaderTitle"><i class="bi bi-shield-lock-fill me-2"></i>Transaction Verification Registry</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-secondary" id="paymentRefLabel">Reference ID Number</label>
                                            <input type="text" id="txtGcashRefId" class="form-control font-monospace text-dark shadow-sm py-2" placeholder="e.g. 5012345678901">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-secondary">Upload Payment Receipt Screenshot File</label>
                                            <input type="file" id="fileGcashReceipt" class="form-control text-dark shadow-sm bg-white" accept="image/*">
                                            <span class="text-muted d-block mt-1 style" style="font-size:11px;">Supported format parameters: JPG, JPEG, PNG image files only.</span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center gap-3">
                                        <button type="button" id="saveGoBackBtn" onclick="switchEnrollmentTab('step2-payment', 33)" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">Go Back</button>
                                        <button type="button" id="saveDatabaseRecordBtn" onclick="saveEnrollmentRecordsData()" class="btn btn-success px-5 py-2.5 fw-bold" <?php echo ($is_admission_closed) ? 'disabled' : ''; ?>><i class="bi bi-check-circle me-2"></i>Save Selection Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="step4-cor" role="tabpanel">
                        <div class="card enrollment-card cor-watermark shadow-lg mx-auto mb-4" style="max-width: 850px;">
                            <div class="row align-items-center border-bottom pb-3 mb-4">
                                <div class="col-sm-2 text-center text-md-start">
                                    <img src="../../assets/images/PCC_logo.png" alt="PCC Logo" style="max-height: 75px;">
                                </div>
                                <div class="col-sm-7 text-center text-md-start mt-2 mt-md-0">
                                    <h4 class="fw-bold text-dark mb-0">POBLACION CENTRAL COLLEGE</h4>
                                    <span class="text-uppercase tracking-wider text-muted fw-bold small" style="font-size: 11px;">Official Registration Form</span>
                                    <div class="small text-secondary mt-1"><?php echo $display_semester_year; ?></div>
                                </div>
                                <div class="col-sm-3 text-center text-md-end mt-3 mt-md-0">
                                    <div class="small border p-2.5 bg-light rounded text-start font-monospace fs-8">
                                        <strong>Form ID:</strong> COR-2026-99A<br>
                                        <strong>Status:</strong> 
                                        <?php if ($db_enrollment_status === 'Enrolled'): ?>
                                            <span class="text-success fw-bold">OFFICIALLY ENROLLED</span>
                                        <?php else: ?>
                                            <span class="text-warning fw-bold">PENDING ADMIN CONFIRMATION</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 small text-dark mb-4 bg-light py-3 px-2 rounded-3 mx-0 fw-medium border">
                                <div class="col-sm-4"><strong>Student Number:</strong> <?php echo htmlspecialchars($student_number); ?></div>
                                <div class="col-sm-4"><strong>Full Name:</strong> <?php echo htmlspecialchars($display_name); ?></div>
                                <div class="col-sm-4"><strong>Your Class Track:</strong> <?php echo htmlspecialchars($formatted_rank); ?></div>
                            </div>

                            <?php if ($db_enrollment_status !== 'Enrolled'): ?>
                                <div class="alert alert-warning border-0 p-3 mb-4 d-flex align-items-center text-dark" style="border-radius:0px;">
                                    <i class="bi bi-clock-history fs-4 me-3 text-warning"></i>
                                    <span class="small">Your course schedule load has been saved. The registrar is checking roster thresholds. You can review your staging schedule details directly below while waiting.</span>
                                </div>
                            <?php endif; ?>

                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-table text-muted me-2"></i>Class Schedule List</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle small text-dark mb-0">
                                    <thead class="table-light fw-bold text-secondary">
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Title</th>
                                            <th class="text-center">Units</th>
                                            <th>Class Location / Room</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corFinalRowsTarget">
                                        <?php if (!empty($officially_enrolled_subjects)): ?>
                                            <?php foreach ($officially_enrolled_subjects as $sub): ?>
                                                <tr>
                                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($sub['subject_code']); ?></td>
                                                    <td class="fw-medium"><?php echo htmlspecialchars($sub['descriptive_title']); ?></td>
                                                    <td class="text-center fw-bold"><?php echo number_format($sub['units'], 1); ?></td>
                                                    <td class="text-muted">Section Cluster <?php echo htmlspecialchars($saved_student_section); ?> Allocation</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No active schedules linked. Choose your items on Step 1.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row align-items-center pt-3 border-top mt-5 g-3">
                                <div class="col-sm-8 text-center text-sm-start text-secondary">
                                    <p class="mb-1 fw-bold text-dark small"><i class="bi bi-shield-fill-check text-success me-1"></i>Official School Verification</p>
                                    <p class="mb-0 text-muted fs-8">This is an official document from the school registrar portal.</p>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-center justify-content-sm-end">
                                    <div class="qr-box shadow-sm">
                                        <i class="bi bi-qr-code-scan text-dark display-6 mb-1 d-block"></i>
                                        <span class="font-monospace text-uppercase text-muted d-block" style="font-size: 8px; letter-spacing: 0.2px;">Portal Code Token</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center d-flex justify-content-center gap-3 back-btn-container">
                            <?php if ($db_enrollment_status !== 'Enrolled' && $year_level_raw > 1 && !$is_admission_closed): ?>
                                <button type="button" id="corGoBackBtn" onclick="switchEnrollmentTab('step3-save', 66)" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">Go Back</button>
                            <?php endif; ?>
                            <button type="button" onclick="window.print();" class="btn btn-primary px-4 py-2.5 fw-semibold"><i class="bi bi-printer-fill me-2"></i>Print Registration Form</button>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="app-footer bg-white border-top small text-muted py-3 px-4">
            <div class="float-start d-none d-sm-inline">Poblacion Central College - &copy; 2026</div>
            <strong><span class="float-end">&nbsp;All rights reserved.</span></strong>
            <div class="clearfix"></div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }

        const masterMatrix = <?php echo json_encode($master_section_subjects); ?>;
        const completeFreeSubjectsInventory = <?php echo json_encode($unmapped_free_subjects); ?>;
        
        let selectedCourses = [];
        let chosenSection = "<?php echo $saved_student_section; ?>";
        let currentActiveMode = "block"; 
        let internalSelectedPaymentChannel = "GCash";
        
        let isEnrollmentSaved = <?php echo ($db_enrollment_status === 'Pending Approval' || $db_enrollment_status === 'Enrolled') ? 'true' : 'false'; ?>;
        const absoluteVerificationState = "<?php echo $db_enrollment_status; ?>";
        const isAdmissionClosedGate = <?php echo $is_admission_closed ? 'true' : 'false'; ?>;
        
        const currentStudentYearLevel = <?= $year_level_raw; ?>;

        function evaluatePaymentChannelView(channelValue) {
            internalSelectedPaymentChannel = channelValue;
            const targetPanel = document.getElementById('panelGcashUploadInputs');
            const panelHeader = document.getElementById('paymentPanelHeaderTitle');
            const refLabel = document.getElementById('paymentRefLabel');
            const txtInput = document.getElementById('txtGcashRefId');

            if (channelValue === 'GCash' || channelValue === 'Bank Transfer') {
                targetPanel.classList.remove('d-none');
                if (channelValue === 'GCash') {
                    panelHeader.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i>GCash Transaction Verification Registry';
                    refLabel.textContent = "13-Digit GCash Reference ID Number";
                    txtInput.placeholder = "e.g. 5012345678901";
                } else {
                    panelHeader.innerHTML = '<i class="bi bi-bank2 me-2"></i>Bank Transfer Verification Registry';
                    refLabel.textContent = "Bank Transfer Reference / Transaction Number";
                    txtInput.placeholder = "e.g. TXN9876543210";
                }
            } else {
                targetPanel.classList.add('d-none');
            }
        }

        function toggleEnrollmentTypeMode(modeValue) {
            if (isEnrollmentSaved || isAdmissionClosedGate) return; 

            if (modeValue === 'free' && currentStudentYearLevel === 1) {
                document.getElementById('modeBlock').checked = true;
                return;
            }

            currentActiveMode = modeValue;
            selectedCourses = [];
            chosenSection = "";
            
            const selectEl = document.getElementById('sectionSelectorField');
            selectEl.value = ""; 
            document.getElementById('subjectDirectoryWrapper').classList.add('d-none');
            refreshEnrollmentEngineLayout();

            const options = selectEl.querySelectorAll('option');

            if (modeValue === 'block') {
                document.getElementById('step1FormTitleLabel').textContent = "Step 1: Choose Your Section";
                document.getElementById('step2FormTitleLabel').textContent = "Step 2: Included Block Subjects";
                options.forEach(opt => {
                    if (opt.value === "") return;
                    opt.style.display = (opt.getAttribute('data-is-block-sec') === '1') ? 'block' : 'none';
                });
            } else {
                document.getElementById('step1FormTitleLabel').textContent = "Step 1: Choose Base Section";
                document.getElementById('step2FormTitleLabel').textContent = "Step 2: Choose Custom Subjects";
                options.forEach(opt => {
                    if (opt.value === "") return;
                    opt.style.display = (opt.getAttribute('data-is-block-sec') === '0') ? 'block' : 'none';
                });
            }
        }

        function processSectionChoice(sectionName) {
            if (isEnrollmentSaved || isAdmissionClosedGate) return;
            chosenSection = sectionName;
            selectedCourses = [];
            const listContainer = document.getElementById('dynamicSubjectsListContainer');
            listContainer.innerHTML = '';

            if (currentActiveMode === 'block') {
                const matchingSubjects = masterMatrix.filter(item => item.section_name === sectionName);
                if (matchingSubjects.length === 0) {
                    listContainer.innerHTML = '<div class="text-muted text-center py-3 fs-8">No subjects configured for this section.</div>';
                } else {
                    matchingSubjects.forEach(sub => {
                        const row = document.createElement('div');
                        row.className = "subject-row-box d-flex justify-content-between align-items-center shadow-sm bg-white border-0";
                        row.innerHTML = `
                            <div>
                                <span class="badge bg-primary-subtle text-primary mb-1 fw-bold">${sub.subject_code}</span>
                                <h6 class="fw-bold text-dark mb-1">${sub.descriptive_title}</h6>
                                <small class="text-secondary font-monospace fs-8">Credits: ${sub.units} Units</small>
                            </div>
                            <span class="badge bg-success-subtle text-success px-3 py-2 small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Selected</span>
                        `;
                        listContainer.appendChild(row);

                        selectedCourses.push({
                            id: sub.subject_master_id,
                            code: sub.subject_code,
                            title: sub.descriptive_title,
                            units: parseFloat(sub.units),
                            section: chosenSection
                        });
                    });
                }
            } else {
                if (completeFreeSubjectsInventory.length === 0) {
                    listContainer.innerHTML = '<div class="text-muted text-center py-3 fs-8">No active subjects found for this class track.</div>';
                } else {
                    completeFreeSubjectsInventory.forEach(sub => {
                        const row = document.createElement('div');
                        row.className = "subject-row-box d-flex justify-content-between align-items-center shadow-sm bg-white border-0";
                        row.innerHTML = `
                            <div>
                                <span class="badge bg-primary-subtle text-primary mb-1 fw-bold">${sub.subject_code}</span>
                                <h6 class="fw-bold text-dark mb-1">${sub.descriptive_title}</h6>
                                <small class="text-secondary font-monospace fs-8">Credits: ${sub.units} Units</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-pcc-primary px-3 fw-bold" data-id="${sub.id}" data-code="${sub.subject_code}" data-title="${sub.descriptive_title}" data-units="${sub.units}" onclick="stageManualFreeCourse(this)">Select Subject</button>
                        `;
                        listContainer.appendChild(row);
                    });
                }
            }
            refreshEnrollmentEngineLayout();
            document.getElementById('subjectDirectoryWrapper').classList.remove('d-none');
        }

        function stageManualFreeCourse(button) {
            if (isEnrollmentSaved || isAdmissionClosedGate) return;
            if (!chosenSection) return;
            const id = button.getAttribute('data-id');
            const code = button.getAttribute('data-code');
            const title = button.getAttribute('data-title');
            const presidentialUnits = parseFloat(button.getAttribute('data-units'));
            if (selectedCourses.some(item => item.code === code)) return;
            selectedCourses.push({ id, code, title, units: presidentialUnits, section: chosenSection });
            refreshEnrollmentEngineLayout();
        }

        function removeCourseFromStaged(code) {
            if (isEnrollmentSaved || isAdmissionClosedGate) return;
            selectedCourses = selectedCourses.filter(item => item.code !== code);
            refreshEnrollmentEngineLayout();
        }

        function refreshEnrollmentEngineLayout() {
            if (absoluteVerificationState === 'Enrolled' || absoluteVerificationState === 'Pending Approval') return;
            const bucket = document.getElementById('stagedContainerBucket');
            const blankAlert = document.getElementById('blankQueueAlert');
            const conflictBox = document.getElementById('conflictIndicatorBox');
            const actionBtn = document.getElementById('nextAssessmentBtn');
            const unitsBadge = document.getElementById('stagedUnitsCounter');
            const assessmentTarget = document.getElementById('assessmentRowsContainer');
            const corTarget = document.getElementById('corFinalRowsTarget');

            bucket.querySelectorAll('.staged-item-card').forEach(el => el.remove());
            assessmentTarget.innerHTML = '';
            corTarget.innerHTML = '';
            document.getElementById('summarySectionLabelField').textContent = chosenSection || "None Selected";

            if (selectedCourses.length === 0) {
                blankAlert.classList.remove('d-none');
                conflictBox.classList.add('d-none');
                actionBtn.disabled = true;
                unitsBadge.textContent = "0.0 Total Units";
                document.getElementById('grandFeeSumDisplay').textContent = "₱6,600.00";
                return;
            }

            blankAlert.classList.add('d-none');
            conflictBox.classList.remove('d-none');
            actionBtn.disabled = false;

            let unitsSum = 0;
            let runningTuitionCost = 0;

            selectedCourses.forEach(course => {
                unitsSum += course.units;
                const tuitionCost = course.units * 1550;
                runningTuitionCost += tuitionCost;

                const dropButtonMarkup = (currentActiveMode === 'block' || isEnrollmentSaved || isAdmissionClosedGate) 
                    ? '' 
                    : `<button type="button" class="btn btn-link text-danger p-0 small mt-2 text-decoration-none fs-8 fw-semibold" onclick="removeCourseFromStaged('${course.code}')"><i class="bi bi-trash3 me-1"></i>Drop</button>`;

                const card = document.createElement('div');
                card.className = "p-3 shadow-sm staged-item-card";
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-2 fw-bold">${course.code} — Section ${course.section}</span>
                            <h6 class="fw-bold text-dark mb-1">${course.title}</h6>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark d-block">${course.units.toFixed(1)} Units</span>
                            ${dropButtonMarkup}
                        </div>
                    </div>
                `;
                bucket.appendChild(card);

                const assessRow = document.createElement('tr');
                assessRow.innerHTML = `
                    <td class="ps-4 py-2.5 font-bold text-dark"><strong>[${course.code}]</strong> ${course.title}</td>
                    <td class="text-center fw-medium">${course.units.toFixed(1)}</td>
                    <td class="pe-4 text-end fw-semibold text-dark">₱${tuitionCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                `;
                assessmentTarget.appendChild(assessRow);

                const corRow = document.createElement('tr');
                corRow.innerHTML = `
                    <td class="fw-bold text-dark">${course.code}</td>
                    <td class="fw-medium">${course.title} (${course.section})</td>
                    <td class="text-center fw-bold">${course.units}</td>
                    <td class="text-muted">Section Group ${course.section}</td>
                `;
                corTarget.appendChild(corRow);
            });

            unitsBadge.textContent = `${unitsSum.toFixed(1)} Total Units`;
            const absoluteGrandTotal = runningTuitionCost + 2100 + 4500;
            document.getElementById('grandFeeSumDisplay').textContent = `₱${absoluteGrandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        function saveEnrollmentRecordsData() {
            if (isAdmissionClosedGate) return;

            if (internalSelectedPaymentChannel === 'GCash' || internalSelectedPaymentChannel === 'Bank Transfer') {
                const refNo = document.getElementById('txtGcashRefId').value.trim();
                const fileInput = document.getElementById('fileGcashReceipt');
                
                if (internalSelectedPaymentChannel === 'GCash' && (refNo.length !== 13 || isNaN(refNo))) { 
                    return alert("GCash Reference ID must be exactly 13 digits."); 
                }
                if (refNo === '') {
                    return alert("Please input your transaction reference number.");
                }
                if (fileInput.files.length === 0) { 
                    return alert("Please upload your payment screenshot file."); 
                }
            }

            const saveBtn = document.getElementById('saveDatabaseRecordBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = 'Processing...';

            const checkedSchemeRadio = document.querySelector('input[name="payment_scheme_choice"]:checked');
            const targetScheme = checkedSchemeRadio ? checkedSchemeRadio.value : 'Full Payment';

            const formData = new FormData();
            formData.append('action', 'confirm_enrollment_db');
            formData.append('section', chosenSection);
            formData.append('payment_method', internalSelectedPaymentChannel);
            formData.append('payment_scheme', targetScheme);

            if (internalSelectedPaymentChannel === 'GCash' || internalSelectedPaymentChannel === 'Bank Transfer') {
                formData.append('gcash_ref_id', document.getElementById('txtGcashRefId').value.trim());
                formData.append('gcash_receipt_file', document.getElementById('fileGcashReceipt').files[0]);
            }

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) { isEnrollmentSaved = true; window.location.reload(); } 
                else { alert(data.message); saveBtn.disabled = false; }
            }).catch(() => { saveBtn.disabled = false; });
        }

        function applyEnrolledLocks() {
            if (isEnrollmentSaved || isAdmissionClosedGate) {
                document.getElementById('sectionSelectorField').disabled = true;
                document.getElementById('modeBlock').disabled = true;
                document.getElementById('modeFree').disabled = true;
                document.getElementById('payGCash').disabled = true;
                document.getElementById('payBank').disabled = true;
                document.getElementById('payCashier').disabled = true;
                document.getElementById('schemeFull').disabled = true;
                document.getElementById('schemeInstallment').disabled = true;
            }
        }

        function switchEnrollmentTab(tabTargetId, progressPercentage) {
            if (absoluteVerificationState !== 'Enrolled' && absoluteVerificationState !== 'Pending Approval' && tabTargetId !== 'step1-builder' && selectedCourses.length === 0) return;
            if (currentStudentYearLevel === 1 && isEnrollmentSaved && (tabTargetId === 'step1-builder' || tabTargetId === 'step2-payment' || tabTargetId === 'step3-save')) return;
            if (absoluteVerificationState === 'Enrolled' && (tabTargetId === 'step1-builder' || tabTargetId === 'step2-payment' || tabTargetId === 'step3-save')) return;

            document.querySelectorAll('.timeline-item').forEach((node, idx) => {
                let checkPercent = (idx === 1) ? 33 : ((idx === 2) ? 66 : 100);
                if (checkPercent < progressPercentage) { node.classList.add('completed'); node.classList.remove('active'); } 
                else if (checkPercent === progressPercentage) { node.classList.add('active'); node.classList.remove('completed'); } 
                else { node.classList.remove('active', 'completed'); }
            });

            document.querySelectorAll('#enrollmentStepContent .tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            if (document.getElementById(tabTargetId)) document.getElementById(tabTargetId).classList.add('show', 'active');
        }

        function runLiveDashboardClock() {
            const dateOptions = { timeZone: 'Asia/Manila', month: 'long', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const now = new Date();
            document.getElementById('liveClockDisplay').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} - ${now.toLocaleTimeString('en-US', timeOptions)}`;
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            runLiveDashboardClock(); setInterval(runLiveDashboardClock, 1000);
            evaluatePaymentChannelView("GCash");
            if (absoluteVerificationState !== 'Enrolled' && absoluteVerificationState !== 'Pending Approval') {
                if (chosenSection !== "") { processSectionChoice(chosenSection); } 
                else { toggleEnrollmentTypeMode("block"); }
            }
            applyEnrolledLocks();
            if (isEnrollmentSaved || absoluteVerificationState === 'Enrolled' || absoluteVerificationState === 'Pending Approval') { switchEnrollmentTab('step4-cor', 100); }
        });
    </script>
</body>
</html>