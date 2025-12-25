<?php
// pendaftaran/form.php
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

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die("Invalid CSRF");

    $nama = $_POST['nama_lengkap'];
    $nisn = $_POST['nisn'];
    $jk = $_POST['jk'];
    $tempat = $_POST['tempat_lahir'];
    $tanggal = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("UPDATE siswa SET nama_lengkap = ?, nisn = ?, jk = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $nama, $nisn, $jk, $tempat, $tanggal, $alamat, $student['id']);
    $stmt->execute();

    $success = "Biodata berhasil diperbarui.";
    // Refresh data
    $student['nama_lengkap'] = $nama;
    $student['nisn'] = $nisn;
    $student['jk'] = $jk;
    $student['tempat_lahir'] = $tempat;
    $student['tanggal_lahir'] = $tanggal;
    $student['alamat'] = $alamat;
}

$title = "Lengkapi Biodata";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <?php include __DIR__ . '/../templates/siswadata.php'; ?>

        <div class="lg:col-span-9 space-y-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Biodata Calon Siswa</h1>
                <p class="text-slate-500 font-semibold mt-1">Pastikan data yang Anda masukkan sesuai dengan dokumen asli.</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-3xl flex items-center text-emerald-700 font-black">
                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl mr-3"></iconify-icon>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <form method="POST" class="p-10 space-y-8">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= xss_clean($student['nama_lengkap']) ?>" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">NISN</label>
                            <input type="text" name="nisn" value="<?= xss_clean($student['nisn']) ?>" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Jenis Kelamin</label>
                            <select name="jk" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                                <option value="L" <?= $student['jk'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= $student['jk'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="<?= xss_clean($student['tempat_lahir']) ?>" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="<?= $student['tanggal_lahir'] ?>" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="col-span-full space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Alamat Lengkap</label>
                            <textarea name="alamat" required rows="3" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all"><?= xss_clean($student['alamat']) ?></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50">
                        <button type="submit" class="bg-primary hover:bg-secondary text-white px-10 py-4 rounded-2xl font-black shadow-lg shadow-primary/30 transition-all active:scale-95 flex items-center">
                            <iconify-icon icon="solar:diskette-bold-duotone" class="mr-2 text-2xl"></iconify-icon>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>