<?php
// pendaftaran/index.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/data_helper.php';
require_once __DIR__ . '/../config/database.php';

require_login();
$user_id = current_user_id();

// Fetch Student Data using centralized helper
$student = get_student_data($user_id);
if (!$student) die("Data siswa tidak ditemukan.");

$title = "Dashboard Siswa";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <?php include __DIR__ . '/../templates/siswadata.php'; ?>

        <div class="lg:col-span-9 space-y-8">
            <!-- Header Welcome -->
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-2xl font-black text-slate-900 mb-2">Halo, <?= xss_clean($student['nama_lengkap']) ?>! 👋</h1>
                    <p class="text-slate-500 font-semibold max-w-md leading-relaxed">Selamat datang di portal pendaftaran. Silakan lengkapi biodata dan berkas untuk melanjutkan proses seleksi.</p>
                </div>
                <div class="bg-primary/5 px-6 py-4 rounded-3xl border border-primary/10 text-center md:text-right relative z-10">
                    <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1">Status Pendaftaran</p>
                    <div class="flex items-center justify-center md:justify-end space-x-2">
                        <div class="w-3 h-3 rounded-full <?= $student['status'] == 'lulus' ? 'bg-emerald-500' : ($student['status'] == 'tidak_lulus' ? 'bg-rose-500' : 'bg-amber-500') ?> animate-pulse"></div>
                        <span class="text-lg font-black text-slate-800 uppercase"><?= $student['status'] ?></span>
                    </div>
                </div>
                <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="absolute -bottom-8 -right-8 text-9xl text-slate-50"></iconify-icon>
            </div>

            <!-- Steps Progress -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative group">
                    <div class="w-12 h-12 <?= !empty($student['nisn']) ? 'bg-emerald-100 text-emerald-600' : 'bg-primary/10 text-primary' ?> rounded-2xl flex items-center justify-center text-2xl font-black mb-6 transition-all group-hover:scale-110">
                        <?php if (!empty($student['nisn'])): ?>
                            <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                        <?php else: ?>
                            1
                        <?php endif; ?>
                    </div>
                    <h3 class="font-black text-slate-800 mb-2">Lengkapi Biodata</h3>
                    <p class="text-xs font-bold text-slate-400 leading-relaxed mb-6">Isi data diri dengan lengkap dan benar sesuai dokumen asli.</p>
                    <a href="form.php" class="text-sm font-black text-primary hover:text-secondary flex items-center">
                        Cek Biodata
                        <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ml-1 text-xl"></iconify-icon>
                    </a>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative group">
                    <div class="w-12 h-12 <?= !empty($student['akte_path']) ? 'bg-emerald-100 text-emerald-600' : 'bg-primary/10 text-primary' ?> rounded-2xl flex items-center justify-center text-2xl font-black mb-6 transition-all group-hover:scale-110">
                        <?php if (!empty($student['akte_path'])): ?>
                            <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                        <?php else: ?>
                            2
                        <?php endif; ?>
                    </div>
                    <h3 class="font-black text-slate-800 mb-2">Upload Berkas</h3>
                    <p class="text-xs font-bold text-slate-400 leading-relaxed mb-6">Unggah Akte, KK, dan Ijazah dalam format gambar/PDF.</p>
                    <a href="upload.php" class="text-sm font-black text-primary hover:text-secondary flex items-center">
                        Upload Sekarang
                        <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ml-1 text-xl"></iconify-icon>
                    </a>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative group">
                    <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-2xl font-black mb-6 transition-all group-hover:scale-110">
                        <iconify-icon icon="solar:notification-lines-bold-duotone"></iconify-icon>
                    </div>
                    <h3 class="font-black text-slate-800 mb-2">Pantau Status</h3>
                    <p class="text-xs font-bold text-slate-400 leading-relaxed mb-6">Cek pengumuman secara berkala di halaman dashboard ini.</p>
                    <span class="text-sm font-black text-slate-300">Harap Menunggu</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Announcement Logic
$show_announcement = false;
$announcement_type = '';

if ($student['seen_announcement'] == 0) {
    if ($student['status'] == 'lulus') {
        $show_announcement = true;
        $announcement_type = 'lulus';
    } elseif ($student['status'] == 'tidak_lulus') {
        $show_announcement = true;
        $announcement_type = 'tidak_lulus';
    }
}
?>

<?php if ($show_announcement): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let type = '<?= $announcement_type ?>';
            let title = type === 'lulus' ? 'SELAMAT! ANDA LULUS' : 'MOHON MAAF';
            let text = type === 'lulus' ?
                'Selamat! Anda dinyatakan LULUS seleksi PPDB. Silakan lakukan daftar ulang.' :
                'Mohon maaf, Anda dinyatakan TIDAK LULUS seleksi tahun ini.';
            let icon = type === 'lulus' ? 'success' : 'error';
            let confirmBtn = type === 'lulus' ? 'Alhamdulillah, Terima Kasih' : 'Tutup';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: confirmBtn,
                width: 600,
                padding: '3em',
                backdrop: `rgba(0,0,123,0.4)`,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mark as seen via AJAX
                    fetch('mark_seen.php', {
                            method: 'POST'
                        })
                        .then(() => {
                            location.reload();
                        });
                }
            });
        });
    </script>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>