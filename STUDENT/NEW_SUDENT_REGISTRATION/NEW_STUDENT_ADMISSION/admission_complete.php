<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Application Submitted</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="icon" href="../../../images/PCC_favicon.png" type="image/png" />
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
            --success-green: #198754;
            --primary-blue: #0A1140;
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
            scroll-behavior: smooth;
        }

        .top-navbar {
            background-color: var(--nav-bg);
            padding: 14px 60px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 40px;
            position: sticky;
            top: 0;
            z-index: 1000;
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

        /* Success Banner */
        .success-banner {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
            padding: 25px 30px;
            border-radius: 8px;
            margin-bottom: 50px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.1);
        }

        .success-icon {
            font-size: 36px;
            color: var(--success-green);
        }

        .success-text h2 {
            font-family: var(--font-heading);
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .success-text p {
            font-size: 15px;
            margin: 0;
            font-family: var(--font-body);
        }

        .form-section-divider {
            margin: 50px 0 30px 0;
        }

        .form-section-divider h4 {
            font-family: var(--font-heading);
            font-size: 20px;
            color: var(--primary-blue);
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

        .form-input-locked {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            font-family: var(--font-body);
            color: #212529;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 4px;
            cursor: default;
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
            color: var(--success-green);
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

        .btn-home {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background-color: var(--primary-blue);
            color: #FFFFFF;
            font-family: var(--font-body);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 40px;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .btn-print:hover {
            background-color: #000;
            transform: translateY(-1px);
        }

        /* ----- CSS-ONLY MODAL STYLES ----- */
        .modal-overlay {
            position: fixed;
            z-index: 2000;
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
            z-index: 2001;
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

        /* Print Settings */
        @media print {

            .top-navbar,
            .header-banner,
            .action-footer,
            .success-banner,
            .modal-overlay {
                display: none !important;
            }

            .main-container {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .form-input-locked {
                border: none;
                background-color: transparent;
                padding-left: 0;
                font-weight: 500;
            }

            .file-review-badge {
                border: none;
                background-color: transparent;
                padding-left: 0;
            }
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

            .success-banner {
                flex-direction: column;
                text-align: center;
            }

            .modal-content {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <nav class="top-navbar">
        <a href="../new_student_registration.php">Home</a>
        <a href="#coursesModal">Courses Offered</a>
    </nav>

    <img src="../../../images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">

    <main class="main-container">

        <div class="success-banner">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <div class="success-text">
                <h2>Application Successfully Submitted!</h2>
                <p>Your admission application has been received. Please save a copy of this form for your records. We
                    will contact you at your registered email regarding the next steps.</p>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>I. Personal Demographics</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>First Name</label>
                <input type="text" class="form-input-locked" value="Joeshua" readonly>
            </div>
            <div class="grid-col-4">
                <label>Middle Name</label>
                <input type="text" class="form-input-locked" value="Santos" readonly>
            </div>
            <div class="grid-col-4">
                <label>Last Name</label>
                <input type="text" class="form-input-locked" value="Dela Cruz" readonly>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>Date of Birth</label>
                <input type="text" class="form-input-locked" value="2005-08-15" readonly>
            </div>
            <div class="grid-col-4">
                <label>Gender</label>
                <input type="text" class="form-input-locked" value="Male" readonly>
            </div>
            <div class="grid-col-4">
                <label>Civil Status</label>
                <input type="text" class="form-input-locked" value="Single" readonly>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Nationality</label>
                <input type="text" class="form-input-locked" value="Filipino" readonly>
            </div>
            <div class="grid-col-6">
                <label>Religious Affiliation</label>
                <input type="text" class="form-input-locked" value="Roman Catholic" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>II. Contact & Location Information</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Active Email Address</label>
                <input type="email" class="form-input-locked" value="joeshua.delacruz@gmail.com" readonly>
            </div>
            <div class="grid-col-6">
                <label>Mobile Number</label>
                <input type="tel" class="form-input-locked" value="09123456789" readonly>
            </div>
            <div class="grid-col-12">
                <label>Current Home Address</label>
                <textarea class="form-input-locked" rows="2"
                    readonly>Block 4 Lot 12, Mabini Street, Barangay Commonwealth, Quezon City, Metro Manila</textarea>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>III. Senior High School Background</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-6">
                <label>Last SHS School Attended</label>
                <input type="text" class="form-input-locked" value="Quezon City Science High School" readonly>
            </div>
            <div class="grid-col-6">
                <label>SHS Track & Strand</label>
                <input type="text" class="form-input-locked" value="Academic Track - STEM" readonly>
            </div>
            <div class="grid-col-6">
                <label>Year Completed / Graduated</label>
                <input type="number" class="form-input-locked" value="2024" readonly>
            </div>
            <div class="grid-col-6">
                <label>SHS School Address</label>
                <input type="text" class="form-input-locked" value="Bago Bantay, Quezon City" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>IV. Course & Academic Preferences</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-12">
                <label>Preferred College Program / Course</label>
                <input type="text" class="form-input-locked" value="BS Information Technology" readonly>
            </div>
            <div class="grid-col-6">
                <label>Academic Term Entering</label>
                <input type="text" class="form-input-locked" value="1st Semester" readonly>
            </div>
            <div class="grid-col-6">
                <label>School Year (A.Y.)</label>
                <input type="text" class="form-input-locked" value="2026-2027" readonly>
            </div>
        </div>

        <div class="form-section-divider">
            <h4>V. Emergency Contact</h4>
            <hr>
        </div>

        <div class="grid-row">
            <div class="grid-col-4">
                <label>Guardian Name</label>
                <input type="text" class="form-input-locked" value="Maria Dela Cruz" readonly>
            </div>
            <div class="grid-col-4">
                <label>Relationship</label>
                <input type="text" class="form-input-locked" value="Mother" readonly>
            </div>
            <div class="grid-col-4">
                <label>Emergency Phone</label>
                <input type="tel" class="form-input-locked" value="09987654321" readonly>
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
                    <i class="bi bi-check-circle-fill"></i> delacruz_sf10.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>PSA Birth Certificate</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> delacruz_psa.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>Certificate of Good Moral Character</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> delacruz_goodmoral.pdf
                </div>
            </div>
            <div class="grid-col-6">
                <label>Recent 2x2 ID Picture</label>
                <div class="file-review-badge">
                    <i class="bi bi-check-circle-fill"></i> delacruz_2x2.jpg
                </div>
            </div>
        </div>

        <div class="action-footer">
            <a href="../new_student_registration.php" class="btn-home">
                <i class="bi bi-house-door-fill"></i> Return to Homepage
            </a>
            <button type="button" class="btn-print" onclick="window.print()">
                Print Application <i class="bi bi-printer-fill"></i>
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
                <li><i class="bi bi-briefcase-fill"></i> Bachelor of Science in Business Administration (BSBA)</li>
                <li><i class="bi bi-buildings-fill"></i> Bachelor of Science in Hospitality Management (BSHM)</li>
                <li><i class="bi bi-mortarboard-fill"></i> Bachelor of Secondary Education (BSEd)</li>
                <li><i class="bi bi-shield-fill-check"></i> Bachelor of Science in Criminology (BSCrim)</li>
            </ul>
        </div>
    </div>

</body>

</html>