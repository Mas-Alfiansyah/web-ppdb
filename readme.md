saya seorang sofware engginer di salah satu PT terbesar di indonesia yang sedang membuat aplikasi sekolah. Bertindak sebagai System Analyst dan menyusun rancangan aplikasi sekolah end-to-end dengan constraint yang saya tentukan:

PHP Native (procedural, non-OOP)

Tailwind CSS via CDN (mobile-first, desktop-ready)

MySQL

Target: aplikasi sekolah yang scalable, maintainable, dan realistis dipakai di PT besar

Saya susun seperti dokumen analisis + blueprint teknis, bukan sekadar contoh kode.

1. Tujuan & Scope Aplikasi Sekolah
   Tujuan

Menyediakan sistem terpusat untuk mengelola aktivitas sekolah secara digital, aman, dan efisien.

Modul Inti (Phase 1 – Wajib)

Auth & Role

Super Admin

Admin Sekolah

Guru

Siswa

Orang Tua (opsional)

Master Data

Tahun Ajaran

Kelas

Jurusan

Mata Pelajaran

Guru

Siswa

Akademik

Jadwal Pelajaran

Absensi

Nilai

Rapor

Administrasi

Pengumuman

Dokumen Sekolah

Profil Sekolah

Utility

Log aktivitas

Export (PDF / Excel)

Backup database (admin)

2. Arsitektur Aplikasi (PHP Native yang Rapi)

Karena tanpa OOP, kunci keberhasilan ada di:

Struktur folder

Pemisahan concern

Konvensi penamaan

📁 Struktur Direktori Final
/school-app
│
├── public/
│ ├── index.php
│ ├── login.php
│ ├── logout.php
│ └── assets/
│
├── app/
│ ├── config/
│ │ ├── database.php
│ │ ├── app.php
│ │ └── roles.php
│ │
│ ├── middleware/
│ │ ├── auth.php
│ │ └── role.php
│ │
│ ├── helpers/
│ │ ├── auth_helper.php
│ │ ├── csrf_helper.php
│ │ ├── date_helper.php
│ │ └── security_helper.php
│ │
│ ├── modules/
│ │ ├── dashboard/
│ │ ├── siswa/
│ │ ├── guru/
│ │ ├── kelas/
│ │ ├── mapel/
│ │ ├── nilai/
│ │ ├── absensi/
│ │ └── pengumuman/
│ │
│ └── views/
│ ├── layouts/
│ │ ├── header.php
│ │ ├── sidebar.php
│ │ └── footer.php
│ └── components/
│
├── storage/
│ ├── uploads/
│ ├── logs/
│ └── backups/
│
├── routes/
│ └── web.php
│
├── .htaccess
└── README.md

📌 Catatan Analyst
Struktur ini sudah “enterprise-grade” walaupun procedural.

3. Pola Routing (Tanpa Framework)
routes/web.php
<?php

$routes = [
'/' => 'dashboard/index.php',
'/siswa' => 'modules/siswa/index.php',
'/siswa/create' => 'modules/siswa/create.php',
'/siswa/store' => 'modules/siswa/store.php',
];

public/index.php

<?php
require_once '../app/config/app.php';
require_once '../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

if (isset($routes[$uri])) {
    require_once "../app/{$routes[$uri]}";
} else {
    http_response_code(404);
    echo "404 Not Found";
}

4. Database Design (MySQL – Normalized)
users
field	type
id	int PK
name	varchar
email	varchar
password	varchar
role	enum
status	tinyint
created_at	datetime
siswa
field	type
id	int PK
nis	varchar
nama	varchar
kelas_id	int FK
tanggal_lahir	date
guru

| id | nama | nip | mapel_id |

kelas

| id | nama | jurusan |

absensi

| id | siswa_id | tanggal | status |

nilai

| id | siswa_id | mapel_id | nilai |

📌 Best Practice

FK index wajib

Gunakan InnoDB

Semua tabel ada created_at, updated_at

5. Autentikasi & Middleware (Procedural Style)
middleware/auth.php
<?php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

middleware/role.php
<?php
function require_role($role)
{
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
}

6. Layout & Tailwind (Mobile First)
CDN
<script src="https://cdn.tailwindcss.com"></script>

Layout Principle

Mobile: sidebar hidden → hamburger

Desktop: sidebar fixed

Component reusable (table, button, modal)

Contoh Button Standard
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
    Simpan
</button>

7. CRUD Flow Standar (Siswa)

index.php → list

create.php → form

store.php → process

edit.php

update.php

delete.php

📌 Semua proses POST:

CSRF token

Sanitasi input

Redirect + flash message

8. Security Checklist (WAJIB)

✔ Password hashing (password_hash)
✔ Prepared statement (mysqli / PDO)
✔ CSRF token
✔ XSS escaping (htmlspecialchars)
✔ Upload validation (MIME + size)
✔ Session regenerate on login
