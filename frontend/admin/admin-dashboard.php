<?php
require_once '../../includes/auth.php';
requireAdminLogin();


// ── Ambil statistik dari DB ──────────────────────────────────────────────────
$totalPending  = 0;
$totalSelesai  = 0;
$totalPengguna = 0;
$totalLayanan  = 0;
$antrean       = [];

if ($conn) {
    $r = $conn->query("SELECT COUNT(*) AS n FROM formulir_layanan WHERE status_layanan = 'menunggu'");
    $totalPending  = $r ? $r->fetch_assoc()['n'] : 0;

    $r = $conn->query("SELECT COUNT(*) AS n FROM formulir_layanan WHERE status_layanan = 'selesai'");
    $totalSelesai  = $r ? $r->fetch_assoc()['n'] : 0;

    $r = $conn->query("SELECT COUNT(*) AS n FROM akun_pengguna");
    $totalPengguna = $r ? $r->fetch_assoc()['n'] : 0;

    $r = $conn->query("SELECT COUNT(*) AS n FROM layanan");
    $totalLayanan  = $r ? $r->fetch_assoc()['n'] : 0;

    // Antrean terbaru — join dengan akun_pengguna dan layanan
    $res = $conn->query(
        "SELECT fl.id_formulir, fl.tanggal_isi, fl.detail_layanan, fl.status_layanan,
                l.nama_layanan,
                p.nama_lengkap, p.nim_nip, p.email, p.status AS peran
         FROM formulir_layanan fl
         JOIN layanan l ON fl.id_layanan = l.id_layanan
         JOIN akun_pengguna p ON fl.id_pengguna = p.id_pengguna
         ORDER BY fl.tanggal_isi DESC
         LIMIT 10"
    );
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $antrean[] = $row;
        }
    }
} else {
    // Data demo
    $totalPending  = 12;
    $totalSelesai  = 96;
    $totalPengguna = 342;
    $totalLayanan  = 9;
    $antrean = [
        ['id_formulir'=>1,'nama_layanan'=>'SSO Email','nama_lengkap'=>'Budiono Siregar','nim_nip'=>'E31180025','email'=>'budiono@gmail.com','peran'=>'mahasiswa','status_layanan'=>'menunggu','tanggal_kunjungan'=>date('Y-m-d'),'detail_layanan'=>'Tidak bisa login ke akun SSO email kampus.'],
        ['id_formulir'=>2,'nama_layanan'=>'Reset Password','nama_lengkap'=>'Layla Ismah Caren','nim_nip'=>'E31133784','email'=>'layla@polije.ac.id','peran'=>'mahasiswa','status_layanan'=>'diproses','tanggal_kunjungan'=>date('Y-m-d'),'detail_layanan'=>'Lupa password akun Polije.'],
        ['id_formulir'=>3,'nama_layanan'=>'Pemasangan VPN','nama_lengkap'=>'Satrio Pandu','nim_nip'=>'E31332784','email'=>'satrio@polije.ac.id','peran'=>'dosen','status_layanan'=>'menunggu','tanggal_kunjungan'=>date('Y-m-d'),'detail_layanan'=>'Membutuhkan bantuan pemasangan VPN.'],
    ];
}

// Status badge helper
function statusBadgeAdmin(string $status): string {
    $map  = ['menunggu' => 'status-active', 'diproses' => 'status-active', 'selesai' => ''];
    $label= ['menunggu' => 'Menunggu', 'diproses' => 'Sedang Diproses', 'selesai' => 'Selesai'];
    $cls  = $map[$status]   ?? '';
    $txt  = $label[$status] ?? ucfirst($status);
    return "<div class=\"user-status $cls\">$txt</div>";
}

$title = 'Admin Dashboard';
include '../../includes/header.php';

?>

<!-- Sidebar -->
<?php include '../../includes/admin-sidebar.php'; ?>

