-- ============================================================
-- database.sql
-- Skrip setup untuk aplikasi booking-ruangan-native
-- Jalankan di HeidiSQL / phpMyAdmin / CLI MySQL
-- ============================================================

-- 1. Buat database (kalau belum ada)
CREATE DATABASE IF NOT EXISTS booking_ruangan
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE booking_ruangan;

-- 2. Hapus tabel lama kalau ada (urutan penting: child dulu)
--    Supaya skrip ini bisa dijalankan ulang (idempotent)
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS rooms;

-- ============================================================
-- TABEL: rooms
-- ============================================================
CREATE TABLE rooms (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100)    NOT NULL,
    kapasitas   INT UNSIGNED    NOT NULL,
    lokasi      VARCHAR(150)    NOT NULL,
    fasilitas   TEXT            NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: users
-- ============================================================
CREATE TABLE users (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL,
    telepon     VARCHAR(20)     NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: bookings
-- ============================================================
CREATE TABLE bookings (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_id      BIGINT UNSIGNED NOT NULL,
    user_id      BIGINT UNSIGNED NOT NULL,
    judul        VARCHAR(150)    NOT NULL,
    deskripsi    TEXT            NULL,
    tanggal      DATE            NOT NULL,
    jam_mulai    TIME            NOT NULL,
    jam_selesai  TIME            NOT NULL,
    status       ENUM('pending','dikonfirmasi','dibatalkan','selesai')
                                 NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),

    -- Index komposit untuk cek konflik (query selalu filter room_id + tanggal)
    KEY idx_room_date (room_id, tanggal),

    -- Index untuk filter "booking per user"
    KEY idx_user (user_id),

    -- Foreign key: RESTRICT = tolak hapus kalau masih dipakai
    CONSTRAINT fk_bookings_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_bookings_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA (data contoh supaya langsung bisa dites)
-- ============================================================

INSERT INTO rooms (nama, kapasitas, lokasi, fasilitas) VALUES
('Ruang Meeting A', 10, 'Lantai 2', 'Proyektor, Whiteboard, AC'),
('Ruang Meeting B', 6,  'Lantai 2', 'TV, Whiteboard, AC'),
('Aula Utama',     50, 'Lantai 1', 'Sound system, Proyektor, Panggung'),
('Ruang Diskusi Kecil', 4, 'Lantai 3', 'Whiteboard, AC');

INSERT INTO users (nama, email, telepon) VALUES
('Budi Santoso',  'budi@example.com',  '081234567890'),
('Siti Aminah',   'siti@example.com',  '081298765432'),
('Andi Pratama',  'andi@example.com',  '081377788899');

-- Booking contoh (besok, supaya tidak kena validasi "masa lalu")
INSERT INTO bookings (room_id, user_id, judul, deskripsi, tanggal, jam_mulai, jam_selesai, status) VALUES
(1, 1, 'Meeting Proyek X',    'Kickoff proyek X',  CURDATE() + INTERVAL 1 DAY, '09:00:00', '11:00:00', 'dikonfirmasi'),
(2, 2, 'Review Desain',       NULL,                CURDATE() + INTERVAL 1 DAY, '13:00:00', '14:30:00', 'pending'),
(1, 3, 'Diskusi Anggaran',    'Q3 budget',         CURDATE() + INTERVAL 2 DAY, '10:00:00', '12:00:00', 'dikonfirmasi');

SELECT 'Setup selesai!' AS pesan,
       (SELECT COUNT(*) FROM rooms)    AS jumlah_rooms,
       (SELECT COUNT(*) FROM users)    AS jumlah_users,
       (SELECT COUNT(*) FROM bookings) AS jumlah_bookings;