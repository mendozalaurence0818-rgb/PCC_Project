<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal - Application Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
    <style>
        :root {
            --nav-bg: #000000;
            --accent-yellow: #FFC107;
            --text-main: #212529;
            --text-muted: #5a6268;
            --input-bg: #fdfdfd;
            --border-color: #ced4da;
            --font-heading: 'Lora', serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: #FFFFFF;
            min-height: 100vh;
            color: var(--text-main);
            line-height: 1.5;
        }

        .top-navbar {
            background-color: var(--nav-bg);
            padding: 14px 60px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 40px;
        }

        .top-navbar a {
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .top-navbar a:hover {
            color: var(--accent-yellow);
        }

        .header-banner {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .main-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 60px 80px 60px;
        }

        .section-intro {
            margin-bottom: 35px;
            border-left: 5px solid var(--accent-yellow);
            padding-left: 25px;
        }

        .section-intro h2 {
            font-family: var(--font-heading);
            font-size: 32px;
            color: #0A1140;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .section-intro p {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 400;
            line-height: 1.6;
        }

        /* --- APPLICATION STATUS ALERTS --- */
        .status-card {
            border-radius: 6px;
            padding: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            border: 1px solid transparent;
        }

        /* Under Review State */
        .status-review {
            background-color: #FFF9E6;
            border-color: #FFEBA8;
        }

        .status-review .status-icon-box {
            background-color: #FFF0C2;
            color: #856404;
        }

        .status-review h5 {
            color: #856404;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .status-review p {
            font-size: 14px;
            color: #66511A;
        }

        /* Approved State */
        .status-approved {
            background-color: #EBF7EE;
            border-color: #C3E6CB;
        }

        .status-approved .status-icon-box {
            background-color: #D4EDDA;
            color: #155724;
        }

        .status-approved h5 {
            color: #155724;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .status-approved p {
            font-size: 14px;
            color: #1E4E2B;
        }

        .status-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .form-section-divider {
            margin: 50px 0 30px 0;
        }

        .form-section-divider h4 {
            font-family: var(--font-heading);
            font-size: 20px;
            color: #0A1140;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-section-divider hr {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin-top: 10px;
        }

        .grid-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .grid-col-4 {
            width: 33.3333%;
            padding: 0 15px;
            margin-bottom: 25px;
        }

        .grid-col-6 {
            width: 50%;
            padding: 0 15px;
            margin-bottom: 25px;
        }

        .grid-col-12 {
            width: 100%;
            padding: 0 15px;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--text-main);
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            transition: all 0.2s;
        }

        .form-input-locked {
            background-color: #e9ecef !important;
            color: #6c757d !important;
            cursor: not-allowed;
            border-color: #ced4da;
            resize: none;
        }

        .file-review-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background-color: #f8f9fa;
            border: 1px dashed var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            color: #212529;
            width: 100%;
        }

        .file-review-badge i {
            color: #198754;
            font-size: 16px;
        }

        .action-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .btn-back-portal {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back-portal:hover {
            color: #000;
        }

        .btn-print-summary {
            background-color: #0A1140;
            color: #FFFFFF;
            font-family: var(--font-body);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 14px 30px;
            border-radius: 4px;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-print-summary:hover {
            background-color: #000;
        }

        /* ----- CSS-ONLY MODAL STYLES ----- */
        .modal-overlay {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay:target {
            opacity: 1;
            visibility: visible;
        }

        .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: default;
        }

        .modal-content {
            background-color: #FFFFFF;
            padding: 40px 50px;
            border-radius: 8px;
            width: 90%;
            max-width: 550px;
            position: relative;
            z-index: 1001;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay:target .modal-content {
            transform: translateY(0);
        }

        .modal-close-btn {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 28px;
            color: #adb5bd;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }

        .modal-close-btn:hover {
            color: #000;
        }

        .modal-content h3 {
            font-family: var(--font-heading);
            font-size: 24px;
            color: #0A1140;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .modal-content hr {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin-bottom: 25px;
        }

        .course-list {
            list-style: none;
            padding: 0;
        }

        .course-list li {
            font-size: 15px;
            color: var(--text-main);
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .course-list li i {
            color: var(--accent-yellow);
            font-size: 18px;
            margin-top: -2px;
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 14px 25px;
                gap: 20px;
            }

            .grid-col-4,
            .grid-col-6 {
                width: 100%;
            }

            .main-container {
                padding: 0 25px 50px 25px;
            }

            .status-card {
                flex-direction: column;
                gap: 12px;
            }

            .modal-content {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <nav class="top-navbar">
        <a href="new_student_registration.php">Home</a>
        <a href="#coursesModal">Courses Offered</a>
    </nav>

    <img src="../../images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">

    <main class="main-container">

        <div class="section-intro">
            <h2>Application Profile Status</h2>
            <p>Tracking Application Status for Application Reference Number:
                <strong>PCC-2026-89421-AR</strong>
            </p>
        </div>

        <div class="status-card status-review">
            <div class="status-icon-box">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="status-details">
                <h5>Application Status: Under Review / Evaluation</h5>
                <p>The Admissions Office is currently verifying your submitted biometric information and academic
                    credentials. Please review the archived parameters listed below to ensure your records remain up to
                    date.</p>
            </div>
        </div>

        <div class="status-card status-approved" style="display: none;">
            <div class="status-icon-box">
                <i class="bi bi-badge-check-fill"></i>
            </div>
            <div class="status-details">
                <h5>Application Status: Approved & Verified</h5>
                <p>Congratulations! Your academic evaluation records and identification credentials have been cleared
                    and confirmed. Welcome to PCC! Please check your verified inbox for immediate registration
                    instructions.</p>
            </div>
        </div>


        <div class="form-section-divider">
            <h4>I. Personal Demographics</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>First Name</label>
                <input type="text" class="form-input form-input-locked" value="Joeshua" readonly>
            </div>
            <div class="grid-col-4">
                <label>Middle Name</label>
                <input type="text" class="form-input form-input-locked" value="Reyes" readonly>
            </div>
            <div class="grid-col-4">
                <label>Last Name</label>
                <input type="text" class="form-input form-input-locked" value="Santos" readonly>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>Date of Birth</label>
                <input type="text" class="form-input form-input-locked" value="2005-05-14" readonly>
            </div>
            <div class="grid-col-4">
                <label>Gender</label>
                <input type="text" class="form-input form-input-locked" value="Male" readonly>
            </div>
            <div class="grid-col-4">
                <label>Civil Status</label>
                <input type="text" class="form-input form-input-locked" value="Single" readonly>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Nationality</label>
                <input type="text" class="form-input form-input-locked" value="Filipino" readonly>
            </div>
            <div class="grid-col-6">
                <label>Religious Affiliation</label>
                <input type="text" class="form-input form-input-locked" value="Roman Catholic" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>II. Contact & Location Information</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Active Email Address</label>
                <input type="email" class="form-input form-input-locked" value="joeshuasantos@email.com" readonly>
            </div>
            <div class="grid-col-6">
                <label>Mobile Number</label>
                <input type="tel" class="form-input form-input-locked" value="0912-345-6789" readonly>
            </div>
            <div class="grid-col-12">
                <label>Current Home Address</label>
                <textarea class="form-input form-input-locked" rows="2"
                    readonly>123 Rizal Street, Barangay Central, Quezon City, Metro Manila</textarea>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>III. Senior High School Background</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Last SHS School Attended</label>
                <input type="text" class="form-input form-input-locked" value="Quezon City National High School"
                    readonly>
            </div>
            <div class="grid-col-6">
                <label>SHS Track & Strand</label>
                <input type="text" class="form-input form-input-locked"
                    value="Academic - STEM (Science, Technology, Engineering, Mathematics)" readonly>
            </div>
            <div class="grid-col-6">
                <label>Year Completed / Graduated</label>
                <input type="number" class="form-input form-input-locked" value="2026" readonly>
            </div>
            <div class="grid-col-6">
                <label>SHS School Address</label>
                <input type="text" class="form-input form-input-locked" value="Quezon City, Metro Manila" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>IV. Course & Academic Preferences</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-12">
                <label>Preferred College Program / Course</label>
                <input type="text" class="form-input form-input-locked"
                    value="Bachelor of Science in Computer Science (BSCS)" readonly>
            </div>
            <div class="grid-col-6">
                <label>Academic Term Entering</label>
                <input type="text" class="form-input form-input-locked" value="1st Semester" readonly>
            </div>
            <div class="grid-col-6">
                <label>School Year (A.Y.)</label>
                <input type="text" class="form-input form-input-locked" value="2026-2027" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>V. Emergency Contact</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>Guardian Name</label>
                <input type="text" class="form-input form-input-locked" value="Kylie Santos" readonly>
            </div>
            <div class="grid-col-4">
                <label>Relationship</label>
                <input type="text" class="form-input form-input-locked" value="Mother" readonly>
            </div>
            <div class="grid-col-4">
                <label>Emergency Phone</label>
                <input type="tel" class="form-input form-input-locked" value="0998-765-4321" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>VI. Uploaded Credentials</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>SF10 / Form 138 (Report Card)</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> form_138_joeshuasantos.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>PSA Birth Certificate</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> psa_birth_cert_joeshuasantos.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>Certificate of Good Moral Character</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> good_moral_joeshuasantos.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>Recent 2x2 ID Picture</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> 2x2_id_picture_joeshuasantos.jpg
                </div>
            </div>
        </div>

        <div class="action-footer">
            <a href="new_student_registration.php" class="btn-back-portal">
                <i class="bi bi-arrow-left"></i> Return to Main Home Portal
            </a>
            <button type="button" class="btn-print-summary" onclick="window.print()">
                Print Statement <i class="bi bi-printer-fill"></i>
            </button>
        </div>
    </main>

    <div id="coursesModal" class="modal-overlay">
        <a href="#" class="modal-backdrop"></a>
        <div class="modal-content">
            <a href="#" class="modal-close-btn">&times;</a>
            <h3>Programs & Courses Offered</h3>
            <hr>
            <ul class="course-list">
                <li><i class="bi bi-book-half"></i> Bachelor of Science in Computer Science (BSCS)</li>
                <li><i class="bi bi-laptop"></i> Bachelor of Science in Information Technology (BSIT)</li>
            </ul>
        </div>
    </div>

</body>

</html>