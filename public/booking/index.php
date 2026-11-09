<?php
// ============================================================
// public/bookings/index.php — Daftar booking
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

// JOIN: ambil nama ruangan & nama user sekaligus
$sql = "SELECT b.*, r.nama AS room_nama, u.nama AS user_nama
        FROM bookings b
        JOIN rooms r ON r.id = b.room_id
        JOIN users u ON u.id = b.user_id
        ORDER BY b.tanggal DESC, b.jam_mulai ASC";
$bookings = $pdo->query($sql)->fetchAll();

ob_start();
?>

<h1>Kelola Booking</h1>

<p>
    <a href="create.php" class="btn btn-primary">+ Tambah Booking</a>
</p>

<?php if (empty($bookings)): ?>
    <p><em>Belum ada booking. Klik "Tambah Booking" untuk mulai.</em></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Ruangan</th>
                <th>User</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $i => $b): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($b['tanggal']) ?></td>
                    <td><?= e(substr($b['jam_mulai'], 0, 5)) ?>–<?= e(substr($b['jam_selesai'], 0, 5)) ?></td>
                    <td><?= e($b['room_nama']) ?></td>
                    <td><?= e($b['user_nama']) ?></td>
                    <td><?= e($b['judul']) ?></td>
                    <td><?= e($b['status']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= (int) $b['id'] ?>" class="btn btn-secondary">Edit</a>
                        <form action="delete.php" method="post" style="display:inline"
                              onsubmit="return confirm('Hapus booking ini?')">
                            <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="../index.php">← Kembali ke Dashboard</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';