-- Migration SQL
-- 1. Remove Jalur
ALTER TABLE siswa DROP FOREIGN KEY IF EXISTS siswa_ibfk_1; -- Just in case there was a FK name
-- If no FK name known, we just drop columns
ALTER TABLE siswa DROP COLUMN IF EXISTS jalur_id;
DROP TABLE IF EXISTS jalur;

-- 2. Create Tahun Ajaran Table
CREATE TABLE IF NOT EXISTS tahun_ajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun VARCHAR(20) NOT NULL, -- e.g. 2024/2025
    status ENUM('aktif', 'non-aktif') DEFAULT 'non-aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default TA if empty
INSERT INTO tahun_ajaran (tahun, status) VALUES ('2024/2025', 'aktif');

-- 3. Create Pengaturan Table (General Settings)
CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunci VARCHAR(50) UNIQUE NOT NULL,
    nilai TEXT,
    keterangan VARCHAR(255)
);

-- Insert Default Settings
INSERT INTO pengaturan (kunci, nilai, keterangan) VALUES 
('kuota_pendaftaran', '100', 'Total kuota penerimaan siswa'),
('tgl_mulai', '2025-01-01', 'Tanggal mulai pendaftaran'),
('tgl_selesai', '2025-07-31', 'Tanggal selesai pendaftaran'),
('nama_sekolah', 'SMK Negeri Contoh', 'Nama instansi pendidikan');

-- 4. Update siswa to include tahun_ajaran_id (to link student to a specific year)
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS tahun_ajaran_id INT AFTER no_pendaftaran;
-- Set default TA id for existing students if any
UPDATE siswa SET tahun_ajaran_id = (SELECT id FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1) WHERE tahun_ajaran_id IS NULL;
