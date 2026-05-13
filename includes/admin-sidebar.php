<!-- Admin Sidebar — HANYA untuk admin, tidak ada link ke beranda -->
<aside class="admin-sidebar">
    <h3>Menu Utama</h3>
    <ul class="admin-menu">
        <li>
            <a href="admin-dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-home-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="admin-pengguna.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin-pengguna.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
        </li>
        <li>
            <a href="admin-layanan.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin-layanan.php') ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i>
                <span>Layanan</span>
            </a>
        </li>
        <li>
            <a href="admin-statistik.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin-statistik.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Statistik</span>
            </a>
        </li>
    </ul>

    <h3>Akun</h3>
    <ul class="admin-menu">
        <li>
            <a href="logout.php" onclick="return confirm('Yakin ingin logout dari panel admin?')"
               style="color:#ef4444;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
