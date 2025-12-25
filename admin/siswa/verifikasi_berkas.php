<?php
// admin/siswa/verifikasi_berkas.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

if (isset($_GET['id']) && isset($_GET['field']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $field = $_GET['field'];
    $status = $_GET['status'];

    // List of allowed fields for security
    $allowed_fields = ['akte_status', 'kk_status', 'ijazah_status'];
    if (!in_array($field, $allowed_fields)) die("Invalid field");

    $stmt = $conn->prepare("UPDATE siswa SET $field = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: detail.php?id=$id&msg=file_success");
    exit();
}
header("Location: index.php");
exit();
