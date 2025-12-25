<?php
// auth/register.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/security.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nisn = trim($_POST['nisn']);

    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $check_email = $conn->prepare("SELECT id FROM akun_siswa WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();

        if ($check_email->get_result()->num_rows > 0) {
            $error = "Alamat email ini sudah terdaftar!";
        } else {
            $conn->begin_transaction();
            try {
                // Get Active Tahun Ajaran
                $stmt_ta = $conn->query("SELECT id FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
                $ta = $stmt_ta->fetch_assoc();
                $ta_id = $ta['id'] ?? null;

                $stmt_siswa = $conn->prepare("INSERT INTO siswa (nama_lengkap, nisn, tahun_ajaran_id) VALUES (?, ?, ?)");
                $stmt_siswa->bind_param("ssi", $nama_lengkap, $nisn, $ta_id);
                if (!$stmt_siswa->execute()) throw new Exception("Error Siswa: " . $stmt_siswa->error);

                $siswa_id = $conn->insert_id;

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_akun = $conn->prepare("INSERT INTO akun_siswa (siswa_id, email, password) VALUES (?, ?, ?)");
                $stmt_akun->bind_param("iss", $siswa_id, $email, $hashed_password);
                if (!$stmt_akun->execute()) throw new Exception("Error Akun: " . $stmt_akun->error);

                $conn->commit();
                $success = "Registrasi Anda Berhasil! Silakan masuk.";
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.";
            }
        }
    }
}

$title = "Registrasi Akun";
include __DIR__ . '/../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full">
        <!-- Logo Display -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-success rounded-2xl shadow-xl shadow-success/30 mb-4 text-white">
                <iconify-icon icon="solar:user-plus-bold-duotone" class="text-4xl"></iconify-icon>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-slate-500 font-semibold mt-2">Buat akun untuk memulai pendaftaran PPDB.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden transition-all duration-500">
            <div class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-xl flex items-center shadow-sm">
                        <iconify-icon icon="solar:danger-bold-duotone" class="text-2xl mr-3"></iconify-icon>
                        <p class="text-sm font-bold"><?= xss_clean($error) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="text-6xl"></iconify-icon>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 mb-2">Pendaftaran Berhasil!</h2>
                        <p class="text-slate-500 font-bold mb-8"><?= xss_clean($success) ?></p>
                        <a href="<?= BASE_URL ?>auth/login.php" class="inline-flex items-center justify-center bg-primary hover:bg-secondary text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-primary/30 transition-all active:scale-[0.98]">
                            Ke Halaman Login
                            <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ml-2 text-2xl"></iconify-icon>
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="" class="space-y-6">
                        <?= csrf_field() ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <iconify-icon icon="solar:user-id-bold-duotone" class="text-xl"></iconify-icon>
                                    </span>
                                    <input type="text" name="nama_lengkap" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="Nama Anda" required value="<?= isset($_POST['nama_lengkap']) ? xss_clean($_POST['nama_lengkap']) : '' ?>">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">NISN</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <iconify-icon icon="solar:card-2-bold-duotone" class="text-xl"></iconify-icon>
                                    </span>
                                    <input type="text" name="nisn" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="10 digit NISN" required value="<?= isset($_POST['nisn']) ? xss_clean($_POST['nisn']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Alamat Email Aktif</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <iconify-icon icon="solar:letter-bold-duotone" class="text-xl"></iconify-icon>
                                </span>
                                <input type="email" name="email" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="email@contoh.com" required value="<?= isset($_POST['email']) ? xss_clean($_POST['email']) : '' ?>">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <iconify-icon icon="solar:key-bold-duotone" class="text-xl"></iconify-icon>
                                    </span>
                                    <input type="password" name="password" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="Buat password" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Konfirmasi Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <iconify-icon icon="solar:shield-keyhole-bold-duotone" class="text-xl"></iconify-icon>
                                    </span>
                                    <input type="password" name="confirm_password" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="Ulangi password" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/30 transition-all active:scale-[0.98] transform flex items-center justify-center">
                            Daftarkan Akun
                            <iconify-icon icon="solar:check-read-bold-duotone" class="ml-2 text-2xl"></iconify-icon>
                        </button>

                        <div class="text-center pt-4">
                            <p class="text-slate-500 font-bold text-sm">
                                Sudah memiliki akun siswa?
                                <a href="<?= BASE_URL ?>auth/login.php" class="text-primary hover:underline ml-1">Masuk sekarang</a>
                            </p>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>