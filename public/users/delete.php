<?php
// ============================================================
// public/users/delete.php — Proses hapus user
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID user tidak valid.');
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT nama FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('error', 'User tidak ditemukan.');
    redirect('index.php');
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    set_flash('success', "User \"{$user['nama']}\" berhasil dihapus.");
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        set_flash('error', "User \"{$user['nama']}\" tidak bisa dihapus karena masih punya booking.");
    } else {
        set_flash('error', 'Gagal menghapus: ' . $e->getMessage());
    }
}

redirect('index.php');