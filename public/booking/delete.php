<?php
// ============================================================
// public/bookings/delete.php — Proses hapus booking
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID booking tidak valid.');
    redirect('index.php');
}

// Ambil judul dulu (untuk flash)
$stmt = $pdo->prepare("SELECT judul FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking tidak ditemukan.');
    redirect('index.php');
}

$stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', "Booking \"{$booking['judul']}\" berhasil dihapus.");
redirect('index.php');