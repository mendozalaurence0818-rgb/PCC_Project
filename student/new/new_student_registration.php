<?php
require_once '../../config/database_connect.php';

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
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
            <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
            <style>
                body {
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column; /* Stack the card and home link correctly */
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
                <p>The Admission Portal is currently undergoing routine maintenance updates. System functionality will be fully restored shortly.</p>
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

try {
    $check_settings_stmt = $conn->query("SELECT enrollment_status FROM system_settings LIMIT 1");
    $system_config = $check_settings_stmt->fetch(PDO::FETCH_ASSOC);

    if ($system_config && strtolower(trim($system_config['enrollment_status'])) === 'closed') {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admissions Closed | PCC</title>
            <link rel="stylesheet" href="../../assets/css/adminlte.css" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
            <style>
                body {
                    background-image: url('../../assets/images/PCC_Main_Background.png');
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    background-size: cover;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .closed-card {
                    max-width: 500px;
                    width: 100%;
                    border: none;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                }

                .icon-circle {
                    width: 70px;
                    height: 70px;
                    background-color: #fff3cd;
                    color: #856404;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    font-size: 32px;
                }
            </style>
        </head>

        <body>
            <div class="card closed-card text-center p-5 bg-white">
                <div class="card-body">
                    <div class="icon-circle"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <h3 class="fw-bold text-danger mb-3">Admissions are Closed</h3>
                    <p class="text-secondary mb-4">We are currently not accepting new student applications for this academic
                        term. Please check back later or check the facebook page for upcoming enrollment updates.</p>
                    <a href="../../index.php" class="btn btn-sm btn-outline-primary px-4">Return to Home</a>
                </div>
            </div>
        </body>

        </html>
        <?php
        exit();
    }
} catch (PDOException $e) {
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Online Admission Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <style>
        body {
            background-image: url('../../assets/images/PCC_Main_Background.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
        }

        .btn-primary-custom {
            background-color: #0D6EFD;
            border-color: #0D6EFD;
            color: #FFFFFF;
            font-weight: 700;
            height: 48px;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: #0B5ED7;
            border-color: #0D6EFD;
            color: #FFFFFF;
        }

        .btn-outline-custom {
            background-color: #FFFFFF;
            color: #0D6EFD;
            border: 1px solid #0D6EFD;
            font-weight: 700;
            height: 48px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover {
            background-color: #0D6EFD;
            color: #FFFFFF;
        }
    </style>
</head>

<body>

    <div style="width: 100%; max-width: 1200px;">

        <div class="text-center" style="color: #FFFFFF; margin-bottom: 30px;">
            <i><img src="../../assets/images/PCC_logo.png" alt="PCC Logo" style="width: 85px; height: 85px;"></i>
            <br>
            <p
                style="font-size: 24px; font-weight: bold; margin-bottom: -5px; margin-top: 10px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                POBLACION CENTRAL COLLEGE
            </p>
            <p style="font-size: 15px; margin-top: 5px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                College Online Admission Portal
            </p>
            <div class="mt-1">
                <a href="../../index.php" class="text-decoration-none small"
                    style="color: white; text-shadow: 1px 1px 4px rgba(0,0,0,0.8); font-weight: 600; letter-spacing: 0.5px;">
                    <i class="bi bi-arrow-left-short me-1"></i>Go back to Home
                </a>
            </div>
        </div>

        <div class="row g-3">

            <div class="col-lg-5 d-flex flex-column justify-content-between">

                <div class="card shadow border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5
                            style="font-weight: 800; margin-bottom: 20px; text-align: center; color: #212529; letter-spacing: 0.3px;">
                            Start New Application
                        </h5>

                        <form id="frmAdmissionApplication" action="NEW_STUDENT_ADMISSION/new_student_profile.php"
                            method="GET">

                            <input type="hidden" id="hdnClassification" name="classification" value="">
                            <input type="hidden" id="hdnYearLevel" name="year_level" value="1">
                            <input type="hidden" id="hdnStudentStatus" name="student_status" value="New">

                            <div class="mb-3">
                                <label
                                    style="font-size: 14px; font-weight: 700; color: #495057; margin-bottom: 6px; display: block;">
                                    Applicant Classification
                                </label>
                                <select id="selClassification" class="form-select form-select-lg"
                                    style="font-size: 15px; border-color: #CED4DA; cursor: pointer; height: 48px;"
                                    required>
                                    <option value="" disabled selected>Select Classification</option>
                                    <option value="Freshman">Incoming College Freshman</option>
                                    <option value="Transferee">College Transferee</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label
                                    style="font-size: 14px; font-weight: 700; color: #495057; margin-bottom: 6px; display: block;">
                                    Academic Term
                                </label>
                                <select id="selAcademicTerm" class="form-select form-select-lg"
                                    style="font-size: 15px; border-color: #CED4DA; background-color: #e9ecef; color: #6c757d; cursor: not-allowed; height: 48px;"
                                    disabled>
                                    <option value="1st Semester" selected>1st Semester, AY 2026-2027</option>
                                </select>
                                <input type="hidden" name="academic_term" value="1st Semester">
                            </div>

                            <button type="button" onclick="executeAdmissionSubmit()"
                                class="btn w-100 btn-lg d-flex align-items-center justify-content-center btn-primary-custom">
                                <i class="bi bi-pencil-square me-2"></i>Apply Now
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow border-0" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5
                            style="font-weight: 800; margin-bottom: 10px; text-align: center; color: #212529; letter-spacing: 0.3px;">
                            Check Application
                        </h5>
                        <p style="font-size: 14px; color: #6C757D; text-align: center; margin-bottom: 20px;">
                            Already applied? Check your admission status here.
                        </p>
                        <form action="new_student_status.php" method="post">
                            <div class="mb-3">
                                <input required type="text" name="reference_number" class="form-control form-control-lg"
                                    placeholder="Application Reference Number"
                                    style="font-size: 15px; border-color: #CED4DA; height: 48px;">
                            </div>
                            <button type="submit"
                                class="btn w-100 btn-lg d-flex align-items-center justify-content-center btn-outline-custom">
                                <i class="bi bi-search me-2"></i>Check Status
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card h-100 shadow border-0" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 35px;">
                        <h4 style="color: #0D6EFD; font-weight: 800; margin-bottom: 20px;"
                            class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>Admission Guidelines
                        </h4>
                        <p style="font-size: 15px; line-height: 1.6; color: #212529; margin-bottom: 20px;">
                            Welcome to the PCC College Online Admission Portal. Please read the instructions carefully
                            before proceeding with your application.
                        </p>

                        <h5 style="font-weight: 700; color: #495057; margin-top: 25px; font-size: 16px;">
                            For Incoming College Freshmen
                        </h5>
                        <ul
                            style="font-size: 14px; line-height: 1.7; color: #555555; margin-bottom: 20px; padding-left: 20px;">
                            <li style="margin-bottom: 6px;">Must be an official graduate of Senior High School.</li>
                            <li style="margin-bottom: 6px;">Prepare a scanned copy or clear photo of your Grade 12
                                Report Card (Form 138).</li>
                            <li style="margin-bottom: 6px;">Have a recent 2x2 ID picture with a white background ready
                                for upload.</li>
                        </ul>

                        <h5 style="font-weight: 700; color: #495057; margin-top: 25px; font-size: 16px;">
                            For College Transferees
                        </h5>
                        <ul
                            style="font-size: 14px; line-height: 1.7; color: #555555; margin-bottom: 20px; padding-left: 20px;">
                            <li style="margin-bottom: 6px;">Prepare a clear scanned copy of your Transcript of Records
                                (TOR) or certified Copy of Grades.</li>
                            <li style="margin-bottom: 6px;">Secure a valid Certificate of Good Moral Character from your
                                previous academic institution.</li>
                        </ul>

                        <div
                            style="background-color: #e7e7e7; padding: 18px 20px; border-radius: 8px; margin-top: 30px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);">
                            <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #43484d;">
                                <strong style="color: #0D6EFD;">Important Note:</strong> Ensure all provided information
                                is accurate and true. Any falsification of documents or system entry data will result in
                                the automatic invalidation of your application records.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script>
        function executeAdmissionSubmit() {
            const selectField = document.getElementById('selClassification');
            const termField = document.getElementById('selAcademicTerm');

            const hdnClassification = document.getElementById('hdnClassification');
            const hdnYearLevel = document.getElementById('hdnYearLevel');
            const hdnStudentStatus = document.getElementById('hdnStudentStatus');

            if (selectField.value === "") {
                alert("Please select your classification before continuing.");
                return;
            }

            const choice = selectField.value;
            const currentTerm = termField.value;

            if (choice === "Freshman") {
                hdnClassification.value = "Regular";
                hdnYearLevel.value = "1";
                hdnStudentStatus.value = "New";
            } else if (choice === "Transferee") {
                hdnClassification.value = "Transferee";
                hdnStudentStatus.value = "Transferee";

                if (currentTerm === "1st Semester") {
                    hdnYearLevel.value = "2";
                } else if (currentTerm === "2nd Semester") {
                    hdnYearLevel.value = "1";
                }
            }

            document.getElementById('frmAdmissionApplication').submit();
        }
    </script>
</body>

</html>