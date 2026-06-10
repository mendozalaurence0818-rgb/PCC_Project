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

        .status-card {
            border-radius: 6px;
            padding: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            border: 1px solid transparent;
        }

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
            max-width: 550px;
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

            .grid-col-3,
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
            <div class="grid-col-3">
                <label>First Name</label>
                <input type="text" class="form-input form-input-locked" value="Joeshua" readonly>
            </div>
            <div class="grid-col-3">
                <label>Middle Name</label>
                <input type="text" class="form-input form-input-locked" value="Reyes" readonly>
            </div>
            <div class="grid-col-3">
                <label>Last Name</label>
                <input type="text" class="form-input form-input-locked" value="Santos" readonly>
            </div>
            <div class="grid-col-3">
                <label>Suffix</label>
                <input type="text" class="form-input form-input-locked" value="N/A" readonly>
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
                <select name="nationality" class="form-input form-input-locked" disabled>
                    <option value="" disabled selected>Select Nationality</option>
                    <option value="Filipino" selected>Filipino</option>
                    <option value="Afghan">Afghan</option>
                    <option value="Albanian">Albanian</option>
                    <option value="Algerian">Algerian</option>
                    <option value="American">American</option>
                    <option value="Andorran">Andorran</option>
                    <option value="Angolan">Angolan</option>
                    <option value="Argentine">Argentine</option>
                    <option value="Armenian">Armenian</option>
                    <option value="Australian">Australian</option>
                    <option value="Austrian">Austrian</option>
                    <option value="Azerbaijani">Azerbaijani</option>
                    <option value="Bahamian">Bahamian</option>
                    <option value="Bahraini">Bahraini</option>
                    <option value="Bangladeshi">Bangladeshi</option>
                    <option value="Barbadian">Barbadian</option>
                    <option value="Belarusian">Belarusian</option>
                    <option value="Belgian">Belgian</option>
                    <option value="Belizean">Belizean</option>
                    <option value="Beninese">Beninese</option>
                    <option value="Bhutanese">Bhutanese</option>
                    <option value="Bolivian">Bolivian</option>
                    <option value="Bosnian">Bosnian</option>
                    <option value="Brazilian">Brazilian</option>
                    <option value="British">British</option>
                    <option value="Bruneian">Bruneian</option>
                    <option value="Bulgarian">Bulgarian</option>
                    <option value="Burkinabe">Burkinabe</option>
                    <option value="Burmese">Burmese</option>
                    <option value="Burundian">Burundian</option>
                    <option value="Cambodian">Cambodian</option>
                    <option value="Cameroonian">Cameroonian</option>
                    <option value="Canadian">Canadian</option>
                    <option value="Cape Verdean">Cape Verdean</option>
                    <option value="Central African">Central African</option>
                    <option value="Chadian">Chadian</option>
                    <option value="Chilean">Chilean</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Colombian">Colombian</option>
                    <option value="Comoran">Comoran</option>
                    <option value="Congolese">Congolese</option>
                    <option value="Costa Rican">Costa Rican</option>
                    <option value="Croatian">Croatian</option>
                    <option value="Cuban">Cuban</option>
                    <option value="Cypriot">Cypriot</option>
                    <option value="Czech">Czech</option>
                    <option value="Danish">Danish</option>
                    <option value="Djiboutian">Djiboutian</option>
                    <option value="Dominican">Dominican</option>
                    <option value="Dutch">Dutch</option>
                    <option value="East Timorese">East Timorese</option>
                    <option value="Ecuadorian">Ecuadorian</option>
                    <option value="Egyptian">Egyptian</option>
                    <option value="Emirati">Emirati</option>
                    <option value="Equatorial Guinean">Equatorial Guinean</option>
                    <option value="Eritrean">Eritrean</option>
                    <option value="Estonian">Estonian</option>
                    <option value="Ethiopian">Ethiopian</option>
                    <option value="Fijian">Fijian</option>
                    <option value="Finnish">Finnish</option>
                    <option value="French">French</option>
                    <option value="Gabonese">Gabonese</option>
                    <option value="Gambian">Gambian</option>
                    <option value="Georgian">Georgian</option>
                    <option value="German">German</option>
                    <option value="Ghanaian">Ghanaian</option>
                    <option value="Greek">Greek</option>
                    <option value="Grenadian">Grenadian</option>
                    <option value="Guatemalan">Guatemalan</option>
                    <option value="Guinean">Guinean</option>
                    <option value="Guyanese">Guyanese</option>
                    <option value="Haitian">Haitian</option>
                    <option value="Honduran">Honduran</option>
                    <option value="Hungarian">Hungarian</option>
                    <option value="Icelandic">Icelandic</option>
                    <option value="Indian">Indian</option>
                    <option value="Indonesian">Indonesian</option>
                    <option value="Iranian">Iranian</option>
                    <option value="Iraqi">Iraqi</option>
                    <option value="Irish">Irish</option>
                    <option value="Israeli">Israeli</option>
                    <option value="Italian">Italian</option>
                    <option value="Ivorian">Ivorian</option>
                    <option value="Jamaican">Jamaican</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Jordanian">Jordanian</option>
                    <option value="Kazakh">Kazakh</option>
                    <option value="Kenyan">Kenyan</option>
                    <option value="Kiribati">Kiribati</option>
                    <option value="Kuwaiti">Kuwaiti</option>
                    <option value="Kyrgyz">Kyrgyz</option>
                    <option value="Laotian">Laotian</option>
                    <option value="Latvian">Latvian</option>
                    <option value="Lebanese">Lebanese</option>
                    <option value="Liberian">Liberian</option>
                    <option value="Libyan">Libyan</option>
                    <option value="Liechtenstein citizen">Liechtenstein citizen</option>
                    <option value="Lithuanian">Lithuanian</option>
                    <option value="Luxembourgish">Luxembourgish</option>
                    <option value="Macedonian">Macedonian</option>
                    <option value="Malagasy">Malagasy</option>
                    <option value="Malawian">Malawian</option>
                    <option value="Malaysian">Malaysian</option>
                    <option value="Maldivian">Maldivian</option>
                    <option value="Malian">Malian</option>
                    <option value="Maltese">Maltese</option>
                    <option value="Marshallese">Marshallese</option>
                    <option value="Mauritanian">Mauritanian</option>
                    <option value="Mauritian">Mauritian</option>
                    <option value="Mexican">Mexican</option>
                    <option value="Micronesian">Micronesian</option>
                    <option value="Moldovan">Moldovan</option>
                    <option value="Monegasque">Monegasque</option>
                    <option value="Mongolian">Mongolian</option>
                    <option value="Montenegrin">Montenegrin</option>
                    <option value="Moroccan">Moroccan</option>
                    <option value="Mozambican">Mozambican</option>
                    <option value="Namibian">Namibian</option>
                    <option value="Nauruan">Nauruan</option>
                    <option value="Nepalese">Nepalese</option>
                    <option value="New Zealander">New Zealander</option>
                    <option value="Nicaraguan">Nicaraguan</option>
                    <option value="Nigerian">Nigerian</option>
                    <option value="Nigerien">Nigerien</option>
                    <option value="North Korean">North Korean</option>
                    <option value="Norwegian">Norwegian</option>
                    <option value="Omani">Omani</option>
                    <option value="Pakistani">Pakistani</option>
                    <option value="Palauan">Palauan</option>
                    <option value="Palestinian">Palestinian</option>
                    <option value="Panamanian">Panamanian</option>
                    <option value="Papua New Guinean">Papua New Guinean</option>
                    <option value="Paraguayan">Paraguayan</option>
                    <option value="Peruvian">Peruvian</option>
                    <option value="Polish">Polish</option>
                    <option value="Portuguese">Portuguese</option>
                    <option value="Qatari">Qatari</option>
                    <option value="Romanian">Romanian</option>
                    <option value="Russian">Russian</option>
                    <option value="Rwandan">Rwandan</option>
                    <option value="Saint Lucian">Saint Lucian</option>
                    <option value="Salvadoran">Salvadoran</option>
                    <option value="Samoan">Samoan</option>
                    <option value="San Marinese">San Marinese</option>
                    <option value="Sao Tomean">Sao Tomean</option>
                    <option value="Saudi">Saudi</option>
                    <option value="Scottish">Scottish</option>
                    <option value="Senegalese">Senegalese</option>
                    <option value="Serbian">Serbian</option>
                    <option value="Seychellois">Seychellois</option>
                    <option value="Sierra Leonean">Sierra Leonean</option>
                    <option value="Singaporean">Singaporean</option>
                    <option value="Slovak">Spacer</option>
                    <option value="Slovenian">Slovenian</option>
                    <option value="Solomon Islander">Solomon Islander</option>
                    <option value="Somali">Somali</option>
                    <option value="South African">South African</option>
                    <option value="South Korean">South Korean</option>
                    <option value="Spanish">Spanish</option>
                    <option value="Sri Lankan">Sri Lankan</option>
                    <option value="Sudanese">Sudanese</option>
                    <option value="Surinamese">Surinamese</option>
                    <option value="Swazi">Swazi</option>
                    <option value="Swedish">Swedish</option>
                    <option value="Swiss">Swiss</option>
                    <option value="Syrian">Syrian</option>
                    <option value="Taiwanese">Taiwanese</option>
                    <option value="Tajik">Tajik</option>
                    <option value="Tanzanian">Tanzanian</option>
                    <option value="Thai">Thai</option>
                    <option value="Togolese">Togolese</option>
                    <option value="Tongan">Tongan</option>
                    <option value="Trinidadian">Trinidadian</option>
                    <option value="Tunisian">Tunisian</option>
                    <option value="Turkish">Turkish</option>
                    <option value="Turkmen">Turkmen</option>
                    <option value="Tuvaluan">Tuvaluan</option>
                    <option value="Ugandan">Ugandan</option>
                    <option value="Ukrainian">Ukrainian</option>
                    <option value="Uruguayan">Uruguayan</option>
                    <option value="Uzbek">Uzbek</option>
                    <option value="Vanuatu citizen">Vanuatu citizen</option>
                    <option value="Venezuelan">Venezuelan</option>
                    <option value="Vietnamese">Vietnamese</option>
                    <option value="Welsh">Welsh</option>
                    <option value="Yemeni">Yemeni</option>
                    <option value="Zambian">Zambian</option>
                    <option value="Zimbabwean">Zimbabwean</option>
                </select>
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
                <select name="shs_strand" class="form-input form-input-locked" disabled>
                    <option value="" disabled selected>Select Strand</option>
                    <option value="STEM" selected>Academic - STEM (Science, Technology, Engineering, Mathematics)
                    </option>
                    <option value="ABM">Academic - ABM (Accountancy, Business, Management)</option>
                    <option value="HUMSS">Academic - HUMSS (Humanities, Social Sciences)</option>
                    <option value="GAS">Academic - GAS (General Academic Strand)</option>
                    <option value="TVL">Technical-Vocational-Livelihood (TVL)</option>
                    <option value="A&D">Arts and Design</option>
                    <option value="Sports">Sports Track</option>
                </select>
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
                <select name="preferred_program" class="form-input form-input-locked" disabled>
                    <option value="" disabled>Select preferred program</option>
                    <option value="BSCS" selected>Bachelor of Science in Computer Science (BSCS)</option>
                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                </select>
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