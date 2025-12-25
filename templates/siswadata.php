<?php
// templates/siswadata.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="lg:col-span-3">
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative top-1">
        <!-- Profile Header -->
        <div class="bg-primary p-8 text-white relative overflow-hidden">
            <iconify-icon icon="solar:user-circle-bold-duotone" class="absolute -bottom-6 -right-6 text-8xl opacity-20"></iconify-icon>
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl font-black mb-4">
                <?= strtoupper(substr($student['nama_lengkap'] ?? 'U', 0, 1)) ?>
            </div>
            <h2 class="font-black text-lg leading-tight mb-1"><?= xss_clean($student['nama_lengkap'] ?? 'User') ?></h2>
            <p class="text-white/70 text-xs font-bold uppercase tracking-widest"><?= $student['no_pendaftaran'] ?? 'Belum Terdaftar' ?></p>
        </div>

        <!-- Menu Navigation -->
        <div class="p-4 space-y-1">
            <a href="index.php" class="flex items-center space-x-3 px-6 py-4 rounded-2xl transition-all font-bold <?= $current_page == 'index.php' ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                <iconify-icon icon="solar:widget-bold-duotone" class="text-2xl"></iconify-icon>
                <span>Dashboard</span>
            </a>
            <a href="form.php" class="flex items-center space-x-3 px-6 py-4 rounded-2xl transition-all font-bold <?= $current_page == 'form.php' ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                <iconify-icon icon="solar:user-id-bold-duotone" class="text-2xl"></iconify-icon>
                <span>Biodata Diri</span>
            </a>
            <a href="upload.php" class="flex items-center space-x-3 px-6 py-4 rounded-2xl transition-all font-bold <?= $current_page == 'upload.php' ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                <iconify-icon icon="solar:cloud-upload-bold-duotone" class="text-2xl"></iconify-icon>
                <span>Upload Berkas</span>
            </a>
            <div class="pt-4 mt-4 border-t border-slate-50">
                <a href="../auth/logout.php" class="flex items-center space-x-3 px-6 py-4 rounded-2xl text-rose-500 hover:bg-rose-50 transition-all font-bold">
                    <iconify-icon icon="solar:logout-bold-duotone" class="text-2xl"></iconify-icon>
                    <span>Keluar Sesi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-slate-900 rounded-3xl p-6 text-white relative overflow-hidden">
        <iconify-icon icon="solar:info-circle-bold-duotone" class="absolute -top-4 -right-4 text-7xl opacity-10"></iconify-icon>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-2">Pusat Bantuan</p>
        <p class="text-xs font-medium text-slate-400 leading-relaxed italic">Jika mengalami kesulitan dalam pengisian data, silakan hubungi panitia PPDB di sekolah.</p>
    </div>
</aside>