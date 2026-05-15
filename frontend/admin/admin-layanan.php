admin-layanan (1).php
<?php
require_once '../../includes/auth.php';
requireAdminLogin();

// ── Ambil daftar layanan dari tabel layanan ───────────────────────────────────
$layananList = [];
if ($conn) {
    $result = $conn->query("SELECT id_layanan, nama_layanan, detail_layanan, is_active FROM layanan ORDER BY id_layanan");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $layananList[] = $row;
        }
    }
} else {
    $layananList = [
        ['id_layanan'=>1,'nama_layanan'=>'SSO Email','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>2,'nama_layanan'=>'Reset Password','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>3,'nama_layanan'=>'Pemasangan VPN','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>4,'nama_layanan'=>'Keluhan IT','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>5,'nama_layanan'=>'Maintenance','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>6,'nama_layanan'=>'Instalasi Software','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>7,'nama_layanan'=>'Konsultasi IT','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>8,'nama_layanan'=>'Keamanan Siber','detail_layanan'=>'','is_active'=>1],
        ['id_layanan'=>9,'nama_layanan'=>'Jaringan & Infrastruktur','detail_layanan'=>'','is_active'=>1],
    ];
}

// ── Hitung jumlah formulir per layanan ──────────────────────────────────────
$formulirCount = [];
if ($conn) {
    $res = $conn->query(
        "SELECT id_layanan, COUNT(*) AS total FROM formulir_layanan GROUP BY id_layanan"
    );
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $formulirCount[$r['id_layanan']] = $r['total'];
        }
    }
}

$iconMap = [
    'SSO Email'                =>'📧','Reset Password'=>'🔑','Pemasangan VPN'=>'🔐',
    'Keluhan IT'               =>'⚡','Maintenance'=>'🎯','Instalasi Software'=>'💻',
    'Konsultasi IT'            =>'🌐','Keamanan Siber'=>'🛡️','Jaringan & Infrastruktur'=>'📡',
];

$title = 'Admin Layanan';
include '../../includes/header.php';
?>

<!-- Sidebar -->
<?php include '../../includes/admin-sidebar.php'; ?>

<!-- Main Content -->
<main class="admin-main">
    <div class="admin-container">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:12px;">
            <h1 class="page-title" style="margin-bottom:0;">Kelola Layanan UPA TIK</h1>
            <button class="btn-primary" onclick="openTambahModal()"
                    style="padding:0.8rem 1.5rem;border-radius:10px;border:2px solid var(--primary);cursor:pointer;font-size:0.9rem;">
                <i class="fas fa-plus"></i> Tambah Layanan
            </button>
        </div>

        <!-- Toast -->
        <div id="toastMsg" style="display:none;background:#059669;color:#fff;padding:12px 20px;border-radius:10px;margin-bottom:20px;font-weight:600;font-size:0.9rem;"></div>

        <!-- Layanan Cards dari database -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:2rem;" id="layananGrid">
            <?php foreach ($layananList as $l):
                $count    = $formulirCount[$l['id_layanan']] ?? 0;
                $icon     = $iconMap[$l['nama_layanan']] ?? '⚙️';
                $isActive = (int)($l['is_active'] ?? 1);
            ?>
            <div class="card" style="position:relative;border-top:3px solid <?= $isActive ? 'var(--primary)' : '#9ca3af' ?>;opacity:<?= $isActive ? '1' : '0.75' ?>;"
                 id="card-layanan-<?= $l['id_layanan'] ?>">

                <!-- Status Badge (klik untuk toggle) -->
                <button id="badge-<?= $l['id_layanan'] ?>"
                        onclick="toggleStatus(<?= $l['id_layanan'] ?>, <?= $isActive ?>)"
                        title="Klik untuk <?= $isActive ? 'menonaktifkan' : 'mengaktifkan' ?> layanan ini"
                        style="position:absolute;top:1.5rem;right:1.5rem;
                               background:<?= $isActive ? '#d4edda' : '#fde8e8' ?>;
                               color:<?= $isActive ? '#155724' : '#991b1b' ?>;
                               padding:0.4rem 0.8rem;border-radius:20px;font-size:0.75rem;font-weight:600;
                               border:none;cursor:pointer;transition:all 0.2s;
                               display:flex;align-items:center;gap:5px;"
                        onmouseover="this.style.filter='brightness(0.88)'"
                        onmouseout="this.style.filter=''"
                >
                    <i class="fas <?= $isActive ? 'fa-toggle-on' : 'fa-toggle-off' ?>" style="font-size:0.85rem;"></i>
                    <?= $isActive ? 'AKTIF' : 'NONAKTIF' ?>
                </button>

                <h3 style="font-size:1.1rem;color:var(--dark);margin-bottom:0.5rem;padding-right:6rem;">
                    <?= htmlspecialchars($l['nama_layanan']) ?>
                </h3>
                <p style="color:#999;font-size:0.85rem;margin-bottom:0.5rem;">
                    ID: #<?= $l['id_layanan'] ?>
                </p>
                <p style="color:#999;font-size:0.85rem;margin-bottom:1rem;min-height:48px;line-height:1.4;">
                    <?= htmlspecialchars($l['detail_layanan'] ? (mb_strlen($l['detail_layanan']) > 120 ? mb_substr($l['detail_layanan'],0,120).'...' : $l['detail_layanan']) : 'Tidak ada detail layanan.') ?>
                </p>
                <p style="color:#666;font-size:0.9rem;margin-bottom:1.5rem;">
                    <strong><?= $count ?></strong> formulir masuk
                </p>

                <div style="display:flex;gap:8px;padding-top:1rem;border-top:1px solid #f0f0f0;">
                    <!-- Edit -->
                    <button class="btn-action" style="padding: 0.5rem 1.5rem; justify-content: center;"
                            onclick='openEditLayananModal(<?= $l['id_layanan'] ?>, <?= json_encode($l['nama_layanan'], JSON_HEX_APOS|JSON_HEX_QUOT) ?>, <?= json_encode($l['detail_layanan'], JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<!-- ====== MODAL TAMBAH LAYANAN ====== -->
