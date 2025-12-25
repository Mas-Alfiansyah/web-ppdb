<?php
// admin/laporan/export_pdf.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../helpers/data_helper.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../siswa/index.php');
    exit();
}

// Fetch student data
$stmt = $conn->prepare("
    SELECT s.*, ta.tahun as nama_tahun 
    FROM siswa s 
    LEFT JOIN tahun_ajaran ta ON s.tahun_ajaran_id = ta.id 
    WHERE s.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) die("Data tidak ditemukan");

// Get school settings
$nama_sekolah = get_setting('nama_sekolah') ?? 'SMK Negeri 1 Contoh';

// Document status summary
$doc_status = get_document_status_summary($id);

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Formulir_PPDB_' . $student['no_pendaftaran'] . '.pdf"');

// We'll use DomPDF approach - generate HTML and convert to PDF
// For simplicity, we'll use wkhtmltopdf or similar, but for now, let's create a printable HTML version
// that can be saved as PDF using browser's print function

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran - <?= htmlspecialchars($student['nama_lengkap']) ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 18pt;
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
        }

        .header p {
            margin: 3px 0;
            font-size: 10pt;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }

        .photo-box {
            width: 4cm;
            height: 6cm;
            border: 2px solid #333;
            float: right;
            margin-left: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .photo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        table.data-table td:first-child {
            width: 35%;
            font-weight: bold;
        }

        table.doc-table {
            border: 1px solid #333;
        }

        table.doc-table th,
        table.doc-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        table.doc-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-valid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-invalid {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-lulus {
            background-color: #d1fae5;
            color: #065f46;
        }

        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
            margin-top: 60px;
        }

        .signature-box.right {
            float: right;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="watermark">DOKUMEN RESMI</div>

    <div class="header">
        <h1><?= strtoupper($nama_sekolah) ?></h1>
        <h2>FORMULIR PENDAFTARAN PESERTA DIDIK BARU</h2>
        <p>Tahun Ajaran <?= $student['nama_tahun'] ?? '-' ?></p>
    </div>

    <?php if (!empty($student['foto_path'])): ?>
        <div class="photo-box">
            <img src="<?= base_url($student['foto_path']) ?>" alt="Foto Siswa">
        </div>
    <?php else: ?>
        <div class="photo-box">
            <div style="text-align: center; color: #999;">
                Foto<br>3x4
            </div>
        </div>
    <?php endif; ?>

    <div class="section-title">DATA PRIBADI</div>
    <table class="data-table">
        <tr>
            <td>Nomor Pendaftaran</td>
            <td>: <?= htmlspecialchars($student['no_pendaftaran'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>: <?= htmlspecialchars($student['nama_lengkap']) ?></td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>: <?= htmlspecialchars($student['nisn']) ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: <?= $student['jk'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>: <?= htmlspecialchars($student['tempat_lahir']) ?>, <?= date('d F Y', strtotime($student['tanggal_lahir'])) ?></td>
        </tr>
        <tr>
            <td>Alamat Lengkap</td>
            <td>: <?= htmlspecialchars($student['alamat']) ?></td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <div class="section-title">STATUS VERIFIKASI DOKUMEN</div>
    <table class="doc-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Dokumen</th>
                <th>Status Upload</th>
                <th>Status Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $documents = [
                ['no' => 1, 'nama' => 'Akte Kelahiran', 'path' => $student['akte_path'], 'status' => $student['akte_status']],
                ['no' => 2, 'nama' => 'Kartu Keluarga', 'path' => $student['kk_path'], 'status' => $student['kk_status']],
                ['no' => 3, 'nama' => 'Ijazah / SKL', 'path' => $student['ijazah_path'], 'status' => $student['ijazah_status']],
                ['no' => 4, 'nama' => 'KIP (Opsional)', 'path' => $student['kip_path'] ?? null, 'status' => $student['kip_status'] ?? 'pending'],
                ['no' => 5, 'nama' => 'KTP Ayah', 'path' => $student['ktp_ayah_path'] ?? null, 'status' => $student['ktp_ayah_status'] ?? 'pending'],
                ['no' => 6, 'nama' => 'KTP Ibu', 'path' => $student['ktp_ibu_path'] ?? null, 'status' => $student['ktp_ibu_status'] ?? 'pending'],
                ['no' => 7, 'nama' => 'Foto 3x4', 'path' => $student['foto_path'] ?? null, 'status' => null],
            ];

            foreach ($documents as $doc):
            ?>
                <tr>
                    <td><?= $doc['no'] ?></td>
                    <td><?= $doc['nama'] ?></td>
                    <td><?= $doc['path'] ? '✓ Sudah Upload' : '✗ Belum Upload' ?></td>
                    <td>
                        <?php if ($doc['status']): ?>
                            <span class="status-badge status-<?= $doc['status'] ?>">
                                <?= strtoupper($doc['status']) ?>
                            </span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="section-title">STATUS PENDAFTARAN</div>
    <table class="data-table">
        <tr>
            <td>Status Kelulusan</td>
            <td>: <span class="status-badge status-<?= $student['status'] ?>">
                    <?= strtoupper($student['status']) ?>
                </span></td>
        </tr>
        <tr>
            <td>Tanggal Pendaftaran</td>
            <td>: <?= date('d F Y H:i', strtotime($student['created_at'])) ?> WIB</td>
        </tr>
        <tr>
            <td>Terakhir Diupdate</td>
            <td>: <?= date('d F Y H:i', strtotime($student['updated_at'])) ?> WIB</td>
        </tr>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Orang Tua / Wali</p>
            <br><br><br>
            <p>_____________________</p>
        </div>

        <div class="signature-box right">
            <p><?= date('d F Y') ?><br>Panitia PPDB</p>
            <br><br><br>
            <p>_____________________</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>