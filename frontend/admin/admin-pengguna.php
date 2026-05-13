<?php
require_once '../../includes/auth.php';
requireAdminLogin();

// ── Ambil daftar pengguna dari akun_pengguna ─────────────────────────────────
$penggunaList = [];
$search = trim($_GET['q'] ?? '');

if ($conn) {
    // Pastikan kolom is_active ada
    $colCheck = $conn->query("SHOW COLUMNS FROM akun_pengguna LIKE 'is_active'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE akun_pengguna ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`");
    }

    if ($search) {
        $like = "%$search%";
        $stmt = $conn->prepare(
            "SELECT id_pengguna, nama_lengkap, nim_nip, email, status,
                    COALESCE(is_active,1) AS is_active
             FROM akun_pengguna
             WHERE nama_lengkap LIKE ? OR nim_nip LIKE ? OR email LIKE ?
             ORDER BY nama_lengkap"
        );
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query(
            "SELECT id_pengguna, nama_lengkap, nim_nip, email, status,
                    COALESCE(is_active,1) AS is_active
             FROM akun_pengguna ORDER BY nama_lengkap"
        );
    }
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $penggunaList[] = $row;
        }
    }
} else {
    // Data demo
    $penggunaList = [
        ['id_pengguna'=>1,'nama_lengkap'=>'Budiono Siregar',  'nim_nip'=>'E31180025','email'=>'budiono@gmail.com',   'status'=>'mahasiswa','is_active'=>1],
        ['id_pengguna'=>2,'nama_lengkap'=>'Layla Ismah Caren','nim_nip'=>'E31133784','email'=>'layla@polije.ac.id',  'status'=>'mahasiswa','is_active'=>1],
        ['id_pengguna'=>3,'nama_lengkap'=>'Satrio Pandu',     'nim_nip'=>'E31332784','email'=>'satrio@polije.ac.id', 'status'=>'dosen',    'is_active'=>1],
        ['id_pengguna'=>4,'nama_lengkap'=>'M.M Benedicta',   'nim_nip'=>'198765321', 'email'=>'mmb@polije.ac.id',   'status'=>'dosen',    'is_active'=>0],
        ['id_pengguna'=>5,'nama_lengkap'=>'Reza Auditore',    'nim_nip'=>'E31180093','email'=>'reza@gmail.com',      'status'=>'mahasiswa','is_active'=>1],
    ];
}


$title = 'Admin Pengguna';
include '../../includes/header.php';
?>

<!-- Sidebar -->
<?php include '../../includes/admin-sidebar.php'; ?>

<!-- Main Content -->
<main class="admin-main">
    <div class="admin-container">

        <h1 class="page-title">Daftar Pengguna</h1>

        <!-- Users Table -->
        <div class="table-card">
            <div class="table-header">
                <h2>Kelola Pengguna Sistem
                    <span style="font-size:0.85rem;font-weight:500;color:#999;margin-left:8px;">
                        (<?= count($penggunaList) ?> pengguna)
                    </span>
                </h2>
                <form method="GET" action="admin-pengguna.php">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="Cari nama / NIM / email..."
                            value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <div class="user-list" id="penggunaList">
                <?php if (empty($penggunaList)): ?>
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-users" style="font-size:3rem;margin-bottom:16px;display:block;"></i>
                    <?= $search ? 'Tidak ada pengguna ditemukan.' : 'Belum ada pengguna terdaftar.' ?>
                </div>
                <?php else: foreach ($penggunaList as $p):
                    $initials = strtoupper(mb_substr($p['nama_lengkap'], 0, 1));
                    $colors = ['#f59e0b','#ec4899','#10b981','#8b5cf6','#f43f5e','#06b6d4','#3b82f6'];
                    $color  = $colors[crc32($p['nama_lengkap']) % count($colors)];
                    $isActive = (int)($p['is_active'] ?? 1);
                ?>
                <div class="user-item" id="pengguna-row-<?= $p['id_pengguna'] ?>"
                     style="<?= !$isActive ? 'opacity:0.55;' : '' ?>">
                    <div class="user-avatar"
                        style="background:linear-gradient(135deg,<?= $color ?> 0%,<?= $color ?>aa 100%);">
                        <?= $initials ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($p['nama_lengkap']) ?></div>
                        <div class="user-role">
                            <?= $statusBadge[$p['status']] ?? ucfirst($p['status']) ?>
                            &mdash; <?= htmlspecialchars($p['nim_nip']) ?>
                            &mdash; <?= htmlspecialchars($p['email']) ?>
                        </div>
                    </div>
                    <div class="user-status <?= $isActive ? 'status-active' : '' ?>"
                         id="status-label-<?= $p['id_pengguna'] ?>">
                        <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                    </div>
                    <div class="user-actions">
                        <button class="btn-action" type="button"
                            onclick="openHistoryModal(<?= (int)$p['id_pengguna'] ?>, <?= htmlspecialchars(json_encode($p['nama_lengkap']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($p['nim_nip']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($p['email']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($p['status']), ENT_QUOTES) ?>)">
                            <i class="fas fa-history"></i> Riwayat
                        </button>
                        <button class="btn-action" type="button"
                            onclick="openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-action" type="button"
                            id="toggle-btn-<?= $p['id_pengguna'] ?>"
                            onclick="toggleStatus(<?= $p['id_pengguna'] ?>, <?= $isActive ?>)"
                            style="<?= !$isActive ? 'background:#059669;color:white;' : 'background:#dc2626;color:white;' ?>">
                            <i class="fas fa-<?= $isActive ? 'ban' : 'check' ?>"></i>
                            <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>

            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f0f0f0;">
                <span style="color:#999;font-size:0.9rem;">
                    Total <?= count($penggunaList) ?> pengguna terdaftar
                </span>
            </div>
        </div>

    </div>
