<?php
// helpers/data_helper.php
// Centralized Data Management Layer for State Synchronization

/**
 * Get student data with fresh query (no caching issues)
 * @param int $user_id - Account ID from akun_siswa
 * @return array|null - Student data with tahun ajaran
 */
function get_student_data($user_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT s.*, ta.tahun as nama_tahun 
        FROM siswa s 
        JOIN akun_siswa a ON s.id = a.siswa_id 
        LEFT JOIN tahun_ajaran ta ON s.tahun_ajaran_id = ta.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

/**
 * Get real-time dashboard statistics
 * @return array - Stats for total, pending, lulus, tidak_lulus
 */
function get_dashboard_stats()
{
    global $conn;

    $stats = [];
    $stats['total'] = $conn->query("SELECT COUNT(*) as c FROM siswa")->fetch_assoc()['c'];
    $stats['lulus'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'lulus'")->fetch_assoc()['c'];
    $stats['pending'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'pending'")->fetch_assoc()['c'];
    $stats['tidak_lulus'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'tidak_lulus'")->fetch_assoc()['c'];
    $stats['cadangan'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'cadangan'")->fetch_assoc()['c'];

    return $stats;
}

/**
 * Get active tahun ajaran
 * @return string - Active academic year or 'Tidak Ada'
 */
function get_active_tahun_ajaran()
{
    global $conn;

    $result = $conn->query("SELECT tahun FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['tahun'];
    }
    return 'Tidak Ada';
}

/**
 * Count uploaded documents for a student
 * @param int $siswa_id - Student ID
 * @return array - ['total' => 7, 'uploaded' => 5, 'percentage' => 71]
 */
function count_uploaded_documents($siswa_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            akte_path, kk_path, ijazah_path, 
            kip_path, ktp_ayah_path, ktp_ibu_path, foto_path
        FROM siswa WHERE id = ?
    ");
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $total = 7; // Total documents (3 wajib + 4 baru, KIP opsional tapi dihitung)
    $uploaded = 0;

    foreach ($row as $path) {
        if (!empty($path)) $uploaded++;
    }

    $percentage = $total > 0 ? round(($uploaded / $total) * 100) : 0;

    return [
        'total' => $total,
        'uploaded' => $uploaded,
        'percentage' => $percentage
    ];
}

/**
 * Get document verification status summary
 * @param int $siswa_id - Student ID
 * @return array - ['all_valid' => bool, 'pending_count' => int, 'invalid_count' => int]
 */
function get_document_status_summary($siswa_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            akte_status, kk_status, ijazah_status,
            kip_status, ktp_ayah_status, ktp_ibu_status
        FROM siswa WHERE id = ?
    ");
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $pending = 0;
    $invalid = 0;
    $valid = 0;

    foreach ($row as $status) {
        if ($status === 'pending') $pending++;
        elseif ($status === 'invalid') $invalid++;
        elseif ($status === 'valid') $valid++;
    }

    return [
        'all_valid' => ($invalid === 0 && $pending === 0),
        'pending_count' => $pending,
        'invalid_count' => $invalid,
        'valid_count' => $valid
    ];
}

/**
 * Mark announcement as seen for student
 * @param int $siswa_id - Student ID
 * @return bool - Success status
 */
function mark_announcement_seen($siswa_id)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE siswa SET seen_announcement = 1 WHERE id = ?");
    $stmt->bind_param("i", $siswa_id);
    return $stmt->execute();
}

/**
 * Reset announcement seen status (when status changes)
 * @param int $siswa_id - Student ID
 * @return bool - Success status
 */
function reset_announcement_seen($siswa_id)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE siswa SET seen_announcement = 0 WHERE id = ?");
    $stmt->bind_param("i", $siswa_id);
    return $stmt->execute();
}

/**
 * Check if student has completed all required documents
 * @param int $siswa_id - Student ID
 * @return bool - True if all required docs uploaded
 */
function has_completed_required_documents($siswa_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            akte_path, kk_path, ijazah_path,
            ktp_ayah_path, ktp_ibu_path, foto_path
        FROM siswa WHERE id = ?
    ");
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    // KIP is optional, others are required
    return !empty($row['akte_path'])
        && !empty($row['kk_path'])
        && !empty($row['ijazah_path'])
        && !empty($row['ktp_ayah_path'])
        && !empty($row['ktp_ibu_path'])
        && !empty($row['foto_path']);
}
