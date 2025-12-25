<?php
// admin/siswa/detail.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare("SELECT s.*, ta.tahun as nama_tahun FROM siswa s LEFT JOIN tahun_ajaran ta ON s.tahun_ajaran_id = ta.id WHERE s.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) die("Data tidak ditemukan");

$title = "Detail Siswa - " . $row['nama_lengkap'];
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar_admin.php';
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="index.php" class="text-slate-400 hover:text-primary transition-colors">
                    <iconify-icon icon="solar:alt-arrow-left-bold-duotone" class="text-2xl"></iconify-icon>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Detail Peserta Didik</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight"><?= xss_clean($row['nama_lengkap']) ?></h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- PDF Export Button -->
            <a href="../laporan/export_pdf.php?id=<?= $id ?>" target="_blank"
                class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-rose-200 transition-all flex items-center active:scale-95">
                <iconify-icon icon="solar:file-download-bold-duotone" class="mr-2 text-xl"></iconify-icon>
                Export PDF
            </a>

            <!-- Status Dropdown (AJAX) -->
            <div class="relative">
                <select id="statusSelect" onchange="updateStatus(<?= $id ?>, this.value)"
                    class="appearance-none bg-white border border-slate-200 text-slate-800 font-black px-6 py-3 pr-10 rounded-2xl shadow-lg shadow-slate-200 cursor-pointer focus:ring-4 focus:ring-primary/10 transition-all uppercase tracking-widest text-sm">
                    <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="lulus" <?= $row['status'] == 'lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="tidak_lulus" <?= $row['status'] == 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                    <option value="cadangan" <?= $row['status'] == 'cadangan' ? 'selected' : '' ?>>Cadangan</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                    <iconify-icon icon="solar:alt-arrow-down-bold-duotone"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Biodata -->
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-xl font-black text-slate-800 mb-8 border-b border-slate-50 pb-4">Biodata Lengkap</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Pendaftaran</p>
                        <p class="font-bold text-slate-700"><?= $row['no_pendaftaran'] ?? '-' ?></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NISN</p>
                        <p class="font-bold text-slate-700"><?= xss_clean($row['nisn']) ?></p>
                    </div>
                    <!-- ... (Other fields similar to before) ... -->
                    <div class="col-span-full">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat</p>
                        <p class="font-bold text-slate-700"><?= xss_clean($row['alamat']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Documents (Direct Check) -->
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-xl font-black text-slate-800 mb-8 border-b border-slate-50 pb-4">Verifikasi Berkas</h3>
                <div class="space-y-4">
                    <?php
                    $docs = [
                        ['label' => 'Akte Kelahiran', 'path' => $row['akte_path'], 'field' => 'akte_status'],
                        ['label' => 'Kartu Keluarga', 'path' => $row['kk_path'], 'field' => 'kk_status'],
                        ['label' => 'Ijazah / SKL', 'path' => $row['ijazah_path'], 'field' => 'ijazah_status'],
                        ['label' => 'KIP (Opsional)', 'path' => $row['kip_path'] ?? null, 'field' => 'kip_status'],
                        ['label' => 'KTP Ayah', 'path' => $row['ktp_ayah_path'] ?? null, 'field' => 'ktp_ayah_status'],
                        ['label' => 'KTP Ibu', 'path' => $row['ktp_ibu_path'] ?? null, 'field' => 'ktp_ibu_status'],
                        ['label' => 'Foto 3x4', 'path' => $row['foto_path'] ?? null, 'field' => null],
                    ];
                    foreach ($docs as $doc):
                    ?>
                        <div class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-primary text-2xl shadow-sm">
                                    <iconify-icon icon="solar:file-check-bold-duotone"></iconify-icon>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?= $doc['label'] ?></p>
                                    <?php if ($doc['field']): ?>
                                        <p class="text-[10px] font-black uppercase tracking-widest <?= $row[$doc['field']] == 'valid' ? 'text-emerald-500' : 'text-amber-500' ?>">
                                            <?= $row[$doc['field']] ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                            <?= $doc['path'] ? 'Uploaded' : 'Belum Upload' ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if ($doc['path']): ?>
                                    <!-- Direct View Button -->
                                    <a href="<?= base_url($doc['path']) ?>" target="_blank"
                                        class="inline-flex items-center bg-white border border-slate-200 hover:border-primary hover:text-primary text-slate-600 px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm group">
                                        <iconify-icon icon="solar:eye-bold-duotone" class="mr-2 text-lg group-hover:scale-110 transition-transform"></iconify-icon>
                                        Lihat Berkas
                                    </a>

                                    <!-- Verification Handlers (Only for documents with status field) -->
                                    <?php if ($doc['field'] && $row[$doc['field']] != 'valid'): ?>
                                        <a href="verifikasi_berkas.php?id=<?= $id ?>&field=<?= $doc['field'] ?>&status=valid"
                                            class="inline-flex items-center bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white px-3 py-2 rounded-xl transition-all" title="Tandai Valid">
                                            <iconify-icon icon="solar:check-circle-bold-duotone" class="text-xl"></iconify-icon>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Belum Upload</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-8">
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <iconify-icon icon="solar:shield-user-bold-duotone" class="absolute -bottom-6 -right-6 text-9xl opacity-10"></iconify-icon>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-2">Pusat Kontrol</p>
                    <p class="text-sm font-medium text-slate-400 leading-relaxed mb-6">
                        Ubah status kelulusan melalui dropdown di atas. Perubahan status akan memicu notifikasi pop-up pada dashboard siswa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStatus(id, status) {
        Swal.fire({
            title: 'Memproses...',
            text: 'Mengupdate status siswa',
            didOpen: () => {
                Swal.showLoading()
            }
        });

        fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Diperbarui',
                        text: 'Siswa akan melihat pengumuman saat login berikutnya.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Optional: Update UI color based on status if needed
                } else {
                    Swal.fire('Error', data.message || 'Gagal update status', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
    }
</script>

<!-- Close Sidebar Wrapper -->
</div>
</div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>