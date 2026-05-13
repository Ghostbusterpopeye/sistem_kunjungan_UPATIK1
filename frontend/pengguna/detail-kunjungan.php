<?php
require_once '../../includes/auth.php';
requireLogin();
$user = getCurrentUser();

$id        = intval($_GET['id'] ?? 0);
$kunjungan = null;
$tindakan  = null;

if ($conn) {
    $stmt = $conn->prepare(
        "SELECT fl.id_formulir, fl.tanggal_isi,
                fl.detail_layanan, fl.status_layanan,
                l.nama_layanan
         FROM formulir_layanan fl
         JOIN layanan l ON fl.id_layanan = l.id_layanan
         WHERE fl.id_formulir = ? AND fl.id_pengguna = ?
         LIMIT 1"
    );
    $stmt->bind_param('ii', $id, $user['id']);
    $stmt->execute();
    $kunjungan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($kunjungan) {
        $t = $conn->prepare(
            "SELECT tl.detail_tindakan, tl.tanggal_tindakan, a.nama_lengkap AS nama_admin
             FROM tindakan_layanan tl
             JOIN akun_admin a ON tl.id_admin = a.id_admin
             WHERE tl.id_formulir = ?
             ORDER BY tl.tanggal_tindakan DESC LIMIT 1"
        );
        $t->bind_param('i', $id);
        $t->execute();
        $tindakan = $t->get_result()->fetch_assoc();
        $t->close();
    }
} else {
    $demo = [
        1 => ['id_formulir'=>1,'tanggal_isi'=>'2026-03-01 08:00:00','nama_layanan'=>'SSO Email',     'detail_layanan'=>'Aktivasi akun SSO email kampus','status_layanan'=>'menunggu'],
        2 => ['id_formulir'=>2,'tanggal_isi'=>'2026-01-03 09:30:00','nama_layanan'=>'Reset Password','detail_layanan'=>'Lupa password SIAKAD',            'status_layanan'=>'selesai'],
        3 => ['id_formulir'=>3,'tanggal_isi'=>'2025-08-05 10:00:00','nama_layanan'=>'SSO Email',     'detail_layanan'=>'Penggantian email SSO',            'status_layanan'=>'selesai'],
    ];
    $kunjungan = $demo[$id] ?? null;
    $tindakan  = ($kunjungan && $kunjungan['status_layanan'] === 'selesai')
        ? ['detail_tindakan'=>'Masalah berhasil diselesaikan oleh tim UPA TIK.','tanggal_tindakan'=>date('Y-m-d', strtotime($kunjungan['tanggal_isi'])),'nama_admin'=>'Admin UPA TIK']
        : null;
}

if (!$kunjungan) {
    header('Location: riwayat.php');
    exit;
}

$statusColor = ['selesai'=>'badge-success','menunggu'=>'badge-warning','diproses'=>'badge-info'];
$statusIcon  = ['selesai'=>'✅','menunggu'=>'⏳','diproses'=>'🔄'];
$statusLabel = ['selesai'=>'Selesai','menunggu'=>'Menunggu','diproses'=>'Diproses'];
$sc = $statusColor[$kunjungan['status_layanan']] ?? 'badge-info';
$si = $statusIcon[$kunjungan['status_layanan']]  ?? '📋';
$sl = $statusLabel[$kunjungan['status_layanan']] ?? ucfirst($kunjungan['status_layanan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Kunjungan #<?= $id ?> - UPA TIK Polije</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Page Header -->
  <div class="pg-page-header">
    <h1>Detail Kunjungan</h1>
    <p>Informasi lengkap formulir kunjungan #<?= $kunjungan['id_formulir'] ?></p>
  </div>

  <div class="pg-content-wrapper">
    <div class="pg-card" style="max-width:720px;margin:40px auto 0;animation:fadeSlideUp 0.4s ease;">

      <!-- Card Header -->
      <br><a href="<?= BASE_URL ?>/index.php" class="auth-back" style="margin-top:0;">← Beranda</a></br>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-family:'Sora',sans-serif;font-size:1.45rem;font-weight:800;color:var(--primary);">
            Kunjungan #<?= $kunjungan['id_formulir'] ?>
          </h2>
          <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">
            Diajukan pada <?= date('d M Y, H:i', strtotime($kunjungan['tanggal_isi'])) ?> WIB
          </p>
        </div>
        <span class="badge <?= $sc ?>" style="font-size:0.875rem;padding:8px 18px;"><?= $si ?> <?= $sl ?></span>
      </div>

      <!-- Detail Fields -->
      <div class="detail-grid">

        <!-- Pemohon -->
        <div class="detail-field">
          <div class="detail-field-label">👤 Pemohon</div>
          <div class="detail-field-value">
            <?= htmlspecialchars($user['nama']) ?>
            <span style="color:var(--text-muted);font-size:0.85rem;font-weight:400;">
              (<?= htmlspecialchars($user['nim']) ?>)
            </span>
          </div>
        </div>

        <!-- Tanggal & Layanan (grid 2 kolom) -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div style="padding:20px;background:var(--bg2);border-radius:var(--radius-sm);border:1px solid var(--border);">
            <div class="detail-field-label">Tanggal Daftar</div>
            <div class="detail-field-value" style="font-size:1.05rem;">
              <?= date('d M Y, H:i', strtotime($kunjungan['tanggal_isi'])) ?> WIB
            </div>
          </div>
          <div class="detail-field">
            <div class="detail-field-label">Jenis Layanan</div>
            <div style="margin-top:6px;">
              <span class="badge badge-service"><?= htmlspecialchars($kunjungan['nama_layanan']) ?></span>
            </div>
          </div>
        </div>

        <!-- Keperluan -->
        <div class="detail-field">
          <div class="detail-field-label">Keperluan / Deskripsi</div>
          <div class="detail-field-value" style="font-weight:400;line-height:1.7;">
            <?= htmlspecialchars($kunjungan['detail_layanan'] ?? '-') ?>
          </div>
        </div>

        <!-- Tindakan admin (jika ada) -->
        <?php if ($tindakan): ?>
        <div class="detail-tindakan">
          <div class="detail-tindakan-label">
            📌 Catatan dari Tim UPA TIK — <?= htmlspecialchars($tindakan['nama_admin']) ?>
          </div>
          <div class="detail-field-value" style="font-weight:400;line-height:1.7;color:var(--text);">
            <?= htmlspecialchars($tindakan['detail_tindakan']) ?>
          </div>
          <small style="color:#6B7280;display:block;margin-top:8px;">
            Ditangani pada: <?= date('d M Y', strtotime($tindakan['tanggal_tindakan'])) ?>
          </small>
        </div>
        <?php endif; ?>

        <!-- Status info jika masih menunggu -->
        <?php if ($kunjungan['status_layanan'] === 'menunggu'): ?>
        <div style="background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.25);border-radius:var(--radius-sm);padding:16px;font-size:0.875rem;color:#92400E;display:flex;gap:10px;align-items:flex-start;">
          <span style="flex-shrink:0;">⏳</span>
          <span>Permintaan kunjungan Anda sedang <strong>menunggu konfirmasi</strong> dari tim UPA TIK. Harap cek kembali secara berkala.</span>
        </div>
        <?php endif; ?>

      </div>

      <!-- Actions -->
      <div style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap;">
        <a href="form-kunjungan.php" class="btn btn-primary" style="margin-left:auto;">+ Kunjungan Baru</a>
      </div>

    </div><!-- end pg-card -->
  </div>
</div>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>
<script src="../../assets/js/main.js"></script>
</body>
</html>
