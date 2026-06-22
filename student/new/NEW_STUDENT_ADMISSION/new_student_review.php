<?php
session_start();

if (!isset($_SESSION['applicant_step1'])) {
    header("Location: new_student_profile.php");
    exit();
}

$data = $_SESSION['applicant_step1'];
$docs = $_SESSION['applicant_step2'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal - Review Application</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="icon" href="../../../assets/images/PCC_favicon.png" type="image/png" />
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
            box-shadow: 0 0 15px #FFC10766;
        }

        .timeline-item.active .timeline-label {
            color: #000;
        }

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
            margin: 30px auto;
            padding: 0 60px 80px 60px;
        }

        .section-intro {
            margin-bottom: 40px;
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

        .grid-col-3 {
            width: 25%;
            padding: 0 15px;
            margin-bottom: 25px;
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
        }

        .form-input-locked {
            background-color: #e9ecef !important;
            color: #6c757d !important;
            cursor: not-allowed;
            border-color: #ced4da;
        }

        .file-review-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 13px;
            color: #212529;
            width: 100%;
            text-align: center;
        }

        .file-review-badge i {
            color: #198754;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .preview-img-frame {
            max-height: 100px;
            max-width: 100%;
            border-radius: 4px;
            margin-top: 8px;
            border: 1px solid #dee2e6;
            object-fit: contain;
        }

        .pdf-download-link {
            font-size: 12px;
            color: #0A1140;
            text-decoration: underline;
            margin-top: 6px;
            font-weight: 500;
        }

        .affirmation-box {
            background-color: #fdfaf2;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 4px;
            margin-top: 30px;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .checkbox-container input {
            margin-top: 4px;
            width: 16px;
            height: 16px;
        }

        .checkbox-label-text {
            font-size: 13px;
            color: #333333;
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

        .modal-overlay {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #00000099;
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
            max-width: 600px;
            position: relative;
            z-index: 1001;
            box-shadow: 0 10px 25px #00000033;
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
            line-height: 1;
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

        .course-item {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .course-header {
            font-size: 15px;
            color: var(--text-main);
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            background-color: #fff;
            font-weight: 500;
        }

        .course-header:hover {
            background-color: #f8f9fa;
            color: #0A1140;
        }

        .course-header i.main-icon {
            color: var(--accent-yellow);
            font-size: 18px;
            margin-right: 10px;
        }

        .course-header i.chevron-icon {
            font-size: 14px;
            color: var(--text-muted);
            transition: transform 0.2s;
        }

        .course-details {
            max-height: 0;
            overflow: hidden;
            background-color: #f8f9fa;
            transition: max-height 0.25s ease-out;
            border-top: 0 solid #e9ecef;
        }

        .course-body {
            padding: 16px;
            font-size: 13.5px;
            color: #495057;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .grid-col-3, .grid-col-4, .grid-col-6 {
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

    <img src="../../../assets/images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">

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
            <p>Please take a moment to look over all the details you have provided before submitting. Once finalized, you will no longer be able to edit these fields.</p>
        </div>

        <form action="admission_complete.php" method="post">

            <div class="form-section-divider">
                <h4>I. Personal Demographics</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-3"><label>First Name</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['first_name']); ?>" readonly></div>
                <div class="grid-col-3"><label>Middle Name</label><input type="text" class="form-input form-input-locked" value="<?php echo !empty($data['middle_name']) ? htmlspecialchars($data['middle_name']) : 'N/A'; ?>" readonly></div>
                <div class="grid-col-3"><label>Last Name</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['last_name']); ?>" readonly></div>
                <div class="grid-col-3"><label>Suffix</label><input type="text" class="form-input form-input-locked" value="<?php echo !empty($data['suffix']) ? htmlspecialchars($data['suffix']) : 'N/A'; ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Date of Birth</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['date_of_birth']); ?>" readonly></div>
                <div class="grid-col-4"><label>Gender</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['gender']); ?>" readonly></div>
                <div class="grid-col-4"><label>Civil Status</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['civil_status']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-6"><label>Nationality</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['nationality']); ?>" readonly></div>
                <div class="grid-col-6"><label>Religious Affiliation</label><input type="text" class="form-input form-input-locked" value="<?php echo !empty($data['religious_affiliation']) ? htmlspecialchars($data['religious_affiliation']) : 'N/A'; ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>II. Contact & Location Information</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-6"><label>Active Email Address</label><input type="email" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['email_address']); ?>" readonly></div>
                <div class="grid-col-6"><label>Mobile Number</label><input type="tel" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['mobile_number']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Region</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_region']); ?>" readonly></div>
                <div class="grid-col-4"><label>Province</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_province']); ?>" readonly></div>
                <div class="grid-col-4"><label>City / Municipality</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_city']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Barangay</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_barangay']); ?>" readonly></div>
                <div class="grid-col-4"><label>Postal Code</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_postal']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-12"><label>House No. / Street / Subdivision</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['address_street']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>III. Senior High School Background</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-6"><label>Last SHS School Attended</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['shs_school_attended']); ?>" readonly></div>
                <div class="grid-col-6"><label>SHS Track & Strand</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['shs_strand']); ?>" readonly></div>
                <div class="grid-col-6"><label>Year Completed / Graduated</label><input type="number" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['shs_year_graduated']); ?>" readonly></div>
                <div class="grid-col-6"><label>SHS School Address</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['shs_school_address']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>IV. Course & Academic Preferences</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-12">
                    <label>Preferred College Program / Course</label>
                    <input type="text" class="form-input form-input-locked" value="<?php echo $data['preferred_program'] === 'BSCS' ? 'Bachelor of Science in Computer Science (BSCS)' : 'Bachelor of Science in Information Technology (BSIT)'; ?>" readonly>
                </div>
                <div class="grid-col-6"><label>Academic Term Entering</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['academic_term'] ?? '1st Semester'); ?>" readonly></div>
                <div class="grid-col-6"><label>School Year (A.Y.)</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['school_year'] ?? '2026-2027'); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>V. Emergency Contact</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-4"><label>Guardian Name</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['guardian_name']); ?>" readonly></div>
                <div class="grid-col-4"><label>Relationship</label><input type="text" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['guardian_relationship']); ?>" readonly></div>
                <div class="grid-col-4"><label>Emergency Phone</label><input type="tel" class="form-input form-input-locked" value="<?php echo htmlspecialchars($data['emergency_phone']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>VI. Uploaded Credentials</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-6">
                    <label>SF10 / Form 138 (Report Card)</label>
                    <div class="file-review-badge">
                        <i class="bi bi-check-circle-fill"></i> Uploaded Successfully
                        <?php if(!empty($docs['shs_card_path']) && strtolower(pathinfo($docs['shs_card_path'], PATHINFO_EXTENSION)) !== 'pdf'): ?>
                            <img src="<?php echo htmlspecialchars($docs['shs_card_path']); ?>" class="preview-img-frame" alt="Form 138 Preview">
                        <?php elseif(!empty($docs['shs_card_path'])): ?>
                            <a href="<?php echo htmlspecialchars($docs['shs_card_path']); ?>" target="_blank" class="pdf-download-link"><i class="bi bi-file-pdf text-danger"></i> View Uploaded PDF</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid-col-6">
                    <label>PSA Birth Certificate</label>
                    <div class="file-review-badge">
                        <i class="bi bi-check-circle-fill"></i> Uploaded Successfully
                        <?php if(!empty($docs['psa_cert_path']) && strtolower(pathinfo($docs['psa_cert_path'], PATHINFO_EXTENSION)) !== 'pdf'): ?>
                            <img src="<?php echo htmlspecialchars($docs['psa_cert_path']); ?>" class="preview-img-frame" alt="PSA Birth Cert Preview">
                        <?php elseif(!empty($docs['psa_cert_path'])): ?>
                            <a href="<?php echo htmlspecialchars($docs['psa_cert_path']); ?>" target="_blank" class="pdf-download-link"><i class="bi bi-file-pdf text-danger"></i> View Uploaded PDF</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid-col-6">
                    <label>Certificate of Good Moral Character</label>
                    <div class="file-review-badge">
                        <i class="bi bi-check-circle-fill"></i> Uploaded Successfully
                        <?php if(!empty($docs['good_moral_path']) && strtolower(pathinfo($docs['good_moral_path'], PATHINFO_EXTENSION)) !== 'pdf'): ?>
                            <img src="<?php echo htmlspecialchars($docs['good_moral_path']); ?>" class="preview-img-frame" alt="Good Moral Preview">
                        <?php elseif(!empty($docs['good_moral_path'])): ?>
                            <a href="<?php echo htmlspecialchars($docs['good_moral_path']); ?>" target="_blank" class="pdf-download-link"><i class="bi bi-file-pdf text-danger"></i> View Uploaded PDF</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid-col-6">
                    <label>Recent 2x2 ID Picture</label>
                    <div class="file-review-badge">
                        <i class="bi bi-check-circle-fill"></i> Uploaded Successfully
                        <?php if(!empty($docs['applicant_photo_path'])): ?>
                            <img src="<?php echo htmlspecialchars($docs['applicant_photo_path']); ?>" class="preview-img-frame" alt="2x2 Photo Preview">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-section-divider">
                <h4>VII. Application Verification Checks</h4>
                <hr>
            </div>

            <div class="affirmation-box">
                <label class="checkbox-container"><input type="checkbox" required><span class="checkbox-label-text">I hereby certify under penalty of perjury that all details, entries, and documentation provided in this online application form are true, complete, and correct to the best of my knowledge and beliefs. I understand that any false metrics or inaccurate statements may act as grounds for cancellation of my admission eligibility at PCC.</span></label>
            </div>

            <div class="action-footer">
                <a href="new_student_updocs.php" class="btn-cancel-app"><i class="bi bi-arrow-left"></i> Back to Uploading of Documents</a>
                <button type="submit" class="btn-advance-step btn-submit-app">Submit Application <i class="bi bi-send-fill"></i></button>
            </div>
        </form>
    </main>

    <div id="coursesModal" class="modal-overlay">
        <a href="#" class="modal-backdrop"></a>
        <div class="modal-content">
            <a href="#" class="modal-close-btn">&times;</a>
            <h3>Programs & Courses Offered</h3>
            <hr>
            <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 20px;">Click a program below to review details.</p>
            <div class="course-list">
                <div class="course-item" id="item-BSCS">
                    <div class="course-header" onclick="toggleCourseDescription('BSCS')">
                        <div><i class="bi bi-book-half main-icon"></i><span>Bachelor of Science in Computer Science (BSCS)</span></div><i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="course-details">
                        <div class="course-body">
                            <p><strong>Overview:</strong> This program focuses on computing concepts, algorithms, theory, software structures, and advanced computational math foundation blocks.</p>
                            <p><strong>Core Focus Areas:</strong> Artificial Intelligence, Software Engineering, Data Structures, Machine Learning, and Algorithm Analysis Design frameworks.</p>
                        </div>
                    </div>
                </div>
                <div class="course-item" id="item-BSIT">
                    <div class="course-header" onclick="toggleCourseDescription('BSIT')">
                        <div><i class="bi bi-laptop main-icon"></i><span>Bachelor of Science in Information Technology (BSIT)</span></div><i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="course-details">
                        <div class="course-body">
                            <p><strong>Overview:</strong> This program prepares students to meet infrastructure development demands through systems administration, network design, and web programming.</p>
                            <p><strong>Core Focus Areas:</strong> Full-Stack Development, Database Management Systems (DBMS), Network Security, and System Integration Deployment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCourseDescription(courseId) {
            const targetItem = document.getElementById('item-' + courseId);
            const allItems = document.querySelectorAll('.course-item');
            allItems.forEach(item => { if (item !== targetItem) { item.classList.remove('expanded'); } });
            targetItem.classList.toggle('expanded');
        }
    </script>
</body>

</html>