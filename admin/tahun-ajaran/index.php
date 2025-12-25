<?php
// admin/tahun-ajaran/index.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';

require_admin();

// Handle Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die("Invalid CSRF");

    $tahun = $_POST['tahun'];
    $status = $_POST['status'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $conn->prepare("UPDATE tahun_ajaran SET tahun = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $tahun, $status, $_POST['id']);
    } else {
        // Create
        $stmt = $conn->prepare("INSERT INTO tahun_ajaran (tahun, status) VALUES (?, ?)");
        $stmt->bind_param("ss", $tahun, $status);
    }

    // Jika set aktif, matikan yang lain
    if ($status === 'aktif') {
        $conn->query("UPDATE tahun_ajaran SET status = 'nonaktif' WHERE status = 'aktif'");
    }

    $stmt->execute();
    $success = "Data tahun ajaran berhasil disimpan.";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tahun_ajaran WHERE id = $id");
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM tahun_ajaran ORDER BY tahun DESC");

$title = "Manajemen Tahun Ajaran";
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar_admin.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Tahun Ajaran</h1>
            <p class="text-slate-500 font-semibold mt-1">Kelola periode pendaftaran aktif.</p>
        </div>
        <button onclick="openModal()" class="bg-primary hover:bg-secondary text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-primary/30 transition-all flex items-center active:scale-95 text-sm">
            <iconify-icon icon="solar:add-circle-bold-duotone" class="mr-2 text-2xl"></iconify-icon>
            Tambah Tahun
        </button>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-6 font-black text-slate-800"><?= $row['tahun'] ?></td>
                            <td class="px-6 py-6">
                                <span class="<?= $row['status'] == 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?> px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-6 text-right space-x-2">
                                <button onclick='editData(<?= json_encode($row) ?>)' class="inline-flex items-center text-primary font-bold hover:underline">Edit</button>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus?')" class="inline-flex items-center text-rose-500 font-bold hover:underline">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modalTA" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-in zoom-in duration-300">
        <form method="POST" class="p-10 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="ta_id">
            <h2 class="text-2xl font-black text-slate-900 mb-8" id="modalTitle">Tambah Tahun Ajaran</h2>

            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Tahun (Contoh: 2024/2025)</label>
                <input type="text" name="tahun" id="ta_tahun" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Status</label>
                <select name="status" id="ta_status" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-primary/10">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="button" onclick="closeModal()" class="flex-1 bg-slate-100 font-black py-4 rounded-2xl">Batal</button>
                <button type="submit" class="flex-1 bg-primary text-white font-black py-4 rounded-2xl shadow-lg shadow-primary/30">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTA').classList.remove('hidden');
        document.getElementById('modalTA').classList.add('flex');
        document.getElementById('modalTitle').innerText = 'Tambah Tahun Ajaran';
        document.getElementById('ta_id').value = '';
    }

    function closeModal() {
        document.getElementById('modalTA').classList.add('hidden');
        document.getElementById('modalTA').classList.remove('flex');
    }

    function editData(data) {
        openModal();
        document.getElementById('modalTitle').innerText = 'Edit Tahun Ajaran';
        document.getElementById('ta_id').value = data.id;
        document.getElementById('ta_tahun').value = data.tahun;
        document.getElementById('ta_status').value = data.status;
    }
</script>

<!-- Close Sidebar Wrapper -->
</div>
</div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>