<!-- Main Content -->
<main class="admin-main">
    <div class="admin-container">

        <h1 class="page-title">Dashboard</h1>

        <!-- TOP CARDS -->
        <div class="dashboard-cards">
            <!-- Card 1 - Total Pending -->
            <div class="card">
                <div class="card-header">Total Pending</div>
                <div class="card-value" style="color:#d97706;"><?= $totalPending ?></div>
                <div class="card-desc">Formulir menunggu tindakan</div>
                <div class="card-footer">
                </div>
            </div>

            <!-- Card 2 - Total Selesai -->
            <div class="card">
                <div class="card-header">Total Selesai</div>
                <div class="card-value" style="color:#059669;"><?= $totalSelesai ?></div>
                <div class="card-desc">Formulir sudah diselesaikan</div>
                <div class="card-footer">
                </div>
            </div>

            <!-- Card 3 - Total Pengguna -->
            <div class="card">
                <div class="card-header">Total Pengguna</div>
                <div class="card-value"><?= $totalPengguna ?></div>
                <div class="card-desc">Pengguna terdaftar</div>
                <div class="card-footer">
                    <a href="admin-pengguna.php" class="card-link">
                        Kelola Pengguna <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 4 - Layanan Aktif -->
            <div class="card">
                <div class="card-header">Layanan Aktif</div>
                <div class="card-value" style="color:#be185d;"><?= $totalLayanan ?></div>
                <div class="card-desc">Jenis layanan tersedia</div>
                <div class="card-footer">
                    <a href="admin-layanan.php" class="card-link">
                        Kelola Layanan <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- ANTREAN TABLE -->
        <div class="table-card">
            <div class="table-header">
                <h2>Antrean Formulir Terbaru</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchAntrean" placeholder="Cari antrean..." oninput="filterAntrean(this.value)">
                </div>
            </div>

            <div class="user-list" id="antreanList">
                <?php if (empty($antrean)): ?>
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:16px;display:block;"></i>
                    Belum ada formulir masuk.
                </div>
                <?php else: foreach ($antrean as $a):
                    $initials = strtoupper(mb_substr($a['nama_lengkap'], 0, 1));
                    $colors = ['#f59e0b','#ec4899','#10b981','#8b5cf6','#f43f5e','#06b6d4','#3b82f6'];
                    $color  = $colors[crc32($a['nama_lengkap']) % count($colors)];
                    $statusLabel = ['menunggu'=>'Menunggu','diproses'=>'Sedang Diproses','selesai'=>'Selesai'];
                    $tglIsi = date('d/m/Y H:i', strtotime($a['tanggal_isi']));
                ?>
                <div class="user-item antrean-row" onclick="openDetailModal(this)"
                    data-id="<?= $a['id_formulir'] ?>"
                    data-nama="<?= htmlspecialchars($a['nama_lengkap']) ?>"
                    data-nim="<?= htmlspecialchars($a['nim_nip']) ?>"
                    data-email="<?= htmlspecialchars($a['email']) ?>"
                    data-layanan="<?= htmlspecialchars($a['nama_layanan']) ?>"
                    data-peran="<?= htmlspecialchars($a['peran']) ?>"
                    data-status="<?= htmlspecialchars($statusLabel[$a['status_layanan']] ?? $a['status_layanan']) ?>"
                    data-tgl="<?= htmlspecialchars($tglIsi) ?>"
                    data-deskripsi="<?= htmlspecialchars($a['detail_layanan']) ?>">

                    <div class="user-avatar" style="background: linear-gradient(135deg, <?= $color ?> 0%, <?= $color ?>aa 100%);">
                        <?= $initials ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($a['nama_lengkap']) ?></div>
                        <div class="user-role"><?= htmlspecialchars($a['nama_layanan']) ?> (<?= ucfirst($a['peran']) ?>)</div>
                    </div>
                    <div class="user-status status-<?= $a['status_layanan'] ?>">
                        <?= $statusLabel[$a['status_layanan']] ?? ucfirst($a['status_layanan']) ?>
                    </div>
                    <div class="user-actions">
                        <button class="btn-action btn-primary" onclick="event.stopPropagation(); tandaiSelesai(this, <?= $a['id_formulir'] ?>)">
                            <i class="fas fa-check"></i> Selesai
                        </button>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
                <a href="admin-pengguna.php" class="card-link">
                    Kelola Semua Pengguna <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- ====== MODAL DETAIL PERMINTAAN ====== -->
        <div class="modal-overlay" id="modalDetailPermintaan">
            <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalDetailTitle">
                <div class="modal-header">
                    <h2 id="modalDetailTitle"><i class="fas fa-clipboard-list" style="margin-right:0.5rem;"></i>Detail Permintaan</h2>
                    <button class="modal-close" onclick="closeDetailModal()" aria-label="Tutup">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="modal-section-title">Informasi Pengguna</p>
                    <div class="modal-field-grid">
                        <div class="modal-field">
                            <label>Nama Lengkap</label>
                            <span id="md-nama">—</span>
                        </div>
                        <div class="modal-field">
                            <label>NIM/NIP</label>
                            <span id="md-nim">—</span>
                        </div>
                        <div class="modal-field">
                            <label>Email</label>
                            <span id="md-email">—</span>
                        </div>
                        <div class="modal-field">
                            <label>Jenis Layanan</label>
                            <span id="md-layanan">—</span>
                        </div>
                        <div class="modal-field">
                            <label>Status Peran</label>
                            <span id="md-peran">—</span>
                        </div>
                        <div class="modal-field">
                            <label>Tanggal Daftar</label>
                            <span id="md-tgl">—</span>
                        </div>
                    </div>

                    <label class="modal-desc-label">Deskripsi / Detail Layanan</label>
                    <textarea class="modal-desc-box" id="md-deskripsi" readonly></textarea>

                    <div class="modal-footer">
                        <button class="modal-btn modal-btn-close" onclick="closeDetailModal()">
                            <i class="fas fa-times"></i> Tutup
                        </button>
                        <button class="modal-btn" onclick="tandaiDiprosesModal()"
                            style="background:#3b82f6;color:#fff;border:none;border-radius:10px;padding:10px 20px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-cog"></i> Diproses
                        </button>
                        <button class="modal-btn modal-btn-done" onclick="tandaiSelesaiModal()">
                            <i class="fas fa-check-circle"></i> Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>


