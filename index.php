<!DOCTYPE html>
<html lang="en">
<?php
$backgrounds = [
    'assets/images/PCC_Main_Background.png',
    'assets/images/PCC_BG2.png'
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
    <link rel="stylesheet" href="assets/css/adminlte.css" />
    <link rel="icon" href="assets/images/PCC_favicon.png" type="image/png" />
    <style>
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

        .portal-grid-standalone {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 25px 40px;
            margin-top: 25px;
            justify-content: center;
            align-items: center;
        }

        .section-header {
            grid-column: span 6;
            text-align: center;
            color: #FFFFFF;
            font-size: 18zpx;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.4);
            padding-bottom: 5px;
            margin-top: 15px;
        }

        .portal-link-item {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: none;
            border: none;
            padding: 0;
            transition: transform 0.2s ease-in-out;
        }

        .portal-link-item.row1-left {
            grid-column: span 3;
            justify-self: end;
        }

        .portal-link-item.row1-right {
            grid-column: span 3;
            justify-self: start;
        }

        .portal-link-item.row2-center {
            grid-column: 2 / span 4;
            justify-self: center;
        }

        .portal-link-item:hover {
            transform: scale(1.1);
        }

        .portal-clickable-img {
            width: 150px;
            height: 150px;
            object-fit: contain;
        }

        @media (max-width: 480px) {
            .portal-grid-standalone {
                gap: 20px;
                margin-top: 15px;
            }

            .portal-clickable-img {
                width: 110px;
                height: 110px;
            }

            .section-header {
                font-size: 11px;
            }
        }
    </style>
</head>

<body class="login-page bg-body-secondary"
    style="display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;">
    <div style="width: 100%; max-width: 550px; padding: 20px;">
        <div class="text-center" style="color: #FFFFFF; margin-bottom: 10px;">
            <i><img src="assets/images/PCC_logo.png" alt="PCC Logo" style="width: 85px; height: 85px;"></i>
            <br>
            <p
                style="font-size: 24px; font-weight: bold; margin-bottom: -5px; margin-top: 10px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                POBLACION CENTRAL COLLEGE
            </p>
            <p style="font-size: 15px; margin-top: 5px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                Home of the PCC Chiefs
            </p>
        </div>

        <div class="portal-grid-standalone">
            <div class="section-header">Academic Section</div>

            <a href="student/old/old_student_login.php" class="portal-link-item row1-left">
                <img src="assets/images/student.png" alt="Student Portal" class="portal-clickable-img">
            </a>

            <a href="student/new/new_student_registration.php" class="portal-link-item row1-right">
                <img src="assets/images/admission.png" alt="Admission Portal" class="portal-clickable-img">
            </a>

            <div class="section-header">Administrative Section</div>

            <a href="admin/login.php" class="portal-link-item row2-center">
                <img src="assets/images/admin.png" alt="Admin Portal" class="portal-clickable-img">
            </a>
        </div>
    </div>

</body>

</html>