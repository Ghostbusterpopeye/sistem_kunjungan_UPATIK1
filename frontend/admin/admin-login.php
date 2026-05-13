<?php
/**
 * frontend/admin/admin-login.php
 * Redirect ke halaman login terpadu (dengan tab Admin)
 */
require_once '../../includes/auth.php';

// Jika sudah login sebagai admin, langsung ke dashboard
if (isAdminLoggedIn()) {
    header('Location: admin-dashboard.php');
    exit;
}

// Redirect ke halaman login terpadu dengan tab admin aktif
header('Location: ' . BASE_URL . '/frontend/auth/login.php?mode=admin');
exit;
