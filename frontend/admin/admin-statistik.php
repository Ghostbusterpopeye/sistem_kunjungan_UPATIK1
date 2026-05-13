<?php
require_once '../../includes/auth.php';
requireAdminLogin();

// ── Statistik dari DB ─────────────────────────────────────────────────────────
$totalKunjungan  = 0;
$totalSelesai    = 0;
$totalPengguna   = 0;
$chartDataTahunIni = array_fill(0, 12, 0); // Jan–Des tahun ini
$chartDataTahunLalu= array_fill(0, 12, 0); // Jan–Des tahun lalu
$perLayanan      = [];
$rataRataBulanan = 0;
$rataRataTahunan = 0;

if ($conn) {
    $year     = date('Y');
    $yearLalu = $year - 1;

    $r = $conn->query("SELECT COUNT(*) AS n FROM formulir_layanan");
    $totalKunjungan = $r ? (int)$r->fetch_assoc()['n'] : 0;

    $r = $conn->query("SELECT COUNT(*) AS n FROM formulir_layanan WHERE status_layanan = 'selesai'");
    $totalSelesai = $r ? (int)$r->fetch_assoc()['n'] : 0;

    $r = $conn->query("SELECT COUNT(*) AS n FROM akun_pengguna");
    $totalPengguna = $r ? (int)$r->fetch_assoc()['n'] : 0;

    // Chart data tahun ini
    $res = $conn->query(
        "SELECT MONTH(tanggal_isi) AS bln, COUNT(*) AS total
         FROM formulir_layanan WHERE YEAR(tanggal_isi) = $year
         GROUP BY MONTH(tanggal_isi)"
    );
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $chartDataTahunIni[$row['bln'] - 1] = (int)$row['total'];
        }
    }

    // Chart data tahun lalu
    $res2 = $conn->query(
        "SELECT MONTH(tanggal_isi) AS bln, COUNT(*) AS total
         FROM formulir_layanan WHERE YEAR(tanggal_isi) = $yearLalu
         GROUP BY MONTH(tanggal_isi)"
    );
    if ($res2) {
        while ($row = $res2->fetch_assoc()) {
            $chartDataTahunLalu[$row['bln'] - 1] = (int)$row['total'];
        }
    }

    // Per layanan
    $resL = $conn->query(
        "SELECT l.nama_layanan, COUNT(fl.id_formulir) AS total
         FROM layanan l
         LEFT JOIN formulir_layanan fl ON l.id_layanan = fl.id_layanan
         GROUP BY l.id_layanan, l.nama_layanan
         ORDER BY total DESC"
    );
    if ($resL) {
        while ($row = $resL->fetch_assoc()) {
            $perLayanan[] = $row;
        }
    }

    // Rata-rata bulanan (berdasarkan bulan aktif tahun ini)
    $bulanAktif = count(array_filter($chartDataTahunIni, fn($v) => $v > 0));
    $totalTahunIni = array_sum($chartDataTahunIni);
    $rataRataBulanan = $bulanAktif > 0 ? round($totalTahunIni / $bulanAktif) : 0;

    // Rata-rata tahunan: total / jumlah tahun berbeda
    $resYear = $conn->query(
        "SELECT COUNT(DISTINCT YEAR(tanggal_isi)) AS tahun FROM formulir_layanan"
    );
    $jumlahTahun = $resYear ? max(1, (int)$resYear->fetch_assoc()['tahun']) : 1;
    $rataRataTahunan = $jumlahTahun > 0 ? round($totalKunjungan / $jumlahTahun) : 0;

} else {
    // Demo data
    $totalKunjungan  = 232;
    $totalSelesai    = 67;
    $totalPengguna   = 45;
    $chartDataTahunIni  = [15, 20, 18, 25, 30, 28, 35, 32, 40, 38, 42, 50];
    $chartDataTahunLalu = [10, 12, 15, 18, 22, 20, 28, 25, 30, 28, 35, 40];
    $rataRataBulanan = 29;
    $rataRataTahunan = 232;
    $perLayanan = [
        ['nama_layanan'=>'SSO Email','total'=>52],
        ['nama_layanan'=>'Reset Password','total'=>45],
        ['nama_layanan'=>'Keluhan IT','total'=>38],
        ['nama_layanan'=>'Pemasangan VPN','total'=>30],
        ['nama_layanan'=>'Konsultasi IT','total'=>25],
    ];
}

$title = 'Admin Statistik';
include '../../includes/header.php';
?>

<!-- Sidebar -->
<?php include '../../includes/admin-sidebar.php'; ?>

