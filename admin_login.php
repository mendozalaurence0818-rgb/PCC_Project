<!DOCTYPE html>
<html lang="en">

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
    <link rel="stylesheet" href="css/adminlte.css" />
    <link rel="icon" href="images/PCC_Logo" type="image/png" />
</head>

<body class="login-page bg-body-secondary"
    style="background-image: url('images/PCC_Background.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">
    <div class="login-box">
        <div class="login-logo" style="color: white;">
            <a href="#"></a>
            <i><img src="images/PCC_Logo.png" alt="" style="width: 100px; height: 100px;"></i>
            <br>
            <p style="font-size: 20px; margin-bottom: -10px; margin-top: 5px;">Home of the PCC Chiefs</p>
            <p style="font-size: 25px; font-weight: bold;">POBLACION CENTRAL COLLEGE</p>
        </div>
        <div>
            <div class="card-body login-card-body">
                <p class="login-box-msg" style="font-size:30px; margin-bottom: -25px;"><b>Admin</b> Portal</p>
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="../index3.html" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Admin ID" required />
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
                        <input type="text" class="form-control" placeholder="Admin Access Code" required />
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