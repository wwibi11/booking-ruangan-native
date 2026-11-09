<?php
// ============================================================
// views/flash.php
// Partial: tampilkan flash message sekali, lalu hapus dari session
// Dipanggil dari layouts.php
// ============================================================

$flash = get_flash();   // dari helpers/functions.php — ambil + hapus
if ($flash):
?>
    <div class="flash flash-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
