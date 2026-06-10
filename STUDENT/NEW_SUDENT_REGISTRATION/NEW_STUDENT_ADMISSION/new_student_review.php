<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal - Review Application</title>
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

        .timeline-bar {
            background-color: #FFFFFF;
            border-bottom: 1px solid #e9ecef;
            padding: 30px 20px;
        }

        .timeline-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .timeline-wrapper::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 5%;
            right: 5%;
            height: 3px;
            border-top: 1.5px solid var(--accent-yellow);
            border-bottom: 1.5px solid var(--accent-yellow);
            z-index: 1;
        }

        .timeline-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .timeline-bubble {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #FFFFFF;
            border: 2px solid var(--border-color);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .timeline-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .timeline-item.active .timeline-bubble {
            border-color: var(--accent-yellow);
            color: #000;
            background-color: var(--accent-yellow);
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.4);
        }

        .timeline-item.active .timeline-label {
            color: #000;
        }

        /* Completed Step Styling */
        .timeline-item.completed .timeline-bubble {
            background-color: #0A1140;
            border-color: #0A1140;
            color: #FFFFFF;
        }

        .timeline-item.completed .timeline-label {
            color: #0A1140;
        }

        .main-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 60px 80px 60px;
        }

        .section-intro {
            margin-bottom: 50px;
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

        .required-mark {
            color: #b02a37;
        }

        .form-input,
        .form-dropdown {
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

        .affirmation-box {
            background-color: #fdfaf2;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 4px;
            margin-top: 40px;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }

        .checkbox-container input {
            margin-top: 4px;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .checkbox-label-text {
            font-size: 13px;
            color: #333333;
            text-transform: none;
            letter-spacing: normal;
            font-weight: 500;
            line-height: 1.6;
        }

        .action-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .btn-cancel-app {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-advance-step {
            background-color: #0A1140;
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

        .btn-advance-step:hover {
            background-color: #000;
            transform: translateY(-1px);
        }

        .btn-submit-app {
            background-color: #198754;
        }

        .btn-submit-app:hover {
            background-color: #146c43;
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

            .timeline-label {
                display: none;
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

    <div class="timeline-bar">
        <div class="timeline-wrapper">
            <div class="timeline-item completed">
                <div class="timeline-bubble"><i class="bi bi-check"></i></div>
                <div class="timeline-label">Student Profile</div>
            </div>
            <div class="timeline-item completed">
                <div class="timeline-bubble"><i class="bi bi-check"></i></div>
                <div class="timeline-label">Credentials</div>
            </div>
            <div class="timeline-item active">
                <div class="timeline-bubble">3</div>
                <div class="timeline-label">Final Review</div>
            </div>
        </div>
    </div>

    <main class="main-container">
        <div class="section-intro">
            <h2>Review Your Application</h2>
            <p>Please take a moment to look over all the details you have provided before submitting. Once finalized,
                you will no longer be able to edit these fields.</p>
        </div>

        <form action="admission_complete.php" method="post">

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

            <div class="affirmation-box">
                <label class="checkbox-container">
                    <input type="checkbox" required>
                    <span class="checkbox-label-text">
                        I hereby certify under penalty of perjury that all details, entries, and documentation provided
                        in this online application form are true, complete, and correct to the best of my knowledge and
                        beliefs. I understand that any false metrics or inaccurate statements may act as grounds for
                        cancellation of my admission eligibility at PCC.
                    </span>
                </label>
            </div>

            <div class="action-footer">
                <a href="new_student_updocs.php" class="btn-cancel-app">
                    <i class="bi bi-arrow-left"></i> Back to Uploading of Documents
                </a>
                <button type="submit" class="btn-advance-step btn-submit-app">
                    Submit Application <i class="bi bi-send-fill"></i>
                </button>
            </div>

        </form>
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