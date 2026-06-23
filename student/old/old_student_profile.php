<?php
session_start();

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true || !isset($_SESSION['student_number'])) {
    header("Location: old_student_login.php");
    exit();
}

require_once '../../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$student_number = $_SESSION['student_number'];
$feedback_msg = "";

$student_id = 0;
$app_id = null;
$is_profile_found = false;

$first_name = $middle_name = $last_name = $suffix = $display_name = "Not Provided";
$email = $course_code = $classification = $enrollment_status = "Not Provided";
$mobile_no = $personal_email = $permanent_address = "Not Provided";
$nationality = $gender = $civil_status = $date_of_birth = "Not Provided";
$religious_affiliation = $classification_type = "Not Provided";
$emergency_person = $emergency_no = $emergency_address = "Not Provided";
$year_level_raw = 0;
$formatted_rank = "Unassigned / Pending";

try {
    $s_stmt = $conn->prepare("SELECT * FROM students WHERE student_number = :sn LIMIT 1");
    $s_stmt->execute([':sn' => $student_number]);
    $student_data = $s_stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_data) {
        $student_id = (int)$student_data['student_id'];
        $_SESSION['student_id'] = $student_id;
        $app_id = $student_data['application_id'];

        if (empty($app_id)) {
            error_log("Profile Audit: students.application_id is NULL for $student_number. Initiating fallback lookups.");
            
            $find_app = $conn->prepare("SELECT application_id FROM applicants WHERE student_number = :sn LIMIT 1");
            $find_app->execute([':sn' => $student_number]);
            $app_id = $find_app->fetchColumn();

            if (empty($app_id)) {
                error_log("Profile Audit: Fallback 1 failed. Attempting Name Match for {$student_data['first_name']} {$student_data['last_name']}.");
                $find_name = $conn->prepare("
                    SELECT application_id FROM applicants 
                    WHERE LOWER(first_name) = LOWER(:fn) AND LOWER(last_name) = LOWER(:ln) 
                    LIMIT 1
                ");
                $find_name->execute([
                    ':fn' => $student_data['first_name'], 
                    ':ln' => $student_data['last_name']
                ]);
                $app_id = $find_name->fetchColumn();
            }

            if (!empty($app_id)) {
                error_log("Profile Audit: Match found (App ID: $app_id). Self-healing the students table.");
                $heal_stmt = $conn->prepare("UPDATE students SET application_id = :aid WHERE student_id = :sid");
                $heal_stmt->execute([':aid' => $app_id, ':sid' => $student_id]);
            } else {
                error_log("Profile Audit CRITICAL: No applicant record exists for student $student_number.");
            }
        }

        $applicant_data = null;
        $guardian_data = null;

        if (!empty($app_id)) {
            $a_stmt = $conn->prepare("SELECT * FROM applicants WHERE application_id = :aid LIMIT 1");
            $a_stmt->execute([':aid' => $app_id]);
            $applicant_data = $a_stmt->fetch(PDO::FETCH_ASSOC);

            if ($applicant_data && !empty($applicant_data['guardian_id'])) {
                $g_stmt = $conn->prepare("SELECT * FROM guardians WHERE guardian_id = :gid LIMIT 1");
                $g_stmt->execute([':gid' => $applicant_data['guardian_id']]);
                $guardian_data = $g_stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        $is_profile_found = true;
        
        $first_name = $student_data['first_name'] ?: ($applicant_data['first_name'] ?? 'Not Provided');
        $middle_name = $student_data['middle_name'] ?: ($applicant_data['middle_name'] ?? '');
        $last_name = $student_data['last_name'] ?: ($applicant_data['last_name'] ?? 'Not Provided');
        $suffix = $student_data['suffix'] ?: ($applicant_data['suffix'] ?? '');
        
        $display_name = trim(preg_replace('/\s+/', ' ', "$first_name $middle_name $last_name $suffix"));
        
        $email = $student_data['email'] ?: ($applicant_data['email_address'] ?? "$student_number@pcc.edu.ph");
        $course_code = $student_data['current_course'] ?: ($applicant_data['preferred_program'] ?? 'TBA');
        $year_level_raw = intval($student_data['year_level'] ?? 1);
        $classification = !empty($student_data['classification']) ? $student_data['classification'] : 'Regular';
        $enrollment_status = !empty($student_data['enrollment_status']) ? $student_data['enrollment_status'] : 'Not Enrolled';
        
        $suffix_str = ($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : (($year_level_raw == 4) ? 'th' : '')));
        $formatted_rank = ($year_level_raw > 0) ? "{$course_code} - {$year_level_raw}{$suffix_str} Year" : "Unassigned / Pending";

        if ($applicant_data) {
            $nationality = !empty($applicant_data['nationality']) ? $applicant_data['nationality'] : "Not Provided";
            $gender = !empty($applicant_data['gender']) ? $applicant_data['gender'] : "Not Provided";
            $civil_status = !empty($applicant_data['civil_status']) ? $applicant_data['civil_status'] : "Not Provided";
            $mobile_no = !empty($applicant_data['mobile_number']) ? $applicant_data['mobile_number'] : "Not Provided";
            $personal_email = !empty($applicant_data['email_address']) ? $applicant_data['email_address'] : "Not Provided";
            $date_of_birth = !empty($applicant_data['date_of_birth']) ? date('F j, Y', strtotime($applicant_data['date_of_birth'])) : "Not Provided";
            $religious_affiliation = !empty($applicant_data['religious_affiliation']) ? $applicant_data['religious_affiliation'] : "Not Provided";
            $classification_type = !empty($applicant_data['classification']) ? ((strtolower($applicant_data['classification']) === 'freshman') ? 'New Student' : 'Transferee') : "Not Provided";
            
            $addr_parts = array_filter([$applicant_data['address_street'], $applicant_data['address_barangay'], $applicant_data['address_city'], $applicant_data['address_province']]);
            $permanent_address = !empty($addr_parts) ? implode(', ', $addr_parts) : "Not Provided";
        }

        if ($guardian_data) {
            $emergency_person = !empty($guardian_data['full_name']) ? $guardian_data['full_name'] : "Not Provided";
            $emergency_no = !empty($guardian_data['emergency_phone']) ? $guardian_data['emergency_phone'] : "Not Provided";
        }
        $emergency_address = ($permanent_address !== "Not Provided") ? $permanent_address : "Not Provided";

    } else {
        error_log("Profile Audit: No record found in 'students' table for student_number $student_number");
    }
} catch (PDOException $e) {
    error_log("Profile Audit Fatal Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if ($_POST['action_type'] === 'update_profile') {
        if (!empty($app_id)) {
            try {
                $update_app = $conn->prepare("UPDATE applicants SET mobile_number = :mobile, email_address = :p_email, address_street = :address WHERE application_id = :app_id");
                $update_app->execute([
                    ':mobile' => trim($_POST['mobile_no']),
                    ':p_email' => trim($_POST['personal_email']),
                    ':address' => trim($_POST['residential_address']),
                    ':app_id' => $app_id
                ]);
                $feedback_msg = "<div class='alert alert-success shadow-sm border-0 mb-4'>Profile details updated successfully.</div>";
                
                $mobile_no = htmlspecialchars(trim($_POST['mobile_no']));
                $personal_email = htmlspecialchars(trim($_POST['personal_email']));
            } catch (PDOException $e) {
                $feedback_msg = "<div class='alert alert-danger shadow-sm border-0 mb-4'>Error syncing profile changes.</div>";
            }
        } else {
            $feedback_msg = "<div class='alert alert-warning shadow-sm border-0 mb-4'>Cannot save edits: No applicant record linked to this account.</div>";
        }
    } elseif ($_POST['action_type'] === 'update_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $feedback_msg = "<div class='alert alert-danger shadow-sm border-0 mb-4'>Passwords do not match.</div>";
        } else {
            try {
                $pass_stmt = $conn->prepare("SELECT password_hash FROM students WHERE student_number = :sn LIMIT 1");
                $pass_stmt->execute([':sn' => $student_number]);
                $user_hash = $pass_stmt->fetchColumn();

                if ($user_hash && password_verify($current_pass, $user_hash)) {
                    $change_stmt = $conn->prepare("UPDATE students SET password_hash = :new_hash WHERE student_number = :sn");
                    $change_stmt->execute([':new_hash' => password_hash($new_pass, PASSWORD_DEFAULT), ':sn' => $student_number]);
                    $feedback_msg = "<div class='alert alert-success shadow-sm border-0 mb-4'>Password updated successfully.</div>";
                } else {
                    $feedback_msg = "<div class='alert alert-danger shadow-sm border-0 mb-4'>Incorrect current password entered.</div>";
                }
            } catch (PDOException $e) {
                $feedback_msg = "<div class='alert alert-danger shadow-sm border-0 mb-4'>Error performing account updates.</div>";
            }
        }
    }
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
} catch (PDOException $e) {}
$display_semester_year = $current_semester . ", AY " . $current_school_year;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Student Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --pcc-blue: #002c5e; --pcc-gold: #f1b813; --pcc-blue-dark: #001d3d; --pcc-gray: #6c757d; }
        body { font-family: 'Source Sans 3', sans-serif; background-color: #f4f6f9 !important; color: #212529; }
        .sidebar-bg { background-color: var(--pcc-blue) !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: var(--pcc-blue) !important; background-color: var(--pcc-gold) !important; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: var(--pcc-blue-dark); }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .sidebar-semester-text { color: #adb5bd; font-size: 11px; font-weight: 500; display: block; margin-top: 4px; }
        .nav-date { font-weight: 600; color: var(--pcc-blue); }
        .btn-pcc-primary { background-color: var(--pcc-blue); color: #fff; font-weight: 600; }
        .btn-pcc-primary:hover { background-color: var(--pcc-blue-dark); color: #fff; }
        .btn-pcc-secondary { background-color: var(--pcc-gold); color: var(--pcc-blue); font-weight: 600; }
        .btn-pcc-secondary:hover { background-color: #dfa50f; color: var(--pcc-blue); }
        
        .inline-panel-box { display: none; margin-top: 15px; border: 1px solid #e3e6f0; border-radius: 12px; background-color: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; animation: slideDownPanel 0.25s ease-out; }
        @keyframes slideDownPanel { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        @media (min-width: 992px) {
            .sidebar-collapse .app-sidebar { margin-left: -250px !important; }
            .sidebar-collapse .app-main, .sidebar-collapse .app-footer, .sidebar-collapse .app-header { margin-left: 0 !important; }
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
                        <span class="d-none d-md-inline"><div class="nav-date" id="liveClockDisplay">Loading Server Time...</div></span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand"
                style="border-right: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link">
                    <img src="../../assets/images/PCC_logo.png" alt="PCC Logo" class="brand-image" />
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
                                <?php echo htmlspecialchars($student_number); ?></div>
                            <div class="status-text small" style="color: #f1b813; margin-top: -3px;">
                                <?php echo htmlspecialchars($formatted_rank); ?></div>
                            <span class="sidebar-semester-text"><?php echo $display_semester_year; ?></span>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header">ACADEMIC HUB</li>
                        <li class="nav-item"><a href="old_student_dashboard.php" class="nav-link "><i
                                    class="nav-icon bi bi-house-door-fill"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_profile.php" class="nav-link sidebar-bg-active"><i
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
                        <li class="nav-item"><a href="old_student_drop.php" class="nav-link"><i
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
                            <h3 class="mb-3 fw-bold">Student Profile</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <?php echo $feedback_msg; ?>

                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm text-center p-4 bg-white" style="border-radius: 16px;">
                            <div class="position-relative d-inline-block mx-auto my-3">
                                <div class="avatar-placeholder d-flex align-items-center justify-content-center text-white shadow"
                                    style="width: 110px; height: 110px; font-size: 44px; background: linear-gradient(135deg, #002c5e, #001d3d); border-radius: 50%;">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <?php if ($is_profile_found && strtolower($enrollment_status) === 'enrolled'): ?>
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-4 border-white rounded-circle p-2" title="Officially Enrolled"></span>
                                <?php else: ?>
                                    <span class="position-absolute bottom-0 end-0 bg-danger border border-4 border-white rounded-circle p-2" title="Not Enrolled / Pending"></span>
                                <?php endif; ?>
                            </div>

                            <h4 class="fw-bold mb-1" style="color: #002c5e;"><?php echo htmlspecialchars($display_name); ?></h4>
                            <p class="text-muted small mb-4 fw-medium" style="letter-spacing: 0.5px;">ID: <?php echo htmlspecialchars($student_number); ?></p>

                            <div class="d-grid gap-2 mb-3">
                                <button type="button" class="btn btn-pcc-primary btn-sm shadow-sm" onclick="toggleInlinePanel('inlineProfileBox', 'inlinePasswordBox')"><i class="fa-solid fa-user-gear me-2"></i>Edit Profile Information</button>
                                <button type="button" class="btn btn-pcc-secondary btn-sm shadow-sm" onclick="toggleInlinePanel('inlinePasswordBox', 'inlineProfileBox')"><i class="fa-solid fa-key me-2"></i>Change System Password</button>
                            </div>

                            <hr class="my-3 opacity-25">

                            <div class="text-start px-2 mt-2">
                                <div class="mb-3">
                                    <label class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">College</label>
                                    <span class="fw-semibold text-secondary" style="font-size: 0.95rem;">College of Computer Studies and Systems</span>
                                </div>
                                <div>
                                    <label class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Institutional Email</label>
                                    <span class="text-break text-primary fw-medium" style="font-size: 0.95rem;"><?php echo htmlspecialchars($email); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="inline-panel-box" id="inlineProfileBox">
                            <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Edit Profile Information</h6>
                                <button type="button" class="btn-close" style="font-size:0.75rem;" onclick="document.getElementById('inlineProfileBox').style.display='none'"></button>
                            </div>
                            <form action="old_student_profile.php" method="POST" class="p-3">
                                <input type="hidden" name="action_type" value="update_profile">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Mobile Phone Number</label>
                                    <input type="text" name="mobile_no" class="form-control form-control-sm" value="<?php echo ($mobile_no !== 'Not Provided') ? htmlspecialchars($mobile_no) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Personal Email Address</label>
                                    <input type="email" name="personal_email" class="form-control form-control-sm" value="<?php echo ($personal_email !== 'Not Provided') ? htmlspecialchars($personal_email) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Residential Street Address</label>
                                    <textarea name="residential_address" class="form-control form-control-sm" rows="2"><?php echo ($permanent_address !== 'Not Provided') ? htmlspecialchars($permanent_address) : ''; ?></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('inlineProfileBox').style.display='none'">Cancel</button>
                                    <button type="submit" class="btn btn-pcc-primary btn-sm px-3">Save</button>
                                </div>
                            </form>
                        </div>

                        <div class="inline-panel-box" id="inlinePasswordBox">
                            <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-shield-lock me-2 text-danger"></i>Change Account Password</h6>
                                <button type="button" class="btn-close" style="font-size:0.75rem;" onclick="document.getElementById('inlinePasswordBox').style.display='none'"></button>
                            </div>
                            <form action="old_student_profile.php" method="POST" class="p-3">
                                <input type="hidden" name="action_type" value="update_password">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Current Verification Password</label>
                                    <input type="password" name="current_password" class="form-control form-control-sm" placeholder="Enter current portal password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">New Security Password</label>
                                    <input type="password" name="new_password" class="form-control form-control-sm" placeholder="Create robust security passcode" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="Re-type security passcode" required>
                                </div>
                                <div class="d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('inlinePasswordBox').style.display='none'">Close</button>
                                    <button type="submit" class="btn btn-danger btn-sm px-3">Modify Key</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3"><i class="fa-solid fa-graduation-cap fa-lg"></i></div>
                                <h5 class="fw-bold m-0 text-uppercase" style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Academic Information</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Registration Status</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary d-flex align-items-center justify-content-between">
                                        <?php if ($is_profile_found && strtolower($enrollment_status) === 'enrolled'): ?>
                                            <span class="fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Officially Enrolled</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>Not Enrolled</span>
                                        <?php endif; ?>
                                        <small class="text-muted font-monospace" style="font-size: 11px;"><?php echo htmlspecialchars($current_semester); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Curriculum Year / Classification</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-semibold text-secondary">
                                        <?php echo htmlspecialchars($formatted_rank); ?> (<?php echo htmlspecialchars($classification); ?>)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 bg-primary-subtle rounded-3 text-primary me-3"><i class="fa-solid fa-address-book fa-lg"></i></div>
                                <h5 class="fw-bold m-0 text-uppercase" style="color: #002c5e; font-size: 0.95rem; letter-spacing: 0.5px;">Contact & Personal Information</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Student Full Name</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary"><?php echo htmlspecialchars($display_name); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Nationality</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary"><?php echo htmlspecialchars($nationality); ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Gender</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary"><?php echo htmlspecialchars($gender); ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Civil Status</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary"><?php echo htmlspecialchars($civil_status); ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Mobile Number</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary font-monospace">
                                        <i class="bi bi-telephone me-2 text-muted"></i><?php echo htmlspecialchars($mobile_no); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Personal Email</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-envelope me-2 text-muted"></i><?php echo htmlspecialchars($personal_email); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Street Address</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i><?php echo htmlspecialchars($permanent_address); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Date of Birth</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-calendar-event me-2 text-muted"></i><?php echo htmlspecialchars($date_of_birth); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Religious Affiliation</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-book me-2 text-muted"></i><?php echo htmlspecialchars($religious_affiliation); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-semibold small mb-1">Student Type</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-person-badge me-2 text-muted"></i><?php echo htmlspecialchars($classification_type); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 16px;">
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="p-2 rounded-3 me-3" style="background-color: #fde8e8; color: #8b0000;"><i class="fa-solid fa-phone-flip fa-lg"></i></div>
                                <h5 class="fw-bold m-0 text-uppercase" style="color: #8b0000; font-size: 0.95rem; letter-spacing: 0.5px;">Emergency Details</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Emergency Contact Person</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary fw-medium text-secondary"><?php echo htmlspecialchars($emergency_person); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-1">Contact Number</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary font-monospace">
                                        <i class="bi bi-telephone me-2 text-muted"></i><?php echo htmlspecialchars($emergency_no); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted fw-semibold small mb-1">Contact Address</label>
                                    <div class="p-2.5 px-3 border border-light-subtle rounded-3 bg-body-tertiary text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i><?php echo htmlspecialchars($emergency_address); ?>
                                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }

        function toggleInlinePanel(showId, hideId) {
            const showPanel = document.getElementById(showId);
            const hidePanel = document.getElementById(hideId);

            hidePanel.style.display = 'none';
            if (showPanel.style.display === 'block') {
                showPanel.style.display = 'none';
            } else {
                showPanel.style.display = 'block';
            }
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