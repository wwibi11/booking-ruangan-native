<?php
// ============================================================
// public/bookings/edit.php — Form edit booking
// ============================================================

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../helpers/functions.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID booking tidak valid.');
    redirect('index.php');
}

// Ambil data booking
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking tidak ditemukan.');
    redirect('index.php');
}

// Data untuk dropdown
$rooms = $pdo->query("SELECT id, nama FROM rooms ORDER BY nama")->fetchAll();
$users = $pdo->query("SELECT id, nama FROM users ORDER BY nama")->fetchAll();

// Old input & errors
$old    = $_SESSION['old']    ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

// Helper: prioritas old > data DB > default
$val = fn(string $key, $default = '') => e($old[$key] ?? $booking[$key] ?? $default);

ob_start();
?>

<h1>Edit Booking</h1>

<form action="update.php" method="post">
    <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">

    <label for="room_id">Ruangan</label>
    <select id="room_id" name="room_id" required>
        <option value="">-- Pilih Ruangan --</option>
        <?php foreach ($rooms as $r): ?>
            <option value="<?= (int) $r['id'] ?>"
                <?= (($old['room_id'] ?? $booking['room_id']) == $r['id']) ? 'selected' : '' ?>>
                <?= e($r['nama']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['room_id'])): ?>
        <p class="error"><?= e($errors['room_id']) ?></p>
    <?php endif; ?>

    <label for="user_id">User</label>
    <select id="user_id" name="user_id" required>
        <option value="">-- Pilih User --</option>
        <?php foreach ($users as $u): ?>
            <option value="<?= (int) $u['id'] ?>"
                <?= (($old['user_id'] ?? $booking['user_id']) == $u['id']) ? 'selected' : '' ?>>
                <?= e($u['nama']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['user_id'])): ?>
        <p class="error"><?= e($errors['user_id']) ?></p>
    <?php endif; ?>

    <label for="judul">Judul</label>
    <input type="text" id="judul" name="judul"
           value="<?= $val('judul') ?>" required maxlength="150">
    <?php if (isset($errors['judul'])): ?>
        <p class="error"><?= e($errors['judul']) ?></p>
    <?php endif; ?>

    <label for="deskripsi">Deskripsi (opsional)</label>
    <textarea id="deskripsi" name="deskripsi" rows="3"><?= $val('deskripsi') ?></textarea>

    <label for="tanggal">Tanggal</label>
    <input type="date" id="tanggal" name="tanggal"
           value="<?= $val('tanggal') ?>" required>
    <?php if (isset($errors['tanggal'])): ?>
        <p class="error"><?= e($errors['tanggal']) ?></p>
    <?php endif; ?>

    <label for="jam_mulai">Jam Mulai</label>
    <input type="time" id="jam_mulai" name="jam_mulai"
           value="<?= e(substr($old['jam_mulai'] ?? $booking['jam_mulai'], 0, 5)) ?>" required>
    <?php if (isset($errors['jam_mulai'])): ?>
        <p class="error"><?= e($errors['jam_mulai']) ?></p>
    <?php endif; ?>

    <label for="jam_selesai">Jam Selesai</label>
    <input type="time" id="jam_selesai" name="jam_selesai"
           value="<?= e(substr($old['jam_selesai'] ?? $booking['jam_selesai'], 0, 5)) ?>" required>
    <?php if (isset($errors['jam_selesai'])): ?>
        <p class="error"><?= e($errors['jam_selesai']) ?></p>
    <?php endif; ?>

    <label for="status">Status</label>
    <select id="status" name="status" required>
        <?php foreach (['pending', 'dikonfirmasi', 'dibatalkan', 'selesai'] as $s): ?>
            <option value="<?= $s ?>"
                <?= (($old['status'] ?? $booking['status']) === $s) ? 'selected' : '' ?>>
                <?= ucfirst($s) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['status'])): ?>
        <p class="error"><?= e($errors['status']) ?></p>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts.php';