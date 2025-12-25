<?php
// index.php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/security.php';
require_once __DIR__ . '/config/database.php';

$title = "Penerimaan Siswa Baru";
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/navbar.php';

// Fetch Global Settings
$nama_sekolah = get_setting('nama_sekolah', 'Sekolah Kami');
$tgl_mulai = get_setting('tgl_mulai', '');
$tgl_selesai = get_setting('tgl_selesai', '');
$kuota = get_setting('kuota_pendaftaran', '0');

// Calculate Remaining Days
$now = time();
$end = strtotime($tgl_selesai);
$days_left = floor(($end - $now) / (60 * 60 * 24));
?>

<main class="relative overflow-hidden">
    <!-- Hero Section -->
    <section class="relative pt-20 pb-20 lg:pt-32 lg:pb-40 bg-white">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[40rem] h-[40rem] bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[30rem] h-[30rem] bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="flex-1 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 bg-primary/5 border border-primary/10 px-4 py-2 rounded-full mb-8">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-xs font-black uppercase tracking-widest text-primary">Pendaftaran Dibuka TA 2024/2025</span>
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8">
                        Mulai Masa Depanmu di <span class="text-primary"><?= xss_clean($nama_sekolah) ?></span>.
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-500 font-medium mb-12 max-w-2xl leading-relaxed mx-auto lg:mx-0">
                        Bergabunglah bersama kami dan temukan potensi terbaikmu dalam lingkungan belajar yang modern, kreatif, dan inovatif.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="<?= BASE_URL ?>auth/register.php" class="w-full sm:w-auto bg-primary hover:bg-secondary text-white px-10 py-5 rounded-2xl font-black shadow-2xl shadow-primary/40 transition-all active:scale-95 transform flex items-center justify-center">
                            Daftar Sekarang
                            <iconify-icon icon="solar:user-plus-bold-duotone" class="ml-2 text-2xl"></iconify-icon>
                        </a>
                        <a href="#prosedur" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 px-10 py-5 rounded-2xl font-black transition-all flex items-center justify-center">
                            Lihat Prosedur
                        </a>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <div class="relative w-full aspect-square max-w-lg mx-auto overflow-hidden rounded-[3rem] shadow-2xl rotate-3 bg-slate-100 border-8 border-white group hover:rotate-0 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <iconify-icon icon="solar:square-academic-cap-bold-duotone" class="text-[15rem] text-primary/10"></iconify-icon>
                        </div>
                    </div>
                    <!-- Badges -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-[2rem] shadow-xl border border-slate-50 animate-bounce">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
                                <iconify-icon icon="solar:verified-check-bold-duotone"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sisa Kuota</p>
                                <p class="text-xl font-black text-slate-800"><?= $kuota ?> Peserta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section id="prosedur" class="py-24 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-slate-900 mb-4">Langkah Mudah Mendaftar</h2>
                <p class="text-slate-500 font-bold max-w-lg mx-auto">Ikuti alur pendaftaran PPDB online kami untuk memudahkan proses administrasi Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 group hover:bg-primary transition-all duration-300">
                    <div class="w-16 h-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:bg-white transition-all">
                        <iconify-icon icon="solar:user-plus-bold-duotone"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-4 group-hover:text-white transition-all">1. Buat Akun</h3>
                    <p class="text-slate-500 group-hover:text-white/70 transition-all font-medium leading-relaxed">Daftarkan alamat email dan buat password untuk mengakses dashboard pendaftar.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 group hover:bg-emerald-500 transition-all duration-300">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:bg-white group-hover:text-emerald-500 transition-all">
                        <iconify-icon icon="solar:pen-new-bold-duotone"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-4 group-hover:text-white transition-all">2. Isi Biodata</h3>
                    <p class="text-slate-500 group-hover:text-white/70 transition-all font-medium leading-relaxed">Lengkapi formulir biodata diri dan unggah berkas persyaratan yang diminta.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 group hover:bg-amber-500 transition-all duration-300">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:bg-white group-hover:text-amber-500 transition-all">
                        <iconify-icon icon="solar:folder-with-files-bold-duotone"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-4 group-hover:text-white transition-all">3. Verifikasi</h3>
                    <p class="text-slate-500 group-hover:text-white/70 transition-all font-medium leading-relaxed">Pantau status pendaftaran Anda secara berkala hingga pengumuman akhir.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="bg-slate-900 rounded-[3rem] p-12 lg:p-20 text-white relative flex flex-col lg:flex-row items-center gap-12 overflow-hidden shadow-2xl shadow-primary/20">
                <iconify-icon icon="solar:calendar-mark-bold-duotone" class="absolute -top-20 -right-20 text-[30rem] text-white/5 pointer-events-none rotate-12"></iconify-icon>
                <div class="flex-1 relative z-10">
                    <h2 class="text-4xl font-black mb-6">Jadwal Pendaftaran</h2>
                    <p class="text-slate-400 font-medium mb-10 leading-relaxed text-lg italic">"Daftarkan segera sebelum kuota penuh. Pastikan Anda mendaftar sesuai dalam rentang waktu yang tersedia."</p>
                    <div class="flex flex-col sm:flex-row gap-8">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-2">Tanggal Mulai</p>
                            <p class="text-2xl font-black"><?= date('d F Y', strtotime($tgl_mulai)) ?></p>
                        </div>
                        <div class="w-px h-12 bg-white/10 hidden sm:block"></div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Batas Akhir</p>
                            <p class="text-2xl font-black"><?= date('d F Y', strtotime($tgl_selesai)) ?></p>
                        </div>
                    </div>
                </div>
                <div class="lg:w-80 relative z-10">
                    <div class="bg-white/10 backdrop-blur-md rounded-[2rem] p-8 border border-white/10 text-center">
                        <h4 class="text-sm font-black uppercase tracking-widest text-white/60 mb-2">Sisa Waktu</h4>
                        <div class="text-6xl font-black mb-2"><?= max(0, $days_left) ?></div>
                        <p class="font-bold text-primary italic uppercase tracking-widest text-xs">Hari Lagi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>