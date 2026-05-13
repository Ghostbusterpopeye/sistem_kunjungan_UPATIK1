<?php
/**
 * setup.php — Halaman instalasi sekali pakai
 * Akses: http://localhost/project_wppl1hal_tes/setup.php
 * HAPUS file ini setelah setup selesai!
 */
require_once 'config/database.php';

$db   = new Database();
$conn = $db->getConnection();

$messages = [];
$errors   = [];

// ── Jalankan ALTER untuk perbesar kolom nama_layanan ──────────────────────────
if ($conn && !$conn->connect_error) {
    $conn->set_charset('utf8mb4');

    // Fix varchar(20) → varchar(100) pada nama_layanan
    $conn->query("ALTER TABLE layanan MODIFY COLUMN nama_layanan VARCHAR(100) NOT NULL");

    // Tambah is_active ke akun_pengguna bila belum ada
    $colCheck = $conn->query("SHOW COLUMNS FROM akun_pengguna LIKE 'is_active'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE akun_pengguna ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`");
        $messages[] = "✅ Kolom is_active ditambahkan ke akun_pengguna.";
    } else {
        $messages[] = "ℹ️  Kolom is_active sudah ada.";
    }

    // Insert layanan default
    $layananDefault = [
        'SSO Email', 'Reset Password', 'Pemasangan VPN', 'Keluhan IT',
        'Maintenance', 'Instalasi Software', 'Konsultasi IT',
        'Keamanan Siber', 'Jaringan & Infrastruktur',
    ];

    $chkL = $conn->query("SELECT COUNT(*) AS n FROM layanan");
    $jumlahLayanan = $chkL ? (int)$chkL->fetch_assoc()['n'] : 0;

    if ($jumlahLayanan === 0) {
        $stmtL = $conn->prepare("INSERT INTO layanan (nama_layanan) VALUES (?)");
        foreach ($layananDefault as $nama) {
            $stmtL->bind_param('s', $nama);
            $stmtL->execute();
        }
        $stmtL->close();
        $messages[] = "✅ " . count($layananDefault) . " layanan default berhasil ditambahkan.";
    } else {
        $messages[] = "ℹ️  Tabel layanan sudah berisi $jumlahLayanan data, tidak diinsert ulang.";
    }

    // Buat akun admin default (nip: admin001, password: admin123)
    $chkA = $conn->prepare("SELECT id_admin FROM akun_admin WHERE nip = ?");
    $nip  = 'admin001';
    $chkA->bind_param('s', $nip);
    $chkA->execute();
    $chkA->store_result();

    if ($chkA->num_rows === 0) {
        $pass   = 'admin123';
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $nama   = 'Admin UPA TIK';
        $insA   = $conn->prepare(
            "INSERT INTO akun_admin (nip, nama_lengkap, password) VALUES (?, ?, ?)"
        );
        $insA->bind_param('sss', $nip, $nama, $hashed);
        $insA->execute();
        $insA->close();
        $messages[] = "✅ Akun admin dibuat: NIP <strong>admin001</strong> | Password <strong>admin123</strong>";
    } else {
        $messages[] = "ℹ️  Akun admin (NIP: admin001) sudah ada, tidak dibuat ulang.";
    }
    $chkA->close();

    // Reset password admin (opsional, jika form disubmit)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_pass'])) {
        $newPass  = trim($_POST['new_pass']);
        $adminNip = trim($_POST['admin_nip']);
        if (strlen($newPass) >= 6 && !empty($adminNip)) {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $upd    = $conn->prepare("UPDATE akun_admin SET password = ? WHERE nip = ?");
            $upd->bind_param('ss', $hashed, $adminNip);
            $upd->execute();
            $upd->close();
            $messages[] = "✅ Password admin NIP <strong>$adminNip</strong> berhasil diubah.";
        } else {
            $errors[] = "❌ NIP atau password tidak valid (min. 6 karakter).";
        }
    }

    // Tampilkan daftar admin
    $admins = [];
    $resAdm = $conn->query("SELECT id_admin, nip, nama_lengkap FROM akun_admin");
    if ($resAdm) {
        while ($r = $resAdm->fetch_assoc()) $admins[] = $r;
    }
} else {
    $errors[] = "❌ Koneksi database gagal! Periksa config/database.php";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup – Sistem Kunjungan UPA TIK</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
    .card { background: white; border-radius: 16px; box-shadow: 0 8px 32px rgba(45,58,140,0.12); padding: 48px; max-width: 680px; width: 100%; }
    h1 { font-size: 1.8rem; color: #1A2468; margin-bottom: 6px; }
    .subtitle { color: #666; font-size: 0.95rem; margin-bottom: 32px; }
    .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 10px; font-size: 0.9rem; line-height: 1.6; }
    .alert-ok  { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-err { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .section { margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 24px; }
    h2 { font-size: 1.1rem; color: #1A2468; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    th { background: #f8fafc; font-weight: 600; color: #555; }
    form { display: flex; flex-direction: column; gap: 12px; }
    input { padding: 12px 16px; border: 1.5px solid #d1d5db; border-radius: 10px; font-size: 0.95rem; outline: none; }
    input:focus { border-color: #2D3A8C; }
    button[type=submit] { padding: 13px; background: #2D3A8C; color: white; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
    button[type=submit]:hover { background: #1A2468; }
    .warning { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; font-size: 0.88rem; margin-top: 28px; }
    .links { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
    .links a { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.88rem; }
    .btn-primary { background: #2D3A8C; color: white; }
    .btn-outline { border: 1.5px solid #2D3A8C; color: #2D3A8C; }
  </style>
</head>
<body>
<div class="card">
  <h1>🔧 Setup – Sistem Kunjungan UPA TIK</h1>
  <p class="subtitle">Halaman instalasi awal. Hapus file ini setelah setup selesai.</p>

  <!-- Status -->
  <?php foreach ($errors   as $e): ?>
  <div class="alert alert-err"><?= $e ?></div>
  <?php endforeach; ?>
  <?php foreach ($messages as $m): ?>
  <div class="alert alert-ok"><?= $m ?></div>
  <?php endforeach; ?>

  <!-- Daftar Admin -->
  <?php if (!empty($admins)): ?>
  <div class="section">
    <h2>👤 Akun Admin Terdaftar</h2>
    <table>
      <thead><tr><th>#</th><th>NIP</th><th>Nama</th></tr></thead>
      <tbody>
        <?php foreach ($admins as $a): ?>
        <tr>
          <td><?= $a['id_admin'] ?></td>
          <td><code><?= htmlspecialchars($a['nip']) ?></code></td>
          <td><?= htmlspecialchars($a['nama_lengkap']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Form Reset Password Admin -->
  <div class="section">
    <h2>🔑 Reset Password Admin</h2>
    <form method="POST">
      <input type="text"     name="admin_nip"  placeholder="NIP Admin (contoh: admin001)" required>
      <input type="password" name="new_pass"    placeholder="Password Baru (min. 6 karakter)" required>
      <button type="submit">Reset Password</button>
    </form>
  </div>

  <!-- Warning -->
  <div class="warning">
    ⚠️ <strong>Penting:</strong> Hapus atau rename file <code>setup.php</code> setelah setup selesai untuk keamanan sistem!
  </div>

  <!-- Links -->
  <div class="links">
    <a href="/" class="btn-outline">🏠 Beranda</a>
    <a href="/project_wppl1hal_tes/frontend/admin/admin-login.php" class="btn-primary">🔐 Login Admin</a>
    <a href="/project_wppl1hal_tes/frontend/auth/login.php" class="btn-outline">👤 Login Pengguna</a>
  </div>
</div>
</body>
</html>
