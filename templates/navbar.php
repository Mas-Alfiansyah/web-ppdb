<?php
// templates/navbar.php
$is_logged_in = is_logged_in();
$user_role = $_SESSION['user_role'] ?? '';
?>
<nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="<?= base_url() ?>" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30 group-hover:scale-110 transition-transform">
                    <img src="../public/images/logo smk.png" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="font-black text-xl tracking-tight text-slate-800">PPDB <span class="text-primary">ONLINE</span></span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="<?= base_url() ?>" class="text-sm font-black text-slate-600 hover:text-primary transition-colors">Beranda</a>
                <?php if ($is_logged_in): ?>
                    <?php if ($user_role === 'admin'): ?>
                        <a href="<?= base_url('admin/dashboard.php') ?>" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl text-sm font-black hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">Panel Admin</a>
                    <?php else: ?>
                        <a href="<?= base_url('pendaftaran/index.php') ?>" class="text-sm font-black text-slate-600 hover:text-primary transition-colors">Dashboard</a>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 bg-slate-100 px-4 py-2 rounded-xl text-sm font-black text-slate-700">
                                <iconify-icon icon="solar:user-circle-bold-duotone" class="text-xl text-primary"></iconify-icon>
                                <span>Akun Saya</span>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 hidden group-hover:block animate-in fade-in slide-in-from-top-2 duration-200">
                                <a href="<?= base_url('auth/logout.php') ?>" class="flex items-center px-4 py-2 text-sm font-bold text-rose-500 hover:bg-rose-50 italic"> Keluar</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('auth/login.php') ?>" class="text-sm font-black text-slate-600 hover:text-primary transition-colors">Masuk</a>
                    <a href="<?= base_url('auth/register.php') ?>" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-black hover:bg-secondary transition-all shadow-lg shadow-primary/30">Daftar Sekarang</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="md:hidden text-slate-800" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <iconify-icon icon="solar:hamburger-menu-bold-duotone" class="text-3xl"></iconify-icon>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 p-6 space-y-4">
        <a href="<?= base_url() ?>" class="block text-sm font-black text-slate-600 hover:text-primary">Beranda</a>
        <?php if ($is_logged_in): ?>
            <?php if ($user_role === 'admin'): ?>
                <a href="<?= base_url('admin/dashboard.php') ?>" class="block bg-slate-900 text-white px-4 py-3 rounded-xl text-center font-black">Panel Admin</a>
            <?php else: ?>
                <a href="<?= base_url('pendaftaran/index.php') ?>" class="block text-sm font-black text-slate-600">Dashboard</a>
                <a href="<?= base_url('auth/logout.php') ?>" class="block text-sm font-black text-rose-500">Keluar</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?= base_url('auth/login.php') ?>" class="block text-sm font-black text-slate-600">Masuk</a>
            <a href="<?= base_url('auth/register.php') ?>" class="block bg-primary text-white px-4 py-3 rounded-xl text-center font-black">Daftar Sekarang</a>
        <?php endif; ?>
    </div>
</nav>