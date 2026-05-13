<?php
/**
 * includes/auth.php
 * Entry point tunggal untuk autentikasi dan koneksi database.
 * Digunakan oleh SEMUA halaman PHP di proyek ini.
 *
 * Path dari root      : require_once 'includes/auth.php';
 * Path dari subfolder : require_once '../../includes/auth.php';
 */

// ── Koneksi database via config/database.php ─────────────────────────────────
require_once __DIR__ . '/../config/database.php';

$db   = new Database();
$conn = $db->getConnection();

// Set charset dan handle error koneksi
if ($conn->connect_error) {
    $conn = null; // Mode fallback / demo
} else {
    $conn->set_charset('utf8mb4');
}

// ── Session ──────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── BASE URL ─────────────────────────────────────────────────────────────────
// Sesuaikan jika nama folder proyek Anda berbeda
if (!defined('BASE_URL')) {
    define('BASE_URL', '/project_wppl1hal_tes');
}

// ════════════════════════════════════════════════════════════════════════════
//  PENGGUNA AUTH
// ════════════════════════════════════════════════════════════════════════════

/**
 * Cek apakah pengguna sudah login.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['pengguna_id']);
}

/**
 * Ambil data pengguna yang sedang login dari session.
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) return null;
    return [
        'id'     => $_SESSION['pengguna_id'],
        'nama'   => $_SESSION['pengguna_nama'],
        'nim'    => $_SESSION['pengguna_nim'],
        'email'  => $_SESSION['pengguna_email']  ?? '',
        'status' => $_SESSION['pengguna_status'] ?? '',
    ];
}

/**
 * Paksa login — redirect ke login jika belum login.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/frontend/auth/login.php');
        exit;
    }
}

/**
 * Hancurkan sesi pengguna dan redirect ke halaman login.
 */
function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/frontend/auth/login.php');
    exit;
}

// ════════════════════════════════════════════════════════════════════════════
//  ADMIN AUTH
// ════════════════════════════════════════════════════════════════════════════

/**
 * Cek apakah admin sudah login.
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_id']);
}

/**
 * Ambil data admin yang sedang login dari session.
 */
function getCurrentAdmin(): ?array
{
    if (!isAdminLoggedIn()) return null;
    return [
        'id'   => $_SESSION['admin_id'],
        'nama' => $_SESSION['admin_nama'],
        'nip'  => $_SESSION['admin_nip'] ?? '',
    ];
}

/**
 * Paksa login admin — redirect ke login admin jika belum login.
 */
function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/frontend/admin/admin-login.php');
        exit;
    }
}

/**
 * Hancurkan sesi admin dan redirect ke halaman login admin.
 */
function adminLogout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/frontend/admin/admin-login.php');
    exit;
}
