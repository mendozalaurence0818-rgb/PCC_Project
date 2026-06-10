<!DOCTYPE html>
<html lang="en">
<?php
$backgrounds = [
    'images/PCC_Main_Background.png',
    'images/PCC_Background2.png'
];

$random_bg = $backgrounds[array_rand($backgrounds)];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poblacion Central College - GoPCC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="css/adminlte.css" />
    <link rel="icon" href="images/PCC_favicon.png" type="image/png" />
    <style>
        .portal-btn-primary {
            background-color: #0D6EFD;
            border: 1px solid #0D6EFD;
            color: #FFFFFF;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .portal-btn-primary:hover {
            background-color: #0B5ED7;
            border-color: #0B5ED7;
            color: #FFFFFF;
        }

        .portal-btn-outline {
            color: #0D6EFD;
            border: 1px solid #0D6EFD;
            background-color: #FFFFFF;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        .portal-btn-outline:hover {
            background-color: #0D6EFD;
            color: #FFFFFF;
            border-color: #0D6EFD;
        }

        body {
            background-image: url('<?php echo $random_bg; ?>');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }

        @media (max-width: 900px) {
            body {
                background-image: url('images/PCC_Smaller_BG.png');
                background-attachment: scroll;
            }
        }
    </style>
</head>

<body class="login-page bg-body-secondary"
    style="display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;">
    <div style="width: 100%; max-width: 450px; padding: 10px;">
        <div class="text-center" style="color: #FFFFFF; margin-bottom: 20px;">
            <i><img src="images/PCC_Logo.png" alt="PCC Logo" style="width: 75px; height: 75px;"></i>
            <br>
            <p style="font-size: 22px; font-weight: bold; margin-bottom: -5px; margin-top: 5px; text-shadow: 1px 1px 3px black;">POBLACION CENTRAL
                COLLEGE</p>
            <p style="font-size: 14px; margin-top: 5px; text-shadow: 1px 1px 3px black;">Home of the PCC Chiefs</p>
        </div>
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body login-card-body" style="padding: 25px;">
                <p class="login-box-msg" style="font-size: 22px; margin-bottom: 20px; text-align: center;">
                    <b>Select</b> Your Portal
                </p>

                <div class="row">
                    <div class="col-12 mb-3">
                        <a href="admin_login.php"
                            class="btn portal-btn-primary w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-lock-fill" style="font-size: 24px; margin-right: 15px;"></i>
                            Admin Portal
                        </a>
                    </div>
                    <div class="col-12 mb-3">
                        <a href="STUDENT/OLD_STUDENT_LOGIN/old_student_login.php"
                            class="btn portal-btn-outline w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-mortarboard-fill" style="font-size: 24px; margin-right: 15px;"></i>
                            Student Portal
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="STUDENT/NEW_SUDENT_REGISTRATION/new_student_registration.php"
                            class="btn portal-btn-outline w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-plus-fill" style="font-size: 24px; margin-right: 15px;"></i>
                            Admission Portal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>