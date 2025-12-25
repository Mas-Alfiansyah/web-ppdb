<?php
// admin/siswa/index.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

// Fetch Data Siswa + Tahun Ajaran
$query = "SELECT s.*, ta.tahun as nama_tahun 
          FROM siswa s 
          LEFT JOIN tahun_ajaran ta ON s.tahun_ajaran_id = ta.id 
          ORDER BY s.created_at DESC";
$result = $conn->query($query);

$title = "Data Siswa";
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar_admin.php';
?>

<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Data Peserta PPDB</h1>
            <p class="text-slate-500 font-semibold mt-1">Daftar seluruh calon siswa yang telah mendaftar.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="../laporan/export.php" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-emerald-200 transition-all flex items-center active:scale-95">
                <iconify-icon icon="solar:file-download-bold-duotone" class="mr-2 text-xl"></iconify-icon>
                Export Excel
            </a>
            <button onclick="window.print()" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-6 py-3 rounded-2xl font-black shadow-sm transition-all flex items-center active:scale-95">
                <iconify-icon icon="solar:printer-bold-duotone" class="mr-2 text-xl"></iconify-icon>
                Cetak PDF
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="overflow-x-auto">
                <table id="tableSiswa" class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Pendaftar</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">NISN</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun Ajaran</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="w-11 h-11 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black mr-4 group-hover:bg-primary group-hover:text-white transition-all">
                                            <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 leading-none mb-1"><?= xss_clean($row['nama_lengkap']) ?></p>
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= $row['no_pendaftaran'] ?? 'Draft' ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 font-bold text-slate-600"><?= xss_clean($row['nisn']) ?></td>
                                <td class="px-6 py-6">
                                    <span class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                        <?= $row['nama_tahun'] ?? '-' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <?php
                                    $st = [
                                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                                        'lulus' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                                        'tidak_lulus' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700'],
                                    ];
                                    $c = $st[$row['status']] ?? $st['pending'];
                                    ?>
                                    <span class="<?= $c['bg'] ?> <?= $c['text'] ?> px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="inline-flex items-center bg-slate-100 hover:bg-primary hover:text-white text-slate-600 px-4 py-2 rounded-xl transition-all font-bold text-sm">
                                        Detail
                                        <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ml-1"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $ !== 'undefined') {
            $('#tableSiswa').DataTable({
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-6 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-6 gap-4"ip>',
                language: {
                    search: "",
                    searchPlaceholder: "Cari peserta...",
                    lengthMenu: "Tampilkan _MENU_",
                }
            });
            // Style the custom search box
            $('.dataTables_filter input').addClass('bg-slate-50 border-slate-200 rounded-2xl px-5 py-3 ml-0 w-full md:w-64');
            $('.dataTables_length select').addClass('bg-slate-50 border-slate-200 rounded-2xl px-4 py-2 mx-2');
        }
    });
</script>

<!-- Close Wrapper (opened in sidebar_admin.php) -->
</div>
</div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>