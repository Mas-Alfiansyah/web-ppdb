<?php
// admin/setting.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../config/database.php';

require_admin();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die("Invalid CSRF");

    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $conn->prepare("UPDATE pengaturan SET nilai = ? WHERE kunci = ?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    $success = "Pengaturan berhasil diperbarui.";
}

$title = "Pengaturan Sistem";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar_admin.php';
?>

<div class="max-w-4xl space-y-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Pengaturan PPDB</h1>
        <p class="text-slate-500 font-semibold mt-1">Konfigurasi informasi sekolah, kuota, dan jadwal pendaftaran.</p>
    </div>

    <?php if (isset($success)): ?>
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center text-emerald-700 font-bold">
            <iconify-icon icon="solar:check-circle-bold" class="text-2xl mr-3"></iconify-icon>
            <?= $success ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <form method="POST" class="p-10 space-y-8">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Nama Sekolah</label>
                    <input type="text" name="settings[nama_sekolah]" value="<?= get_setting('nama_sekolah') ?>" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Kuota Pendaftaran</label>
                    <input type="number" name="settings[kuota_pendaftaran]" value="<?= get_setting('kuota_pendaftaran') ?>" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Tanggal Mulai</label>
                    <input type="date" name="settings[tgl_mulai]" value="<?= get_setting('tgl_mulai') ?>" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Tanggal Selesai</label>
                    <input type="date" name="settings[tgl_selesai]" value="<?= get_setting('tgl_selesai') ?>" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10 transition-all">
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

<!-- Close Sidebar Wrapper -->
</div>
</div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>