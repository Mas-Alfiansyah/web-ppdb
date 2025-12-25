<?php
// config/database.php

$host = 'localhost';
$user = 'root'; // Adjust if necessary
$pass = '';     // Adjust if necessary
$db   = 'db_ppdb';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
