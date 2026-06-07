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
        
        /* Modern Card Customizations */
        .enrollment-card { border: none; box-shadow: 0 0 25px rgba(0,0,0,0.06); border-radius: 12px; overflow: hidden; }

        /* Custom Interactive Selection Cards */
        .schedule-option, .block-option, .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #fff;
            position: relative;
        }
        .schedule-option:hover, .block-option:hover, .payment-option:hover { 
            background-color: #fafbfc; 
            border-color: #ced4da;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        
        /* Interactive Selections Natively Driven by Form Elements */
        .form-check-input { cursor: pointer; }
        .form-check-input:checked { background-color: var(--pcc-blue); border-color: var(--pcc-blue); }
        .form-check-input:checked + .schedule-details, .form-check-input:checked + .payment-details { font-weight: bold; color: var(--pcc-blue); }
        .block-option:has(input:checked), .payment-option:has(input:checked) { border-color: var(--pcc-blue); background-color: #f7faff; box-shadow: 0 4px 15px rgba(0, 44, 94, 0.06); }
        .block-option:has(input:checked)::before { content: '\f26b'; font-family: 'bootstrap-icons'; position: absolute; top: 18px; right: 20px; color: var(--pcc-blue); font-size: 1.2rem; }
        
        /* PCC Nav Pill Accent Refinements */
        .nav-pills .nav-link { color: #495057; font-weight: 600; padding: 10px 24px; border: 1px solid transparent; transition: all 0.2s; }
        .nav-pills .nav-link:hover { background-color: #eeddb21a; color: var(--pcc-blue); }
        .nav-pills .nav-link.active { background-color: var(--pcc-blue); color: #fff; font-weight: 600; box-shadow: 0 4px 10px rgba(0, 44, 94, 0.15); }
        
        /* Customized Form Actions Buttons */
        .btn-pcc-primary { background-color: var(--pcc-blue); color: #fff; transition: all 0.2s; }
        .btn-pcc-primary:hover { background-color: var(--pcc-blue-dark); color: #fff; box-shadow: 0 4px 12px rgba(0, 44, 94, 0.2); }
        .btn-outline-pcc { border-color: var(--pcc-blue); color: var(--pcc-blue); }
        .btn-outline-pcc:hover { background-color: var(--pcc-blue); color: #fff; }

        /* Crisp Table Cleanups */
        .table th { font-weight: 700; letter-spacing: 0.5px; background-color: #f8f9fa !important; border-bottom: 2px solid #edeff1 !important; }
        .table tbody tr { transition: background-color 0.15s; }
        .table-hover tbody tr:hover { background-color: #fdfdfd; }
        .table tr:has(input[type="checkbox"]:checked) { background-color: #fcfdfe; }
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
                            <div class="status-text fw-semibold small">BSIT - Incoming 4th Year</div>
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
                        <div class="col-sm-6">
                            <h3 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Semestral Enrollment Portal</h3>
                            <p class="text-muted small mb-0 fw-medium">Academic Year 2026-2027 | 1st Semester</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">

                    <!-- Main Core Setup Window Wrapping Tabs, Tables, and Selection Blocks -->
                    <form action="" method="POST">
                        <div class="row">
                            
                            <!-- LEFT COLUMN: Main Scheduling Area (Full Schedule View) -->
                            <div class="col-xl-8 col-lg-7">
                                <div class="card enrollment-card mb-4">
                                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                            <i class="bi bi-book-half me-2 text-secondary"></i>Schedule Allocation
                                        </h5>
                                        
                                        <!-- Functional Layout Control Toggle Tabs -->
                                        <ul class="nav nav-pills" id="enrollmentModeTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active rounded-pill px-4" id="block-tab" data-bs-toggle="tab" data-bs-target="#block-mode" type="button" role="tab">
                                                    <i class="bi bi-grid-1x2-fill me-2"></i>Block Section
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill px-4" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual-mode" type="button" role="tab">
                                                    <i class="bi bi-list-check me-2"></i>Individual Subjects
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <div class="tab-content" id="enrollmentModeContent">
                                            
                                            <!-- BLOCK SECTION PRE-CONFIGURED MODULES MODE -->
                                            <div class="tab-pane fade show active p-4" id="block-mode" role="tabpanel">
                                                <p class="text-muted mb-4 small"><i class="bi bi-info-circle me-1"></i> Select a predefined schedule block. All subjects for your year level will be automatically assigned to the times indicated below.</p>
                                                
                                                <div class="row g-3">
                                                    <!-- Block Standard Template Item: Morning Session A -->
                                                    <div class="col-lg-12 col-xl-6">
                                                        <label class="d-block block-option m-0">
                                                            <div class="form-check d-flex p-0 m-0">
                                                                <input class="form-check-input me-3 mt-1" type="radio" name="selected_block" value="BSIT-4A" checked>
                                                                <div class="block-details w-100">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <h6 class="fw-bold text-dark mb-0 fs-5">Block BSIT-4A (Morning)</h6>
                                                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded">Available</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-3 border-bottom pb-2 fw-medium"><i class="bi bi-tags me-1"></i>Total Units: 8 units &nbsp;|&nbsp; Mon, Wed, Fri Schedule</p>
                                                                    <ul class="list-unstyled small mb-0 text-secondary">
                                                                        <li class="mb-2"><span class="fw-bold text-dark">IT411:</span> MWF 09:00 AM - 10:30 AM</li>
                                                                        <li class="mb-2"><span class="fw-bold text-dark">IT412:</span> MWF 11:00 AM - 12:00 PM</li>
                                                                        <li><span class="fw-bold text-dark">PE400:</span> SAT 07:00 AM - 09:00 AM</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Block Standard Template Item: Afternoon Session B -->
                                                    <div class="col-lg-12 col-xl-6">
                                                        <label class="d-block block-option m-0">
                                                            <div class="form-check d-flex p-0 m-0">
                                                                <input class="form-check-input me-3 mt-1" type="radio" name="selected_block" value="BSIT-4B">
                                                                <div class="block-details w-100">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <h6 class="fw-bold text-dark mb-0 fs-5">Block BSIT-4B (Afternoon)</h6>
                                                                        <span class="badge bg-warning-subtle text-warning-dark px-2 py-1 rounded" style="color: #664d03; background-color: #fff3cd;">Few Slots Left</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-3 border-bottom pb-2 fw-medium"><i class="bi bi-tags me-1"></i>Total Units: 8 units &nbsp;|&nbsp; Tue, Thu Schedule</p>
                                                                    <ul class="list-unstyled small mb-0 text-secondary">
                                                                        <li class="mb-2"><span class="fw-bold text-dark">IT411:</span> TTH 01:00 PM - 03:30 PM</li>
                                                                        <li class="mb-2"><span class="fw-bold text-dark">IT412:</span> TTH 04:00 PM - 05:30 PM</li>
                                                                        <li><span class="fw-bold text-dark">PE400:</span> SAT 09:00 AM - 11:00 AM</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="enrollment_type" value="block">
                                            </div>
                                            
                                            <!-- SEPARATED INDIVIDUAL COURSE SELECTIONS PROCESSING MODE -->
                                            <div class="tab-pane fade" id="individual-mode" role="tabpanel">
                                                <div class="p-3 bg-light-subtle border-bottom px-4">
                                                    <p class="text-muted small mb-0"><i class="bi bi-info-circle text-primary me-1"></i> Select your subjects manually and click <strong>"Choose Time"</strong> to configure specific isolated slots for each course row chosen.</p>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0">
                                                        <thead class="table-light small text-uppercase text-secondary">
                                                            <tr>
                                                                <th class="ps-4" style="width: 8%;">Enroll</th>
                                                                <th style="width: 15%;">Subject Code</th>
                                                                <th style="width: 42%;">Description</th>
                                                                <th style="width: 10%;">Units</th>
                                                                <th class="pe-4 text-end" style="width: 25%;">Schedule Selection</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-secondary">
                                                            <!-- Row Item Course 1 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="IT411" checked>
                                                                </td>
                                                                <td><span class="fw-bold text-dark">IT411</span></td>
                                                                <td class="text-dark">Capstone Project 1 (Proposal & Prototyping)</td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1">3 Units</span></td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-pcc btn-sm rounded-pill fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalIT411">
                                                                        <i class="bi bi-clock me-1"></i> Choose Time
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <!-- Row Item Course 2 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="IT412" checked>
                                                                </td>
                                                                <td><span class="fw-bold text-dark">IT412</span></td>
                                                                <td class="text-dark">Information Assurance and Security 2</td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1">3 Units</span></td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-pcc btn-sm rounded-pill fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalIT412">
                                                                        <i class="bi bi-clock me-1"></i> Choose Time
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <!-- Row Item Course 3 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="PE400">
                                                                </td>
                                                                <td><span class="fw-bold text-dark">PE400</span></td>
                                                                <td class="text-dark">Advanced Team Sports Elective</td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1">2 Units</span></td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-pcc btn-sm rounded-pill fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalPE400">
                                                                        <i class="bi bi-clock me-1"></i> Choose Time
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <!-- Central Form Operational Interactive Processing Footers -->
                                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <button type="reset" class="btn btn-outline-secondary btn-sm fw-semibold me-2 px-3 py-2"><i class="bi bi-x-circle me-1"></i> Reset Fields</button>
                                            <button type="submit" name="action" value="draft" class="btn btn-success btn-sm text-white fw-semibold px-3 py-2"><i class="bi bi-file-earmark-check me-1"></i> Save Draft</button>
                                        </div>
                                        <button type="submit" name="action" value="lock" class="btn btn-pcc-primary fw-semibold px-4 py-2"><i class="bi bi-arrow-right-circle me-2"></i>Proceed to Enrollment</button>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN: Exact Fee Layout Structure & Payment Terms -->
                            <div class="col-xl-4 col-lg-5">
                                
                                <!-- Exact Tuition & Fees Breakdown Panel -->
                                <div class="card enrollment-card mb-4 bg-light-subtle border-top border-4 border-primary">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Exact Tuition Breakdown</h6>
                                    </div>
                                    <div class="card-body pb-2">
                                        <p class="text-dark small mb-3 fw-semibold"><i class="bi bi-file-earmark-text me-1 text-secondary"></i>Official Statement of Account (8 Units)</p>
                                        
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-secondary">Tuition Fee</span>
                                            <span class="fw-semibold text-dark">₱ 12,400.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-secondary">Miscellaneous Fees</span>
                                            <span class="fw-semibold text-dark">₱ 4,500.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-secondary">Laboratory Fees</span>
                                            <span class="fw-semibold text-dark">₱ 2,100.00</span>
                                        </div>
                                        <hr class="my-2 text-secondary">
                                        <div class="d-flex justify-content-between mt-2 mb-4">
                                            <span class="fw-bold text-dark">Total Tuition Amount</span>
                                            <span class="fw-bold text-primary" style="font-size: 1.1rem;">₱ 19,000.00</span>
                                        </div>

                                        <!-- PAYMENT TERM SELECTOR -->
                                        <h6 class="fw-bold text-dark mb-3 fs-6 border-top pt-4">Select Payment Plan</h6>
                                        
                                        <!-- Option 1: Full Payment -->
                                        <label class="d-block payment-option p-3 mb-3 m-0">
                                            <div class="form-check d-flex m-0 p-0 align-items-center">
                                                <input class="form-check-input me-3" type="radio" name="payment_term" value="full" checked>
                                                <div class="payment-details w-100 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold text-dark m-0">Full Payment</span>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">5% Discount</span>
                                                </div>
                                            </div>
                                        </label>

                                        <!-- Option 2: Installment Plan -->
                                        <label class="d-block payment-option p-3 m-0">
                                            <div class="form-check m-0 p-0">
                                                <div class="d-flex align-items-center mb-2">
                                                    <input class="form-check-input me-3" type="radio" name="payment_term" value="installment">
                                                    <span class="fw-bold text-dark payment-details m-0">Installment Plan</span>
                                                </div>
                                                <div class="small text-muted ms-4 ps-1 mt-2 border-top pt-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Upon Enrollment:</span> <span class="fw-semibold text-dark">₱ 4,000.00</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Prelims:</span> <span>₱ 5,000.00</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Midterms:</span> <span>₱ 5,000.00</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Finals:</span> <span>₱ 5,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        
                                    </div>
                                    <div class="card-footer bg-white border-top py-3 mt-2">
                                        <div class="small text-success text-center fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Confirmed Semester Amount</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- MODAL POP-UPS FOR INDIVIDUAL SCHEDULE MODAL ASSIGNMENTS -->
                        <!-- Modal configuration segment for IT411 -->
                        <div class="modal fade" id="modalIT411" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                    <div class="modal-header bg-light border-0 py-3">
                                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-primary"></i>IT411 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <label class="d-block schedule-option mb-3">
                                            <div class="form-check d-flex align-items-center m-0 p-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT411" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold text-dark">Section A - Mon/Wed/Fri</div>
                                                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>09:00 AM - 10:30 AM &nbsp;|&nbsp; Lab 1</div>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <label class="d-block schedule-option m-0">
                                            <div class="form-check d-flex align-items-center m-0 p-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT411" value="sched2">
                                                <div class="schedule-details">
                                                    <div class="fw-bold text-dark">Section B - Tue/Thu</div>
                                                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>01:00 PM - 03:30 PM &nbsp;|&nbsp; Lab 2</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-pcc-primary w-100 fw-bold py-2 rounded-pill" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal configuration segment for IT412 -->
                        <div class="modal fade" id="modalIT412" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                    <div class="modal-header bg-light border-0 py-3">
                                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-primary"></i>IT412 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <label class="d-block schedule-option m-0">
                                            <div class="form-check d-flex align-items-center m-0 p-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT412" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold text-dark">Section A - Mon/Wed/Fri</div>
                                                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>11:00 AM - 12:00 PM &nbsp;|&nbsp; Lec 304</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-pcc-primary w-100 fw-bold py-2 rounded-pill" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal configuration segment for PE400 -->
                        <div class="modal fade" id="modalPE400" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                    <div class="modal-header bg-light border-0 py-3">
                                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-primary"></i>PE400 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <label class="d-block schedule-option m-0">
                                            <div class="form-check d-flex align-items-center m-0 p-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_PE400" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold text-dark">Section A - Saturday</div>
                                                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>07:00 AM - 09:00 AM &nbsp;|&nbsp; Gymnasium</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-pcc-primary w-100 fw-bold py-2 rounded-pill" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
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