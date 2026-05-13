<?php
require_once '../../includes/auth.php';
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontak - UPA TIK Polije</title>
  <!-- Menggunakan CSS yang sama dengan halaman Layanan -->
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/pengguna.css">
  <!-- Font Awesome untuk ikon profesional -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include '../../includes/navbar.php'; ?>

<div class="pg-wrapper">

  <!-- Page Header (Disamakan dengan halaman Layanan) -->
  <div class="pg-page-header">
    <h1>Hubungi Kami</h1>
    <p>Kami siap melayani kebutuhan teknologi informasi Anda</p>
  </div>

  <!-- Main Content -->
  <section style="background:var(--bg2); padding: 70px 40px;">
    <div style="max-width:1200px; margin:0 auto;">
      
      <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; align-items: start;">
        
        <!-- Sisi Kiri: Kartu Informasi -->
        <div class="reveal">
          <div style="background:white; padding:40px; border-radius:16px; box-shadow:var(--card-shadow); border:1px solid var(--border);">
            <h3 style="color:var(--primary); font-family:'Sora',sans-serif; font-weight:800; margin-bottom:24px;">Informasi Kontak</h3>
            
            <div class="info-list" style="display:flex; flex-direction:column; gap:25px;">
              
              <div style="display:flex; gap:15px;">
                <div style="width:40px; height:40px; background:rgba(37,99,235,0.1); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                  <h4 style="font-size:1rem; margin-bottom:5px; color:var(--primary);">Alamat Kantor</h4>
                  <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.5;">
                    Gedung TI, Politeknik Negeri Jember<br>
                    Jl. Mastrip, Krajan Timur, Sumbersari, Jember 68121
                  </p>
                </div>
              </div>

              <div style="display:flex; gap:15px;">
                <div style="width:40px; height:40px; background:rgba(37,99,235,0.1); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <i class="fas fa-envelope"></i>
                </div>
                <div>
                  <h4 style="font-size:1rem; margin-bottom:5px; color:var(--primary);">Email Resmi</h4>
                  <p style="font-size:0.9rem; color:var(--text-muted);">upa-tik@polije.ac.id</p>
                </div>
              </div>

              <div style="display:flex; gap:15px;">
                <div style="width:40px; height:40px; background:rgba(37,99,235,0.1); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                  <h4 style="font-size:1rem; margin-bottom:5px; color:var(--primary);">Telepon</h4>
                  <p style="font-size:0.9rem; color:var(--text-muted);">(0331) 333532</p>
                </div>
              </div>

              <div style="display:flex; gap:15px;">
                <div style="width:40px; height:40px; background:rgba(37,99,235,0.1); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <i class="fas fa-clock"></i>
                </div>
                <div>
                  <h4 style="font-size:1rem; margin-bottom:5px; color:var(--primary);">Jam Operasional</h4>
                  <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.6;">
                    Senin - Kamis: 07.30 - 16.00 WIB<br>
                    Jumat: 07.30 - 16.30 WIB<br>
                    <span style="color:#ef4444; font-weight:600;">Sabtu - Minggu: Tutup</span>
                  </p>
                </div>
              </div>

            </div>

            <div style="margin-top:35px; padding-top:25px; border-top:1px solid var(--border);">
               <a href="form-kunjungan.php" class="btn btn-primary" style="width:100%; text-align:center; padding:14px; display:block;">
                 <i class="fas fa-calendar-check" style="margin-right:8px;"></i> Buat Jadwal Kunjungan
               </a>
            </div>
          </div>
        </div>

        <!-- Sisi Kanan: Peta (Diselaraskan agar tinggi proporsional) -->
        <div class="reveal">
          <div style="background:white; padding:15px; border-radius:16px; box-shadow:var(--card-shadow); border:1px solid var(--border); height: 100%;">
            <div style="border-radius:12px; overflow:hidden; height: 480px;">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3815.71952794116!2d113.7230733!3d-8.1599551!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd695b617d8f623%3A0xf6c4437632474338!2sPoliteknik%20Negeri%20Jember!5e1!3m2!1sid!2sid!4v1776530209374!5m2!1sid!2sid"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
            <div style="padding:15px 5px;">
               <a href="https://maps.google.com/?q=Politeknik+Negeri+Jember" target="_blank" style="color:var(--primary); font-weight:600; text-decoration:none; font-size:0.9rem;">
                 <i class="fas fa-external-link-alt" style="margin-right:5px;"></i> Buka di Google Maps
               </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- FAQ Section (Tetap dipertahankan dengan gaya yang lebih bersih) -->
  <section style="background:white; padding:70px 40px;">
    <div style="max-width:850px; margin:0 auto;">
      <div style="text-align:center; margin-bottom:50px;">
        <h2 style="font-family:'Sora',sans-serif; color:var(--primary); font-weight:800;">Pertanyaan Umum</h2>
        <div style="width:50px; height:4px; background:var(--primary); margin: 15px auto; border-radius:2px;"></div>
      </div>

      <div class="faq-container">
        <?php
        $faqs = [
          ['q' => 'Bagaimana cara mengajukan kunjungan?', 'a' => 'Login ke sistem, pilih menu "Layanan", kemudian klik "Form Kunjungan". Isi data diri dan jenis layanan, lalu submit.'],
          ['q' => 'Berapa lama proses penanganan?', 'a' => 'Layanan akun (SSO/Password) biasanya 10-15 menit. Untuk keluhan teknis hardware atau jaringan berkisar 1-3 hari kerja.'],
          ['q' => 'Apakah melayani kunjungan di luar jam kerja?', 'a' => 'Saat ini layanan hanya tersedia pada jam operasional resmi. Untuk keadaan darurat, silakan hubungi melalui email resmi.'],
        ];
        foreach ($faqs as $i => $faq): ?>
          <div style="background:var(--bg2); border-radius:12px; padding:20px; margin-bottom:15px; cursor:pointer;" onclick="toggleFaq(<?= $i ?>)">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <h4 style="font-size:1rem; color:var(--text-main); font-weight:600;"><?= $faq['q'] ?></h4>
              <i id="icon-<?= $i ?>" class="fas fa-chevron-down" style="transition:0.3s; color:var(--primary);"></i>
            </div>
            <p id="answer-<?= $i ?>" style="display:none; margin-top:15px; font-size:0.9rem; color:var(--text-muted); line-height:1.7; border-top:1px solid #ddd; padding-top:15px;">
              <?= $faq['a'] ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

</div> <!-- End pg-wrapper -->

<footer>
  <p>© <?= date('Y') ?> UPA TIK Politeknik Negeri Jember. All rights reserved.</p>
</footer>

<script src="../../assets/js/main.js"></script>
<script>
function toggleFaq(i) {
  const ans = document.getElementById('answer-' + i);
  const icon = document.getElementById('icon-' + i);
  const isHidden = ans.style.display === 'none';
  ans.style.display = isHidden ? 'block' : 'none';
  icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0)';
}
</script>
</body>
</html>