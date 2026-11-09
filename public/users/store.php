<?php
// ============================================================
// public/users/store.php — Proses simpan user baru
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$nama    = trim($_POST['nama']    ?? '');
$email   = trim($_POST['email']   ?? '');
$telepon = trim($_POST['telepon'] ?? '');

$errors = [];

if ($nama === '') {
    $errors['nama'] = 'Nama wajib diisi.';
} elseif (strlen($nama) > 100) {
    $errors['nama'] = 'Nama maksimal 100 karakter.';
}

if ($email === '') {
    $errors['email'] = 'Email wajib diisi.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Format email tidak valid.';
} elseif (strlen($email) > 150) {
    $errors['email'] = 'Email maksimal 150 karakter.';
} else {
    // Cek unik — hanya kalau format valid
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors['email'] = 'Email sudah dipakai user lain.';
    }
}

if ($telepon !== '' && strlen($telepon) > 20) {
    $errors['telepon'] = 'Telepon maksimal 20 karakter.';
}

if (!empty($errors)) {
    $_SESSION['old']    = compact('nama', 'email', 'telepon');
    $_SESSION['errors'] = $errors;
    redirect('create.php');
}

$sql = "INSERT INTO users (nama, email, telepon) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nama,
    $email,
    $telepon !== '' ? $telepon : null,
]);

set_flash('success', "User \"{$nama}\" berhasil ditambahkan.");
redirect('index.php');