</main>

<!-- ====== MODAL EDIT PENGGUNA ====== -->
<div class="modal-overlay" id="modalEditPengguna">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2><i class="fas fa-user-edit" style="margin-right:0.5rem;"></i>Edit Pengguna</h2>
            <button class="modal-close" onclick="closeEditModal()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" onsubmit="submitEdit(event)">
                <input type="hidden" id="edit-id" name="id_pengguna">
                <div class="modal-field-grid">
                    <div class="modal-field" style="grid-column:1/-1;">
                        <label>Nama Lengkap *</label>
                        <input type="text" id="edit-nama" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="modal-field">
                        <label>NIM / NIP *</label>
                        <input type="text" id="edit-nim" name="nim_nip" class="form-control" required>
                    </div>
                    <div class="modal-field">
                        <label>Email *</label>
                        <input type="email" id="edit-email" name="email" class="form-control" required>
                    </div>
                    <div class="modal-field">
                        <label>Status</label>
                        <select id="edit-status" name="status" class="form-control">
                            <option value="mahasiswa">🎓 Mahasiswa</option>
                            <option value="dosen">👨‍🏫 Dosen / Staff</option>
                        </select>
                    </div>
                </div>
                <div id="edit-error" style="color:#dc2626;font-size:0.88rem;margin-top:8px;display:none;"></div>
                <div class="modal-footer" style="margin-top:20px;">
                    <button type="button" class="modal-btn modal-btn-close" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="modal-btn modal-btn-done" id="editSaveBtn">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====== MODAL RIWAYAT PENGGUNA ====== -->
<div class="modal-overlay" id="modalHistoryPengguna">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalHistoryTitle">
        <div class="modal-header">
            <h2 id="modalHistoryTitle"><i class="fas fa-history" style="margin-right:0.5rem;"></i>Riwayat Pengguna</h2>
            <button class="modal-close" onclick="closeHistoryModal()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            <p class="modal-section-title">Informasi Akun</p>
            <div class="modal-field-grid">
                <div class="modal-field">
                    <label>Nama Lengkap</label>
                    <span id="hist-nama">—</span>
                </div>
                <div class="modal-field">
                    <label>NIM / NIP</label>
                    <span id="hist-nim">—</span>
                </div>
                <div class="modal-field">
                    <label>Email</label>
                    <span id="hist-email">—</span>
                </div>
                <div class="modal-field">
                    <label>Status</label>
                    <span id="hist-status">—</span>
                </div>
            </div>

            <p class="modal-section-title">Riwayat Kunjungan</p>
            <div id="historyLoading" style="color:#6b7280;padding:1rem 0;">Memuat riwayat...</div>
            <div id="historyError" style="display:none;color:#dc2626;margin-bottom:1rem;"></div>
            <div id="historyEmpty" style="display:none;color:#374151;margin-bottom:1rem;">Belum ada riwayat kunjungan untuk pengguna ini.</div>
            <div id="historyContent" style="display:none;overflow-x:auto;">
                <table class="table" style="width:100%;margin-top:0.75rem;">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:0.75rem 0.5rem;">Tanggal</th>
                            <th style="text-align:left;padding:0.75rem 0.5rem;">Layanan</th>
                            <th style="text-align:left;padding:0.75rem 0.5rem;">Detail</th>
                            <th style="text-align:left;padding:0.75rem 0.5rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyRows"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-close" type="button" onclick="closeHistoryModal()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

