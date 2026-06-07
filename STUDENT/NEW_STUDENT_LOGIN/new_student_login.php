<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
</head>

<body class="register-page bg-body-secondary"
    style="background-image: url('../../images/PCC_Background.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;">
    <div style="width: 100%; max-width: 600px; padding: 10px;">
        <div class="text-center" style="color: #FFFFFF; margin-bottom: 10px;">
            <a href="#"></a>
            <i><img src="../../images/PCC_Logo.png" alt="PCC Logo" style="width: 65px; height: 65px;"></i>
            <br>
            <p style="font-size: 20px; font-weight: bold; margin-bottom: -5px; margin-top: 5px;">POBLACION CENTRAL
                COLLEGE</p>
            <p style="font-size: 13px; margin-top: 5px;">Home of the PCC Chiefs</p>
        </div>
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body register-card-body" style="padding: 15px 20px;">
                <p class="login-box-msg" style="font-size: 20px; margin-bottom: -10px; text-align: center;">
                    <b>Registration</b> Form
                </p>
                <p class="login-box-msg" style="text-align: center; margin-bottom: 15px; font-size: 14px;">
                    Complete your information to apply
                </p>

                <form action="../admissions_success.html" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="First Name" required />
                                <div class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="Last Name" required />
                                <div class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control" placeholder="Middle Name (Optional)" />
                        <div class="input-group-text">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="date" class="form-control" required />
                                <div class="input-group-text">
                                    <i class="bi bi-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <select class="form-control" required>
                                    <option value="" disabled selected>Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <div class="input-group-text">
                                    <i class="bi bi-gender-ambiguous"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="email" class="form-control" placeholder="Email Address" required />
                                <div class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="tel" class="form-control" placeholder="Phone Number" required />
                                <div class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <textarea class="form-control" rows="2" placeholder="Complete Home Address" required></textarea>
                        <div class="input-group-text">
                            <i class="bi bi-house-door"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="password" class="form-control" placeholder="Create Password" required />
                                <div class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="password" class="form-control" placeholder="Confirm Password" required />
                                <div class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-sm"
                                    style="background-color: #0D6EFD; border-color: #0D6EFD; padding: 6px; font-size: 14px;">Submit
                                    Application</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-3 border-top pt-2" style="border-color: #DEE2E6;">
                    <p style="margin-bottom: 2px; font-size: 13px;">Already have an account?</p>
                    <a href="login.html"
                        style="color: #0D6EFD; text-decoration: none; font-weight: bold; font-size: 14px;">Sign In
                        Here</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>