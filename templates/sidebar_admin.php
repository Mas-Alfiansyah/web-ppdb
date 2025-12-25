<?php
// templates/sidebar_admin.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="min-h-screen flex flex-col md:flex-row bg-slate-50">
    <!-- Sidebar Desktop -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white border-r border-slate-100 w-72 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50">
        <div class="h-full flex flex-col p-6">
            <!-- Logo Sidebar -->
            <div class="flex items-center space-x-3 mb-10 px-2">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30">
                    <iconify-icon icon="solar:shield-check-bold-duotone" class="text-2xl"></iconify-icon>
                </div>
                <span class="font-black text-xl tracking-tight text-slate-800">Admin <span class="text-primary text-[10px] block font-black uppercase tracking-widest leading-none">Control Panel</span></span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-2 overflow-y-auto custom-scrollbar">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 px-2">Main Menu</p>

                <a href="<?= base_url('admin/dashboard.php') ?>" class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= $current_page == 'dashboard.php' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                    <iconify-icon icon="solar:widget-bold-duotone" class="text-2xl"></iconify-icon>
                    <span>Dashboard</span>
                </a>

                <a href="<?= base_url('admin/siswa/index.php') ?>" class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= $current_page == 'index.php' && strpos($_SERVER['PHP_SELF'], 'siswa') !== false ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="text-2xl"></iconify-icon>
                    <span>Peserta Didik</span>
                </a>

                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-8 mb-4 px-2">Data Akademik</p>

                <a href="<?= base_url('admin/tahun-ajaran/index.php') ?>" class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= $current_page == 'index.php' && strpos($_SERVER['PHP_SELF'], 'tahun-ajaran') !== false ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                    <iconify-icon icon="solar:calendar-date-bold-duotone" class="text-2xl"></iconify-icon>
                    <span>Tahun Ajaran</span>
                </a>

                <a href="<?= base_url('admin/setting.php') ?>" class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= $current_page == 'setting.php' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' ?>">
                    <iconify-icon icon="solar:settings-bold-duotone" class="text-2xl"></iconify-icon>
                    <span>Pengaturan</span>
                </a>
            </nav>

            <!-- User Profile Bottom -->
            <div class="mt-auto pt-6 border-t border-slate-50">
                <div class="bg-slate-900 rounded-3xl p-4 text-white relative overflow-hidden group">
                    <div class="flex items-center space-x-3 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center font-black">A</div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-black truncate">Administrator</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Master Admin</p>
                        </div>
                        <a href="<?= base_url('auth/logout.php') ?>" class="text-rose-500 hover:text-rose-400 transition-colors">
                            <iconify-icon icon="solar:logout-bold-duotone" class="text-2xl"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay Mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <!-- Main Content Area -->
    <main class="flex-1 md:ml-72 p-6 md:p-10">
        <!-- Header Mobile (Hanya muncul di mobile) -->
        <div class="flex items-center justify-between md:hidden mb-8">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white">
                    <iconify-icon icon="solar:shield-check-bold-duotone" class="text-2xl"></iconify-icon>
                </div>
                <h2 class="font-black text-slate-800">PPDB Admin</h2>
            </div>
            <button id="sidebar-toggle" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-900 shadow-xl shadow-slate-200 border border-slate-100">
                <iconify-icon icon="solar:hamburger-menu-bold-duotone" class="text-2xl"></iconify-icon>
            </button>
        </div>