<div class="modal-overlay" id="modalTambahLayanan">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle" style="margin-right:0.5rem;"></i>Tambah Layanan Baru</h2>
            <button class="modal-close" onclick="closeTambahModal()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            <form id="tambahForm" onsubmit="submitTambah(event)">
                <div class="modal-field" style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;">Nama Layanan * <small style="color:#999;font-weight:400;">(maks. 50 karakter)</small></label>
                    <input type="text" id="tambah-nama" name="nama_layanan" maxlength="50"
                           placeholder="Contoh: SSO Email"
                           style="width:100%;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.95rem;outline:none;" required>
                </div>
                <div class="modal-field" style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;">Detail Layanan <small style="color:#999;font-weight:400;">(maks. 500 karakter)</small></label>
                    <textarea id="tambah-detail" name="detail_layanan" maxlength="500"
                              placeholder="Jelaskan layanan ini secara singkat..."
                              style="width:100%;min-height:120px;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.95rem;outline:none;resize:vertical;"></textarea>
                </div>
                <div id="tambah-error" style="color:#dc2626;font-size:0.88rem;margin-top:4px;display:none;"></div>
                <div class="modal-footer" style="margin-top:20px;">
                    <button type="button" class="modal-btn modal-btn-close" onclick="closeTambahModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="modal-btn modal-btn-done" id="tambahSaveBtn">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====== MODAL EDIT LAYANAN ====== -->
<div class="modal-overlay" id="modalEditLayanan">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2><i class="fas fa-edit" style="margin-right:0.5rem;"></i>Edit Layanan</h2>
            <button class="modal-close" onclick="closeEditLayananModal()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editLayananForm" onsubmit="submitEditLayanan(event)">
                <input type="hidden" id="edit-layanan-id" name="id_layanan">
                <div class="modal-field" style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;">Nama Layanan * <small style="color:#999;font-weight:400;">(maks. 50 karakter)</small></label>
                    <input type="text" id="edit-layanan-nama" name="nama_layanan" maxlength="50"
                           style="width:100%;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.95rem;outline:none;" required>
                </div>
                <div class="modal-field" style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;">Detail Layanan <small style="color:#999;font-weight:400;">(maks. 500 karakter)</small></label>
                    <textarea id="edit-detail" name="detail_layanan" maxlength="500"
                              style="width:100%;min-height:120px;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.95rem;outline:none;resize:vertical;"></textarea>
                </div>
                <div id="edit-layanan-error" style="color:#dc2626;font-size:0.88rem;margin-top:4px;display:none;"></div>
                <div class="modal-footer" style="margin-top:20px;">
                    <button type="button" class="modal-btn modal-btn-close" onclick="closeEditLayananModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="modal-btn modal-btn-done" id="editLayananSaveBtn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/js/main.js"></script>
