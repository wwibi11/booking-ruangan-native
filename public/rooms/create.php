<?php
// ============================================================
// public/rooms/create.php — Form tambah ruangan
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

// Ambil old input & error dari session (kalau ada)
$old    = $_SESSION['old']    ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

ob_start();
?>

<h1>Tambah Ruangan</h1>

<form action="store.php" method="post">
    <label for="nama">Nama Ruangan</label>
    <input type="text" id="nama" name="nama"
           value="<?= e($old['nama'] ?? '') ?>" required maxlength="100">
    <?php if (isset($errors['nama'])): ?>
        <p class="error"><?= e($errors['nama']) ?></p>
    <?php endif; ?>

    <label for="kapasitas">Kapasitas (orang)</label>
    <input type="number" id="kapasitas" name="kapasitas"
           value="<?= e($old['kapasitas'] ?? '') ?>" required min="1" max="1000">
    <?php if (isset($errors['kapasitas'])): ?>
        <p class="error"><?= e($errors['kapasitas']) ?></p>
    <?php endif; ?>

    <label for="lokasi">Lokasi</label>
    <input type="text" id="lokasi" name="lokasi"
           value="<?= e($old['lokasi'] ?? '') ?>" required maxlength="150">
    <?php if (isset($errors['lokasi'])): ?>
        <p class="error"><?= e($errors['lokasi']) ?></p>
    <?php endif; ?>

    <label for="fasilitas">Fasilitas (opsional)</label>
    <textarea id="fasilitas" name="fasilitas" rows="3"><?= e($old['fasilitas'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';