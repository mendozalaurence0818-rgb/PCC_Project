<?php
session_start();
require_once '../../config/database_connect.php';

$applicant = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reference_number'])) {
    $ref_num = strtoupper(trim($_POST['reference_number']));

    try {
        $query = "SELECT a.*, g.full_name AS guardian_name, g.relationship AS guardian_relationship, g.emergency_phone,
                         ab.shs_school_attended, ab.shs_strand, ab.shs_year_graduated, ab.shs_school_address
                  FROM applicants a
                  INNER JOIN guardians g ON a.guardian_id = g.guardian_id
                  LEFT JOIN academic_backgrounds ab ON a.application_id = ab.application_id
                  WHERE a.reference_number = :ref_num LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute([':ref_num' => $ref_num]);
        $applicant = $stmt->fetch();

        if (!$applicant) {
            $error_message = "No profile matched the Reference Number: <strong>" . htmlspecialchars($ref_num) . "</strong>. Please verify the code entries and try again.";
        }
    } catch (PDOException $e) {
        $error_message = "Tracking engine extraction malfunction error: " . $e->getMessage();
    }
} else {
    header("Location: new_student_registration.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal - Track Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="icon" href="../../assets/images/PCC_favicon.png" type="image/png" />
    <style>
        :root {
            --accent-yellow: #FFC107;
            --text-main: #212529;
            --text-muted: #5a6268;
            --input-bg: #f8f9fa;
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

        .header-banner {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .main-container {
            max-width: 1000px;
            margin: 40px auto;
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

        .status-badge-container {
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .status-Pending { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
        .status-Approved { background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; }
        .status-Rejected { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .status-Review { background-color: #cff4fc; border: 1px solid #bceeec; color: #055160; }

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

        .grid-col-3 { width: 25%; padding: 0 15px; margin-bottom: 25px; }
        .grid-col-4 { width: 33.3333%; padding: 0 15px; margin-bottom: 25px; }
        .grid-col-6 { width: 50%; padding: 0 15px; margin-bottom: 25px; }
        .grid-col-12 { width: 100%; padding: 0 15px; margin-bottom: 25px; }

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
            color: #6c757d !important;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: not-allowed;
        }

        .action-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .btn-return-home, .btn-print-form {
            font-family: var(--font-body);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 40px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-return-home {
            background-color: #0A1140;
            color: #FFFFFF;
        }

        .btn-return-home:hover {
            background-color: #000;
            transform: translateY(-1px);
        }

        .btn-print-form {
            background-color: #198754;
            color: white;
        }

        .btn-print-form:hover {
            background-color: #157347;
            transform: translateY(-1px);
        }

        .error-card {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }

        /* Printable Sheet Header (hidden on web screen browser views) */
        .print-only-header {
            display: none;
        }

        @media (max-width: 768px) {
            .grid-col-3, .grid-col-4, .grid-col-6 { width: 100%; }
            .main-container { padding: 0 25px 50px 25px; }
        }

        /* ==========================================================================
           PRINT ENGINE CSS STYLING RULES
           ========================================================================== */
        @media print {
            body {
                background: none !important;
                color: #000000 !important;
                font-size: 12pt;
            }

            .header-banner, .action-footer, .status-badge-container {
                display: none !important; /* Hide non-printable system controls */
            }

            .main-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Custom dynamic header on paper copy configurations */
            .print-only-header {
                display: flex !important;
                align-items: center;
                gap: 20px;
                border-bottom: 3px double #0A1140;
                padding-bottom: 15px;
                margin-bottom: 30px;
            }

            .print-only-header img {
                width: 75px;
                height: 75px;
            }

            .print-only-header h1 {
                font-family: var(--font-heading);
                font-size: 22pt;
                color: #0A1140;
                line-height: 1.1;
            }

            .print-only-header p {
                font-size: 10pt;
                color: #555;
            }

            .section-intro h2 {
                font-size: 18pt !important;
            }

            /* Modify forms layout for standard letter-sheet presentation templates */
            .grid-row {
                display: block !important; /* Prevent row breaks across pages mid-way */
            }

            .grid-col-3, .grid-col-4, .grid-col-6, .grid-col-12 {
                float: left;
                margin-bottom: 15px !important;
            }

            .grid-col-3 { width: 25% !important; }
            .grid-col-4 { width: 33.3333% !important; }
            .grid-col-6 { width: 50% !important; }
            .grid-col-12 { width: 100% !important; }

            /* Clear floating rules */
            .grid-row::after {
                content: "";
                display: table;
                clear: both;
            }

            /* Style fields to look like clean form lines instead of grey web boxes */
            .form-input {
                background-color: transparent !important;
                border: none !important;
                border-bottom: 1px solid #000000 !important;
                border-radius: 0 !important;
                padding: 4px 0 !important;
                font-size: 11pt !important;
                color: #000000 !important;
            }

            label {
                color: #000000 !important;
                font-size: 9pt !important;
                margin-bottom: 2px !important;
            }

            .form-section-divider {
                margin: 30px 0 15px 0 !important;
                page-break-inside: avoid;
            }

            .form-section-divider h4 {
                font-size: 13pt !important;
            }
        }
    </style>
</head>

<body>

    <img src="../../assets/images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">

    <main class="main-container">

        <?php if ($error_message): ?>
            <div class="error-card shadow-sm">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                <p><?php echo $error_message; ?></p>
                <div class="action-footer">
                    <a href="new_student_registration.php" class="btn-return-home"><i class="bi bi-arrow-left"></i> Back to Admission</a>
                </div>
            </div>
        <?php elseif ($applicant): ?>

            <div class="print-only-header">
                <img src="../../assets/images/PCC_logo.png" alt="PCC Official Institutional Seal Logo">
                <div>
                    <h1>POBLACION CENTRAL COLLEGE</h1>
                    <p>Official Student Registration Summary Copy • Academic Admission System Hub Engine</p>
                    <p style="font-weight: bold; margin-top: 3px;">Application Reference Token: <?php echo htmlspecialchars($applicant['reference_number']); ?></p>
                </div>
            </div>

            <div class="section-intro">
                <h2>Application Tracking Details</h2>
                <p>Review the captured snapshot parameters and validation check status for Reference Token:
                    <strong><?php echo htmlspecialchars($applicant['reference_number']); ?></strong>
                </p>
            </div>

            <?php
            $status = $applicant['application_status'];
            $status_classes = [
                'Pending' => 'status-Pending',
                'Under Review' => 'status-Review',
                'Approved' => 'status-Approved',
                'Rejected' => 'status-Rejected'
            ];
            $status_icons = [
                'Pending' => 'bi-clock-history',
                'Under Review' => 'bi-search',
                'Approved' => 'bi-check-circle-fill',
                'Rejected' => 'bi-x-circle-fill'
            ];
            $current_class = $status_classes[$status] ?? 'status-Pending';
            $current_icon = $status_icons[$status] ?? 'bi-clock-history';
            ?>
            <div class="status-badge-container <?php echo $current_class; ?>">
                <i class="bi <?php echo $current_icon; ?>" style="font-size: 42px;"></i>
                <div>
                    <h3 style="font-size: 22px; font-weight: 800; margin-bottom: 2px;">Application Status: <?php echo htmlspecialchars($status); ?></h3>
                    <p style="font-size: 14px; opacity: 0.9;">
                        <?php if ($status === 'Pending'): ?>
                            Your form entries are queued and awaiting verification by the registrar evaluation team.
                        <?php elseif ($status === 'Under Review'): ?>
                            The admissions unit is currently evaluating your uploaded document portfolios.
                        <?php elseif ($status === 'Approved'): ?>
                            Congratulations! Your application has been approved. Please check your email for enrollment directives.
                        <?php else: ?>
                            Your application has been rejected. Please reach out to the admissions tracking desk for clarity.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="form-section-divider">
                <h4>I. Personal Demographics</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-3"><label>First Name</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['first_name']); ?>" readonly></div>
                <div class="grid-col-3"><label>Middle Name</label><input type="text" class="form-input" value="<?php echo !empty($applicant['middle_name']) ? htmlspecialchars($applicant['middle_name']) : 'N/A'; ?>" readonly></div>
                <div class="grid-col-3"><label>Last Name</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['last_name']); ?>" readonly></div>
                <div class="grid-col-3"><label>Suffix</label><input type="text" class="form-input" value="<?php echo !empty($applicant['suffix']) ? htmlspecialchars($applicant['suffix']) : 'N/A'; ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Date of Birth</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['date_of_birth']); ?>" readonly></div>
                <div class="grid-col-4"><label>Gender</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['gender']); ?>" readonly></div>
                <div class="grid-col-4"><label>Civil Status</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['civil_status']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-6"><label>Nationality</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['nationality']); ?>" readonly></div>
                <div class="grid-col-6"><label>Religious Affiliation</label><input type="text" class="form-input" value="<?php echo !empty($applicant['religious_affiliation']) ? htmlspecialchars($applicant['religious_affiliation']) : 'N/A'; ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>II. Contact & Location Information</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-6"><label>Active Email Address</label><input type="email" class="form-input" value="<?php echo htmlspecialchars($applicant['email_address']); ?>" readonly></div>
                <div class="grid-col-6"><label>Mobile Number</label><input type="tel" class="form-input" value="<?php echo htmlspecialchars($applicant['mobile_number']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Region</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_region']); ?>" readonly></div>
                <div class="grid-col-4"><label>Province</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_province']); ?>" readonly></div>
                <div class="grid-col-4"><label>City / Municipality</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_city']); ?>" readonly></div>
            </div>

            <div class="grid-row">
                <div class="grid-col-4"><label>Barangay</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_barangay']); ?>" readonly></div>
                <div class="grid-col-4"><label>Postal Code</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_postal'] ?? 'N/A'); ?>" readonly></div>
                <div class="grid-col-4"><label>Street Address</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['address_street']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>III. Senior High School Background</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-6"><label>Last SHS School Attended</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['shs_school_attended']); ?>" readonly></div>
                <div class="grid-col-6"><label>SHS Track & Strand</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['shs_strand']); ?>" readonly></div>
                <div class="grid-col-6"><label>Year Completed / Graduated</label><input type="number" class="form-input" value="<?php echo htmlspecialchars($applicant['shs_year_graduated']); ?>" readonly></div>
                <div class="grid-col-6"><label>SHS School Address</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['shs_school_address']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>IV. Course & Academic Preferences</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-12"><label>Preferred College Program / Course</label><input type="text" class="form-input" value="<?php echo $applicant['preferred_program'] === 'BSCS' ? 'Bachelor of Science in Computer Science (BSCS)' : 'Bachelor of Science in Information Technology (BSIT)'; ?>" readonly></div>
                <div class="grid-col-6"><label>Academic Term Entering</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['academic_term']); ?>" readonly></div>
                <div class="grid-col-6"><label>School Year (A.Y.)</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['school_year']); ?>" readonly></div>
            </div>

            <div class="form-section-divider">
                <h4>V. Emergency Contact</h4>
                <hr>
            </div>
            <div class="grid-row">
                <div class="grid-col-4"><label>Guardian Name</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['guardian_name']); ?>" readonly></div>
                <div class="grid-col-4"><label>Relationship</label><input type="text" class="form-input" value="<?php echo htmlspecialchars($applicant['guardian_relationship']); ?>" readonly></div>
                <div class="grid-col-4"><label>Emergency Phone</label><input type="tel" class="form-input" value="<?php echo htmlspecialchars($applicant['emergency_phone']); ?>" readonly></div>
            </div>

            <div class="action-footer">
                <button type="button" onclick="window.print();" class="btn-print-form">
                    <i class="bi bi-printer-fill"></i> Print Form Document
                </button>
                <a href="new_student_registration.php" class="btn-return-home">
                    <i class="bi bi-house-door-fill"></i> Finish Tracking
                </a>
            </div>

        <?php endif; ?>
    </main>

</body>

</html>