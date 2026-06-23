<?php
session_start();
require_once '../../config/database_connect.php';
date_default_timezone_set('Asia/Manila');

$toast_notification = "";

try {
    $settings_query = $conn->query("SELECT old_student_enrollment FROM system_settings LIMIT 1");
    $portal_config = $settings_query->fetch(PDO::FETCH_ASSOC);
    if ($portal_config && $portal_config['old_student_enrollment'] === 'Closed') {
        echo "<div style='min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa; font-family: sans-serif;'><div style='text-align: center; max-width: 450px; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'><h2 style='color: #dc3545; margin-bottom: 10px;'>Portal Access Closed</h2><p style='color: #6c757d; line-height: 1.5;'>The Old Student Portal registration and enrollment access pipeline is currently closed or locked by university administration.</p><br><a href='../../index.php' style='display: inline-block; padding: 8px 20px; background: #002c5e; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px;'>Return Home</a></div></div>";
        exit();
    }
} catch (PDOException $e) {
}

$backgrounds = [
    '../../assets/images/PCC_Main_Background.png',
    '../../assets/images/PCC_BG2.png'
];
$random_bg = $backgrounds[array_rand($backgrounds)];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = trim($_POST['student_no']);
    $email = trim($_POST['email']);
    $access_code = trim($_POST['access_code']);

    try {
        $check_id_stmt = $conn->prepare("SELECT * FROM students WHERE student_number = :num LIMIT 1");
        $check_id_stmt->execute([':num' => $student_no]);
        $student = $check_id_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Login Denied!</strong> Incorrect Student ID.</div>";
        } elseif ($student['email'] !== $email) {
            $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Login Denied!</strong> Incorrect Email Address.</div>";
        } elseif (!password_verify($access_code, $student['password_hash'])) {
            $toast_notification = "<div class='alert alert-danger position-fixed bottom-0 end-0 m-3 z-3 shadow'><strong>Login Denied!</strong> Incorrect Student Access Code.</div>";
        } else {
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_number'] = $student['student_number'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];

            header("Location: old_student_dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $toast_notification = "<div class='alert alert-danger m-3'>Authentication Fault Loop: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

try {
    // 1. Fetch system maintenance settings from database
    $maintenance_check = $conn->query("SELECT system_maintenance FROM system_settings LIMIT 1");
    $maintenance_config = $maintenance_check->fetch(PDO::FETCH_ASSOC);

    if ($maintenance_config && strtolower(trim($maintenance_config['system_maintenance'])) === 'enabled') {

        // 2. Safely fallback or pick a random background image so the CSS template doesn't throw errors
        $maintenance_backgrounds = [
            '../../assets/images/PCC_Main_Background.png',
            '../../assets/images/PCC_BG2.png'
        ];
        $selected_maintenance_bg = $maintenance_backgrounds[array_rand($maintenance_backgrounds)];
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>PCC | System Under Maintenance</title>
            <!-- Included Bootstrap Icons so the "Go back to Home" arrow renders beautifully -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
                crossorigin="anonymous" />
            <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
            <style>
                body {
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    /* Stack the card and home link correctly */
                    align-items: center;
                    justify-content: center;
                    background-image: url('<?php echo $selected_maintenance_bg; ?>');
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    background-size: cover;
                    font-family: sans-serif;
                    margin: 0;
                }

                .maintenance-card {
                    text-align: center;
                    width: 90%;
                    max-width: 480px;
                    padding: 40px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                }

                h2 {
                    color: #002c5e;
                    margin: 0 0 12px 0;
                    font-size: 26px;
                    font-weight: 700;
                }

                p {
                    color: #6c757d;
                    line-height: 1.6;
                    font-size: 14px;
                    margin-bottom: 0;
                }

                .badge {
                    display: inline-block;
                    padding: 6px 14px;
                    background: #fff3cd;
                    color: #856404;
                    font-size: 12px;
                    font-weight: 700;
                    border-radius: 50px;
                    text-transform: uppercase;
                    margin-bottom: 20px;
                }

                .home-link-container {
                    text-align: center;
                    margin-top: 20px;
                }

                .home-link-container a {
                    color: white;
                    text-decoration: none;
                    font-size: 14px;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.9);
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    transition: opacity 0.2s;
                }

                .home-link-container a:hover {
                    opacity: 0.85;
                }
            </style>
        </head>

        <body>
            <div class="maintenance-card">
                <div class="badge">Down For Maintenance</div>
                <h2>Portal Temporarily Offline</h2>
                <p>The Student Portal is currently undergoing routine maintenance updates.
                    System functionality will be fully restored shortly.</p>
            </div>

            <div class="home-link-container">
                <a href="../../index.php">
                    <i class="bi bi-arrow-left-short fs-5"></i>Go back to Home
                </a>
            </div>
        </body>

        </html>
        <?php
        exit();
    }
} catch (PDOException $e) {
    // Fails silently to prevent exposing full database errors to public users
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('<?php echo $random_bg; ?>');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }

        @media (max-width: 900px) {
            body {
                background-image: url('../../assets/images/PCC_Smaller_BG.png');
                background-attachment: scroll;
            }
        }
    </style>
</head>

<body class="login-page bg-body-secondary">
    <?php echo $toast_notification; ?>

    <div class="login-box">
        <div class="login-logo" style="color: white;">
            <i><img src="../../assets/images/PCC_logo.png" alt="PCC Logo"
                    style="width: 100px; height: 100px; text-shadow: 1px 1px 3px black;"></i>
            <br>
            <p
                style="font-size: 25px; font-weight: bold; margin-bottom: -10px; margin-top: 5px; text-shadow: 1px 1px 3px black;">
                POBLACION CENTRAL COLLEGE</p>
            <p style="font-size: 15px; margin-top: 5px; text-shadow: 1px 1px 3px black;">Home of the PCC Chiefs</p>
        </div>
        <div class="card border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body login-card-body bg-white">
                <p class="login-box-msg text-dark" style="font-size:30px; margin-bottom: -25px;"><b>Student</b> Portal
                </p>
                <p class="login-box-msg text-secondary">Sign in to start your session</p>

                <form action="old_student_login.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="student_no" class="form-control" placeholder="Student ID" required />
                        <div class="input-group-text bg-light border-start-0 text-secondary">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="PCC Email Address"
                            required />
                        <div class="input-group-text bg-light border-start-0 text-secondary">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="access_code" class="form-control" placeholder="Student Access Code"
                            required />
                        <div class="input-group-text bg-light border-start-0 text-secondary">
                            <i class="bi bi-key-fill"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary fw-semibold shadow-sm"
                                    style="background-color: #002c5e; border-color: #002c5e;">Sign In</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="text-center mt-3">
        <a href="../../index.php" class="text-decoration-none small"
            style="color: white; text-shadow: 1px 1px 4px rgba(0,0,0,0.8); font-weight: 600; letter-spacing: 0.5px;">
            <i class="bi bi-arrow-left-short me-1"></i>Go back to Home
        </a>
    </div>
</body>

</html>