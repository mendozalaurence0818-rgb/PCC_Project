<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Student Portal - Enrollment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link class="rtl_container" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Required for native HTML Modal and Tab popups -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --pcc-blue: #002c5e;
            --pcc-gold: #f1b813;
            --pcc-blue-dark: #001d3d;
            --pcc-gray: #6c757d;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f4f6f9 !important;
        }

        .sidebar-bg { background-color: var(--pcc-blue) !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: var(--pcc-blue) !important; background-color: var(--pcc-gold) !important; font-weight: 600; }
        
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: var(--pcc-blue-dark); }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .user-info .status-text { color: var(--pcc-gold); }
        
        .enrollment-card { border: none; box-shadow: 0 0 25px rgba(0,0,0,0.06); border-radius: 12px; overflow: hidden; }

        .schedule-option, .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #fff;
            position: relative;
        }
        .schedule-option:hover, .payment-option:hover { 
            background-color: #fafbfc; 
            border-color: #ced4da;
            transform: translateY(-2px);
        }
        
        .form-check-input:checked { background-color: var(--pcc-blue); border-color: var(--pcc-blue); }
        .payment-option:has(input:checked) { border-color: var(--pcc-blue); background-color: #f7faff; }
        
        .nav-pills .nav-link { color: #495057; font-weight: 600; padding: 12px 24px; border: 1px solid transparent; transition: all 0.2s; }
        .nav-pills .nav-link.active { background-color: var(--pcc-blue); color: #fff; box-shadow: 0 4px 10px rgba(0, 44, 94, 0.15); }
        
        .btn-pcc-primary { background-color: var(--pcc-blue); color: #fff; }
        .btn-pcc-primary:hover { background-color: var(--pcc-blue-dark); color: #fff; }

        /* Real-Time Calendar Grid Styles */
        .schedule-grid-table th, .schedule-grid-table td {
            border: 1px solid #edeff1;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
            font-size: 0.8rem;
            height: 50px;
        }
        .schedule-grid-table thead th {
            background-color: #f8f9fa !important;
            color: var(--pcc-blue) !important;
            font-weight: 700;
        }
        .time-col { font-weight: 600; background-color: #f8f9fa; width: 110px; }
        .staged-slot { background-color: #e6f2ff; color: #002c5e; font-weight: 700; border: 1px solid #b3d7ff; border-radius: 4px; }
        .conflict-slot { background-color: #ffeef0; color: #dc3545; font-weight: 700; border: 1px solid #fecdd3; border-radius: 4px; }
        
        /* Official COR Layout Simulation Styles */
        .cor-watermark { border: 2px solid #002c5e; padding: 30px; background-color: #fff; position: relative; border-radius: 8px; }
        .qr-box { border: 2px dashed #ced4da; padding: 15px; text-align: center; background: #fdfdfd; border-radius: 8px; width: 140px; }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header Section -->
        <nav class="app-header navbar navbar-expand bg-body shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date fw-semibold text-secondary" style="margin-top:6px; margin-bottom: 9px; font-size: 0.9rem;">
                                <i class="bi bi-clock-history me-2"></i>
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <?php echo date('F j, Y') . " - " . date("h:iA"); ?>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar Navigation -->
        <aside class="app-sidebar sidebar-bg shadow">
            <div class="sidebar-brand" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link d-flex align-items-center py-3">
                    <img src="../../images/PCC_Logo.png" alt="PCC Logo" class="brand-image me-2" style="max-height: 35px;" />
                    <span class="brand-text fw-bold text-white fs-5">PCC Student</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder"><i class="fa-solid fa-user-graduate"></i></div>
                        </div>
                        <div class="user-info">
                            <div class="username text-truncate" style="max-width: 140px;">Juan Dela Cruz</div>
                            <div class="status-text fw-semibold small">BSIT - 4th Year</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column mt-3" id="navigation">
                        <li class="nav-header opacity-75 text-uppercase tracking-wider fs-7 px-4 mb-2">Registration Shuttle</li>
                        <li class="nav-item px-2">
                            <a href="#" class="nav-link sidebar-bg-active rounded">
                                <i class="nav-icon bi bi-card-checklist me-2"></i>
                                <span>Online Enrollment</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="app-main py-4">
            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-12">
                            <h3 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Semestral Enrollment Portal</h3>
                            <p class="text-muted small mb-3 fw-medium">Academic Year 2026-2027 | 1st Semester</p>
                            
                            <!-- MODERN STEP FLOW SEPARATOR PANEL -->
                            <ul class="nav nav-pills bg-white p-2 rounded shadow-sm border" id="enrollmentSteps" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-pill text-nowrap" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1-builder" type="button" role="tab">
                                        <i class="bi bi-calendar3 me-2"></i>1. Interactive Schedule Builder
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill text-nowrap" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2-payment" type="button" role="tab">
                                        <i class="bi bi-credit-card-2-front me-2"></i>2. Tuition Assessment & Payment
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill text-nowrap" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3-cor" type="button" role="tab">
                                        <i class="bi bi-file-earmark-check me-2"></i>3. Official COR View
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="tab-content" id="enrollmentStepContent">

                            <!-- ========================================== -->
                            <!-- STEP 1: INTERACTIVE SCHEDULE BUILDER       -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade show active" id="step1-builder" role="tabpanel">
                                <div class="row g-4">
                                    <!-- LEFT COLUMN: Subject Directory Sidebar & Rule Validator -->
                                    <div class="col-xl-4 col-lg-5">
                                        <!-- Subject Directory Sidebar -->
                                        <div class="card enrollment-card mb-4">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-search me-2 text-primary"></i>Subject Directory</h5>
                                            </div>
                                            <div class="card-body p-3">
                                                <input type="text" class="form-content form-control form-control-sm mb-3" placeholder="Search Open Courses (e.g., Core, GE)...">
                                                
                                                <!-- Available Sections Accordion Container -->
                                                <div class="accordion" id="subjectAccordion">
                                                    
                                                    <!-- IT411 Accordion Frame (2 Sections) -->
                                                    <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                                        <h2 class="accordion-header" id="headingIT411">
                                                            <button class="accordion-button collapsed fw-bold py-2 bg-light text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIT411">
                                                                IT411 - Capstone Project 1 (Core)
                                                            </button>
                                                        </h2>
                                                        <div id="collapseIT411" class="accordion-collapse collapse" data-bs-parent="#subjectAccordion">
                                                            <div class="accordion-body p-2 bg-white">
                                                                <div class="p-2 border rounded mb-2 small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec A (MWF 9-10:30AM)</span><span class="text-success">Seats: 35/40</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Prof. A. Santos | Room: Lab 1</div>
                                                                    <button type="button" class="btn btn-sm btn-pcc-primary py-0 px-2 mt-1 rounded-pill">Stage Section</button>
                                                                </div>
                                                                <div class="p-2 border rounded small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec B (TTH 1-3:30PM)</span><span class="text-danger">Full (40/40)</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Dr. E. Ramos | Room: Lab 2</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- IT412 Accordion Frame (2 Sections) -->
                                                    <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                                        <h2 class="accordion-header" id="headingIT412">
                                                            <button class="accordion-button collapsed fw-bold py-2 bg-light text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIT412">
                                                                IT412 - Information Assurance 2 (Core)
                                                            </button>
                                                        </h2>
                                                        <div id="collapseIT412" class="accordion-collapse collapse" data-bs-parent="#subjectAccordion">
                                                            <div class="accordion-body p-2 bg-white">
                                                                <div class="p-2 border rounded mb-2 small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec A (MWF 11AM-12PM)</span><span class="text-success">Seats: 12/35</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Prof. M. Torres | Room: Lec 304</div>
                                                                    <button type="button" class="btn btn-sm btn-pcc-primary py-0 px-2 mt-1 rounded-pill">Stage Section</button>
                                                                </div>
                                                                <div class="p-2 border rounded small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec B (TTH 10:30AM-12:00PM)</span><span class="text-success">Seats: 25/35</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Dr. R. Reyes | Room: Lec 202</div>
                                                                    <button type="button" class="btn btn-sm btn-pcc-primary py-0 px-2 mt-1 rounded-pill">Stage Section</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PE400 Accordion Frame (2 Sections) -->
                                                    <div class="accordion-item border rounded-3 mb-0 overflow-hidden">
                                                        <h2 class="accordion-header" id="headingPE400">
                                                            <button class="accordion-button collapsed fw-bold py-2 bg-light text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePE400">
                                                                PE400 - Advanced Team Sports (GE Elective)
                                                            </button>
                                                        </h2>
                                                        <div id="collapsePE400" class="accordion-collapse collapse" data-bs-parent="#subjectAccordion">
                                                            <div class="accordion-body p-2 bg-white">
                                                                <div class="p-2 border rounded mb-2 small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec A (SAT 7-09:00AM)</span><span class="text-warning">Seats: 38/40</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Coach J. Perez | Room: Gym</div>
                                                                    <button type="button" class="btn btn-sm btn-pcc-primary py-0 px-2 mt-1 rounded-pill">Stage Section</button>
                                                                </div>
                                                                <div class="p-2 border rounded small bg-light-subtle">
                                                                    <div class="d-flex justify-content-between fw-bold"><span>Sec B (SAT 1:00PM-3:00PM)</span><span class="text-success">Seats: 15/40</span></div>
                                                                    <div class="text-muted mt-1">Faculty: Coach M. Santos | Room: Court 2</div>
                                                                    <button type="button" class="btn btn-sm btn-pcc-primary py-0 px-2 mt-1 rounded-pill">Stage Section</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Unit Counter & Rule Validator Sidebar -->
                                        <div class="card enrollment-card mb-4 border-start border-4 border-warning bg-light-subtle">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h6 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-warning"></i>Unit Counter & Rules</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between small fw-bold text-secondary mb-1">
                                                        <span>Cumulative Credit Load:</span>
                                                        <span class="text-dark">8 / 21 Max Units</span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 38%"></div>
                                                    </div>
                                                </div>
                                                
                                                <ul class="list-unstyled small mb-0 text-secondary">
                                                    <li class="mb-2 text-success"><i class="bi bi-check-circle-fill me-2"></i>Prerequisite Evaluation Resolved</li>
                                                    <li class="mb-2 text-success"><i class="bi bi-check-circle-fill me-2"></i>Year Level Constraint Cleared</li>
                                                    <li class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Constraint Alert: Total units under full-time limit.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIGHT COLUMN: Real-Time Visual Schedule Grid Calendar Display -->
                                    <div class="col-xl-8 col-lg-7">
                                        <div class="card enrollment-card h-100">
                                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-calendar-week me-2 text-secondary"></i>Real-Time Visual Schedule Grid</h5>
                                                <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill small fw-bold">Live Conflict Matrix</span>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="table-responsive">
                                                    <table class="table schedule-grid-table table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th class="time-col">Time Block</th>
                                                                <th>Mon</th>
                                                                <th>Tue</th>
                                                                <th>Wed</th>
                                                                <th>Thu</th>
                                                                <th>Fri</th>
                                                                <th>Sat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="time-col">07:00 - 09:00 AM</td>
                                                                <td></td><td></td><td></td><td></td><td></td>
                                                                <td class="staged-slot">PE400<br><span class="fw-normal fs-8">Gym</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="time-col">09:00 - 10:30 AM</td>
                                                                <td class="staged-slot">IT411<br><span class="fw-normal fs-8">Lab 1</span></td>
                                                                <td></td>
                                                                <td class="staged-slot">IT411<br><span class="fw-normal fs-8">Lab 1</span></td>
                                                                <td></td>
                                                                <td class="staged-slot">IT411<br><span class="fw-normal fs-8">Lab 1</span></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="time-col">11:00 - 12:00 PM</td>
                                                                <td class="staged-slot">IT412<br><span class="fw-normal fs-8">Lec 304</span></td>
                                                                <td></td>
                                                                <td class="staged-slot">IT412<br><span class="fw-normal fs-8">Lec 304</span></td>
                                                                <td></td>
                                                                <td class="staged-slot">IT412<br><span class="fw-normal fs-8">Lec 304</span></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="time-col">01:00 - 03:30 PM</td>
                                                                <td></td>
                                                                <td class="text-muted bg-light fs-8">Staging Conflict Slot Window</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top py-3 text-end">
                                                <button type="button" onclick="document.getElementById('step2-tab').click();" class="btn btn-pcc-primary fw-semibold px-4 py-2">Lock Classes & Assessment <i class="bi bi-arrow-right-circle ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- STEP 2: TUITION ASSESSMENT & PAYMENT VIEW  -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade" id="step2-payment" role="tabpanel">
                                <div class="row g-4">
                                    <!-- LEFT COLUMN: Itemized Invoice & Discounts Section -->
                                    <div class="col-xl-8 col-lg-7">
                                        <!-- Itemized Invoice Ledger -->
                                        <div class="card enrollment-card mb-4">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Itemized Invoice Ledger</h5>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table align-middle mb-0">
                                                        <thead class="table-light text-secondary small text-uppercase">
                                                            <tr>
                                                                <th class="ps-4">Operational utility details</th>
                                                                <th>Units / Basis</th>
                                                                <th class="pe-4 text-end">Rate Cost</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-secondary small">
                                                            <tr>
                                                                <td class="ps-4 fw-bold text-dark">Tuition Fee Assessment</td>
                                                                <td>8 Units Standard</td>
                                                                <td class="pe-4 text-end fw-semibold text-dark">₱12,400.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="ps-4 fw-bold text-dark">Specific Lab Utility Allocations</td>
                                                                <td>IT411 Capstone Resource Access</td>
                                                                <td class="pe-4 text-end fw-semibold text-dark">₱2,100.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="ps-4 fw-bold text-dark">Miscellaneous Operational Infrastructure Fees</td>
                                                                <td>Registration, Library, Athletics, Medical</td>
                                                                <td class="pe-4 text-end fw-semibold text-dark">₱4,500.00</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Discounts & Scholarships Panel -->
                                        <div class="card enrollment-card mb-4 border-start border-4 border-success bg-white">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h6 class="card-title mb-0 fw-bold text-success"><i class="bi bi-patch-check-fill me-2"></i>Discounts & Scholarships Panel</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="d-flex justify-content-between align-items-center small border-bottom pb-2 mb-2">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">Institutional Academic Scholarship Grant</span>
                                                        <small class="text-muted">Maintained 1.75 Cumulative GPA Standing Threshold</small>
                                                    </div>
                                                    <span class="fw-bold text-success">- ₱5,000.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center small">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">Recognized Working-Student Tuition Exception</span>
                                                        <small class="text-muted">Office of the Dean Endorsement Token Waiver</small>
                                                    </div>
                                                    <span class="fw-bold text-muted">₱0.00 (Inactive)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIGHT COLUMN: Plan Selector & Payment Gateway -->
                                    <div class="col-xl-4 col-lg-5">
                                        <!-- Payment Plan Selector Toggle -->
                                        <div class="card enrollment-card mb-4">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h6 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Payment Plan Selector</h6>
                                            </div>
                                            <div class="card-body">
                                                <label class="d-block payment-option m-0 p-3 mb-2">
                                                    <div class="form-check d-flex m-0 p-0 align-items-center">
                                                        <input class="form-check-input me-3" type="radio" name="payment_term" value="full" checked>
                                                        <div class="w-100 d-flex justify-content-between align-items-center small">
                                                            <span class="fw-bold text-dark">Full Payment Option</span>
                                                            <span class="badge bg-success-subtle text-success">5% Subtraction Apply</span>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="d-block payment-option m-0 p-3">
                                                    <div class="form-check d-flex m-0 p-0 align-items-center">
                                                        <input class="form-check-input me-3" type="radio" name="payment_term" value="installment">
                                                        <span class="fw-bold text-dark small">Multi-Tiered Installment Schedule</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Payment Gateway Interface -->
                                        <div class="card enrollment-card mb-4 border-top border-4 border-primary">
                                            <div class="card-header bg-white py-3 border-bottom">
                                                <h6 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Payment Gateway Interface</h6>
                                            </div>
                                            <div class="card-body small text-secondary">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-dark">Select Direct Link Route</label>
                                                    <select class="form-select form-select-sm">
                                                        <option>Secure Digital Card Processing (Visa/Mastercard)</option>
                                                        <option>Direct Electronic Banking Transfer Link</option>
                                                        <option>Visual Over-the-Counter Proof Uploader</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-dark">Upload Proof of Payment Asset</label>
                                                    <input type="file" name="payment_proof" class="form-control form-control-sm">
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark">Net Assessment Total:</span>
                                                    <h5 class="fw-bold text-primary mb-0">₱14,000.00</h5>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top py-3 text-center">
                                                <button type="button" onclick="document.getElementById('step3-tab').click();" class="btn btn-success text-white fw-bold w-100 py-2"><i class="bi bi-credit-card-fill me-2"></i>Execute Payment Checkout</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- STEP 3: OFFICIAL COR VIEW                  -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade" id="step3-cor" role="tabpanel">
                                <div class="card enrollment-card cor-watermark mx-auto shadow-lg mb-4" style="max-width: 900px;">
                                    
                                    <!-- Institutional Header Section -->
                                    <div class="row align-items-center border-bottom pb-3 mb-4">
                                        <div class="col-md-2 text-center text-md-start">
                                            <img src="../../images/PCC_Logo.png" alt="PCC Logo" style="max-height: 70px;">
                                        </div>
                                        <div class="col-md-7 text-center text-md-start mt-2 mt-md-0">
                                            <h4 class="fw-bold text-dark mb-0">POBLACION CENTRAL COLLEGE</h4>
                                            <span class="text-uppercase tracking-wider small text-secondary fw-semibold">Official Certificate of Registration (COR)</span>
                                            <div class="small text-muted mt-1 fw-medium">Active Academic Cycle: First Semester, AY 2026-2027</div>
                                        </div>
                                        <div class="col-md-3 text-center text-md-end mt-3 mt-md-0">
                                            <div class="small border p-2 bg-light rounded text-start font-monospace fs-8">
                                                <strong>Token:</strong> COR-9941A2<br>
                                                <strong>Status:</strong> IMMUTABLE DOCUMENT
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Finalized Student Info Metadata Frame -->
                                    <div class="row g-3 text-secondary small mb-4 bg-light p-3 rounded mx-0">
                                        <div class="col-sm-4"><strong>Student ID:</strong> 2024-001234</div>
                                        <div class="col-sm-4"><strong>Student Name:</strong> Juan Dela Cruz</div>
                                        <div class="col-sm-4"><strong>Program Track:</strong> BSIT - 4th Year</div>
                                    </div>

                                    <!-- Finalized Schedule Table -->
                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-table me-2 text-secondary"></i>Finalized Course Roster Table</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-striped align-middle mb-0 small text-secondary">
                                            <thead class="table-light text-dark fw-bold">
                                                <tr>
                                                    <th>Course Code</th>
                                                    <th>Subject Narrative Description</th>
                                                    <th class="text-center">Units</th>
                                                    <th>Assigned Facility Room Layout</th>
                                                    <th>Faculty Registry Authority</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold text-dark">IT411</td>
                                                    <td>Capstone Project 1 (Proposal & Prototyping)</td>
                                                    <td class="text-center">3</td>
                                                    <td>Laboratory Station 1</td>
                                                    <td>Prof. A. Santos</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-dark">IT412</td>
                                                    <td>Information Assurance and Security 2</td>
                                                    <td class="text-center">3</td>
                                                    <td>Lecture Hall 304</td>
                                                    <td>Prof. M. Torres</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-dark">PE400</td>
                                                    <td>Advanced Team Sports Elective</td>
                                                    <td class="text-center">2</td>
                                                    <td>Physical Campus Gymnasium</td>
                                                    <td>Coach J. Perez</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Lower Block Footer containing Verification Box -->
                                    <div class="row align-items-center pt-3 border-top mt-4 g-4">
                                        <div class="col-md-8 text-center text-md-start text-secondary small">
                                            <p class="mb-1 fw-bold text-dark"><i class="bi bi-shield-fill-check me-1 text-success"></i>Authenticity Verification Guarantee</p>
                                            <p class="mb-0 text-muted fs-8">This document represents an authenticated, electronically signed certificate. Any structural changes invalidates processing credentials.</p>
                                        </div>
                                        
                                        <!-- Verification QR Code Security Box -->
                                        <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                                            <div class="qr-box shadow-sm text-center">
                                                <i class="bi bi-qr-code-scan text-dark display-6 mb-1 d-block"></i>
                                                <span class="font-monospace text-uppercase text-muted d-block" style="font-size: 0.65rem;">Cryptographic Facility Entrance Token</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="text-center">
                                    <button type="button" onclick="window.print();" class="btn btn-outline-secondary px-4 py-2 fw-semibold shadow-sm"><i class="bi bi-printer me-2"></i>Print Official COR Document</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Required Bootstrap Bundle for enabling Tabs and Modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>