<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - UPA TIK POLIJE' : 'UPA TIK POLIJE'; ?></title>

    <!-- Fonts (sama dengan halaman pengguna) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin stylesheet (warna identik dengan halaman pengguna) -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>
    <!-- ── Admin Header ──────────────────────────────────────────────────── -->
    <header class="admin-header">

        <!-- Logo / brand — BUKAN link ke beranda (admin terisolasi) -->
        <div class="admin-logo">
            <div class="admin-logo-text">UPA TIK <span>Admin</span></div>
        </div>

        <!-- Nav kanan: judul halaman + info user + logout -->
        <nav class="admin-nav">
            <div class="admin-header-title">
                <?php echo isset($title) ? htmlspecialchars($title) : 'Dashboard'; ?>
            </div>

            <div class="admin-user-section">
                <?php
                $adminNama = isset($_SESSION['admin_nama']) ? $_SESSION['admin_nama'] : 'Admin';
                $adminInit = strtoupper(mb_substr($adminNama, 0, 1));
                ?>
                <div class="admin-user-widget">
                    <div class="admin-user-info">
                        <div class="admin-user-name"><?= htmlspecialchars($adminNama) ?></div>      <!--nama admin-->
                        <div class="admin-user-role">       <!--role admin-->
                            <span class="admin-role-dot"></span>
                            Admin UPA TIK
                        </div>
                    </div>
                    <div class="admin-avatar-circle"><?= $adminInit ?></div>     <!--avatar admin-->
                </div>
            </div>
        </nav>
    </header>
