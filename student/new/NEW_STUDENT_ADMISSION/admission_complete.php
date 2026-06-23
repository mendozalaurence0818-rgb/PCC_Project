<?php
session_start();

if (!isset($_SESSION['applicant_step1'])) {
    header("Location: new_student_review.php");
    exit();
}

$data = $_SESSION['applicant_step1'];
$docs = $_SESSION['applicant_step2'] ?? [];

require_once '../../../config/database_connect.php';

$ref_num = 'PCC-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

try {
    $conn->beginTransaction();
    $sql1 = "INSERT INTO guardians (full_name, relationship, emergency_phone) VALUES (:name, :rel, :phone)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([
        ':name' => $data['guardian_name'],
        ':rel' => $data['guardian_relationship'],
        ':phone' => $data['emergency_phone']
    ]);
    $guardian_id = $conn->lastInsertId();

    // FIXED: Added year_level to the column list and the VALUES parameters
    $sql2 = "INSERT INTO applicants (
                reference_number, classification, year_level, academic_term, school_year,
                first_name, middle_name, last_name, suffix, date_of_birth, gender, civil_status,
                nationality, religious_affiliation, email_address, mobile_number,
                address_region, address_province, address_city, address_barangay, address_postal, address_street,
                preferred_program, guardian_id, shs_card_path, psa_cert_path, good_moral_path, applicant_photo_path
             ) VALUES (
                :ref, :classification, :year_level, :term, :sy,
                :fname, :mname, :lname, :suffix, :dob, :gender, :civil,
                :nat, :religion, :email, :mobile,
                :region, :province, :city, :barangay, :postal, :street,
                :program, :guardian_id, :shs_card, :psa_cert, :good_moral, :photo
             )";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([
        ':ref' => $ref_num,
        ':classification' => $data['classification'],
        // FIXED: Explicitly bind the session-calculated year level to the query
        ':year_level' => $data['year_level'],
        ':term' => $data['academic_term'],
        ':sy' => $data['school_year'],
        ':fname' => $data['first_name'],
        ':mname' => $data['middle_name'],
        ':lname' => $data['last_name'],
        ':suffix' => $data['suffix'],
        ':dob' => $data['date_of_birth'],
        ':gender' => $data['gender'],
        ':civil' => $data['civil_status'],
        ':nat' => $data['nationality'],
        ':religion' => $data['religious_affiliation'],
        ':email' => $data['email_address'],
        ':mobile' => $data['mobile_number'],
        ':region' => $data['address_region'],
        ':province' => $data['address_province'],
        ':city' => $data['address_city'],
        ':barangay' => $data['address_barangay'],
        ':postal' => $data['address_postal'] ?? '',
        ':street' => $data['address_street'],
        ':program' => $data['preferred_program'],
        ':guardian_id' => $guardian_id,
        ':shs_card' => $docs['shs_card_path'] ?? null,
        ':psa_cert' => $docs['psa_cert_path'] ?? null,
        ':good_moral' => $docs['good_moral_path'] ?? null,
        ':photo' => $docs['applicant_photo_path'] ?? null
    ]);
    $application_id = $conn->lastInsertId();

    $sql3 = "INSERT INTO academic_backgrounds (application_id, shs_school_attended, shs_strand, shs_year_graduated, shs_school_address) 
             VALUES (:app_id, :school, :strand, :year, :address)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->execute([
        ':app_id' => $application_id,
        ':school' => $data['shs_school_attended'],
        ':strand' => $data['shs_strand'],
        ':year' => $data['shs_year_graduated'],
        ':address' => $data['shs_school_address']
    ]);

    $conn->commit();

    unset($_SESSION['applicant_step1']);
    unset($_SESSION['applicant_step2']);

} catch (Exception $e) {
    $conn->rollBack();
    die("System Enrollment Transaction Error: " . $e->getMessage());
}

try {
    $inserted_app_id = $conn->lastInsertId();

    $log_msg = "Submitted an Application .";

    $log_stmt = $conn->prepare("INSERT INTO system_updates (admin_id, student_id, module_tab, custom_message) VALUES (NULL, :student_id, 'ADMISSIONS', :msg)");
    $log_stmt->execute([
        ':student_id' => $inserted_app_id,
        ':msg' => $log_msg
    ]);

} catch (PDOException $e) {
    error_log("Dashboard Log Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC | Admission Portal - Submission Complete</title>
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
            margin: 30px auto;
            padding: 0 60px 80px 60px;
        }

        .success-banner {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .success-banner h3 {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .ref-display-badge {
            background-color: #ffffff;
            border: 2px dashed #198754;
            color: #198754;
            display: inline-block;
            font-size: 24px;
            font-weight: 800;
            padding: 10px 24px;
            margin: 15px 0;
            border-radius: 6px;
            letter-spacing: 1px;
        }

        .action-footer {
            display: flex;
            justify-content: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .btn-advance-step {
            background-color: #0D6EFD;
            color: #FFFFFF;
            font-family: var(--font-body);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 40px;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .btn-advance-step:hover {
            background-color: #0B5ED7;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <nav class="top-navbar">
        <a href="../../../index.php">Dashboard</a>
    </nav>
    <img src="../../../assets/images/PCC_Admission.png" alt="Admission Portal Header" class="header-banner">
    <div class="main-container">
        <div class="success-banner">
            <i class="bi bi-check-circle-fill text-success"
                style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
            <h3>Application Submitted Successfully!</h3>
            <div class="ref-display-badge"><?php echo htmlspecialchars($ref_num); ?></div>
        </div>
        <div class="action-footer">
            <a href="../../../index.php" class="btn-advance-step"><i class="bi bi-house-door-fill me-2"></i> Exit to
                Portal Home</a>
        </div>
    </div>
</body>

</html>