<?php
require_once '../../includes/auth.php';
requireLogin();
$user = getCurrentUser();

$error   = '';
$success = '';

// ── Load daftar layanan ───────────────────────────────────────────────────────
$layananList = [];
if ($conn) {
    $res = $conn->query("SELECT id_layanan, nama_layanan FROM layanan WHERE is_active = 1 ORDER BY id_layanan");
    if ($res) {
        while ($row = $res->fetch_assoc()) $layananList[] = $row;
    }
}
if (empty($layananList)) {
    $layananList = [
        ['id_layanan'=>1,'nama_layanan'=>'SSO Email'],
        ['id_layanan'=>2,'nama_layanan'=>'Reset Password'],
        ['id_layanan'=>3,'nama_layanan'=>'Pemasangan VPN'],
        ['id_layanan'=>4,'nama_layanan'=>'Keluhan IT'],
        ['id_layanan'=>5,'nama_layanan'=>'Maintenance'],
        ['id_layanan'=>6,'nama_layanan'=>'Instalasi Software'],
        ['id_layanan'=>7,'nama_layanan'=>'Konsultasi IT'],
        ['id_layanan'=>8,'nama_layanan'=>'Keamanan Siber'],
        ['id_layanan'=>9,'nama_layanan'=>'Jaringan & Infrastruktur'],
    ];
}

// ── Proses submit ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLayanan = intval($_POST['id_layanan'] ?? 0);
    $keperluan = trim($_POST['keperluan']    ?? '');

    if (!$idLayanan || empty($keperluan)) {
        $error = 'Semua field wajib diisi.';
    } else {
        if ($conn) {
            $stmt = $conn->prepare(
                "INSERT INTO formulir_layanan
                    (id_pengguna, id_layanan, tanggal_isi, detail_layanan, status_layanan)
                 VALUES (?, ?, NOW(), ?, 'menunggu')"
            );
            $stmt->bind_param('iis', $user['id'], $idLayanan, $keperluan);
            if ($stmt->execute()) {
                // Redirect ke halaman layanan setelah berhasil
                header('Location: layanan.php?success=1');
                exit;
            } else {
                $error = 'Gagal mendaftarkan kunjungan: ' . $conn->error;
            }
            $stmt->close();
        } else {
            // Mode demo: langsung redirect
            header('Location: layanan.php?success=1');
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
  <title>Form Kunjungan - UPA TIK Polije</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Page Header -->
  <div class="pg-page-header">
    <h1>Form Kunjungan</h1>
    <p>Daftarkan jadwal kunjungan Anda ke UPA TIK Polije</p>
  </div>

  <div class="pg-content-wrapper">
    <div class="pg-card" style="max-width:720px;margin:40px auto 0;animation:fadeSlideUp 0.4s ease;">

      <?php if ($success): ?>
      <!-- Success State -->
      <div style="text-align:center;padding:32px 0;">
        <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
        <h2 style="font-family:'Sora',sans-serif;font-size:1.6rem;font-weight:800;color:var(--primary);margin-bottom:12px;">
          Kunjungan Didaftarkan!
        </h2>
        <p style="color:var(--text-muted);margin-bottom:32px;">
          <?= htmlspecialchars($success) ?>
        </p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
          <a href="riwayat.php" class="btn btn-primary"> Lihat Riwayat</a>
          <a href="form-kunjungan.php" class="btn btn-outline-dark">+ Kunjungan Lain</a>
          <a href="index.php" class="btn btn-outline-dark"> Beranda</a>
        </div>
      </div>

      <?php else: ?>
      <!-- Header info -->
      <br><a href="<?= BASE_URL ?>/index.php" class="auth-back" style="margin-top:0;">← Beranda</a></br>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:var(--primary);">
            Buat Jadwal Kunjungan
          </h2>
          <p style="color:var(--text-muted);font-size:0.9rem;margin-top:4px;">
            Halo, <?= htmlspecialchars($user['nama']) ?>! Silakan isi form di bawah.
          </p>
        </div>
      </div>

      <?php if ($error): ?>
      <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="form-kunjungan.php">
        <!-- Info Pemohon (disabled) -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:4px;">
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control no-icon"
                   value="<?= htmlspecialchars($user['nama']) ?>" disabled
                   style="background:var(--bg2);color:var(--text-muted);">
          </div>
          <div class="form-group">
            <label>NIM / NIP</label>
            <input type="text" class="form-control no-icon"
                   value="<?= htmlspecialchars($user['nim']) ?>" disabled
                   style="background:var(--bg2);color:var(--text-muted);">
          </div>
        </div>

        <div class="form-group">
          <label>Jenis Layanan <span style="color:var(--danger);">*</span></label>
          <select name="id_layanan" class="form-control" required>
            <option value="">-- Pilih Jenis Layanan --</option>
            <?php foreach ($layananList as $l): ?>
            <option value="<?= $l['id_layanan'] ?>"
              <?= (intval($_POST['id_layanan'] ?? 0) === $l['id_layanan']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($l['nama_layanan']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Keperluan / Deskripsi Masalah <span style="color:var(--danger);">*</span></label>
          <textarea name="keperluan" class="form-control" rows="5"
                    placeholder="Jelaskan keperluan atau masalah yang Anda hadapi secara detail..."
                    required><?= htmlspecialchars($_POST['keperluan'] ?? '') ?></textarea>
        </div>

        <!-- Catatan -->
        <div style="background:rgba(45,58,140,0.05);border:1px solid rgba(45,58,140,0.12);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:24px;font-size:0.85rem;color:var(--primary);display:flex;gap:10px;align-items:flex-start;">
          <span style="flex-shrink:0;">📌</span>
          <span><strong>Catatan:</strong> Kunjungan akan dikonfirmasi oleh tim UPA TIK. Harap hadir 10 menit sebelum waktu yang ditentukan. Bawa kartu identitas (KTM/KTP).</span>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="font-size:1rem;padding:16px;">
          Daftarkan Kunjungan
        </button>
      </form>
      <?php endif; ?>

    </div><!-- end pg-card -->
  </div>
</div>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>
<script src="../../assets/js/main.js"></script>
</body>
</html>
