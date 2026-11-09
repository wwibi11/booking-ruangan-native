<?php
// ============================================================
// public/bookings/update.php — Proses update booking
// TANPA cek konflik & TANPA validasi tanggal (sengaja — nanti)
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id          = (int) ($_POST['id']          ?? 0);
$room_id     = (int) ($_POST['room_id']     ?? 0);
$user_id     = (int) ($_POST['user_id']     ?? 0);
$judul       = trim($_POST['judul']       ?? '');
$deskripsi   = trim($_POST['deskripsi']   ?? '');
$tanggal     = trim($_POST['tanggal']     ?? '');
$jam_mulai   = trim($_POST['jam_mulai']   ?? '');
$jam_selesai = trim($_POST['jam_selesai'] ?? '');
$status      = trim($_POST['status']      ?? 'pending');

if ($id <= 0) {
    set_flash('error', 'ID booking tidak valid.');
    redirect('index.php');
}

// Validasi (sama seperti store)
$errors = [];

if ($room_id <= 0) {
    $errors['room_id'] = 'Ruangan wajib dipilih.';
}

if ($user_id <= 0) {
    $errors['user_id'] = 'User wajib dipilih.';
}

if ($judul === '') {
    $errors['judul'] = 'Judul wajib diisi.';
} elseif (strlen($judul) > 150) {
    $errors['judul'] = 'Judul maksimal 150 karakter.';
}

if ($tanggal === '') {
    $errors['tanggal'] = 'Tanggal wajib diisi.';
}

if ($jam_mulai === '') {
    $errors['jam_mulai'] = 'Jam mulai wajib diisi.';
}

if ($jam_selesai === '') {
    $errors['jam_selesai'] = 'Jam selesai wajib diisi.';
}

if ($jam_mulai !== '' && $jam_selesai !== '') {
    if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
        $errors['jam_selesai'] = 'Jam selesai harus setelah jam mulai.';
    }
}

if (!in_array($status, ['pending', 'dikonfirmasi', 'dibatalkan', 'selesai'], true)) {
    $errors['status'] = 'Status tidak valid.';
}

if (!empty($errors)) {
    $_SESSION['old'] = compact(
        'room_id', 'user_id', 'judul', 'deskripsi',
        'tanggal', 'jam_mulai', 'jam_selesai', 'status'
    );
    $_SESSION['errors'] = $errors;
    redirect("edit.php?id={$id}");
}

// UPDATE
$sql = "UPDATE bookings
        SET room_id = ?, user_id = ?, judul = ?, deskripsi = ?,
            tanggal = ?, jam_mulai = ?, jam_selesai = ?, status = ?
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $room_id,
    $user_id,
    $judul,
    $deskripsi !== '' ? $deskripsi : null,
    $tanggal,
    $jam_mulai,
    $jam_selesai,
    $status,
    $id,
]);

set_flash('success', "Booking \"{$judul}\" berhasil diupdate.");
redirect('index.php');