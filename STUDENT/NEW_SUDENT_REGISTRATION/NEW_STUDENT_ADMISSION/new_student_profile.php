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

        /* NEW HEADER IMAGE STYLE */
        .header-banner {
            width: 100%;
            max-height: 400px;
            /* You can adjust this height to your liking */
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
        }
    </style>
</head>

<body>

    <nav class="top-navbar">
        <a href="#home">Home</a>
        <a href="#courses">Courses Offered</a>
    </nav>

    <img src="../../../images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">

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

        <form action="application_step2.html" method="post">

            <div class="form-section-divider">
                <h4>I. Personal Demographics</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-4">
                    <label>First Name <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="grid-col-4">
                    <label>Middle Name</label>
                    <input type="text" class="form-input">
                </div>
                <div class="grid-col-4">
                    <label>Last Name <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" required>
                </div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4">
                    <label>Date of Birth <span class="required-mark">*</span></label>
                    <input type="date" class="form-input" required>
                </div>
                <div class="grid-col-4">
                    <label>Gender <span class="required-mark">*</span></label>
                    <select class="form-dropdown" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="grid-col-4">
                    <label>Civil Status <span class="required-mark">*</span></label>
                    <select class="form-dropdown" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>
            </div>

            <div class="grid-row">
                <div class="grid-col-6">
                    <label>Nationality <span class="required-mark">*</span></label>
                    <select class="form-dropdown" required>
                        <option value="Filipino" selected>Filipino</option>
                        <option value="American">American</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="grid-col-6">
                    <label>Religious Affiliation</label>
                    <input type="text" class="form-input" placeholder="Optional">
                </div>
            </div>

            <div class="form-section-divider">
                <h4>II. Contact & Location Information</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-6">
                    <label>Active Email Address <span class="required-mark">*</span></label>
                    <input type="email" class="form-input" required>
                </div>
                <div class="grid-col-6">
                    <label>Mobile Number <span class="required-mark">*</span></label>
                    <input type="tel" class="form-input" placeholder="09XX-XXX-XXXX" required>
                </div>
                <div class="grid-col-12">
                    <label>Current Home Address <span class="required-mark">*</span></label>
                    <textarea class="form-input" rows="3"
                        placeholder="Street Name, Barangay, City/Municipality, Province" required></textarea>
                </div>
            </div>

            <div class="form-section-divider">
                <h4>III. Senior High School Background</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-6">
                    <label>Last SHS School Attended <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" placeholder="Senior High School Name" required>
                </div>
                <div class="grid-col-6">
                    <label>SHS Track & Strand <span class="required-mark">*</span></label>
                    <select class="form-dropdown" required>
                        <option value="" disabled selected>Select Strand</option>
                        <option value="STEM">Academic - STEM (Science, Technology, Engineering, Mathematics)</option>
                        <option value="ABM">Academic - ABM (Accountancy, Business, Management)</option>
                        <option value="HUMSS">Academic - HUMSS (Humanities, Social Sciences)</option>
                        <option value="GAS">Academic - GAS (General Academic Strand)</option>
                        <option value="TVL">Technical-Vocational-Livelihood (TVL)</option>
                        <option value="A&D">Arts and Design</option>
                        <option value="Sports">Sports Track</option>
                    </select>
                </div>
                <div class="grid-col-6">
                    <label>Year Completed / Graduated <span class="required-mark">*</span></label>
                    <input type="number" class="form-input" placeholder="YYYY" min="1900" max="2030" required>
                </div>
                <div class="grid-col-6">
                    <label>SHS School Address <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" placeholder="City, Province" required>
                </div>
            </div>

            <div class="form-section-divider">
                <h4>IV. Course & Academic Preferences</h4>
                <hr>
            </div>

            <div class="grid-row">
                <div class="grid-col-12">
                    <label>Preferred College Program / Course <span class="required-mark">*</span></label>
                    <select class="form-dropdown" required>
                        <option value="" disabled selected>Select preferred program</option>
                        <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                        <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                        <option value="BSBA">Bachelor of Science in Business Administration (BSBA)</option>
                        <option value="BSHM">Bachelor of Science in Hospitality Management (BSHM)</option>
                        <option value="BSED">Bachelor of Secondary Education (BSEd)</option>
                        <option value="BSCrim">Bachelor of Science in Criminology (BSCrim)</option>
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
                    <label>Guardian Name <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="grid-col-4">
                    <label>Relationship <span class="required-mark">*</span></label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="grid-col-4">
                    <label>Emergency Phone <span class="required-mark">*</span></label>
                    <input type="tel" class="form-input" required>
                </div>
            </div>

            <div class="action-footer">
                <a href="login.html" class="btn-cancel-app">
                    <i class="bi bi-arrow-left"></i> Return to Portal
                </a>
                <button type="submit" class="btn-advance-step">
                    Save and Proceed <i class="bi bi-chevron-right"></i>
                </button>
            </div>

        </form>
    </main>

</body>

</html>