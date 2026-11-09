<?php
// ============================================================
// config/database.php
// Koneksi PDO tunggal, di-include di semua file yang butuh DB
// ============================================================

// --- Konfigurasi (nanti bisa dipindah ke .env) ---
$DB_HOST = 'localhost';
$DB_NAME = 'booking_ruangan';
$DB_USER = 'root';
$DB_PASS = '';          // Laragon default: kosong
$DB_CHARSET = 'utf8mb4';

// --- DSN (Data Source Name) ---
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

// --- Opsi PDO ---
$options = [
    // Lempar exception kalau query error (jangan diam-diam false)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // Hasil fetch default: array asosiatif
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Matikan emulasi prepared statement → pakai prepared statement asli MySQL
    // Lebih aman & lebih cepat untuk query berulang
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Buat koneksi ---
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Jangan tampilkan detail error ke user di production!
    // Untuk development, tampilkan supaya cepat debug.
    die('Koneksi database gagal: ' . $e->getMessage());
}