<?php
// ============================================================
// public/users/edit.php — Form edit user
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID user tidak valid.');
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('error', 'User tidak ditemukan.');
    redirect('index.php');
}

$old    = $_SESSION['old']    ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

$val = fn(string $key) => e($old[$key] ?? $user[$key] ?? '');

ob_start();
?>

<h1>Edit User</h1>

<form action="update.php" method="post">
    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

    <label for="nama">Nama</label>
    <input type="text" id="nama" name="nama"
           value="<?= $val('nama') ?>" required maxlength="100">
    <?php if (isset($errors['nama'])): ?>
        <p class="error"><?= e($errors['nama']) ?></p>
    <?php endif; ?>

    <label for="email">Email</label>
    <input type="email" id="email" name="email"
           value="<?= $val('email') ?>" required maxlength="150">
    <?php if (isset($errors['email'])): ?>
        <p class="error"><?= e($errors['email']) ?></p>
    <?php endif; ?>

    <label for="telepon">Telepon (opsional)</label>
    <input type="text" id="telepon" name="telepon"
           value="<?= $val('telepon') ?>" maxlength="20">
    <?php if (isset($errors['telepon'])): ?>
        <p class="error"><?= e($errors['telepon']) ?></p>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';