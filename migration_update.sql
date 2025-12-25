-- Migration Update: Add New Upload Fields & Synchronization
-- Run this after database.sql
-- Date: 2025-12-25

-- Add new document upload fields to siswa table
ALTER TABLE siswa 
ADD COLUMN IF NOT EXISTS kip_path VARCHAR(255) DEFAULT NULL AFTER ijazah_status,
ADD COLUMN IF NOT EXISTS kip_status ENUM('pending','valid','invalid') DEFAULT 'pending' AFTER kip_path,
ADD COLUMN IF NOT EXISTS ktp_ayah_path VARCHAR(255) DEFAULT NULL AFTER kip_status,
ADD COLUMN IF NOT EXISTS ktp_ayah_status ENUM('pending','valid','invalid') DEFAULT 'pending' AFTER ktp_ayah_path,
ADD COLUMN IF NOT EXISTS ktp_ibu_path VARCHAR(255) DEFAULT NULL AFTER ktp_ayah_status,
ADD COLUMN IF NOT EXISTS ktp_ibu_status ENUM('pending','valid','invalid') DEFAULT 'pending' AFTER ktp_ibu_path,
ADD COLUMN IF NOT EXISTS foto_path VARCHAR(255) DEFAULT NULL AFTER ktp_ibu_status,
ADD COLUMN IF NOT EXISTS seen_announcement TINYINT(1) DEFAULT 0 AFTER foto_path;

-- Add indexes for better query performance
ALTER TABLE siswa 
ADD INDEX idx_status (status),
ADD INDEX idx_tahun_ajaran (tahun_ajaran_id);

-- Update existing records to have seen_announcement = 0 if NULL
UPDATE siswa SET seen_announcement = 0 WHERE seen_announcement IS NULL;

-- Show updated structure
DESCRIBE siswa;
