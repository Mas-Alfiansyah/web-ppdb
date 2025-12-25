<?php
// admin/siswa/update_status.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/data_helper.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$status = $data['status'] ?? null;

if (!$id || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

$allowed_statuses = ['pending', 'lulus', 'tidak_lulus', 'cadangan'];
if (!in_array($status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

$stmt = $conn->prepare("UPDATE siswa SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    // Reset announcement seen flag so student sees the new status
    reset_announcement_seen($id);

    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
exit;
