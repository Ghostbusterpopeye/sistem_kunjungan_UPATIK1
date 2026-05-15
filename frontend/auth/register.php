<?php
require_once '../../includes/auth.php';

if (isLoggedIn()) {
  header('Location: ' . BASE_URL . '/frontend/pengguna/riwayat.php');
  exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama   = trim($_POST['nama']     ?? '');
  $nim    = trim($_POST['nim']      ?? '');
  $email  = trim($_POST['email']    ?? '');
  $status = trim($_POST['status']   ?? 'mahasiswa');
  $pass   = $_POST['password']      ?? '';

  $allowedStatus = ['mahasiswa', 'dosen'];

  if (empty($nama) || empty($nim) || empty($email) || empty($pass)) {
    $error = 'Semua field wajib diisi.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Format email tidak valid.';
  } elseif (strlen($nim) < 9) {
    $error = 'nim/nip minimal 9 karakter.';
  } elseif (strlen($nim) > 16) {
    $error = 'nim/nip maksimal 16 karakter.';
  } elseif (!in_array($status, $allowedStatus)) {
    $error = 'Status tidak valid.';
  } else {
    if ($conn) {
      $chk = $conn->prepare("SELECT id_pengguna FROM akun_pengguna WHERE nim_nip = ? OR email = ?");
      $chk->bind_param('ss', $nim, $email);
      $chk->execute();
      $chk->store_result();
      if ($chk->num_rows > 0) {
        $error = 'NIM/NIP atau Email sudah terdaftar. Silakan login.';
      } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $ins = $conn->prepare(
          "INSERT INTO akun_pengguna (nama_lengkap, nim_nip, email, password, status)
                     VALUES (?, ?, ?, ?, ?)"
        );
        $ins->bind_param('sssss', $nama, $nim, $email, $hashed, $status);
        if ($ins->execute()) {
          // Auto-login: ambil id pengguna yang baru dibuat
          $new_id = $conn->insert_id;
          $_SESSION['pengguna_id']     = $new_id;
          $_SESSION['pengguna_nama']   = $nama;
          $_SESSION['pengguna_nim']    = $nim;
          $_SESSION['pengguna_email']  = $email;
          $_SESSION['pengguna_status'] = $status;
          // Redirect langsung ke beranda pengguna
          header('Location: ' . BASE_URL . '/index.php');
          exit;
        } else {
          $error = 'Gagal membuat akun. Coba lagi.';
        }
        $ins->close();
      }
      $chk->close();
    } else {
      // Mode demo: simulasi auto-login
      $_SESSION['pengguna_id']     = 1;
      $_SESSION['pengguna_nama']   = $nama;
      $_SESSION['pengguna_nim']    = $nim;
      $_SESSION['pengguna_email']  = $email;
      $_SESSION['pengguna_status'] = $status;
      header('Location: ' . BASE_URL . '/index.php');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - UPA TIK Polije</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <a href="<?= BASE_URL ?>/" class="auth-back">← Kembali ke Beranda</a>

    <h1>Buat Akun Baru</h1>
    <p class="subtitle">Lengkapi data di bawah ini untuk mendaftar.</p>

    <?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="register.php">
      <div class="form-group">
        <div class="input-wrapper">
        <input type="text" name="nama" class="form-control" 
              placeholder="Nama Lengkap" 
              value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <div class="input-wrapper">
        <input type="text" name="nim" class="form-control" 
              placeholder="NIM / NIP" 
              value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <div class="input-wrapper">
        <input type="email" name="email" class="form-control" 
              placeholder="Alamat Email" 
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label style="font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:6px;display:block;">
          Status Pengguna
        </label>
        <div class="input-wrapper">
          <select name="status" class="form-control" required>
            <option value="" disabled selected>Pilih Status...</option>
            <option value="mahasiswa" <?= (($_POST['status'] ?? '') === 'mahasiswa') ? 'selected' : '' ?>>Mahasiswa</option>
            <option value="dosen" <?= (($_POST['status'] ?? '') === 'dosen') ? 'selected' : '' ?>>Dosen / Staff</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <div class="input-wrapper">
          <input type="password" name="password" class="form-control"
                 placeholder="Buat Password (min. 6 karakter)" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
    </form>
    <?php endif; ?>

    <div class="auth-footer">
      Sudah punya akun? <a href="login.php">Login</a>
    </div>
  </div>
</div>
<script src="../../assets/js/main.js"></script>
</body>
</html>
