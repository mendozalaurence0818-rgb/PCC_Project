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
    style="background-image: url('../../images/PCC_Main_Background.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover; display: flex; justify-content: flex-start; align-items: center; min-height: 100vh; margin: 0; padding: 20px 5%;">

    <div style="width: 100%; max-width: 900px;">

        <div class="text-start" style="color: #FFFFFF; margin-bottom: 20px; padding-left: 10px;">
            <img src="../../images/PCC_Logo.png" alt="PCC Logo" style="width: 60px; height: 60px;">
            <p
                style="font-size: 24px; font-weight: bold; color: #000000; margin-bottom: 0; margin-top: 5px; text-shadow: 1px 1px 3px #FFFFFF;">
                POBLACION CENTRAL COLLEGE</p>
            <p style="font-size: 18px; margin-top: 0; color: #000000; text-shadow: 1px 1px 2px #FFFFFF;">College Online
                Admission
            </p>
        </div>

        <div class="row g-3">

            <div class="col-lg-5">

                <div class="card shadow mb-3" style="border-radius: 10px; border: none;">
                    <div class="card-body" style="padding: 15px;">
                        <h6 style="font-weight: bold; margin-bottom: 15px; text-align: center; color: #212529;">Start
                            New Application</h6>
                        <form action="NEW_STUDENT_ADMISSION/new_student_profile.php" method="get">
                            <div class="mb-2">
                                <label
                                    style="font-size: 12px; font-weight: bold; color: #495057; margin-bottom: 3px;">Applicant
                                    Classification</label>
                                <select name="classification" class="form-select"
                                    style="padding: 6px 10px; font-size: 13px; border-color: #CED4DA; cursor: pointer;"
                                    required>
                                    <option value="" disabled selected>Select Classification</option>
                                    <option value="freshman">Incoming College Freshman</option>
                                    <option value="transferee">College Transferee</option>
                                    <option value="second_degree">Second Degree</option>
                                    <option value="cross_enrollee">Cross-Enrollee</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label
                                    style="font-size: 12px; font-weight: bold; color: #495057; margin-bottom: 3px;">Academic
                                    Term</label>
                                <select class="form-select"
                                    style="padding: 6px 10px; font-size: 13px; border-color: #CED4DA; background-color: #e9ecef; color: #6c757d; cursor: not-allowed;"
                                    disabled>
                                    <option value="1st_sem" selected>1st Semester, AY 2026-2027</option>
                                </select>
                                <input type="hidden" name="academic_term" value="1st_sem">
                            </div>
                            <button type="submit" class="btn w-100"
                                style="background-color: #0D6EFD; border-color: #0D6EFD; color: #FFFFFF; font-weight: bold; padding: 8px; border-radius: 6px; font-size: 14px;">
                                <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Apply Now
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow" style="border-radius: 10px; border: none;">
                    <div class="card-body" style="padding: 15px;">
                        <h6 style="font-weight: bold; margin-bottom: 10px; text-align: center; color: #212529;">Retrieve
                            Application</h6>
                        <p style="font-size: 12px; color: #6C757D; text-align: center; margin-bottom: 10px;">Already
                            applied? Check your admission status here.</p>
                        <form action="application_status.html" method="post">
                            <div class="mb-2">
                                <input type="text" name="reference_number" class="form-control"
                                    placeholder="Application Reference Number"
                                    style="padding: 6px 10px; font-size: 13px; border-color: #CED4DA;" required>
                            </div>
                            <button type="submit" class="btn w-100"
                                style="background-color: #FFFFFF; color: #0D6EFD; border: 1px solid #0D6EFD; font-weight: bold; padding: 6px; border-radius: 6px; font-size: 13px;">
                                <i class="bi bi-search" style="margin-right: 5px;"></i>Check Status
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card h-100 shadow" style="border-radius: 10px; border: none;">
                    <div class="card-body" style="padding: 20px;">
                        <h5 style="color: #0D6EFD; font-weight: bold; margin-bottom: 15px;">
                            <i class="bi bi-info-circle-fill" style="margin-right: 8px;"></i>Admission Guidelines
                        </h5>
                        <p style="font-size: 13px; color: #212529; margin-bottom: 10px;">Welcome to the PCC College
                            Online Admission Portal.
                            Please read the instructions carefully before proceeding with your application.</p>

                        <h6 style="font-weight: bold; color: #495057; margin-top: 15px; font-size: 14px;">For Incoming
                            College Freshmen
                        </h6>
                        <ul style="font-size: 12px; color: #6C757D; margin-bottom: 10px; padding-left: 20px;">
                            <li style="margin-bottom: 3px;">Must be a graduate of Senior High School.</li>
                            <li style="margin-bottom: 3px;">Prepare a scanned or clear photo of your Grade 12 Report
                                Card (Form 138).</li>
                            <li style="margin-bottom: 3px;">Have a recent 2x2 ID picture with a white background ready.
                            </li>
                        </ul>

                        <h6 style="font-weight: bold; color: #495057; margin-top: 15px; font-size: 14px;">For College
                            Transferees</h6>
                        <ul style="font-size: 12px; color: #6C757D; margin-bottom: 10px; padding-left: 20px;">
                            <li style="margin-bottom: 3px;">Prepare a scanned copy of your Transcript of Records (TOR)
                                or Copy of Grades.</li>
                            <li style="margin-bottom: 3px;">Secure a Certificate of Good Moral Character from your
                                previous institution.</li>
                        </ul>

                        <div
                            style="background-color: #E9ECEF; padding: 10px 15px; border-radius: 6px; border-left: 4px solid #0D6EFD; margin-top: 15px;">
                            <p style="margin: 0; font-size: 12px; color: #495057;">
                                <strong>Important Note:</strong> Ensure all provided information is accurate and true.
                                Any falsification of documents or data will result in the automatic invalidation of your
                                application.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>