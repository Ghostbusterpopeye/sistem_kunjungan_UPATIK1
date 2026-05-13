<?php
require_once 'includes/auth.php';
$user = getCurrentUser();

// ── Statistik real dari DB ────────────────────────────────────────────────────
$statPengguna  = 0;
$statFormulir  = 0;
$statLayanan   = 0;
$layananList   = [];

if ($conn) {
    // 1. Ambil Statistik
    $statPengguna = ($r = $conn->query("SELECT COUNT(*) AS n FROM akun_pengguna")) ? (int)$r->fetch_assoc()['n'] : 0;
    $statFormulir = ($r = $conn->query("SELECT COUNT(*) AS n FROM formulir_layanan")) ? (int)$r->fetch_assoc()['n'] : 0;
    $statLayanan  = ($r = $conn->query("SELECT COUNT(*) AS n FROM layanan WHERE is_active = 1")) ? (int)$r->fetch_assoc()['n'] : 0;

    // 2. Ambil List Layanan (Limit 6 agar tampilan rapi)
    $res = $conn->query("SELECT id_layanan, nama_layanan, detail_layanan FROM layanan WHERE is_active = 1 ORDER BY id_layanan LIMIT 6");
    if ($res) {
        $layananList = []; 
        while ($row = $res->fetch_assoc()) $layananList[] = $row;
    }
}

// Fallback / data demo jika DB belum ada
if (empty($layananList)) {
    $statPengguna = 500;
    $statFormulir = 1200;
    $statLayanan  = 9;
    $layananList  = [
        ['id_layanan'=>1,'nama_layanan'=>'Pemasangan VPN'],
        ['id_layanan'=>2,'nama_layanan'=>'Keluhan IT'],
        ['id_layanan'=>3,'nama_layanan'=>'Maintenance'],
        ['id_layanan'=>4,'nama_layanan'=>'SSO Email'],
        ['id_layanan'=>5,'nama_layanan'=>'Reset Password'],
        ['id_layanan'=>6,'nama_layanan'=>'Konsultasi IT'],
        ['id_layanan'=>7,'nama_layanan'=>'Instalasi Software'],
        ['id_layanan'=>8,'nama_layanan'=>'Keamanan Siber'],
        ['id_layanan'=>9,'nama_layanan'=>'Jaringan & Infrastruktur'],
        ['id_layanan'=>10,'nama_layanan'=>'E-Learning'],
    ];
}

