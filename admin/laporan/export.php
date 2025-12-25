<?php
// admin/laporan/export.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="data_siswa_ppdb_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header
fputcsv($output, ['No', 'No Pendaftaran', 'Nama Lengkap', 'NISN', 'JK', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Tahun Ajaran', 'Status']);

// Data
$query = "SELECT s.*, ta.tahun as nama_tahun 
          FROM siswa s 
          LEFT JOIN tahun_ajaran ta ON s.tahun_ajaran_id = ta.id 
          ORDER BY s.created_at DESC";
$result = $conn->query($query);

$no = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $no++,
        $row['no_pendaftaran'],
        $row['nama_lengkap'],
        $row['nisn'],
        $row['jk'],
        $row['tempat_lahir'],
        $row['tanggal_lahir'],
        $row['alamat'],
        $row['nama_tahun'],
        $row['status']
    ]);
}

fclose($output);
exit();