<script src="../../assets/js/main.js"></script>
<script>
    let currentRow = null;

    function filterAntrean(val) {
        const rows = document.querySelectorAll('.antrean-row');
        rows.forEach(r => {
            const text = r.dataset.nama + r.dataset.layanan + r.dataset.nim;
            r.style.display = text.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
        });
    }

    function openDetailModal(row) {
        currentRow = row;
        document.getElementById('md-nama').textContent    = row.dataset.nama      || '—';
        document.getElementById('md-nim').textContent     = row.dataset.nim       || '—';
        document.getElementById('md-email').textContent   = row.dataset.email     || '—';
        document.getElementById('md-layanan').textContent = row.dataset.layanan   || '—';
        document.getElementById('md-peran').textContent   = row.dataset.peran     || '—';
        document.getElementById('md-tgl').textContent     = row.dataset.tgl       || '—';
        document.getElementById('md-deskripsi').value     = row.dataset.deskripsi || '';
        // Simpan id formulir ke currentRow agar dapat diakses saat submit
        currentRow._formulirId = row.dataset.id;
        document.getElementById('modalDetailPermintaan').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        document.getElementById('modalDetailPermintaan').classList.remove('active');
        document.body.style.overflow = '';
        currentRow = null;
    }

    function balasEmailModal() {
        const email   = document.getElementById('md-email').textContent;
        const nama    = document.getElementById('md-nama').textContent;
        const layanan = document.getElementById('md-layanan').textContent;
        window.location.href = 'mailto:' + email +
            '?subject=Balasan Permintaan ' + encodeURIComponent(layanan) + ' - UPA TIK POLIJE' +
            '&body=Yth. ' + encodeURIComponent(nama) + ',%0D%0A%0D%0ATerima kasih telah menghubungi layanan UPA TIK POLIJE.%0D%0A%0D%0A[Isi balasan di sini]%0D%0A%0D%0AHormat kami,%0D%0AAdmin UPA TIK POLIJE';
    }

    function balasEmail(email) {
        window.location.href = 'mailto:' + email + '?subject=Informasi Layanan UPA TIK POLIJE';
    }

    async function tandaiSelesaiModal() {
        if (!currentRow) return;
        const id = currentRow.dataset.id;
        if (!id) { alert('ID formulir tidak ditemukan.'); return; }
        await updateStatus(id, 'selesai', currentRow);
        closeDetailModal();
    }

    async function tandaiDiprosesModal() {
        if (!currentRow) return;
        const id = currentRow.dataset.id;
        if (!id) { alert('ID formulir tidak ditemukan.'); return; }
        await updateStatus(id, 'diproses', currentRow);
        closeDetailModal();
    }

    async function tandaiSelesai(btn, idFormulir) {
        const row = btn.closest('.user-item');
        await updateStatus(idFormulir, 'selesai', row);
    }

    async function updateStatus(idFormulir, status, row) {
        const label = {selesai:'selesai',diproses:'sedang diproses',menunggu:'menunggu'};
        if (!confirm('Tandai formulir #' + idFormulir + ' sebagai ' + label[status] + '?')) return;
        const fd = new FormData();
        fd.append('id_formulir', idFormulir);
        fd.append('status', status);
        try {
            const res  = await fetch('<?= BASE_URL ?>/backend/formulir/update-status.php', {method:'POST',body:fd});
            const data = await res.json();
            if (data.success) {
                const statusEl = row ? row.querySelector('.user-status') : null;
                const labelMap = {selesai:'Selesai',diproses:'Sedang Diproses',menunggu:'Menunggu'};
                if (statusEl) {
                    statusEl.textContent = labelMap[status] ?? status;
                    statusEl.className   = 'user-status' + (status !== 'selesai' ? ' status-active' : '');
                }
                showToast('✓ Status berhasil diperbarui: ' + (labelMap[status] ?? status));
            } else {
                alert('Gagal: ' + data.message);
            }
        } catch(e) {
            alert('Terjadi kesalahan jaringan.');
        }
    }

    function showToast(msg) {
        const t = document.createElement('div');
        t.textContent = msg;
        Object.assign(t.style, {
            position:'fixed', bottom:'2rem', right:'2rem',
            background:'#059669', color:'#fff',
            padding:'0.85rem 1.5rem', borderRadius:'10px',
            fontWeight:'600', fontSize:'0.9rem',
            boxShadow:'0 8px 24px rgba(5,150,105,0.3)',
            zIndex:'9999'
        });
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    document.getElementById('modalDetailPermintaan').addEventListener('click', function(e) {
        if (e.target === this) closeDetailModal();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetailModal(); });
</script>
</body>
</html>
