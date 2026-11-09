<?php
// ============================================================
// public/rooms/update.php — Proses update ruangan
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Ambil input
$id        = (int) ($_POST['id']        ?? 0);
$nama      = trim($_POST['nama']      ?? '');
$kapasitas = trim($_POST['kapasitas'] ?? '');
$lokasi    = trim($_POST['lokasi']    ?? '');
$fasilitas = trim($_POST['fasilitas'] ?? '');

if ($id <= 0) {
    set_flash('error', 'ID ruangan tidak valid.');
    redirect('index.php');
}

// Validasi (sama seperti store)
$errors = [];

if ($nama === '') {
    $errors['nama'] = 'Nama ruangan wajib diisi.';
} elseif (strlen($nama) > 100) {
    $errors['nama'] = 'Nama maksimal 100 karakter.';
}

if ($kapasitas === '') {
    $errors['kapasitas'] = 'Kapasitas wajib diisi.';
} elseif (!ctype_digit($kapasitas) || (int) $kapasitas < 1) {
    $errors['kapasitas'] = 'Kapasitas harus angka bulat ≥ 1.';
} elseif ((int) $kapasitas > 1000) {
    $errors['kapasitas'] = 'Kapasitas maksimal 1000.';
}

if ($lokasi === '') {
    $errors['lokasi'] = 'Lokasi wajib diisi.';
} elseif (strlen($lokasi) > 150) {
    $errors['lokasi'] = 'Lokasi maksimal 150 karakter.';
}

// Kalau error → balik ke form edit
if (!empty($errors)) {
    $_SESSION['old']    = [
        'nama'      => $nama,
        'kapasitas' => $kapasitas,
        'lokasi'    => $lokasi,
        'fasilitas' => $fasilitas,
    ];
    $_SESSION['errors'] = $errors;
    redirect("edit.php?id={$id}");
}

// UPDATE
$sql = "UPDATE rooms
        SET nama = ?, kapasitas = ?, lokasi = ?, fasilitas = ?
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nama,
    (int) $kapasitas,
    $lokasi,
    $fasilitas !== '' ? $fasilitas : null,
    $id,
]);

set_flash('success', "Ruangan \"{$nama}\" berhasil diupdate.");
redirect('index.php');