<script>
const BASE_URL = '<?= BASE_URL ?>';

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, ok = true) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
        position:'fixed', bottom:'2rem', right:'2rem',
        background: ok ? '#059669' : '#dc2626', color:'#fff',
        padding:'0.85rem 1.5rem', borderRadius:'10px',
        fontWeight:'600', fontSize:'0.9rem',
        boxShadow:'0 8px 24px rgba(0,0,0,0.2)', zIndex:'9999'
    });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── TAMBAH ────────────────────────────────────────────────────────────────────
function openTambahModal() {
    document.getElementById('tambah-nama').value = '';
    document.getElementById('tambah-detail').value = '';
    document.getElementById('tambah-error').style.display = 'none';
    document.getElementById('modalTambahLayanan').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeTambahModal() {
    document.getElementById('modalTambahLayanan').classList.remove('active');
    document.body.style.overflow = '';
}
async function submitTambah(e) {
    e.preventDefault();
    const btn = document.getElementById('tambahSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const fd = new FormData(document.getElementById('tambahForm'));
    try {
        const res  = await fetch(BASE_URL + '/backend/layanan/tambah.php', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            closeTambahModal();
            showToast('✅ ' + data.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            const el = document.getElementById('tambah-error');
            el.textContent = data.message;
            el.style.display = 'block';
        }
    } catch { alert('Terjadi kesalahan jaringan.'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Tambah';
    }
}

// ── EDIT ──────────────────────────────────────────────────────────────────────
function openEditLayananModal(id, nama, detail) {
    document.getElementById('edit-layanan-id').value   = id;
    document.getElementById('edit-layanan-nama').value = nama;
    document.getElementById('edit-detail').value       = detail || '';
    document.getElementById('edit-layanan-error').style.display = 'none';
    document.getElementById('modalEditLayanan').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditLayananModal() {
    document.getElementById('modalEditLayanan').classList.remove('active');
    document.body.style.overflow = '';
}
async function submitEditLayanan(e) {
    e.preventDefault();
    const btn = document.getElementById('editLayananSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const fd = new FormData(document.getElementById('editLayananForm'));
    try {
        const res  = await fetch(BASE_URL + '/backend/layanan/edit.php', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            closeEditLayananModal();
            showToast('✅ ' + data.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            const el = document.getElementById('edit-layanan-error');
            el.textContent = data.message;
            el.style.display = 'block';
        }
    } catch { alert('Terjadi kesalahan jaringan.'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
}

// ── TOGGLE AKTIF / NONAKTIF ──────────────────────────────────────────────────
async function toggleStatus(id, isActive) {
    if (!confirm(`${isActive ? 'Nonaktifkan' : 'Aktifkan'} layanan ini?\nLayanan yang dinonaktifkan tidak akan tampil di halaman publik.`)) return;

    const fd = new FormData();
    fd.append('id_layanan', id);
    try {
        const res  = await fetch(BASE_URL + '/backend/layanan/toggle-status.php', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            const card   = document.getElementById('card-layanan-' + id);
            const badge  = document.getElementById('badge-' + id);
            const active = data.is_active === 1;

            // Update badge (badge IS tombol toggle sekarang)
            badge.innerHTML        = `<i class="fas ${active ? 'fa-toggle-on' : 'fa-toggle-off'}" style="font-size:0.85rem;"></i> ${active ? 'AKTIF' : 'NONAKTIF'}`;
            badge.style.background = active ? '#d4edda' : '#fde8e8';
            badge.style.color      = active ? '#155724' : '#991b1b';
            badge.title            = `Klik untuk ${active ? 'menonaktifkan' : 'mengaktifkan'} layanan ini`;
            badge.onclick          = () => toggleStatus(id, active ? 1 : 0);

            // Update card style
            card.style.borderTopColor = active ? 'var(--primary)' : '#9ca3af';
            card.style.opacity        = active ? '1' : '0.75';

            showToast('✅ ' + data.message);
        } else {
            showToast('❌ ' + data.message, false);
        }
    } catch { alert('Terjadi kesalahan jaringan.'); }
}



// Close modal on overlay click / Escape
['modalTambahLayanan','modalEditLayanan'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active'), document.body.style.overflow = '';
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeTambahModal();
        closeEditLayananModal();
    }
});
</script>
</body>
</html>