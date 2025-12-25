<?php
// auth/login.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/security.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check Student Login (Email)
    $stmt = $conn->prepare("SELECT id, email, password FROM akun_siswa WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'siswa';
            $_SESSION['user_email'] = $user['email'];
            header("Location: " . BASE_URL . "pendaftaran/index.php");
            exit();
        } else {
            $error = "Password yang Anda masukkan salah.";
        }
    } else {
        // Check Admin Login (Username)
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users_admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_username'] = $user['username'];
                header("Location: " . BASE_URL . "admin/dashboard.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah.";
            }
        } else {
            $error = "Akun tidak ditemukan. Pastikan email/username benar.";
        }
    }
}

$title = "Login Masuk";
include __DIR__ . '/../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo Display -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl shadow-xl shadow-primary/30 mb-4 text-white">
                <iconify-icon icon="solar:lock-password-bold-duotone" class="text-4xl"></iconify-icon>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight"><?= APP_NAME ?></h1>
            <p class="text-slate-500 font-semibold mt-2">Selamat datang kembali! Silakan login.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <div class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-xl flex items-center shadow-sm">
                        <iconify-icon icon="solar:danger-bold-duotone" class="text-2xl mr-3"></iconify-icon>
                        <p class="text-sm font-bold"><?= xss_clean($error) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Email (Siswa) / Username (Admin)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <iconify-icon icon="solar:user-bold-duotone" class="text-xl"></iconify-icon>
                            </span>
                            <input type="text" name="username" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="Masukkan login Anda" required autofocus>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <iconify-icon icon="solar:key-bold-duotone" class="text-xl"></iconify-icon>
                            </span>
                            <input type="password" name="password" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none transition-all font-medium text-slate-800" placeholder="********" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/30 transition-all active:scale-[0.98] transform flex items-center justify-center">
                        Masuk Sekarang
                        <iconify-icon icon="solar:login-bold-duotone" class="ml-2 text-2xl"></iconify-icon>
                    </button>

                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <span class="w-full border-t border-slate-100"></span>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-slate-400 font-bold uppercase tracking-widest text-[10px]">Atau</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-slate-500 font-bold text-sm">
                            Belum punya akun siswa?
                            <a href="<?= BASE_URL ?>auth/register.php" class="text-primary hover:underline ml-1">Daftar di sini</a>
                        </p>
                    </div>

                    <div class="text-center mt-6">
                        <a href="<?= BASE_URL ?>" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                            <iconify-icon icon="solar:alt-arrow-left-bold-duotone" class="mr-1"></iconify-icon>
                            Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// include __DIR__ . '/../templates/footer.php'; 
?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</body>

</html>