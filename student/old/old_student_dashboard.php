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
    error_log("Dashboard Data Fetch Error: " . $e->getMessage());
}

$is_profile_found = $student_data ? true : false;

$first_name = $student_data['first_name'] ?: ($applicant_data['first_name'] ?? 'Not Provided');
$middle_name = $student_data['middle_name'] ?: ($applicant_data['middle_name'] ?? '');
$last_name = $student_data['last_name'] ?: ($applicant_data['last_name'] ?? 'Not Provided');
$suffix = $student_data['suffix'] ?: ($applicant_data['suffix'] ?? '');

$display_name = trim(preg_replace('/\s+/', ' ', "$first_name $middle_name $last_name $suffix"));

$email = $student_data['email'] ?? ($applicant_data['email_address'] ?? ($student_number . "@pcc.edu.ph"));
$course_code = $student_data['current_course'] ?? ($applicant_data['preferred_program'] ?? 'TBA');
$year_level_raw = intval($student_data['year_level'] ?? 1);
$classification = !empty($student_data['classification']) ? $student_data['classification'] : 'Regular';
$enrollment_status = !empty($student_data['enrollment_status']) ? $student_data['enrollment_status'] : 'Not Enrolled';

$suffix_str = ($year_level_raw == 1) ? 'st' : (($year_level_raw == 2) ? 'nd' : (($year_level_raw == 3) ? 'rd' : (($year_level_raw == 4) ? 'th' : '')));
$formatted_rank = ($year_level_raw > 0) ? "{$course_code} - {$year_level_raw}{$suffix_str} Year" : "Unassigned / Pending";