<!-- Main Content -->
<main class="admin-main">
    <div class="admin-container">

        <h1 class="page-title">Statistik Kunjungan</h1>

        <!-- Stat Summary Cards -->
        <div class="dashboard-cards" style="margin-bottom:2rem;">
            <div class="card" style="text-align:center;">
                <div class="card-header">Total Kunjungan</div>
                <div class="card-value" style="font-size:3rem;"><?= $totalKunjungan ?></div>
                <div class="card-desc">Semua formulir yang pernah masuk</div>
            </div>
            <div class="card" style="text-align:center;">
                <div class="card-header">Total Selesai</div>
                <div class="card-value" style="font-size:3rem;color:#059669;"><?= $totalSelesai ?></div>
                <div class="card-desc">Formulir telah diselesaikan</div>
            </div>
            <div class="card" style="text-align:center;">
                <div class="card-header">Rata-rata Bulanan</div>
                <div class="card-value" style="font-size:3rem;color:#8b5cf6;"><?= $rataRataBulanan ?></div>
                <div class="card-desc">Kunjungan per bulan (tahun ini)</div>
            </div>
            <div class="card" style="text-align:center;">
                <div class="card-header">Rata-rata Tahunan</div>
                <div class="card-value" style="font-size:3rem;color:#f59e0b;"><?= $rataRataTahunan ?></div>
                <div class="card-desc">Kunjungan per tahun</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:3fr 0fr;gap:0rem;align-items:start;">
            <!-- Chart Card -->
            <div class="table-card" style="min-height:500px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                    <h2 style="margin:0;">Grafik Kunjungan Bulanan</h2>
                    <div style="display:flex;gap:8px;">
                        <button onclick="setChartYear('ini')" id="btn-ini"
                            style="padding:6px 14px;border-radius:8px;border:1.5px solid var(--primary);background:var(--primary);color:white;font-size:0.8rem;cursor:pointer;font-weight:600;">
                            <?= date('Y') ?>
                        </button>
                        <button onclick="setChartYear('lalu')" id="btn-lalu"
                            style="padding:6px 14px;border-radius:8px;border:1.5px solid #ccc;background:white;color:#666;font-size:0.8rem;cursor:pointer;font-weight:600;">
                            <?= date('Y') - 1 ?>
                        </button>
                    </div>
                </div>
                <div style="height:400px;">
                    <canvas id="kunjunganChart"></canvas>
                </div>
            </div>
        </div>

        <div>            <!-- Right Column -->
            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <!-- Per Layanan -->
                <div class="table-card">
                    <h2 style="margin-bottom:1.2rem;font-size:1rem;">Populer per Layanan</h2>
                    <?php if (empty($perLayanan)): ?>
                    <p style="color:#999;font-size:0.9rem;">Belum ada data.</p>
                    <?php else:
                        $maxTotal = max(array_column($perLayanan,'total')) ?: 1;
                        foreach ($perLayanan as $pl):
                            $pct = round(($pl['total'] / $maxTotal) * 100);
                    ?>
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                            <span style="font-weight:600;color:#333;"><?= htmlspecialchars($pl['nama_layanan']) ?></span>
                            <span style="color:#666;"><?= $pl['total'] ?></span>
                        </div>
                        <div style="height:6px;background:#f0f0f0;border-radius:4px;">
                            <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,var(--primary),#6C7FE8);border-radius:4px;transition:width 0.6s ease;"></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>

                <!-- Export Excel -->
                <button class="btn-primary" style="width:100%;padding:1rem;border:2px solid var(--primary);border-radius:10px;cursor:pointer;font-size:0.9rem;" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Ekspor Laporan Excel
                </button>
            </div>
        </div>                    
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="../../assets/js/main.js"></script>
<script>
const dataIni  = <?= json_encode(array_values($chartDataTahunIni)) ?>;
const dataLalu = <?= json_encode(array_values($chartDataTahunLalu)) ?>;
const perLayananData = <?= json_encode($perLayanan) ?>;
const labels   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
let chart;

function buildChart(data, yearLabel) {
    const ctx = document.getElementById('kunjunganChart').getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(45,58,140,0.35)');
    gradient.addColorStop(1, 'rgba(45,58,140,0.0)');

    if (chart) chart.destroy();
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Kunjungan ' + yearLabel,
                data,
                borderColor: '#2D3A8C',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2D3A8C',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, labels: { font:{ size:12 }, color:'#666' } },
                tooltip: { backgroundColor:'#2A2A3E', titleFont:{size:13}, bodyFont:{size:14,weight:'bold'}, padding:12, displayColors:false, cornerRadius:8 }
            },
            scales: {
                x: { grid:{display:false}, ticks:{font:{size:12},color:'#999'} },
                y: { beginAtZero:true, grid:{color:'#f0f0f0'}, ticks:{font:{size:12},color:'#999'} }
            },
            interaction: { intersect:false, mode:'index' }
        }
    });
}

