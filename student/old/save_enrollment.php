<?php
session_start();
require_once '../../config/database_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_logged_in']) || !isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized session.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$subjects = $input['subjects'] ?? [];
$school_year = $input['school_year'] ?? '';
$semester = $input['semester'] ?? '';
$student_id = $_SESSION['student_id'];

if (empty($subjects) || empty($school_year) || empty($semester)) {
    echo json_encode(['success' => false, 'message' => 'Missing enrollment payload data.']);
    exit();
}

try {
    $conn->beginTransaction();

    // 1. Prevent duplicate processing by removing any unsubmitted/draft records for this specific term
    $clear_stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = :sid AND school_year = :sy AND semester = :sem AND midterm_grade IS NULL AND final_grade IS NULL");
    $clear_stmt->execute([':sid' => $student_id, ':sy' => $school_year, ':sem' => $semester]);

    // 2. Map through subjects and insert rows
    $insert_stmt = $conn->prepare("
        INSERT INTO enrollments (student_id, subject_id, school_year, semester, remarks) 
        VALUES (:student_id, :subject_id, :school_year, :semester, 'Enrolled')
    ");

    foreach ($subjects as $sub) {
        // Fetch the subject master record ID using its unique alphanumeric subject_code
        $sub_lookup = $conn->prepare("SELECT id FROM subjects WHERE subject_code = :code LIMIT 1");
        $sub_lookup->execute([':code' => $sub['code']]);
        $subject_master_id = $sub_lookup->fetchColumn();

        if ($subject_master_id) {
            $insert_stmt->execute([
                ':student_id'  => $student_id,
                ':subject_id'  => $subject_master_id,
                ':school_year' => $school_year,
                ':semester'    => $semester
            ]);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Enrollment saved successfully!']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database Processing Error: ' . $e->getMessage()]);
}