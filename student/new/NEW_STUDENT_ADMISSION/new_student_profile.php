<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['applicant_reference'] = 'PCC-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

    $_SESSION['applicant_step1'] = [
        'first_name' => htmlspecialchars(trim($_POST['first_name'])),
        'middle_name' => htmlspecialchars(trim($_POST['middle_name'] ?? '')),
        'last_name' => htmlspecialchars(trim($_POST['last_name'])),
        'suffix' => htmlspecialchars(trim($_POST['suffix'] ?? '')),
        'date_of_birth' => $_POST['date_of_birth'],
        'gender' => $_POST['gender'],
        'civil_status' => $_POST['civil_status'],
        'nationality' => $_POST['nationality'],
        'religious_affiliation' => htmlspecialchars(trim($_POST['religious_affiliation'] ?? '')),
        'email_address' => filter_var(trim($_POST['email_address']), FILTER_SANITIZE_EMAIL),
        'mobile_number' => htmlspecialchars(trim($_POST['mobile_number'])),
        'address_region' => $_POST['address_region'],
        'address_province' => $_POST['address_province'],
        'address_city' => $_POST['address_city'],
        'address_barangay' => $_POST['address_barangay'],
        'address_postal' => htmlspecialchars(trim($_POST['address_postal'])),
        'address_street' => htmlspecialchars(trim($_POST['address_street'])),
        'shs_school_attended' => htmlspecialchars(trim($_POST['shs_school_attended'])),
        'shs_strand' => $_POST['shs_strand'],
        'shs_year_graduated' => intval($_POST['shs_year_graduated']),
        'shs_school_address' => htmlspecialchars(trim($_POST['shs_school_address'])),
        'preferred_program' => $_POST['preferred_program'],
        'academic_term' => '1st Semester',
        'school_year' => '2026-2027',
        'guardian_name' => htmlspecialchars(trim($_POST['guardian_name'])),
        'guardian_relationship' => $_POST['guardian_relationship'],
        'emergency_phone' => htmlspecialchars(trim($_POST['emergency_phone']))
    ];

    header("Location: new_student_updocs.php");
    exit();
}
$form = $_SESSION['applicant_step1'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal</title>
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
            box-shadow: 0 0 15px #FFC10766;
        }

        .timeline-item.active .timeline-label {
            color: #000;
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

        .grid-col-8 {
            width: 66.6666%;
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

        .form-input:focus,
        .form-dropdown:focus {
            border-color: #0A1140;
            outline: none;
            box-shadow: none;
            background-color: #fff;
        }

        .form-input-locked {
            background-color: #e9ecef !important;
            color: #6c757d !important;
            cursor: not-allowed;
            border-color: #ced4da;
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

        .course-select-btn {
            background-color: #0A1140;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .course-select-btn:hover {
            background-color: #000;
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

        @media (max-width: 768px) {
            .top-navbar {
                padding: 14px 25px;
                gap: 20px;
            }

            .grid-col-3,
            .grid-col-4,
            .grid-col-6,
            .grid-col-8 {
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
            <div class="timeline-item active">
                <div class="timeline-bubble">1</div>
                <div class="timeline-label">Student Profile</div>
            </div>
            <div class="timeline-item">
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
            <h2>Application for Admission</h2>
            <p>Please provide accurate information as per your official legal documents. All fields marked with an
                asterisk (<span class="required-mark">*</span>) are required.</p>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-section-divider">
                <h4>I. Personal Demographics</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-3"><label>First Name <span class="required-mark">*</span></label><input type="text"
                        name="first_name" class="form-input" placeholder=""
                        value="<?php echo $form['first_name'] ?? ''; ?>" required></div>
                <div class="grid-col-3"><label>Middle Name</label><input type="text" name="middle_name"
                        class="form-input" placeholder="" value="<?php echo $form['middle_name'] ?? ''; ?>">
                </div>
                <div class="grid-col-3"><label>Last Name <span class="required-mark">*</span></label><input type="text"
                        name="last_name" class="form-input" placeholder=""
                        value="<?php echo $form['last_name'] ?? ''; ?>" required></div>
                <div class="grid-col-3"><label>Suffix</label><input type="text" name="suffix" class="form-input"
                        placeholder="e.g., Jr., Sr., III" value="<?php echo $form['suffix'] ?? ''; ?>"></div>
            </div>
            <div class="grid-row">
                <div class="grid-col-4"><label>Date of Birth <span class="required-mark">*</span></label><input
                        type="date" name="date_of_birth" class="form-input"
                        value="<?php echo $form['date_of_birth'] ?? ''; ?>" required></div>
                <div class="grid-col-4"><label>Gender <span class="required-mark">*</span></label>
                    <select name="gender" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['gender']) ? 'selected' : ''; ?>>Select Gender
                        </option>
                        <option value="Male" <?php echo isset($form['gender']) && $form['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo isset($form['gender']) && $form['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="grid-col-4"><label>Civil Status <span class="required-mark">*</span></label>
                    <select name="civil_status" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['civil_status']) ? 'selected' : ''; ?>>Select
                            Status</option>
                        <option value="Single" <?php echo isset($form['civil_status']) && $form['civil_status'] === 'Single' ? 'selected' : ''; ?>>Single</option>
                        <option value="Married" <?php echo isset($form['civil_status']) && $form['civil_status'] === 'Married' ? 'selected' : ''; ?>>Married</option>
                        <option value="Widowed" <?php echo isset($form['civil_status']) && $form['civil_status'] === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                    </select>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-col-6"><label>Nationality <span class="required-mark">*</span></label>
                    <select name="nationality" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['nationality']) ? 'selected' : ''; ?>>Select
                            Nationality</option>
                        <?php
                        $nationalities = ['Filipino', 'American', 'British', 'Canadian', 'Chinese', 'Japanese', 'Singaporean', 'South Korean'];
                        foreach ($nationalities as $nat) {
                            $selected = (isset($form['nationality']) && $form['nationality'] === $nat) ? 'selected' : '';
                            echo "<option value=\"$nat\" $selected>$nat</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="grid-col-6"><label>Religious Affiliation</label><input type="text"
                        name="religious_affiliation" class="form-input" placeholder="e.g., Roman Catholic, Islam, etc."
                        value="<?php echo $form['religious_affiliation'] ?? ''; ?>"></div>
            </div>

            <div class="form-section-divider">
                <h4>II. Contact & Location Information</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-6">
                    <label>Active Email Address <span class="required-mark">*</span></label>
                    <input type="email" name="email_address" class="form-input" placeholder="juan.delacruz@email.com"
                        value="<?php echo $form['email_address'] ?? ''; ?>" required>
                </div>
                <div class="grid-col-6">
                    <label>Mobile Number <span class="required-mark">*</span></label>
                    <input type="tel" name="mobile_number" class="form-input" placeholder="09XXXXXXXXX" minlength="11"
                        maxlength="11" pattern="09[0-9]{9}" value="<?php echo $form['mobile_number'] ?? ''; ?>"
                        required>
                </div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Region <span class="required-mark">*</span></label>
                    <select id="address_region" name="address_region" class="form-dropdown"
                        onchange="handleRegionChange()" required>
                        <option value="" disabled <?php echo !isset($form['address_region']) ? 'selected' : ''; ?>>
                            Select Region</option>
                        <option value="NCR" <?php echo isset($form['address_region']) && $form['address_region'] === 'NCR' ? 'selected' : ''; ?>>National Capital Region (NCR)</option>
                        <option value="Region III" <?php echo isset($form['address_region']) && $form['address_region'] === 'Region III' ? 'selected' : ''; ?>>Region III (Central Luzon)
                        </option>
                        <option value="Region IV-A" <?php echo isset($form['address_region']) && $form['address_region'] === 'Region IV-A' ? 'selected' : ''; ?>>Region IV-A (CALABARZON)
                        </option>
                    </select>
                </div>
                <div class="grid-col-4"><label>Province <span class="required-mark">*</span></label>
                    <select id="address_province" name="address_province" class="form-dropdown"
                        onchange="handleProvinceChange()" required>
                        <option value="" disabled selected>Select Region First</option>
                    </select>
                </div>
                <div class="grid-col-4"><label>City / Municipality <span class="required-mark">*</span></label>
                    <select id="address_city" name="address_city" class="form-dropdown" onchange="handleCityChange()"
                        required>
                        <option value="" disabled selected>Select Province First</option>
                    </select>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-col-4"><label>Barangay <span class="required-mark">*</span></label>
                    <select id="address_barangay" name="address_barangay" class="form-dropdown" required>
                        <option value="" disabled selected>Select City First</option>
                    </select>
                </div>
                <div class="grid-col-4"><label>Postal Code <span class="required-mark">*</span></label>
                    <select id="address_postal" name="address_postal" class="form-dropdown" required>
                        <option value="" disabled selected>Select Region First</option>
                    </select>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-col-12"><label>House No. / Street / Subdivision <span
                            class="required-mark">*</span></label><input type="text" name="address_street"
                        class="form-input" placeholder="House No., Street, Subdivision, Village"
                        value="<?php echo $form['address_street'] ?? ''; ?>" required></div>
            </div>

            <div class="form-section-divider">
                <h4>III. Senior High School Background</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-6"><label>Last SHS School Attended <span
                            class="required-mark">*</span></label><input type="text" name="shs_school_attended"
                        class="form-input" placeholder=""
                        value="<?php echo $form['shs_school_attended'] ?? ''; ?>" required></div>
                <div class="grid-col-6"><label>SHS Track & Strand <span class="required-mark">*</span></label>
                    <select name="shs_strand" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['shs_strand']) ? 'selected' : ''; ?>>Select
                            Strand</option>
                        <?php
                        $strands = ['STEM' => 'Academic - STEM (Science, Technology, Engineering, Mathematics)', 'ABM' => 'Academic - ABM (Accountancy, Business, Management)', 'HUMSS' => 'Academic - HUMSS (Humanities, Social Sciences)', 'GAS' => 'Academic - GAS (General Academic Strand)', 'TVL' => 'Technical-Vocational-Livelihood (TVL)', 'A&D' => 'Arts and Design', 'Sports' => 'Sports Track'];
                        foreach ($strands as $key => $label) {
                            $selected = (isset($form['shs_strand']) && $form['shs_strand'] === $key) ? 'selected' : '';
                            echo "<option value=\"$key\" $selected>$label</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="grid-col-6"><label>Year Completed / Graduated <span
                            class="required-mark">*</span></label><input type="number" name="shs_year_graduated"
                        class="form-input" placeholder="YYYY" min="1900" max="2030"
                        value="<?php echo $form['shs_year_graduated'] ?? ''; ?>" required></div>
                <div class="grid-col-6"><label>SHS School Address <span class="required-mark">*</span></label><input
                        type="text" name="shs_school_address" class="form-input" placeholder=""
                        value="<?php echo $form['shs_school_address'] ?? ''; ?>" required></div>
            </div>

            <div class="form-section-divider">
                <h4>IV. Course & Academic Preferences</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-12">
                    <label>Preferred College Program / Course <span class="required-mark">*</span></label>
                    <select id="preferred_program" name="preferred_program" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['preferred_program']) ? 'selected' : ''; ?>>
                            Select preferred program</option>
                        <option value="BSCS" <?php echo isset($form['preferred_program']) && $form['preferred_program'] === 'BSCS' ? 'selected' : ''; ?>>Bachelor of Science in Computer
                            Science (BSCS)</option>
                        <option value="BSIT" <?php echo isset($form['preferred_program']) && $form['preferred_program'] === 'BSIT' ? 'selected' : ''; ?>>Bachelor of Science in Information
                            Technology (BSIT)</option>
                    </select>
                </div>
                <div class="grid-col-6"><label>Academic Term Entering</label><input type="text" name="academic_term"
                        class="form-input form-input-locked" value="1st Semester" readonly></div>
                <div class="grid-col-6"><label>School Year (A.Y.)</label><input type="text" name="school_year"
                        class="form-input form-input-locked" value="2026-2027" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>V. Emergency Contact</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-4">
                    <label>Guardian Name <span class="required-mark">*</span></label>
                    <input type="text" name="guardian_name" class="form-input" placeholder=""
                        value="<?php echo $form['guardian_name'] ?? ''; ?>" required>
                </div>
                <div class="grid-col-4">
                    <label>Relationship <span class="required-mark">*</span></label>
                    <select name="guardian_relationship" class="form-dropdown" required>
                        <option value="" disabled <?php echo !isset($form['guardian_relationship']) ? 'selected' : ''; ?>>Select Relationship</option>
                        <?php
                        $relationships = ['Mother', 'Father', 'Grandmother', 'Grandfather', 'Aunt', 'Uncle', 'Sibling', 'Legal Guardian', 'Other'];
                        foreach ($relationships as $rel) {
                            $selected = (isset($form['guardian_relationship']) && $form['guardian_relationship'] === $rel) ? 'selected' : '';
                            echo "<option value=\"$rel\" $selected>$rel</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="grid-col-4">
                    <label>Emergency Phone <span class="required-mark">*</span></label>
                    <input type="tel" name="emergency_phone" class="form-input" placeholder="e.g., 09XX-XXX-XXXX"
                        minlength="11" maxlength="11" pattern="09[0-9]{9}"
                        value="<?php echo $form['emergency_phone'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="action-footer">
                <a href="../new_student_registration.php" class="btn-cancel-app"><i class="bi bi-arrow-left"></i>
                    Return</a>
                <button type="submit" class="btn-advance-step">Save and Proceed <i
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
                details, then use the button inside to select it.</p>
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
                                Structures, Machine Learning, and Algorithm Analysis.</p><button type="button"
                                class="course-select-btn" onclick="selectCourseAndClose('BSCS')"><i
                                    class="bi bi-check2-circle"></i> Select BSCS</button>
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
                                (DBMS), Network Security, and System Integration.</p><button type="button"
                                class="course-select-btn" onclick="selectCourseAndClose('BSIT')"><i
                                    class="bi bi-check2-circle"></i> Select BSIT</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const locationData = {
            "NCR": {
                provinces: ["Metro Manila"],
                cities: { "Metro Manila": ["Manila", "Quezon City", "Caloocan", "Malabon", "Navotas"] },
                barangays: {
                    "Manila": ["Barangay 101", "Barangay 102", "Barangay 201", "Poblacion"],
                    "Quezon City": ["Bagong Pag-asa", "Socorro", "Commonwealth"],
                    "Caloocan": ["Barangay 1", "Barangay 2"],
                    "Malabon": ["Hulong Duhat", "Catmon"],
                    "Navotas": ["Tangos", "San Jose"]
                },
                postalCodes: [
                    { value: "1000", label: "1000 - Manila Central" },
                    { value: "1012", label: "1012 - San Nicolas" },
                    { value: "1013", label: "1013 - Tondo" },
                    { value: "1100", label: "1100 - Quezon City Central" }
                ]
            },
            "Region III": {
                provinces: ["Bulacan", "Pampanga"],
                cities: { "Bulacan": ["Malolos", "Meycauayan"], "Pampanga": ["San Fernando", "Angeles"] },
                barangays: {
                    "Malolos": ["Guinhawa", "San Juan"], "Meycauayan": ["Bangcal", "Calvario"],
                    "San Fernando": ["Dolores", "San Jose"], "Angeles": ["Balibago", "Malabanias"]
                },
                postalCodes: [
                    { value: "3000", label: "3000 - Malolos, Bulacan" },
                    { value: "3020", label: "3020 - Meycauayan, Bulacan" },
                    { value: "2000", label: "2000 - San Fernando, Pampanga" }
                ]
            },
            "Region IV-A": {
                provinces: ["Cavite", "Laguna"],
                cities: { "Cavite": ["Cavite City", "Dasmarinas"], "Laguna": ["San Pablo City", "Calamba"] },
                barangays: {
                    "Cavite City": ["Barangay 1", "Barangay 2"], "Dasmarinas": ["Burol", "Salitran"],
                    "San Pablo City": ["Concepcion", "San Lucas"], "Calamba": ["Parian", "Real"]
                },
                postalCodes: [
                    { value: "4100", label: "4100 - Cavite City, Cavite" },
                    { value: "4114", label: "4114 - Dasmarinas, Cavite" },
                    { value: "4000", label: "4000 - San Pablo City, Laguna" }
                ]
            }
        };

        function handleRegionChange() {
            const region = document.getElementById("address_region").value;
            const provinceSelect = document.getElementById("address_province");
            provinceSelect.innerHTML = '<option value="" disabled selected>Select Province</option>';
            document.getElementById("address_city").innerHTML = '<option value="" disabled selected>Select Province First</option>';
            document.getElementById("address_barangay").innerHTML = '<option value="" disabled selected>Select City First</option>';
            if (region && locationData[region]) {
                locationData[region].provinces.forEach(prov => {
                    const opt = document.createElement("option"); opt.value = prov; opt.text = prov;
                    provinceSelect.appendChild(opt);
                });
            }
            updatePostalCodes(region);
        }

        function handleProvinceChange() {
            const region = document.getElementById("address_region").value;
            const province = document.getElementById("address_province").value;
            const citySelect = document.getElementById("address_city");
            citySelect.innerHTML = '<option value="" disabled selected>Select City / Municipality</option>';
            document.getElementById("address_barangay").innerHTML = '<option value="" disabled selected>Select City First</option>';
            if (region && province && locationData[region].cities[province]) {
                locationData[region].cities[province].forEach(city => {
                    const opt = document.createElement("option"); opt.value = city; opt.text = city;
                    citySelect.appendChild(opt);
                });
            }
        }

        function handleCityChange() {
            const region = document.getElementById("address_region").value;
            const city = document.getElementById("address_city").value;
            const brgySelect = document.getElementById("address_barangay");
            brgySelect.innerHTML = '<option value="" disabled selected>Select Barangay</option>';
            if (region && city && locationData[region].barangays[city]) {
                locationData[region].barangays[city].forEach(brgy => {
                    const opt = document.createElement("option"); opt.value = brgy; opt.text = brgy;
                    brgySelect.appendChild(opt);
                });
            }
        }

        function updatePostalCodes(region) {
            const postalSelect = document.getElementById("address_postal");
            postalSelect.innerHTML = "";
            if (region && locationData[region]) {
                locationData[region].postalCodes.forEach(code => {
                    const option = document.createElement("option"); option.value = code.value; option.text = code.label;
                    postalSelect.appendChild(option);
                });
            } else {
                const defaultOption = document.createElement("option"); defaultOption.value = ""; defaultOption.text = "Select Region First";
                defaultOption.disabled = true; defaultOption.selected = true;
                postalSelect.appendChild(defaultOption);
            }
        }

        window.onload = function () {
            const savedRegion = "<?php echo $form['address_region'] ?? ''; ?>";
            if (savedRegion !== "") {
                handleRegionChange();
                const savedProv = "<?php echo $form['address_province'] ?? ''; ?>";
                if (savedProv !== "") {
                    document.getElementById("address_province").value = savedProv; handleProvinceChange();
                    const savedCity = "<?php echo $form['address_city'] ?? ''; ?>";
                    if (savedCity !== "") {
                        document.getElementById("address_city").value = savedCity; handleCityChange();
                        const savedBrgy = "<?php echo $form['address_barangay'] ?? ''; ?>";
                        if (savedBrgy !== "") { document.getElementById("address_barangay").value = savedBrgy; }
                    }
                }
                const savedPostal = "<?php echo $form['address_postal'] ?? ''; ?>";
                if (savedPostal !== "") { document.getElementById("address_postal").value = savedPostal; }
            }
        };

        function toggleCourseDescription(courseId) { const targetItem = document.getElementById('item-' + courseId); const allItems = document.querySelectorAll('.course-item'); allItems.forEach(item => { if (item !== targetItem) { item.classList.remove('expanded'); } }); targetItem.classList.toggle('expanded'); }
        function selectCourseAndClose(courseValue) { const courseSelect = document.getElementById('preferred_program'); if (courseSelect) { courseSelect.value = courseValue; } window.location.hash = '#'; }
    </script>
</body>

</html>