function setChartYear(which) {
    const ini  = document.getElementById('btn-ini');
    const lalu = document.getElementById('btn-lalu');
    if (which === 'ini') {
        ini.style.background  = 'var(--primary)'; ini.style.color  = 'white'; ini.style.borderColor = 'var(--primary)';
        lalu.style.background = 'white';           lalu.style.color = '#666';  lalu.style.borderColor = '#ccc';
        buildChart(dataIni,  '<?= date("Y") ?>');
    } else {
        lalu.style.background = 'var(--primary)'; lalu.style.color = 'white'; lalu.style.borderColor = 'var(--primary)';
        ini.style.background  = 'white';           ini.style.color  = '#666';  ini.style.borderColor  = '#ccc';
        buildChart(dataLalu, '<?= date("Y")-1 ?>');
    }
}

function exportExcel() {
    const today = new Date();
    const filename = `Laporan_Statistik_UPA_TIK_${today.toISOString().slice(0,10)}.xls`;
    
    // Header Laporan
    let html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="UTF-8">
        <style>
            .title { font-size: 16pt; font-weight: bold; text-align: center; }
            .subtitle { font-size: 12pt; color: #666; text-align: center; }
            .header-cell { background-color: #2D3A8C; color: #ffffff; font-weight: bold; text-align: center; border: 0.5pt solid #000; }
            .data-cell { border: 0.5pt solid #ccc; text-align: center; }
            .label-cell { background-color: #f3f4f6; font-weight: bold; border: 0.5pt solid #ccc; }
            .num-cell { mso-number-format:"\#\,\#\#0"; border: 0.5pt solid #ccc; text-align: center; }
        </style>
    </head>
    <body>
        <table>
            <tr><td colspan="5" class="title">LAPORAN STATISTIK KUNJUNGAN UPA TIK</td></tr>
            <tr><td colspan="5" class="subtitle">Dicetak pada: ${today.toLocaleDateString('id-ID')} ${today.toLocaleTimeString('id-ID')}</td></tr>
            <tr><td></td></tr>
            
            <tr><td colspan="5" style="font-weight:bold; font-size:12pt; background:#e5e7eb;">1. RINGKASAN DATA</td></tr>
            <tr>
                <td class="header-cell">Total Kunjungan</td>
                <td class="header-cell">Total Selesai</td>
                <td class="header-cell">Total Pengguna</td>
                <td class="header-cell">Rata-rata Bulanan</td>
                <td class="header-cell">Rata-rata Tahunan</td>
            </tr>
            <tr>
                <td class="num-cell"><?= $totalKunjungan ?></td>
                <td class="num-cell"><?= $totalSelesai ?></td>
                <td class="num-cell"><?= $totalPengguna ?></td>
                <td class="num-cell"><?= $rataRataBulanan ?></td>
                <td class="num-cell"><?= $rataRataTahunan ?></td>
            </tr>
            <tr><td></td></tr>

            <tr><td colspan="13" style="font-weight:bold; font-size:12pt; background:#e5e7eb;">2. DATA KUNJUNGAN BULANAN (TAHUN <?= date('Y') ?>)</td></tr>
            <tr>
                <td class="header-cell">Kategori</td>
                ${labels.map(l => `<td class="header-cell">${l}</td>`).join('')}
            </tr>
            <tr>
                <td class="label-cell">Jumlah</td>
                ${dataIni.map(d => `<td class="num-cell">${d}</td>`).join('')}
            </tr>
            <tr><td></td></tr>

            <tr><td colspan="2" style="font-weight:bold; font-size:12pt; background:#e5e7eb;">3. POPULER PER LAYANAN</td></tr>
            <tr>
                <td class="header-cell" style="width:250px;">Nama Layanan</td>
                <td class="header-cell">Total Permintaan</td>
            </tr>
            ${perLayananData.map(item => `
                <tr>
                    <td class="data-cell" style="text-align:left; padding-left:5px;">${item.nama_layanan}</td>
                    <td class="num-cell">${item.total}</td>
                </tr>
            `).join('')}
        </table>
    </body>
    </html>`;

    const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', () => buildChart(dataIni, '<?= date("Y") ?>'));
</script>
</body>
</html>
