<?php
// ============================================================
// public/users/create.php — Form tambah user
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

$old    = $_SESSION['old']    ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

ob_start();
?>

<h1>Tambah User</h1>

<form action="store.php" method="post">
    <label for="nama">Nama</label>
    <input type="text" id="nama" name="nama"
           value="<?= e($old['nama'] ?? '') ?>" required maxlength="100">
    <?php if (isset($errors['nama'])): ?>
        <p class="error"><?= e($errors['nama']) ?></p>
    <?php endif; ?>

    <label for="email">Email</label>
    <input type="email" id="email" name="email"
           value="<?= e($old['email'] ?? '') ?>" required maxlength="150">
    <?php if (isset($errors['email'])): ?>
        <p class="error"><?= e($errors['email']) ?></p>
    <?php endif; ?>

    <label for="telepon">Telepon (opsional)</label>
    <input type="text" id="telepon" name="telepon"
           value="<?= e($old['telepon'] ?? '') ?>" maxlength="20">
    <?php if (isset($errors['telepon'])): ?>
        <p class="error"><?= e($errors['telepon']) ?></p>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';