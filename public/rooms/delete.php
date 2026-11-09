<?php
// ============================================================
// public/rooms/delete.php — Proses hapus ruangan
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID ruangan tidak valid.');
    redirect('index.php');
}

// Ambil nama dulu (untuk flash message)
$stmt = $pdo->prepare("SELECT nama FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if (!$room) {
    set_flash('error', 'Ruangan tidak ditemukan.');
    redirect('index.php');
}

// Coba hapus
try {
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->execute([$id]);

    set_flash('success', "Ruangan \"{$room['nama']}\" berhasil dihapus.");
} catch (PDOException $e) {
    // FK RESTRICT: ruangan masih dipakai booking
    if ($e->getCode() === '23000') {
        set_flash('error', "Ruangan \"{$room['nama']}\" tidak bisa dihapus karena masih dipakai oleh booking.");
    } else {
        set_flash('error', 'Gagal menghapus: ' . $e->getMessage());
    }
}

redirect('index.php');