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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <link rel="icon" href="../../images/PCC_favicon.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Required for native HTML Modal and Tab popups -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .sidebar-bg { background-color: #002c5e !important; }
        .sidebar-bg .nav-link, .sidebar-bg .brand-link, .sidebar-bg .nav-header { color: #ffffff !important; }
        .sidebar-bg-active { color: #002c5e !important; background-color: #f1b813 !important; }
        
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 15px 20px; }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #ffffff; background-color: #001d3d; }
        .user-info .username { color: #ffffff; font-weight: 600; }
        .user-info .status-text { color: #ffffff; }
        
        .enrollment-card { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 10px; }

        /* Step Progress Tracker Styling */
        .step-indicator { display: flex; justify-content: space-between; position: relative; margin-bottom: 30px; }
        .step-indicator::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 4px; background-color: #e0e0e0; transform: translateY(-50%); z-index: 1; }
        .step-progress-bar { position: absolute; top: 50%; left: 0; width: 33%; height: 4px; background-color: #002c5e; transform: translateY(-50%); z-index: 2; }
        .step-item { position: relative; z-index: 3; text-align: center; background: #f8f9fa; padding: 0 10px; }
        .step-icon { width: 40px; height: 40px; border-radius: 50%; background-color: #e0e0e0; color: #6c757d; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold; border: 3px solid #fff; }
        .step-item.completed .step-icon { background-color: #198754; color: white; }
        .step-item.active .step-icon { background-color: #002c5e; color: white; box-shadow: 0 0 0 3px rgba(0, 44, 94, 0.2); }
        .step-label { font-size: 0.85rem; font-weight: 600; color: #6c757d; }
        .step-item.active .step-label { color: #002c5e; }

        /* Custom Selection Styling */
        .schedule-option, .block-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #fff;
        }
        .schedule-option:hover, .block-option:hover { background-color: #f8f9fa; border-color: #cdd4da; }
        
        /* Highlight chosen items natively via CSS */
        .form-check-input:checked + .schedule-details { font-weight: bold; color: #002c5e; }
        .form-check-input:checked + .block-details { color: #002c5e; }
        .block-option:has(input:checked) { border-color: #002c5e; background-color: #f4f8ff; box-shadow: 0 0 0 1px #002c5e; }
        
        /* Custom Pills specific to PCC */
        .nav-pills .nav-link.active { background-color: #002c5e; color: #fff; font-weight: bold; }
        .nav-pills .nav-link { color: #495057; font-weight: 600; }
    </style>
</head>

<body class="fixed-header sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <span class="d-none d-md-inline">
                            <div class="nav-date" style="margin-top:6px; margin-bottom: 9px;">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <?php echo date('F j, Y') . " - " . date("h:iA"); ?>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar sidebar-bg">
            <div class="sidebar-brand" style="border-right: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="#" class="brand-link">
                    <img src="images/PCC_Logo.png" alt="PCC Logo" class="brand-image" />
                    <span class="brand-text fw-bold" style="color: white;">PCC Student</span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="border-right: 1px solid rgba(255, 255, 255, 0.1)">
                <nav class="mt-2">
                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder"><i class="fa-solid fa-user-graduate"></i></div>
                        </div>
                        <div class="user-info">
                            <div class="username">Juan Dela Cruz</div>
                            <div class="status-text" style="color: #f1b813; font-size: 0.85rem; margin-top: -5px">BSIT - Incoming 4th Year</div>
                        </div>
                    </div>
                    <ul class="nav sidebar-menu flex-column" id="navigation">
                        <li class="nav-header">REGISTRATION SHUTTLE</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link sidebar-bg-active">
                                <i class="nav-icon bi bi-card-checklist"></i>
                                <p>Online Enrollment</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-1 mt-3 fw-bold">Semestral Enrollment Portal</h3>
                            <p class="text-muted small">Academic Year 2026-2027 | 1st Semester</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Top Widgets -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="info-box bg-white shadow-sm d-flex align-items-center p-3 rounded">
                                <span class="info-box-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; font-size: 22px;">
                                    <i class="bi bi-calendar-check-fill"></i>
                                </span>
                                <div class="info-box-content ms-3">
                                    <span class="text-muted small text-uppercase d-block">Current Phase</span>
                                    <h5 class="fw-bold mb-0 text-warning">Course Advising</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form Wrapping Tabs, Tables, and Modals -->
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-12">
                                <div class="card enrollment-card mb-4">
                                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                                        <h5 class="card-title mb-0 fw-bold text-dark me-3">
                                            <i class="bi bi-book-half me-2 text-primary"></i>Schedule Allocation
                                        </h5>
                                        
                                        <!-- Bootstrap Tabs for Block vs Individual Selection -->
                                        <ul class="nav nav-pills mt-2 mt-md-0" id="enrollmentModeTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active rounded-pill px-4" id="block-tab" data-bs-toggle="tab" data-bs-target="#block-mode" type="button" role="tab">
                                                    <i class="bi bi-grid-1x2-fill me-1"></i> Block Section
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill px-4" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual-mode" type="button" role="tab">
                                                    <i class="bi bi-list-check me-1"></i> Individual Subjects
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <div class="tab-content" id="enrollmentModeContent">
                                            
                                            <!-- TAB 1: BLOCK SECTION MODE -->
                                            <div class="tab-pane fade show active p-4" id="block-mode" role="tabpanel">
                                                <p class="text-muted mb-4">Select a predefined schedule block. All subjects for your year level will be automatically assigned to the times indicated.</p>
                                                
                                                <div class="row">
                                                    <!-- Block Option A -->
                                                    <div class="col-lg-6">
                                                        <label class="d-block block-option">
                                                            <div class="form-check d-flex m-0">
                                                                <input class="form-check-input me-3 mt-1" type="radio" name="selected_block" value="BSIT-4A" checked>
                                                                <div class="block-details w-100">
                                                                    <div class="d-flex justify-content-between">
                                                                        <h5 class="fw-bold mb-1">Block BSIT-4A (Morning)</h5>
                                                                        <span class="badge bg-success text-white">Available</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-3 border-bottom pb-2">Total Units: 8 units | Mon, Wed, Fri Schedule</p>
                                                                    <ul class="list-unstyled small mb-0">
                                                                        <li class="mb-2"><span class="fw-bold">IT411:</span> MWF 09:00 AM - 10:30 AM</li>
                                                                        <li class="mb-2"><span class="fw-bold">IT412:</span> MWF 11:00 AM - 12:00 PM</li>
                                                                        <li><span class="fw-bold">PE400:</span> SAT 07:00 AM - 09:00 AM</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Block Option B -->
                                                    <div class="col-lg-6">
                                                        <label class="d-block block-option">
                                                            <div class="form-check d-flex m-0">
                                                                <input class="form-check-input me-3 mt-1" type="radio" name="selected_block" value="BSIT-4B">
                                                                <div class="block-details w-100">
                                                                    <div class="d-flex justify-content-between">
                                                                        <h5 class="fw-bold mb-1">Block BSIT-4B (Afternoon)</h5>
                                                                        <span class="badge bg-warning text-dark">Few Slots Left</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-3 border-bottom pb-2">Total Units: 8 units | Tue, Thu Schedule</p>
                                                                    <ul class="list-unstyled small mb-0">
                                                                        <li class="mb-2"><span class="fw-bold">IT411:</span> TTH 01:00 PM - 03:30 PM</li>
                                                                        <li class="mb-2"><span class="fw-bold">IT412:</span> TTH 04:00 PM - 05:30 PM</li>
                                                                        <li><span class="fw-bold">PE400:</span> SAT 09:00 AM - 11:00 AM</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!-- Notice for server-side processing -->
                                                <input type="hidden" name="enrollment_type" value="block">
                                            </div>
                                            
                                            <!-- TAB 2: INDIVIDUAL SUBJECTS MODE -->
                                            <div class="tab-pane fade" id="individual-mode" role="tabpanel">
                                                <div class="p-3 bg-light border-bottom">
                                                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Select your subjects manually and click "Choose Time" to pick a schedule for each specific subject.</p>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0">
                                                        <thead class="table-light small text-uppercase text-secondary">
                                                            <tr>
                                                                <th class="ps-4" style="width: 5%;">Enroll</th>
                                                                <th style="width: 15%;">Subject Code</th>
                                                                <th style="width: 45%;">Description</th>
                                                                <th style="width: 10%;">Units</th>
                                                                <th class="pe-4 text-end" style="width: 25%;">Schedule Selection</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Row 1 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="IT411" checked>
                                                                </td>
                                                                <td><span class="fw-bold text-dark">IT411</span></td>
                                                                <td>Capstone Project 1 (Proposal & Prototyping)</td>
                                                                <td>3</td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalIT411">
                                                                        <i class="bi bi-clock me-1"></i> Choose Time
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <!-- Row 2 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="IT412" checked>
                                                                </td>
                                                                <td><span class="fw-bold text-dark">IT412</span></td>
                                                                <td>Information Assurance and Security 2</td>
                                                                <td>3</td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalIT412">
                                                                        <i class="bi bi-clock me-1"></i> Choose Time
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <!-- Row 3 -->
                                                            <tr>
                                                                <td class="ps-4">
                                                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="PE400">
                                                                </td>
                                                                <td><span class="fw-bold text-dark">PE400</span></td>
                                                                <td>Advanced Team Sports Elective</td>
                                                                <td>2</td>
                                                                <td class="pe-4 text-end">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalPE400">
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
                                    
                                    <!-- Actions -->
                                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="reset" class="btn btn-outline-secondary btn-sm fw-bold me-2"><i class="bi bi-x-circle me-1"></i> Reset Fields</button>
                                            <button type="submit" name="action" value="draft" class="btn btn-success btn-sm text-white fw-bold"><i class="bi bi-file-earmark-check me-1"></i> Save Draft</button>
                                        </div>
                                        <button type="submit" name="action" value="lock" class="btn text-white fw-bold px-4" style="background-color: #002c5e;"><i class="bi bi-arrow-right-circle me-2"></i>Proceed to Assessment</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- MODAL POP-UPS FOR INDIVIDUAL SCHEDULES     -->
                        <!-- Nested inside the form to capture inputs   -->
                        <!-- ========================================== -->

                        <!-- Modal for IT411 -->
                        <div class="modal fade" id="modalIT411" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-range me-2 text-primary"></i>IT411 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="d-block schedule-option">
                                            <div class="form-check d-flex align-items-center m-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT411" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold">Section A - Mon/Wed/Fri</div>
                                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>09:00 AM - 10:30 AM | Lab 1</div>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <label class="d-block schedule-option">
                                            <div class="form-check d-flex align-items-center m-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT411" value="sched2">
                                                <div class="schedule-details">
                                                    <div class="fw-bold">Section B - Tue/Thu</div>
                                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>01:00 PM - 03:30 PM | Lab 2</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-primary w-100 fw-bold" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for IT412 -->
                        <div class="modal fade" id="modalIT412" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-range me-2 text-primary"></i>IT412 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="d-block schedule-option">
                                            <div class="form-check d-flex align-items-center m-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_IT412" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold">Section A - Mon/Wed/Fri</div>
                                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>11:00 AM - 12:00 PM | Lec 304</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-primary w-100 fw-bold" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for PE400 -->
                        <div class="modal fade" id="modalPE400" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-range me-2 text-primary"></i>PE400 Schedules</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="d-block schedule-option">
                                            <div class="form-check d-flex align-items-center m-0">
                                                <input class="form-check-input me-3" type="radio" name="schedule_PE400" value="sched1" checked>
                                                <div class="schedule-details">
                                                    <div class="fw-bold">Section A - Saturday</div>
                                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>07:00 AM - 09:00 AM | Gymnasium</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-primary w-100 fw-bold" data-bs-dismiss="modal">Confirm Selection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Required Bootstrap Bundle for enabling Tabs and Modals (No custom script tags required) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>