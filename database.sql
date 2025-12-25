-- Database Creation Script for PPDB Online
-- Run this script in your MySQL Database (e.g. phpMyAdmin)

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users Admin Table
DROP TABLE IF EXISTS `users_admin`;
CREATE TABLE `users_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','panitia') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin: admin / password
INSERT INTO `users_admin` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


-- 2. Tahun Ajaran Table
DROP TABLE IF EXISTS `tahun_ajaran`;
CREATE TABLE `tahun_ajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun` varchar(20) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Tahun Ajaran
INSERT INTO `tahun_ajaran` (`tahun`, `status`) VALUES
('2025/2026', 'aktif');


-- 3. Pengaturan Table
DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Settings
INSERT INTO `pengaturan` (`kunci`, `nilai`, `keterangan`) VALUES
('nama_sekolah', 'SMK Negeri 1 Contoh', 'Nama instansi pendidikan'),
('kuota_pendaftaran', '200', 'Total kuota penerimaan siswa'),
('tgl_mulai', '2025-01-01', 'Tanggal mulai pendaftaran'),
('tgl_selesai', '2025-08-31', 'Tanggal selesai pendaftaran');


-- 4. Siswa Table
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `no_pendaftaran` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `jk` enum('L','P') DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('pending','lulus','tidak_lulus','cadangan') NOT NULL DEFAULT 'pending',
  `akte_path` varchar(255) DEFAULT NULL,
  `akte_status` enum('pending','valid','invalid') NOT NULL DEFAULT 'pending',
  `kk_path` varchar(255) DEFAULT NULL,
  `kk_status` enum('pending','valid','invalid') NOT NULL DEFAULT 'pending',
  `ijazah_path` varchar(255) DEFAULT NULL,
  `ijazah_status` enum('pending','valid','invalid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nisn` (`nisn`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 5. Akun Siswa Table
DROP TABLE IF EXISTS `akun_siswa`;
CREATE TABLE `akun_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `siswa_id` (`siswa_id`),
  CONSTRAINT `akun_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