$announcements_list = [];
try {
    $notice_stmt = $conn->query("SELECT *, UNIX_TIMESTAMP(created_at) as unix_time FROM notices WHERE status = 'Published' ORDER BY id DESC LIMIT 5");
    if ($notice_stmt) {
        $announcements_list = $notice_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $announcements_list = [];
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
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link class="rtl_container" rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
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

        .portal-panel-card {
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            background: #fff;
            overflow: hidden;
        }

        .panel-header-bar {
            background-color: #f8f9fc;
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-header-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--pcc-blue);
            margin: 0;
        }

        .panel-body-content {
            padding: 1.25rem;
            color: #212529;
        }

        .notice-row-container {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .notice-title-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
            display: block;
        }

        .notice-body-content {
            font-weight: 500;
            color: #495057;
            display: block;
        }

        .notice-image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 132%;
            margin-top: 15px;
        }

        .notice-image-banner {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
            border: 1px solid #eaecf4;
            margin: 0 auto;
        }

        .quick-access-btn {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            font-weight: 600;
            text-align: left;
            height: 100%;
        }

        .quick-access-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 44, 94, 0.08) !important;
            background-color: #fafbfc;
        }

        .bg-pcc-blue {
            background-color: var(--pcc-blue) !important;
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
                        <li class="nav-item"><a href="old_student_dashboard.php" class="nav-link sidebar-bg-active"><i
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
            <div class="container-fluid">

                <div class="portal-panel-card" id="announcementsFeedCard">
                    <div class="panel-header-bar">
                        <h6 class="panel-header-title text-secondary"><i
                                class="bi bi-bell-fill me-2 text-primary"></i>Recent Board Announcements</h6>
                        <button type="button" class="btn btn-xs btn-link text-muted p-0"
                            onclick="closeDashboardBox('announcementsFeedCard')"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="panel-body-content p-0 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-dark">
                                <tbody>
                                    <?php if (empty($announcements_list)): ?>
                                        <tr>
                                            <td class="ps-4 py-4 text-muted small"><i class="bi bi-info-circle me-2"></i>No
                                                custom bulletin board announcements published yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($announcements_list as $notice): ?>
                                            <?php
                                            $headline = htmlspecialchars($notice['title']);
                                            $body_text = htmlspecialchars($notice['content']);

                                            $img_url = "";
                                            if (!empty($notice['image_path'])) {
                                                $clean_name = basename($notice['image_path']);
                                                $img_url = "../../uploads/notices/" . $clean_name;
                                            }

                                            $time_raw = intval($notice['unix_time'] ?? time());
                                            $elapsed = (time() - $time_raw);

                                            if ($elapsed < 2) {
                                                $time_string = "Just now";
                                            } elseif ($elapsed < 60) {
                                                $time_string = $elapsed . " seconds ago";
                                            } elseif ($elapsed < 3600) {
                                                $time_string = floor($elapsed / 60) . " mins ago";
                                            } elseif ($elapsed < 86400) {
                                                $time_string = floor($elapsed / 3600) . " hours ago";
                                            } else {
                                                $time_string = date('M d, Y', $time_raw);
                                            }
                                            ?>
                                            <tr>
                                                <td class="ps-4 py-3" style="width: 75%;">
                                                    <div class="notice-row-container">
                                                        <div>
                                                            <span class="notice-title-text"><?php echo $headline; ?></span>
                                                            <span class="notice-body-content"><?php echo $body_text; ?></span>
                                                        </div>
                                                        <?php if (!empty($img_url)): ?>
                                                            <div class="notice-image-wrapper">
                                                                <img src="<?php echo $img_url; ?>" alt="Notice Board Image"
                                                                    class="notice-image-banner shadow-sm">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="pe-4 text-end text-muted small"
                                                    style="width: 25%; vertical-align: top; padding-top: 1rem;"><i
                                                        class="bi bi-clock me-1"></i> <?php echo $time_string; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="portal-panel-card" id="welcomePortalCard">
                    <div class="panel-header-bar">
                        <h6 class="panel-header-title text-secondary"><i
                                class="bi bi-star-fill me-2 text-warning"></i>Welcome!</h6>
                        <button type="button" class="btn btn-xs btn-link text-muted p-0"
                            onclick="closeDashboardBox('welcomePortalCard')"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="panel-body-content bg-white py-3 text-dark">
                        <p class="mb-0 fw-medium">Welcome to the PCC Student Portal</p>
                    </div>
                </div>

                <div class="portal-panel-card border-0 overflow-hidden shadow-sm" id="enrollmentStatusCard">
                    <div class="bg-pcc-blue text-white p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1" style="font-size: 1.15rem;">Enrollment Status!</h5>
                            <div class="small opacity-90">Your latest enrollment was First Semester 2026</div>
                            <div class="small fw-semibold mt-1">Current active semester [
                                <?php echo htmlspecialchars($current_semester . " " . $current_school_year); ?> ]
                            </div>
                        </div>
                        <button type="button" class="btn btn-link text-white opacity-75 p-0 align-self-start"
                            onclick="closeDashboardBox('enrollmentStatusCard')"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>

                <div class="portal-panel-card">
                    <div class="panel-header-bar">
                        <h6 class="panel-header-title text-secondary"><i
                                class="bi bi-link-45deg me-2 text-primary"></i>Quick Navigation Links</h6>
                    </div>
                    <div class="panel-body-content bg-white">
                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3">
                            <div class="col">
                                <a href="old_student_profile.php"
                                    class="btn bg-white w-100 py-3 rounded shadow-sm quick-access-btn text-dark text-decoration-none">
                                    <div class="d-flex align-items-center px-2">
                                        <i class="bi bi-file-earmark-person-fill text-primary fs-3 me-3"></i>
                                        <div>
                                            <div class="fw-bold small">Profile</div><span class="text-muted small"
                                                style="font-size:10px;">View Info &raquo;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="old_student_enrollment.php"
                                    class="btn bg-white w-100 py-3 rounded shadow-sm quick-access-btn text-dark text-decoration-none">
                                    <div class="d-flex align-items-center px-2">
                                        <i class="bi bi-laptop text-warning fs-3 me-3"></i>
                                        <div>
                                            <div class="fw-bold small">Enrollment</div><span class="text-muted small"
                                                style="font-size:10px;">Register &raquo;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="#"
                                    class="btn bg-white w-100 py-3 rounded shadow-sm quick-access-btn text-dark text-decoration-none">
                                    <div class="d-flex align-items-center px-2">
                                        <i class="bi bi-calendar-week-fill text-success fs-3 me-3"></i>
                                        <div>
                                            <div class="fw-bold small">Schedules</div><span class="text-muted small"
                                                style="font-size:10px;">Class Times &raquo;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="old_student_grades.php"
                                    class="btn bg-white w-100 py-3 rounded shadow-sm quick-access-btn text-dark text-decoration-none">
                                    <div class="d-flex align-items-center px-2">
                                        <i class="bi bi-journal-check text-danger fs-3 me-3"></i>
                                        <div>
                                            <div class="fw-bold small">Grades</div><span class="text-muted small"
                                                style="font-size:10px;">Transcripts &raquo;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="old_student_drop.php"
                                    class="btn bg-white w-100 py-3 rounded shadow-sm quick-access-btn text-dark text-decoration-none">
                                    <div class="d-flex align-items-center px-2">
                                        <i class="bi bi-gear-fill text-info fs-3 me-3"></i>
                                        <div>
                                            <div class="fw-bold small">Dropping</div><span class="text-muted small"
                                                style="font-size:10px;">Modify Units &raquo;</span>
                                        </div>
                                    </div>
                                </a>
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

        function closeDashboardBox(elementId) {
            const target = document.getElementById(elementId);
            if (target) {
                target.style.opacity = '0';
                setTimeout(() => {
                    target.style.display = 'none';
                }, 300);
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