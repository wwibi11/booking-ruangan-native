<?php
// ============================================================
// helpers/functions.php
// Fungsi bantu: escape, redirect, flash message, validasi dasar
// ============================================================

// ---------- 1. Escape output (cegah XSS) ----------
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// ---------- 2. Redirect + stop eksekusi ----------
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

// ---------- 3. Flash message (pesan sekali tampil) ----------
// Pakai session. Set di satu request, tampil di request berikutnya, lalu dihapus.
function set_flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);   // hapus setelah dibaca
        return $flash;
    }
    return null;
}

// ---------- 4. Ambil input POST/GET dengan aman ----------
function input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

// ---------- 5. Validasi: tanggal tidak boleh masa lalu ----------
function validate_tanggal_tidak_masa_lalu(string $tanggal): ?string
{
    // strtotime('2024-01-01') → timestamp. Bandingkan dengan hari ini (00:00).
    if (strtotime($tanggal) === false) {
        return 'Format tanggal tidak valid.';
    }
    if (strtotime($tanggal) < strtotime(date('Y-m-d'))) {
        return 'Tanggal tidak boleh di masa lalu.';
    }
    return null;
}

// ---------- 6. Validasi: jam selesai harus setelah jam mulai ----------
function validate_jam_selesai_setelah_mulai(string $jam_mulai, string $jam_selesai): ?string
{
    if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
        return 'Jam selesai harus setelah jam mulai.';
    }
    return null;
}