<script src="../../assets/js/main.js"></script>
<script>
// ── Edit Modal ────────────────────────────────────────────────────────────────
function openEditModal(p) {
    document.getElementById('edit-id').value     = p.id_pengguna;
    document.getElementById('edit-nama').value   = p.nama_lengkap;
    document.getElementById('edit-nim').value    = p.nim_nip;
    document.getElementById('edit-email').value  = p.email;
    document.getElementById('edit-status').value = p.status;
    document.getElementById('edit-error').style.display = 'none';
    document.getElementById('modalEditPengguna').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('modalEditPengguna').classList.remove('active');
    document.body.style.overflow = '';
}

async function submitEdit(e) {
    e.preventDefault();
    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const fd = new FormData(document.getElementById('editForm'));

    try {
        const res  = await fetch('<?= BASE_URL ?>/backend/pengguna/edit.php', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            closeEditModal();
            showToast('✅ ' + data.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            const errEl = document.getElementById('edit-error');
            errEl.textContent = data.message;
            errEl.style.display = 'block';
        }
    } catch(err) {
        alert('Terjadi kesalahan jaringan.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
    }
}

// ── Toggle Status ─────────────────────────────────────────────────────────────
async function toggleStatus(id, isActive) {
    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    if (!confirm(`Yakin ingin ${action} akun ini?`)) return;

    const fd = new FormData();
    fd.append('id_pengguna', id);

    try {
        const res  = await fetch('<?= BASE_URL ?>/backend/pengguna/toggle-status.php', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            showToast('✅ ' + data.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch(err) {
        alert('Terjadi kesalahan jaringan.');
    }
}

function openHistoryModal(id, nama, nim, email, status) {
    document.getElementById('hist-nama').textContent   = nama || '—';
    document.getElementById('hist-nim').textContent    = nim || '—';
    document.getElementById('hist-email').textContent  = email || '—';
    document.getElementById('hist-status').textContent = status || '—';
    document.getElementById('historyRows').innerHTML    = '';
    document.getElementById('historyLoading').style.display = '';
    document.getElementById('historyError').style.display  = 'none';
    document.getElementById('historyContent').style.display = 'none';
    document.getElementById('historyEmpty').style.display   = 'none';
    document.getElementById('modalHistoryPengguna').classList.add('active');
    document.body.style.overflow = 'hidden';
    loadHistory(id);
}

function closeHistoryModal() {
    document.getElementById('modalHistoryPengguna').classList.remove('active');
    document.body.style.overflow = '';
}

async function loadHistory(userId) {
    try {
        const res = await fetch('<?= BASE_URL ?>/backend/pengguna/history.php?id=' + encodeURIComponent(userId));
        const data = await res.json();
        document.getElementById('historyLoading').style.display = 'none';
        if (!data.success) {
            document.getElementById('historyError').textContent = data.message || 'Gagal memuat riwayat.';
            document.getElementById('historyError').style.display = '';
            return;
        }
        const rows = data.history || [];
        if (rows.length === 0) {
            document.getElementById('historyEmpty').style.display = '';
            return;
        }
        const tbody = document.getElementById('historyRows');
        for (const item of rows) {
            const date = new Date(item.tanggal_isi);
            const row  = document.createElement('tr');
            row.innerHTML = `
                <td style="padding:0.75rem 0.5rem;white-space:nowrap;font-size:0.95rem;">${date.toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })}</td>
                <td style="padding:0.75rem 0.5rem;font-size:0.95rem;">${escapeHtml(item.nama_layanan || '–')}</td>
                <td style="padding:0.75rem 0.5rem;font-size:0.95rem;max-width:260px;white-space:normal;word-break:break-word;">${escapeHtml(item.detail_layanan || '–')}</td>
                <td style="padding:0.75rem 0.5rem;font-size:0.95rem;">${escapeHtml(item.status_layanan || '–')}</td>
            `;
            tbody.appendChild(row);
        }
        document.getElementById('historyContent').style.display = '';
    } catch (err) {
        document.getElementById('historyLoading').style.display = 'none';
        document.getElementById('historyError').textContent = 'Terjadi kesalahan jaringan saat memuat riwayat.';
        document.getElementById('historyError').style.display = '';
    }
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
        position:'fixed', bottom:'2rem', right:'2rem',
        background:'#059669', color:'#fff',
        padding:'0.85rem 1.5rem', borderRadius:'10px',
        fontWeight:'600', fontSize:'0.9rem',
        boxShadow:'0 8px 24px rgba(5,150,105,0.3)', zIndex:'9999'
    });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

['modalEditPengguna','modalHistoryPengguna'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('click', function(e) {
        if (e.target === this) {
            if (id === 'modalEditPengguna') closeEditModal();
            if (id === 'modalHistoryPengguna') closeHistoryModal();
        }
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeEditModal();
        closeHistoryModal();
    }
});
</script>
</body>
</html>