<?php
require_once '../../includes/auth.php';
$user = getCurrentUser();

// ── Ambil layanan ─────────────────────────────────────────────────────────────
$layananList = [];
if ($conn) {
    // Tambahkan detail_layanan ke dalam SELECT
    $result = $conn->query("SELECT id_layanan, nama_layanan, detail_layanan FROM layanan WHERE is_active = 1 ORDER BY id_layanan");
    if ($result) {
        while ($row = $result->fetch_assoc()) $layananList[] = $row;
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
        ['id_layanan'=>10,'nama_layanan'=>'E-Learning'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Layanan - UPA TIK Polije</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Page Header -->
  <div class="pg-page-header">
    <h1>Layanan Kami</h1>
    <p><?= count($layananList) ?> layanan aktif tersedia di UPA TIK Polije</p>
  </div>

  <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
  <div style="background:#d1fae5;border-left:4px solid #059669;padding:16px 32px;display:flex;align-items:center;gap:12px;font-weight:600;color:#065f46;font-size:0.95rem;">
    🎉 Kunjungan berhasil didaftarkan! Tim UPA TIK akan segera menghubungi Anda. &nbsp;
    <a href="riwayat.php" style="color:#059669;text-decoration:underline;font-weight:700;">Lihat Riwayat →</a>
  </div>
  <?php endif; ?>

  <!-- Layanan Grid -->
  <section style="background:var(--bg2);padding:70px 40px;">
    <div style="max-width:1200px;margin:0 auto;">

      <div style="text-align:center;margin-bottom:48px;">
        <p style="color:var(--text-muted);max-width:540px;margin:0 auto;line-height:1.7;">
          Unit Pelayanan Akademik Teknologi Informasi dan Komunikasi (UPA TIK) Polije siap
          membantu kebutuhan teknis Anda dengan layanan profesional berikut.
        </p>
      </div>

      <div class="services-grid">
        <?php foreach ($layananList as $l): ?>
          <div class="service-card reveal">
            <h3><?= htmlspecialchars($l['nama_layanan']) ?></h3>
            <p>
              <?= !empty($l['detail_layanan']) 
                  ? htmlspecialchars($l['detail_layanan']) 
                  : 'Layanan teknis UPA TIK Polije.' ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="text-align:center;margin-top:48px;" class="reveal">
        <?php if ($user): ?>
        <a href="form-kunjungan.php" class="btn btn-primary" style="font-size:1rem;padding:16px 40px;">
          Buat Jadwal Kunjungan
        </a>
        <?php else: ?>
        <p style="color:var(--text-muted);margin-bottom:16px;">Silakan login untuk membuat jadwal kunjungan</p>
        <a href="<?= BASE_URL ?>/frontend/auth/login.php" class="btn btn-primary" style="font-size:1rem;padding:16px 40px;">
          Login untuk Kunjungan
        </a>
        <?php endif; ?>
      </div>
    </div>
  </section>


</div>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>
<script src="../../assets/js/main.js"></script>
</body>
</html>
