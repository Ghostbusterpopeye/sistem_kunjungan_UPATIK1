<?php
/**
 * includes/navbar.php
 * Navbar dinamis — berubah sesuai status login pengguna.
 */
$_currentUser = function_exists('getCurrentUser') ? getCurrentUser() : null;
$_base = defined('BASE_URL') ? BASE_URL : '/project_wppl1hal_tes';
$_home = $_base . '/index.php';
?>
<nav class="navbar" id="mainNavbar">
  <div class="navbar-container">

    <!-- LOGO -->
    <a href="<?= $_home ?>" class="navbar-brand">
      Sistem Kunjungan <span>UPA TIK</span>
    </a>

    <!-- GRUP KANAN (Nav + Auth) -->
    <div class="nav-right-group">
      
      <!-- NAV LINKS -->
      <ul class="nav-links" id="navLinks">
        <li><a href="<?= $_home ?>">Beranda</a></li>
        <li><a href="<?= $_base ?>/frontend/pengguna/layanan.php">Layanan</a></li>
        <?php if ($_currentUser): ?>
          <li><a href="<?= $_base ?>/frontend/pengguna/riwayat.php">Riwayat</a></li>
        <?php endif; ?>
        <li><a href="<?= $_base ?>/frontend/pengguna/kontak.php">Kontak</a></li>
      </ul>

      <!-- AUTH / USER DROPDOWN -->
      <div class="nav-auth">
        <?php if ($_currentUser): ?>
          <div class="nav-user" id="navUser">
            <div class="nav-user-avatar">
              <?= mb_strtoupper(mb_substr($_currentUser['nama'], 0, 1)) ?>
            </div>
            <div class="nav-user-info">
              <div class="nav-user-name"><?= htmlspecialchars($_currentUser['nama']) ?></div>
              <div class="nav-user-email"><?= ucfirst($_currentUser['status']) ?></div>
            </div>

            <!-- Dropdown menu -->
            <div class="user-dropdown">
              <div class="user-dropdown-header">
                <strong><?= htmlspecialchars($_currentUser['nama']) ?></strong>
                <span><?= htmlspecialchars($_currentUser['nim'] ?? '-') ?> — <?= ucfirst($_currentUser['status']) ?></span>
              </div>
              <a href="<?= $_base ?>/frontend/pengguna/form-kunjungan.php" class="dropdown-item">📝 Buat Kunjungan</a>
              <a href="<?= $_base ?>/frontend/pengguna/riwayat.php" class="dropdown-item">📋 Riwayat</a>
              <a href="<?= $_base ?>/frontend/pengguna/profile.php" class="dropdown-item">⚙️ Profil</a>
              <a href="<?= $_base ?>/frontend/auth/logout.php" class="dropdown-item danger">🚪 Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= $_base ?>/frontend/auth/login.php" class="btn btn-primary nav-login-btn">
            🔐 Login
          </a>
        <?php endif; ?>
      </div>

      <!-- HAMBURGER (mobile) -->
      <div class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Menu">
        <span></span><span></span><span></span>
      </div>

    </div>
  </div>
</nav>