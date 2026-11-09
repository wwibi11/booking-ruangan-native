<?php
// index.php — halaman depan / dashboard
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../helpers/functions.php';

// Tes cepat koneksi & jumlah data
$jumlah_rooms    = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$jumlah_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$jumlah_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Ruangan — Dashboard</title>
</head>
<body>
    <h1>Aplikasi Booking Ruangan</h1>

    <p>✅ Koneksi database berhasil.</p>

    <ul>
        <li>Ruangan: <strong><?= e((string)$jumlah_rooms) ?></strong></li>
        <li>User: <strong><?= e((string)$jumlah_users) ?></strong></li>
        <li>Booking: <strong><?= e((string)$jumlah_bookings) ?></strong></li>
    </ul>

    <h2>Menu (akan dibuat bertahap)</h2>
    <ul>
        <li><a href="rooms/index.php">Kelola Ruangan</a></li>
        <li><a href="users/index.php">Kelola User</a></li>
        <li><a href="booking/index.php">Kelola Booking</a></li>
    </ul>
</body>
</html>