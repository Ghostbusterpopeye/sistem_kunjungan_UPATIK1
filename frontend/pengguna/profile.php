<?php
require_once '../../includes/auth.php';
requireLogin();
$user = getCurrentUser();

$error   = '';
$success = '';

// Hanya update nama (hapus fitur ganti password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');

    if (empty($nama)) {
        $error = 'Nama tidak boleh kosong.';
    } else {
        if ($conn) {
            $upd = $conn->prepare(
                "UPDATE akun_pengguna SET nama_lengkap = ? WHERE id_pengguna = ?"
            );
            $upd->bind_param('si', $nama, $user['id']);
            $upd->execute();
            $upd->close();
            $_SESSION['pengguna_nama'] = $nama;
            $success = 'Profil berhasil diperbarui.';
        } else {
            $_SESSION['pengguna_nama'] = $nama;
            $success = 'Profil berhasil diperbarui (mode demo).';
        }
        $user = getCurrentUser();
    }
}

$initials = mb_strtoupper(mb_substr($user['nama'], 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - Sistem Kunjungan UPA TIK</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Profile Hero -->
  <div class="pg-profile-hero" style="background: linear-gradient(rgba(15,20,60,0.58),rgba(15,20,60,0.58)),
  url('<?= BASE_URL ?>/assets/polije.png') center/cover no-repeat; background-blend-mode: overlay;">
    <div class="pg-profile-avatar"><?= $initials ?></div>
    <div class="pg-profile-name"><?= htmlspecialchars($user['nama']) ?></div>
    <div class="pg-profile-meta">
      <span class="pg-chip"><?= htmlspecialchars($user['nim']) ?></span>
      <span class="pg-chip pg-chip-accent"><?= ucfirst($user['status']) ?></span>
    </div>
  </div>

  <div class="pg-profile-card">

    <!-- Alerts -->
    <?php if ($error): ?>
    <div class="alert alert-danger" style="margin-bottom:16px;">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px;">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Info Akun (read-only) -->
    <div class="pg-card" style="margin-bottom:20px;">
      <div style="margin-top:10px;text-align:left;">
      <a href="<?= BASE_URL ?>/index.php" class="auth-back">←Beranda</a>
    </div>
      <div class="pg-card-title">
        Informasi Akun
      </div>
      <div class="form-group">
        <label>NIM / NIP</label>
        <input type="text" class="form-control no-icon"
               value="<?= htmlspecialchars($user['nim']) ?>" disabled
               style="background:var(--bg2);color:var(--text-muted);">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="text" class="form-control no-icon"
               value="<?= htmlspecialchars($user['email']) ?>" disabled
               style="background:var(--bg2);color:var(--text-muted);">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Status</label>
        <input type="text" class="form-control no-icon"
               value="<?= ucfirst($user['status']) ?>" disabled
               style="background:var(--bg2);color:var(--text-muted);">
      </div>
    </div>

    <!-- Edit Nama -->
    <div class="pg-card">
      <div class="pg-card-title">✏️ Edit Nama Lengkap</div>

      <form method="POST" action="profile.php">
        <div class="form-group">
          <label>Nama Lengkap <span style="color:var(--danger);">*</span></label>
          <input type="text" name="nama" class="form-control no-icon"
                 value="<?= htmlspecialchars($user['nama']) ?>" required
                 placeholder="Nama lengkap Anda">
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:14px;">
            💾 Simpan Perubahan
          </button>
          <a href="<?= BASE_URL ?>/frontend/auth/logout.php"
             class="btn" style="color:var(--danger);border:1.5px solid var(--danger);padding:14px 24px;border-radius:50px;font-weight:600;font-size:0.9rem;text-decoration:none;display:inline-flex;align-items:center;">
            🚪 Logout
          </a>
        </div>
      </form>


    </div>

  </div><!-- end pg-profile-card -->
</div>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>
<script src="../../assets/js/main.js"></script>
</body>
</html>
