<?php
// pendaftaran/mark_seen.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/data_helper.php';
require_once __DIR__ . '/../config/database.php';

require_login();
$user_id = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get student data
    $student = get_student_data($user_id);

    if ($student) {
        // Mark announcement as seen using centralized helper
        mark_announcement_seen($student['id']);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404); // Student not found
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
    exit;
}
