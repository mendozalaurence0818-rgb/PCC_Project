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
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
</head>

<body class="bg-body-secondary"
    style="background-image: url('../../images/PCC_Main_Background.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover; display: flex; justify-content: flex-start; align-items: center; min-height: 100vh; margin: 0; padding: 40px 5%;">

    <div style="width: 100%; max-width: 1200px;">

        <div class="text-start" style="color: #FFFFFF; margin-bottom: 30px; padding-left: 10px;">
            <img src="../../images/PCC_Logo.png" alt="PCC Logo" style="width: 75px; height: 75px;">
            <p
                style="font-size: 32px; font-weight: 800; color: #000000; margin-bottom: 0; margin-top: 8px; text-shadow: 1px 1px 4px #FFFFFF; letter-spacing: 0.5px;">
                POBLACION CENTRAL COLLEGE</p>
            <p style="font-size: 20px; font-weight: 500; margin-top: 2px; color: #333333; text-shadow: 1px 1px 2px #FFFFFF;">
                College Online Admission Portal
            </p>
        </div>

        <div class="row g-3">

            <div class="col-lg-5 d-flex flex-column justify-content-between">

                <div class="card shadow border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5 style="font-weight: 800; margin-bottom: 20px; text-align: center; color: #212529; letter-spacing: 0.3px;">
                            Start New Application
                        </h5>
                        <form action="NEW_STUDENT_ADMISSION/new_student_profile.php" method="get">
                            <div class="mb-3">
                                <label style="font-size: 14px; font-weight: 700; color: #495057; margin-bottom: 6px; display: block;">
                                    Applicant Classification
                                </label>
                                <select name="classification" class="form-select form-select-lg"
                                    style="font-size: 15px; border-color: #CED4DA; cursor: pointer; height: 48px;"
                                    required>
                                    <option value="" disabled selected>Select Classification</option>
                                    <option value="freshman">Incoming College Freshman</option>
                                    <option value="transferee">College Transferee</option>
                                    <option value="second_degree">Second Degree</option>
                                    <option value="cross_enrollee">Cross-Enrollee</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label style="font-size: 14px; font-weight: 700; color: #495057; margin-bottom: 6px; display: block;">
                                    Academic Term
                                </label>
                                <select class="form-select form-select-lg"
                                    style="font-size: 15px; border-color: #CED4DA; background-color: #e9ecef; color: #6c757d; cursor: not-allowed; height: 48px;"
                                    disabled>
                                    <option value="1st_sem" selected>1st Semester, AY 2026-2027</option>
                                </select>
                                <input type="hidden" name="academic_term" value="1st_sem">
                            </div>
                            <button type="submit" class="btn w-100 btn-lg d-flex align-items-center justify-content-center"
                                style="background-color: #0D6EFD; border-color: #0D6EFD; color: #FFFFFF; font-weight: 700; height: 48px; border-radius: 8px; font-size: 16px; transition: background-color 0.2s;">
                                <i class="bi bi-pencil-square me-2"></i>Apply Now
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow border-0" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5 style="font-weight: 800; margin-bottom: 10px; text-align: center; color: #212529; letter-spacing: 0.3px;">
                            Retrieve Application
                        </h5>
                        <p style="font-size: 14px; color: #6C757D; text-align: center; margin-bottom: 20px;">
                            Already applied? Check your admission status here.
                        </p>
                        <form action="application_status.html" method="post">
                            <div class="mb-3">
                                <input type="text" name="reference_number" class="form-control form-control-lg"
                                    placeholder="Application Reference Number"
                                    style="font-size: 15px; border-color: #CED4DA; height: 48px;" required>
                            </div>
                            <button type="submit" class="btn w-100 btn-lg d-flex align-items-center justify-content-center"
                                style="background-color: #FFFFFF; color: #0D6EFD; border: 1px solid #0D6EFD; font-weight: 700; height: 48px; border-radius: 8px; font-size: 15px; transition: all 0.2s;">
                                <i class="bi bi-search me-2"></i>Check Status
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card h-100 shadow border-0" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 35px;">
                        <h4 style="color: #0D6EFD; font-weight: 800; margin-bottom: 20px;" class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>Admission Guidelines
                        </h4>
                        <p style="font-size: 15px; line-height: 1.6; color: #212529; margin-bottom: 20px;">
                            Welcome to the PCC College Online Admission Portal. Please read the instructions carefully before proceeding with your application configuration mapping parameters.
                        </p>

                        <h5 style="font-weight: 700; color: #495057; margin-top: 25px; font-size: 16px;">
                            For Incoming College Freshmen
                        </h5>
                        <ul style="font-size: 14px; line-height: 1.7; color: #555555; margin-bottom: 20px; padding-left: 20px;">
                            <li style="margin-bottom: 6px;">Must be an official graduate of Senior High School.</li>
                            <li style="margin-bottom: 6px;">Prepare a scanned copy or clear photo of your Grade 12 Report Card (Form 138).</li>
                            <li style="margin-bottom: 6px;">Have a recent 2x2 ID picture with a white background ready for upload.</li>
                        </ul>

                        <h5 style="font-weight: 700; color: #495057; margin-top: 25px; font-size: 16px;">
                            For College Transferees
                        </h5>
                        <ul style="font-size: 14px; line-height: 1.7; color: #555555; margin-bottom: 20px; padding-left: 20px;">
                            <li style="margin-bottom: 6px;">Prepare a clear scanned copy of your Transcript of Records (TOR) or certified Copy of Grades.</li>
                            <li style="margin-bottom: 6px;">Secure a valid Certificate of Good Moral Character from your previous academic institution.</li>
                        </ul>

                        <div style="background-color: #F8F9FA; padding: 18px 20px; border-radius: 8px; border-left: 5px solid #0D6EFD; margin-top: 30px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);">
                            <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #495057;">
                                <strong>Important Note:</strong> Ensure all provided information is accurate and true. Any falsification of documents or system entry data data will result in the automatic invalidation of your application records.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>