<?php
// admin/siswa/verifikasi.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $stmt = $conn->prepare("UPDATE siswa SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: detail.php?id=$id&msg=success");
    exit();
}
header("Location: index.php");
exit();
