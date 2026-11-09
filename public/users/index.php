<?php
// ============================================================
// public/users/index.php — Daftar user
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

$stmt = $pdo->query("SELECT * FROM users ORDER BY nama ASC");
$users = $stmt->fetchAll();

ob_start();
?>

<h1>Kelola User</h1>

<p>
    <a href="create.php" class="btn btn-primary">+ Tambah User</a>
</p>

<?php if (empty($users)): ?>
    <p><em>Belum ada user. Klik "Tambah User" untuk mulai.</em></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $i => $user): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($user['nama']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['telepon'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= (int) $user['id'] ?>" class="btn btn-secondary">Edit</a>

                        <form action="delete.php" method="post" style="display:inline"
                              onsubmit="return confirm('Hapus user ini?')">
                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
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