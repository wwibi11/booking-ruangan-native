<?php
// ============================================================
// public/rooms/edit.php — Form edit ruangan
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

// Ambil ID dari query string
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID ruangan tidak valid.');
    redirect('index.php');
}

// Ambil data ruangan dari DB
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if (!$room) {
    set_flash('error', 'Ruangan tidak ditemukan.');
    redirect('index.php');
}

// Old input (kalau update gagal) — fallback ke data DB
$old    = $_SESSION['old']    ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

// Helper: ambil nilai — prioritas old input > data DB > kosong
$val = fn(string $key) => e($old[$key] ?? $room[$key] ?? '');

ob_start();
?>

<h1>Edit Ruangan</h1>

<form action="update.php" method="post">
    <input type="hidden" name="id" value="<?= (int) $room['id'] ?>">

    <label for="nama">Nama Ruangan</label>
    <input type="text" id="nama" name="nama"
           value="<?= $val('nama') ?>" required maxlength="100">
    <?php if (isset($errors['nama'])): ?>
        <p class="error"><?= e($errors['nama']) ?></p>
    <?php endif; ?>

    <label for="kapasitas">Kapasitas (orang)</label>
    <input type="number" id="kapasitas" name="kapasitas"
           value="<?= $val('kapasitas') ?>" required min="1" max="1000">
    <?php if (isset($errors['kapasitas'])): ?>
        <p class="error"><?= e($errors['kapasitas']) ?></p>
    <?php endif; ?>

    <label for="lokasi">Lokasi</label>
    <input type="text" id="lokasi" name="lokasi"
           value="<?= $val('lokasi') ?>" required maxlength="150">
    <?php if (isset($errors['lokasi'])): ?>
        <p class="error"><?= e($errors['lokasi']) ?></p>
    <?php endif; ?>

    <label for="fasilitas">Fasilitas (opsional)</label>
    <textarea id="fasilitas" name="fasilitas" rows="3"><?= $val('fasilitas') ?></textarea>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';