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
    error_log("Grades Data Fetch Error: " . $e->getMessage());
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

try {
    $grades_query = $conn->prepare("
        SELECT 
            s.subject_code, 
            s.descriptive_title, 
            s.units, 
            e.midterm_grade, 
            e.final_grade, 
            e.remarks,
            e.school_year
        FROM enrollments e
        JOIN subjects s ON e.subject_id = s.id
        WHERE e.student_id = :student_id 
          AND e.semester = :sem 
          AND e.school_year = :sy
    ");
    $grades_query->execute([
        ':student_id' => $student_id,
        ':sem' => $current_semester,
        ':sy' => $current_school_year
    ]);
    $enrolled_subjects = $grades_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$first_name = $student_data['first_name'] ?: ($applicant_data['first_name'] ?? 'Not Provided');
$middle_name = $student_data['middle_name'] ?: ($applicant_data['middle_name'] ?? '');
$last_name = $student_data['last_name'] ?: ($applicant_data['last_name'] ?? 'Not Provided');
$suffix = $student_data['suffix'] ?: ($applicant_data['suffix'] ?? '');

$display_name = trim(preg_replace('/\s+/', ' ', "$first_name $middle_name $last_name $suffix"));

$course_code = $student_data['current_course'] ?? 'TBA';
$year_level_raw = intval($student_data['year_level'] ?? 1);
$suffix_str = ($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : (($year_level_raw == 4) ? 'th' : '')));
$formatted_rank = ($year_level_raw > 0) ? "{$course_code} - {$year_level_raw}{$suffix_str} Year" : "Unassigned";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Grades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
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

        .content-card {
            border: none;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .btn-pcc-primary {
            background-color: var(--pcc-blue);
            color: #fff;
        }

        .btn-pcc-primary:hover {
            background-color: var(--pcc-blue-dark);
            color: #fff;
        }

        .status-badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }

            .app-header,
            .app-sidebar,
            footer,
            .btn,
            .card-header .badge,
            .info-box {
                display: none !important;
            }

            .app-main {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .content-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                border: 1px solid #ced4da !important;
                padding: 8px !important;
                color: #000000 !important;
            }

            .print-header-canvas {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
            }

            .print-student-info {
                display: block !important;
                margin-bottom: 25px;
                padding: 15px;
                border: 1px solid #dee2e6;
                background-color: #f8f9fa !important;
            }
        }

        .print-header-canvas,
        .print-student-info {
            display: none;
        }

        .btn,
        .badge {
            border-radius: 0px !important;
        }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
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
                        <li class="nav-item"><a href="old_student_grades.php" class="nav-link sidebar-bg-active"><i
                                    class="nav-icon bi bi-journal-check"></i>
                                <p>Grades</p>
                            </a></li>
                        <li class="nav-item"><a href="old_student_drop.php" class="nav-link"><i
                                    class="nav-icon bi bi-gear-fill"></i>
                                <p>Dropping of Subject</p>
                            </a></li>
                        <li class="nav-item">
                            <a href="old_student_login.php" class="nav-link text-danger"
                                onclick="return confirm('Are you sure you want to log out?');">
                                <i class="nav-icon bi bi-box-arrow-left text-danger"></i>
                                <p class="text-danger fw-bold">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main p-4">
            <div class="print-header-canvas">
                <img src="../../assets/images/PCC_logo.png" alt="Logo" style="max-height: 65px; margin-bottom: 10px;">
                <h4 class="fw-bold mb-0">POBLACION CENTRAL COLLEGE</h4>
                <p class="small text-uppercase text-secondary tracking-wider mb-1">Office of the School Registrar</p>
                <div class="fw-bold small text-dark mt-2">OFFICIAL TERM GRADE REPORT SLIP</div>
                <div class="small text-muted">
                    <?= htmlspecialchars($current_semester . " | AY " . $current_school_year); ?>
                </div>
            </div>

            <div class="print-student-info rounded-1">
                <div class="row g-2 small text-dark">
                    <div class="col-6"><strong>Student Number:</strong> <?= htmlspecialchars($student_number); ?></div>
                    <div class="col-6"><strong>Date Generated:</strong> <?= date('F j, Y - h:i A'); ?></div>
                    <div class="col-6"><strong>Full Name:</strong> <?= htmlspecialchars($display_name); ?></div>
                    <div class="col-6"><strong>Class Program Standing:</strong>
                        <?= htmlspecialchars($student_data['course'] ?? 'BSIT') . " - Track " . htmlspecialchars($student_data['year_level'] ?? '3rd Year'); ?>
                    </div>
                </div>
            </div>

            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold">Grades</h3>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <button class="btn btn-pcc-primary btn-sm fw-semibold rounded-pill px-4 py-2"
                                onclick="window.print();">
                                <i class="bi bi-printer-fill me-2"></i> Print Official Grade Slip
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <div
                                class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded content-card h-100">
                                <span
                                    class="info-box-icon bg-primary text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 55px; height: 55px; font-size: 24px;"><i
                                        class="bi bi-trophy-fill"></i></span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Cumulative GPA</span>
                                    <h3 class="fw-bold mb-0 text-dark">
                                        <?php echo htmlspecialchars($student_data['gpa'] ?? '—'); ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div
                                class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded content-card h-100">
                                <span
                                    class="info-box-icon bg-success text-white d-flex align-items-center justify-content-center rounded"
                                    style="width: 55px; height: 55px; font-size: 24px;"><i
                                        class="bi bi-check-circle-fill"></i></span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Academic
                                        Standing</span>
                                    <h3 class="fw-bold mb-0 text-success">Regular</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-5">
                            <div class="card content-card h-100">
                                <div
                                    class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between gap-3">
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        <i class="bi bi-file-text-fill me-2 text-primary"></i>Active Semester Grades
                                    </h5>
                                    <div>
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-bold font-monospace fs-7">
                                            <?= htmlspecialchars($current_semester . " | AY " . $current_school_year); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary small text-uppercase">
                                                <tr>
                                                    <th class="ps-4 py-3">Course Code</th>
                                                    <th class="py-3">Descriptive Title</th>
                                                    <th class="text-center py-3">Units</th>
                                                    <th class="text-center py-3">Midterm</th>
                                                    <th class="text-center py-3">Finals</th>
                                                    <th class="text-center py-3">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark small">
                                                <?php
                                                $total_units = 0.0;
                                                if (!empty($enrolled_subjects)):
                                                    foreach ($enrolled_subjects as $row):
                                                        $is_dropped = ($row['remarks'] === 'Dropped');

                                                        if (!$is_dropped) {
                                                            $total_units += (float) $row['units'];
                                                        }
                                                        ?>
                                                        <tr
                                                            class="<?= $is_dropped ? 'table-light text-decoration-line-through text-muted' : ''; ?>">
                                                            <td
                                                                class="ps-4 fw-bold <?= $is_dropped ? 'text-secondary' : 'text-primary'; ?>">
                                                                <?php echo htmlspecialchars($row['subject_code']); ?>
                                                            </td>
                                                            <td class="fw-medium">
                                                                <?php echo htmlspecialchars($row['descriptive_title']); ?>
                                                                <?= $is_dropped ? ' <small class="text-danger fw-bold">(Official Drop)</small>' : ''; ?>
                                                            </td>
                                                            <td class="text-center fw-semibold text-muted">
                                                                <?php echo $is_dropped ? '0.0' : number_format($row['units'], 1); ?>
                                                            </td>
                                                            <td class="text-center fw-semibold">
                                                                <?php echo ($is_dropped || !$row['midterm_grade']) ? '—' : htmlspecialchars($row['midterm_grade']); ?>
                                                            </td>
                                                            <td class="text-center fw-semibold">
                                                                <?php echo ($is_dropped || !$row['final_grade']) ? '—' : htmlspecialchars($row['final_grade']); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($is_dropped): ?>
                                                                    <span
                                                                        class="badge bg-danger-subtle text-danger status-badge">Dropped</span>
                                                                <?php elseif (!empty($row['midterm_grade']) || !empty($row['final_grade'])): ?>
                                                                    <span
                                                                        class="badge bg-success-subtle text-success status-badge"><?php echo htmlspecialchars($row['remarks']); ?></span>
                                                                <?php else: ?>
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-secondary status-badge">No
                                                                        Grade</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    endforeach;
                                                else:
                                                    ?>
                                                    <tr>
                                                        <td colspan="6"
                                                            class="text-center class-record-empty py-5 text-muted">
                                                            <i class="bi bi-folder-x fs-3 d-block text-secondary mb-2"></i>
                                                            No enrolled subjects found for the current semester.
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot class="bg-light border-top">
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold text-secondary py-3">Term
                                                        Summary:</td>
                                                    <td class="text-center fw-bold text-dark py-3">
                                                        <?php echo number_format($total_units, 1); ?>
                                                    </td>
                                                    <td colspan="1" class="text-end fw-bold text-secondary py-3">Term
                                                        GPA:</td>
                                                    <td class="text-center fw-bold fs-6 text-muted py-3">—</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script>
        function toggleSidebarMenu(event) { event.preventDefault(); document.body.classList.toggle('sidebar-collapse'); }

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