<?php
session_start();
if (!isset($_SESSION['applicant_step1'])) {
    header("Location: new_student_profile.php");
    exit();
}

$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = '../uploads/credentials/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $max_file_size = 5 * 1024 * 1024;
    $uploaded_paths = [];

    $files_to_upload = [
        'form_138' => 'shs_card_path',
        'birth_certificate' => 'psa_cert_path',
        'good_moral' => 'good_moral_path',
        'id_picture' => 'applicant_photo_path'
    ];

    foreach ($files_to_upload as $input_name => $session_key) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$input_name]['tmp_name'];
            $file_name = $_FILES[$input_name]['name'];
            $file_size = $_FILES[$input_name]['size'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_all_array($file_extension, $allowed_extensions)) {
                $error_message = "Error: Invalid file format for " . str_replace('_', ' ', $input_name) . ". Only PDF, JPEG, and PNG are accepted.";
                break;
            }

            if ($file_size > $max_file_size) {
                $error_message = "Error: File size exceeds the 5MB limit.";
                break;
            }
            $new_filename = $input_name . '_' . uniqid() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $destination)) {
                $uploaded_paths[$session_key] = $destination;
            } else {
                $error_message = "Operational Error: Failed to move file structure to storage directory matrices.";
                break;
            }
        } else {
            $error_message = "Missing Required File Input Matrix parameters.";
            break;
        }
    }

    if (empty($error_message)) {
        $_SESSION['applicant_step2'] = $uploaded_paths;
        header("Location: new_student_review.php");
        exit();
    }
}

function in_all_array($needle, $haystack)
{
    return in_array($needle, $haystack);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Upload Documents</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
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
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.4);
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

        .grid-col-12 {
            width: 100%;
            padding: 0 15px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .label-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: block;
        }

        .required-mark {
            color: #b02a37;
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

        .form-input:focus {
            border-color: #0A1140;
            outline: none;
            box-shadow: none;
            background-color: #fff;
        }

        .form-input[type="file"] {
            padding: 8px;
            background-color: #f8f9fa;
        }

        .form-input[type="file"]::file-selector-button {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            color: #495057;
            padding: 6px 16px;
            border-radius: 4px;
            margin-right: 15px;
            cursor: pointer;
            font-family: var(--font-body);
            font-weight: 500;
            transition: all 0.2s;
        }

        .form-input[type="file"]::file-selector-button:hover {
            background-color: #dee2e6;
            color: #000;
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
            max-width: 600px;
            position: relative;
            z-index: 1011;
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

        .course-body p {
            margin-bottom: 12px;
        }

        .course-item.expanded {
            border-color: #0A1140;
        }

        .course-item.expanded .course-details {
            max-height: 250px;
            border-top-width: 1px;
        }

        .course-item.expanded i.chevron-icon {
            transform: rotate(180deg);
            color: #0A1140;
        }

        .alert-error-banner {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 14px 25px;
                gap: 20px;
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
            <div class="timeline-item active">
                <div class="timeline-bubble">2</div>
                <div class="timeline-label">Credentials</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-bubble">3</div>
                <div class="timeline-label">Final Review</div>
            </div>
        </div>
    </div>
    <main class="main-container">
        <div class="section-intro">
            <h2>Upload Credentials</h2>
            <p>Please upload clear, legible scanned copies or high-resolution photos of your credentials. Only files in
                <strong>PDF, JPEG, or PNG</strong> format are allowed. The maximum file size limit per upload is
                <strong>5 MB</strong>.
            </p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert-error-banner">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="new_student_updocs.php" method="post" enctype="multipart/form-data">
            <div class="form-section-divider">
                <h4>I. Required Academic Documents</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-12">
                    <label><i class="bi bi-file-earmark-text me-1"></i> Report Card (Form 138) <span
                            class="required-mark">*</span></label>
                    <span class="label-desc">Upload a copy of your last/current report card.</span>
                    <input type="file" name="form_138" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <div class="grid-col-12">
                    <label><i class="bi bi-file-earmark-person me-1"></i> PSA Birth Certificate <span
                            class="required-mark">*</span></label>
                    <span class="label-desc">Clear, unblemished copy issued by the Philippine Statistics
                        Authority.</span>
                    <input type="file" name="birth_certificate" class="form-input" accept=".pdf,.jpg,.jpeg,.png"
                        required>
                </div>
                <div class="grid-col-12">
                    <label><i class="bi bi-shield-check me-1"></i> Certificate of Good Moral Character <span
                            class="required-mark">*</span></label>
                    <span class="label-desc">Issued by your previous Senior High School Principal or Guidance
                        Office.</span>
                    <input type="file" name="good_moral" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <div class="grid-col-12">
                    <label><i class="bi bi-image me-1"></i> Recent 2x2 ID Picture <span
                            class="required-mark">*</span></label>
                    <span class="label-desc">Must feature a plain white background, taken within the last 6
                        months.</span>
                    <input type="file" name="id_picture" class="form-input" accept=".jpg,.jpeg,.png" required>
                </div>
            </div>
            <div class="action-footer">
                <a href="new_student_profile.php" class="btn-cancel-app"><i class="bi bi-arrow-left"></i> Back to
                    Personal Profile</a>
                <button type="submit" class="btn-advance-step">Review & Submit <i
                        class="bi bi-chevron-right"></i></button>
            </div>
        </form>
    </main>

    <div id="coursesModal" class="modal-overlay">
        <a href="#" class="modal-backdrop"></a>
        <div class="modal-content">
            <a href="#" class="modal-close-btn">&times;</a>
            <h3>Programs & Courses Offered</h3>
            <hr>
            <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 20px;">Click a program below to review
                details.</p>
            <div class="course-list">
                <div class="course-item" id="item-BSCS">
                    <div class="course-header" onclick="toggleCourseDescription('BSCS')">
                        <div><i class="bi bi-book-half main-icon"></i><span>Bachelor of Science in Computer Science
                                (BSCS)</span></div><i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="course-details">
                        <div class="course-body">
                            <p><strong>Overview:</strong> This program focuses on computing concepts, algorithms,
                                theory, software structures, and advanced computational math foundation blocks.</p>
                            <p><strong>Core Focus Areas:</strong> Artificial Intelligence, Software Engineering, Data
                                Structures, Machine Learning, and Algorithm Analysis.</p>
                        </div>
                    </div>
                </div>
                <div class="course-item" id="item-BSIT">
                    <div class="course-header" onclick="toggleCourseDescription('BSIT')">
                        <div><i class="bi bi-laptop main-icon"></i><span>Bachelor of Science in Information Technology
                                (BSIT)</span></div><i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="course-details">
                        <div class="course-body">
                            <p><strong>Overview:</strong> This program prepares students to meet infrastructure
                                development demands through systems administration, network design, and web programming.
                            </p>
                            <p><strong>Core Focus Areas:</strong> Full-Stack Development, Database Management Systems
                                (DBMS), Network Security, and System Integration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleCourseDescription(courseId) { const targetItem = document.getElementById('item-' + courseId); const allItems = document.querySelectorAll('.course-item'); allItems.forEach(item => { if (item !== targetItem) { item.classList.remove('expanded'); } }); targetItem.classList.toggle('expanded'); }
    </script>
</body>

</html>