<?php
require_once '../../includes/auth.php';
requireLogin();
$user = getCurrentUser();

// ── Fetch riwayat ─────────────────────────────────────────────────────────────
$riwayat = [];
if ($conn) {
    $stmt = $conn->prepare(
        "SELECT fl.id_formulir, fl.tanggal_isi,
                fl.detail_layanan, fl.status_layanan,
                l.nama_layanan
         FROM formulir_layanan fl
         JOIN layanan l ON fl.id_layanan = l.id_layanan
         WHERE fl.id_pengguna = ?
         ORDER BY fl.tanggal_isi DESC"
    );
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $riwayat[] = $row;
    }
    $stmt->close();
} else {
    $riwayat = [
        ['id_formulir'=>1,'tanggal_isi'=>'2026-03-01 08:00:00','nama_layanan'=>'SSO Email',     'detail_layanan'=>'Aktivasi akun SSO email kampus','status_layanan'=>'menunggu'],
        ['id_formulir'=>2,'tanggal_isi'=>'2026-01-03 09:00:00','nama_layanan'=>'Reset Password','detail_layanan'=>'Lupa password SIAKAD',            'status_layanan'=>'selesai'],
        ['id_formulir'=>3,'tanggal_isi'=>'2025-08-05 10:00:00','nama_layanan'=>'SSO Email',     'detail_layanan'=>'Penggantian email SSO',            'status_layanan'=>'selesai'],
    ];
}

function statusBadge(string $status): string {
    $map   = ['selesai'=>'badge-success','menunggu'=>'badge-warning','diproses'=>'badge-info'];
    $label = ['selesai'=>'Selesai','menunggu'=>'Menunggu','diproses'=>'Diproses'];
    $cls   = $map[$status]   ?? 'badge-info';
    $txt   = $label[$status] ?? ucfirst($status);
    return "<span class='badge $cls'>$txt</span>";
}

$total    = count($riwayat);
$selesai  = count(array_filter($riwayat, fn($r) => $r['status_layanan'] === 'selesai'));
$menunggu = count(array_filter($riwayat, fn($r) => $r['status_layanan'] === 'menunggu'));
$diproses = count(array_filter($riwayat, fn($r) => $r['status_layanan'] === 'diproses'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Kunjungan - UPA TIK Polije</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Page Header -->
  <div class="pg-page-header">
    <h1>Riwayat Kunjungan</h1>
    <p>Riwayat seluruh kunjungan Anda ke UPA TIK Polije</p>
  </div>

  <div class="pg-content-wrapper">

    <!-- Stats -->
  <div>
    <div class="pg-section-label" style="margin-top:80px;">Statistik</div>
    <div class="pg-stats-grid" style="margin-bottom:32px;">
      <div class="pg-stat-card">
        <div class="pg-stat-number"><?= $total ?></div>
        <div class="pg-stat-label">Total Kunjungan</div>
      </div>
      <div class="pg-stat-card">
        <div class="pg-stat-number" style="color:#d97706;"><?= $menunggu ?></div>
        <div class="pg-stat-label">Menunggu</div>
      </div>
      <div class="pg-stat-card">
        <div class="pg-stat-number" style="color:#2563eb;"><?= $diproses ?></div>
        <div class="pg-stat-label">Diproses</div>
      </div>
      <div class="pg-stat-card">
        <div class="pg-stat-number" style="color:#059669;"><?= $selesai ?></div>
        <div class="pg-stat-label">Selesai</div>
      </div>
    </div>
  </div>

    <!-- Toolbar -->
    <div class="pg-toolbar">
      <h2>Daftar Kunjungan</h2>
      <a href="form-kunjungan.php" class="btn btn-primary">+ Kunjungan Baru</a>
    </div>

    <!-- Table / Empty -->
    <?php if (empty($riwayat)): ?>
    <div class="pg-empty-card">
      <div class="pg-empty-icon">📭</div>
      <h3 style="color:var(--text);margin-bottom:8px;">Belum ada riwayat kunjungan</h3>
      <p style="margin-bottom:20px;">Anda belum pernah membuat jadwal kunjungan ke UPA TIK.</p>
      <a href="form-kunjungan.php" class="btn btn-primary">+ Buat Kunjungan Pertama</a>
    </div>
    <?php else: ?>
    <div class="table-card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Tgl Kunjungan</th>
              <th>Jenis Layanan</th>
              <th>Keperluan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($riwayat as $r):
              $tgl = date('d M Y, H:i', strtotime($r['tanggal_isi']));
            ?>
            <tr>
              <td style="white-space:nowrap;font-weight:600;"><?= $tgl ?></td>
              <td><span class="badge badge-service"><?= htmlspecialchars($r['nama_layanan']) ?></span></td>
              <td style="max-width:240px;color:var(--text-muted);font-size:0.875rem;">
                <?= htmlspecialchars(substr($r['detail_layanan'] ?? '-', 0, 70)) ?>
                <?= strlen($r['detail_layanan'] ?? '') > 70 ? '...' : '' ?>
              </td>
              <td><?= statusBadge($r['status_layanan']) ?></td>
              <td>
                <a href="detail-kunjungan.php?id=<?= $r['id_formulir'] ?>"
                   class="btn btn-outline-dark" style="padding:7px 16px;font-size:0.8rem;white-space:nowrap;">
                   👁 Detail
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?> 
  </div>
</div>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>
<script src="../../assets/js/main.js"></script>
</body>
</html>
