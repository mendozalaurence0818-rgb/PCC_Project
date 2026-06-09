<!DOCTYPE html>
<html lang="en">
<?php
$backgrounds = [
    '../../images/PCC_Main_Background.png',
    '../../images/PCC_Background2.png'
];

$random_bg = $backgrounds[array_rand($backgrounds)];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
    <style>
        body {
            background-image:
                url('<?php echo $random_bg; ?>');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }

        @media (max-width: 900px) {
            body {
                background-image:
                    url('../../images/PCC_Smaller_BG.png');
                background-attachment: scroll;
            }
        }
    </style>
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo" style="color: white;">
            <a href="#"></a>
            <i><img src="../../images/PCC_Logo.png" alt="" style="width: 100px; height: 100px; text-shadow: 1px 1px 3px black;"></i>
            <br>
            <p style="font-size: 25px; font-weight: bold; margin-bottom: -10px; margin-top: 5px; text-shadow: 1px 1px 3px black;">POBLACION CENTRAL
                COLLEGE</p>
            <p style="font-size: 15px; margin-top: 5px; text-shadow: 1px 1px 3px black;">Home of the PCC Chiefs</p>
        </div>
        <div>
            <div class="card-body login-card-body">
                <p class="login-box-msg" style="font-size:30px; margin-bottom: -25px;"><b>Student</b> Portal</p>
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="old_student_dashboard.php" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Student ID" required />
                        <div class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="PCC Email Address" required />
                        <div class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Student Access Code" required />
                        <div class="input-group-text">
                            <i class="bi bi-key-fill"></i>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Antibot Validation" name="antibot_val"
                            required autocomplete="off" />
                        <div class="input-group-text">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Sign In</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>