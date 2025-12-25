<?php
// pendaftaran/upload.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../config/database.php';

require_login();
$user_id = current_user_id();

// Fetch Student Data
$stmt = $conn->prepare("
    SELECT s.* 
    FROM siswa s 
    JOIN akun_siswa a ON s.id = a.siswa_id 
    WHERE a.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) die("Data siswa tidak ditemukan.");

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die("Invalid CSRF");

    $type = $_POST['type'];
    $allowed_types = ['akte', 'kk', 'ijazah'];
    if (!in_array($type, $allowed_types)) die("Invalid upload type");

    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $new_filename = $type . '_' . $student['id'] . '_' . time() . '.' . $file_ext;
        $target_path = 'uploads/' . $new_filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/../' . $target_path)) {
            $stmt = $conn->prepare("UPDATE siswa SET {$type}_path = ?, {$type}_status = 'pending' WHERE id = ?");
            $stmt->bind_param("si", $target_path, $student['id']);
            $stmt->execute();
            $success = ucfirst($type) . " berhasil diupload.";

            // Refresh student data
            $student[$type . '_path'] = $target_path;
        }
    }
}

function status_badge($status, $path)
{
    if (!$path) return '<span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">Belum Upload</span>';
    $colors = [
        'pending' => 'bg-amber-100 text-amber-700',
        'valid' => 'bg-emerald-100 text-emerald-700',
        'invalid' => 'bg-rose-100 text-rose-700',
    ];
    $c = $colors[$status] ?? $colors['pending'];
    return '<span class="' . $c . ' px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">' . $status . '</span>';
}

$title = "Upload Berkas";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <?php include __DIR__ . '/../templates/siswadata.php'; ?>

        <div class="lg:col-span-9 space-y-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Berkas Persyaratan</h1>
                <p class="text-slate-500 font-semibold mt-1">Unggah dokumen pendukung untuk proses verifikasi data.</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-3xl flex items-center text-emerald-700 font-black">
                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl mr-3"></iconify-icon>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $files = [
                    ['label' => 'Akte Kelahiran', 'type' => 'akte', 'path' => $student['akte_path'], 'status' => $student['akte_status']],
                    ['label' => 'Kartu Keluarga', 'type' => 'kk', 'path' => $student['kk_path'], 'status' => $student['kk_status']],
                    ['label' => 'Ijazah / SKL', 'type' => 'ijazah', 'path' => $student['ijazah_path'], 'status' => $student['ijazah_status']],
                ];

                foreach ($files as $file):
                ?>
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col group">
                        <div class="p-8 flex-1">
                            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                                <iconify-icon icon="solar:file-send-bold-duotone"></iconify-icon>
                            </div>
                            <h3 class="font-black text-slate-800 mb-2"><?= $file['label'] ?></h3>
                            <div class="mb-6"><?= status_badge($file['status'], $file['path']) ?></div>

                            <?php if ($file['path']): ?>
                                <a href="<?= base_url($file['path']) ?>" target="_blank" class="inline-flex items-center text-primary font-bold text-xs hover:underline decoration-2 underline-offset-4">
                                    Preview Dokumen
                                    <iconify-icon icon="solar:eye-bold-duotone" class="ml-1"></iconify-icon>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="p-6 bg-slate-50 border-t border-slate-100">
                            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                <?= csrf_field() ?>
                                <input type="hidden" name="type" value="<?= $file['type'] ?>">
                                <label class="block">
                                    <span class="sr-only">Pilih File</span>
                                    <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-primary file:text-white hover:file:bg-secondary transition-all cursor-pointer">
                                </label>
                                <button type="submit" class="w-full bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white py-3 rounded-2xl font-black text-xs transition-all active:scale-95">Upload Sekarang</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>