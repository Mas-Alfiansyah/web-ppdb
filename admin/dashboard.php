<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../config/database.php';

require_admin();

// Get Active Tahun Ajaran
$ta_result = $conn->query("SELECT tahun FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
$active_ta = $ta_result->fetch_assoc()['tahun'] ?? 'Tidak Ada';

// Stats
$stats = [];
$stats['total'] = $conn->query("SELECT COUNT(*) as c FROM siswa")->fetch_assoc()['c'];
$stats['lulus'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'lulus'")->fetch_assoc()['c'];
$stats['pending'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'pending'")->fetch_assoc()['c'];
$stats['tidak_lulus'] = $conn->query("SELECT COUNT(*) as c FROM siswa WHERE status = 'tidak_lulus'")->fetch_assoc()['c'];

$title = "Dashboard Admin";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar_admin.php';
?>

<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Overview Dashboard</h1>
            <p class="text-slate-500 font-semibold mt-1">Status penerimaan siswa tahun ajaran <span class="text-primary"><?= $active_ta ?></span>.</p>
        </div>
        <div class="flex items-center space-x-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-xs font-black uppercase tracking-widest text-slate-400">Sistem Online</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center group hover:bg-primary transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-3xl group-hover:bg-white group-hover:text-primary transition-all">
                <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
            </div>
            <div class="ml-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-white/70">Total Pendaftar</p>
                <h3 class="text-2xl font-black text-slate-900 group-hover:text-white leading-none mt-1"><?= $stats['total'] ?></h3>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center group hover:bg-amber-500 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl group-hover:bg-white group-hover:text-amber-500 transition-all">
                <iconify-icon icon="solar:clock-circle-bold-duotone"></iconify-icon>
            </div>
            <div class="ml-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-white/70">Pending</p>
                <h3 class="text-2xl font-black text-slate-900 group-hover:text-white leading-none mt-1"><?= $stats['pending'] ?></h3>
            </div>
        </div>

        <!-- Lulus -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center group hover:bg-emerald-500 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl group-hover:bg-white group-hover:text-emerald-500 transition-all">
                <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
            </div>
            <div class="ml-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-white/70">Lulus</p>
                <h3 class="text-2xl font-black text-slate-900 group-hover:text-white leading-none mt-1"><?= $stats['lulus'] ?></h3>
            </div>
        </div>

        <!-- Tidak Lulus -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center group hover:bg-rose-500 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-3xl group-hover:bg-white group-hover:text-rose-500 transition-all">
                <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
            </div>
            <div class="ml-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-white/70">Tidak Lulus</p>
                <h3 class="text-2xl font-black text-slate-900 group-hover:text-white leading-none mt-1"><?= $stats['tidak_lulus'] ?></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Actions -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Selamat Datang, Admin!</h2>
                    <p class="text-slate-500 font-medium mb-8 max-w-lg leading-relaxed">Kelola seluruh proses pendaftaran siswa baru mulai dari verifikasi berkas hingga pengumuman kelulusan dalam satu dashboard terintegrasi.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="siswa/index.php" class="bg-primary hover:bg-secondary text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-primary/30 transition-all active:scale-95 flex items-center">
                            Kelola Data Peserta
                            <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ml-2 text-2xl"></iconify-icon>
                        </a>
                        <a href="setting.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-8 py-4 rounded-2xl font-black transition-all active:scale-95">Pengaturan PPDB</a>
                    </div>
                </div>
                <iconify-icon icon="solar:user-speak-bold-duotone" class="absolute -bottom-10 -right-10 text-[15rem] text-slate-50 opacity-50 pointer-events-none"></iconify-icon>
            </div>
        </div>

        <!-- Info / Quick Tips -->
        <div class="space-y-6">
            <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-xl shadow-slate-200">
                <iconify-icon icon="solar:magic-stick-bold-duotone" class="absolute -bottom-6 -right-6 text-9xl opacity-10"></iconify-icon>
                <h4 class="text-lg font-black mb-4 flex items-center">
                    <iconify-icon icon="solar:check-read-bold-duotone" class="mr-2 text-2xl text-primary"></iconify-icon>
                    Tips Cepat
                </h4>
                <div class="space-y-4">
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Lakukan verifikasi berkas setiap hari untuk mempercepat antrian pendaftaran.</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Export data secara berkala sebagai backup fisik dokumen pendaftaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>