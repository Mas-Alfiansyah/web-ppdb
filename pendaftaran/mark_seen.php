<?php
// pendaftaran/mark_seen.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../config/database.php';

require_login();
$user_id = current_user_id();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        UPDATE siswa s 
        JOIN akun_siswa a ON s.id = a.siswa_id 
        SET s.seen_announcement = 1 
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false]);
    }
    exit;
}
