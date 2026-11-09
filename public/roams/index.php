<?php
// ============================================================
// public/rooms/index.php — Daftar ruangan
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

// Ambil semua ruangan, urut nama
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY nama ASC");
$rooms = $stmt->fetchAll();

// Mulai output buffer untuk layout
ob_start();
?>

<h1>Kelola Ruangan</h1>

<p>
    <a href="create.php" class="btn btn-primary">+ Tambah Ruangan</a>
</p>

<?php if (empty($rooms)): ?>
    <p><em>Belum ada ruangan. Klik "Tambah Ruangan" untuk mulai.</em></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Kapasitas</th>
                <th>Lokasi</th>
                <th>Fasilitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $i => $room): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($room['nama']) ?></td>
                    <td><?= (int) $room['kapasitas'] ?> orang</td>
                    <td><?= e($room['lokasi']) ?></td>
                    <td><?= e($room['fasilitas'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= (int) $room['id'] ?>" class="btn btn-secondary">Edit</a>

                        <form action="delete.php" method="post" style="display:inline"
                              onsubmit="return confirm('Hapus ruangan ini?')">
                            <input type="hidden" name="id" value="<?= (int) $room['id'] ?>">
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
// Layout
require __DIR__ . '/../../views/layouts.php';