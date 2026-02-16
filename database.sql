-- =====================================================
-- DATABASE AKADEMIK - Script Pembuatan Database
-- =====================================================

-- Buat Database
CREATE DATABASE IF NOT EXISTS db_akademik;
USE db_akademik;

-- =====================================================
-- TABEL USERS (Pengguna)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL MATA KULIAH (Jadwal Kuliah)
-- =====================================================
CREATE TABLE IF NOT EXISTS matakuliah (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    hari VARCHAR(20) NOT NULL, -- Senin, Selasa, dst
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    ruang VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL TUGAS (Pekerjaan Rumah & Assignment)
-- =====================================================
CREATE TABLE IF NOT EXISTS tugas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    matakuliah_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    deadline DATE NOT NULL,
    status ENUM('Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai', 'Terlambat') DEFAULT 'Belum Dikerjakan',
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (matakuliah_id) REFERENCES matakuliah(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL MATERI (Bahan Ajar)
-- =====================================================
CREATE TABLE IF NOT EXISTS materi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    matakuliah_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (matakuliah_id) REFERENCES matakuliah(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INDEX untuk Performa Query
-- =====================================================
CREATE INDEX idx_user_id ON matakuliah(user_id);
CREATE INDEX idx_user_id_tugas ON tugas(user_id);
CREATE INDEX idx_matakuliah_id_tugas ON tugas(matakuliah_id);
CREATE INDEX idx_user_id_materi ON materi(user_id);
CREATE INDEX idx_matakuliah_id_materi ON materi(matakuliah_id);