$descMap = [
    'Pemasangan VPN'            => 'Akses jaringan internal kampus dari luar area Politeknik Negeri Jember dengan aman dan terenkripsi.',
    'Keluhan IT'                => 'Layanan penanganan keluhan IT. Tim kami siap merespons masalah teknis Anda secara cepat dan profesional.',
    'Maintenance'               => 'Maintenance server seluruh jurusan Polije. Pemeliharaan rutin untuk menjamin performa 24/7.',
    'SSO Email'                 => 'Aktivasi dan manajemen akun Single Sign-On (SSO) email institusi untuk semua layanan kampus.',
    'Reset Password'            => 'Pemulihan akses akun SIAKAD, e-learning, dan sistem kampus lainnya dengan verifikasi aman.',
    'Konsultasi IT'             => 'Konsultasi teknis seputar penggunaan teknologi informasi di lingkungan kampus Polije.',
    'Instalasi Software'        => 'Instalasi dan konfigurasi perangkat lunak berlisensi untuk kebutuhan akademik.',
    'Keamanan Siber'            => 'Perlindungan data dari ancaman siber. Penanganan insiden keamanan informasi.',
    'Jaringan & Infrastruktur'  => 'Pengelolaan WiFi kampus, LAN, switch & router. Laporan masalah koneksi ditangani cepat.',
    'E-Learning'              => 'Dukungan teknis untuk platform e-learning Website pembelajaran mahasiswa.',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Kunjungan UPA TIK - Politeknik Negeri Jember</title>
  <meta name="description" content="Sistem Kunjungan UPA TIK - Pusat bantuan teknologi informasi untuk civitas akademika Politeknik Negeri Jember.">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ══ HERO ══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="beranda"
  style="background: linear-gradient(rgba(15,20,60,0.58),rgba(15,20,60,0.58)),
         url('assets/polije.png') center/cover no-repeat;">
  <div class="hero-content">
    <h1>Sistem Kunjungan<br>UPA TIK</h1>
    <p>Pusat bantuan teknologi informasi untuk civitas akademika<br>Politeknik Negeri Jember.</p>
    <div class="hero-btns">
      <?php if ($user): ?>
        <!-- Sudah login: tidak ada tombol Login -->
        <a href="<?= BASE_URL ?>/frontend/pengguna/form-kunjungan.php" class="btn btn-outline">Buat Kunjungan</a>
        <a href="<?= BASE_URL ?>/frontend/pengguna/riwayat.php"        class="btn btn-primary">Laporan Saya</a>
      <?php else: ?>
        <!-- Belum login -->
        <a href="<?= BASE_URL ?>/frontend/auth/login.php" class="btn btn-outline"> Mulai Kunjungan</a>
        <a href="<?= BASE_URL ?>/frontend/auth/login.php" class="btn btn-primary"> Login</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ══ LAYANAN ════════════════════════════════════════════════════════════════ -->
<section style="background:var(--bg2);padding:90px 40px;" id="layanan">
  <div style="max-width:1200px;margin:0 auto;">
    <div class="section-header reveal">
      <h2>Layanan Kami</h2>
      <p><?= $statLayanan ?> jenis layanan tersedia di UPA TIK Polije</p>
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
    <div style="text-align:center;margin-top:20px;" class="reveal">
      <a href="<?= BASE_URL ?>/frontend/pengguna/layanan.php" class="btn btn-primary">
        Lihat Semua Layanan
      </a>
    </div>
  </div>
</section>

<!-- ══ STATISTIK ══════════════════════════════════════════════════════════════ -->
<section class="section" id="tentang">
  <div class="section-header reveal" style="margin-bottom:40px;">
    <h2>UPA TIK Statistik</h2>
    <p>Melayani civitas akademika Politeknik Negeri Jember dengan profesional</p>
  </div>
  <div class="stats-grid reveal">
    <div class="stat-card">
      <div class="number"><?= $statPengguna > 0 ? number_format($statPengguna) : '500+' ?></div>
      <div class="label">Pengguna Terdaftar</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $statFormulir > 0 ? number_format($statFormulir) : '1.2K+' ?></div>
      <div class="label">Tiket Diselesaikan</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $statLayanan > 0 ? $statLayanan : '9' ?></div>
      <div class="label">Jenis Layanan</div>
    </div>
  </div>
</section>


<!-- ══ CTA ════════════════════════════════════════════════════════════════════ -->
<section style="background:linear-gradient(135deg,var(--primary),var(--accent));padding:80px 40px;text-align:center;">
  <h2 style="font-family:'Sora',sans-serif;color:white;font-size:2rem;font-weight:800;margin-bottom:16px;" class="reveal">
    Butuh bantuan IT? Kami siap membantu! 
  </h2>
  <p style="color:rgba(255,255,255,0.85);margin-bottom:32px;font-size:1rem;" class="reveal">
    Buat jadwal kunjungan dan dapatkan layanan IT terbaik dari tim profesional UPA TIK Polije.
  </p>
  <div class="reveal">
    <?php if ($user): ?>
      <!-- Sudah login: tidak tampilkan tombol Login -->
      <a href="<?= BASE_URL ?>/frontend/pengguna/form-kunjungan.php"
         class="btn btn-outline" style="margin-right:12px;">Buat Kunjungan</a>
      <a href="<?= BASE_URL ?>/frontend/pengguna/riwayat.php"
         class="btn" style="background:white;color:var(--primary);font-weight:700;">
        Laporan Saya
      </a>
    <?php else: ?>
      <!-- Belum login: tampilkan tombol Login -->
      <a href="<?= BASE_URL ?>/frontend/auth/login.php"
         class="btn btn-outline" style="margin-right:12px;">Mulai Kunjungan</a>
      <a href="<?= BASE_URL ?>/frontend/auth/login.php"
         class="btn" style="background:white;color:var(--primary);font-weight:700;">
        Login Sekarang
      </a>
    <?php endif; ?>
  </div>
</section>

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
