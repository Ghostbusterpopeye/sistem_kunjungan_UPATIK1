<?php
/**
 * backend/pengguna/toggle-status.php
 * API: nonaktifkan / aktifkan akun pengguna
 * POST: id_pengguna (int)
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Akses ditolak.']);
    exit;
}

$id = intval($_POST['id_pengguna'] ?? 0);
if (!$id || !$conn) {
    echo json_encode(['success'=>false,'message'=>'Parameter tidak valid atau DB error.']);
    exit;
}

// Cek status sekarang
$chk = $conn->prepare("SELECT status FROM akun_pengguna WHERE id_pengguna = ?");
$chk->bind_param('i', $id);
$chk->execute();
$res = $chk->get_result();
$row = $res->fetch_assoc();
$chk->close();

if (!$row) {
    echo json_encode(['success'=>false,'message'=>'Pengguna tidak ditemukan.']);
    exit;
}

// NOTE: tabel akun_pengguna tidak punya kolom is_active, kita tambah via ALTER jika belum ada
// Untuk sekarang, kita hapus akun (soft delete) — atau kita tambahkan kolom is_active dulu
// Solusi: UPDATE nama_lengkap dengan prefix [NONAKTIF] sebagai flag, atau:
// Kita buat kolom is_active dengan default 1
// Karena tidak bisa ALTER table dari sini tanpa hak, kita hapus pengguna jika confirm

// Cek apakah sudah ada kolom is_active
$colCheck = $conn->query("SHOW COLUMNS FROM akun_pengguna LIKE 'is_active'");
if ($colCheck && $colCheck->num_rows === 0) {
    // Tambahkan kolom is_active
    $conn->query("ALTER TABLE akun_pengguna ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`");
}

// Baca is_active saat ini
$stmtCur = $conn->prepare("SELECT COALESCE(is_active,1) AS aktif FROM akun_pengguna WHERE id_pengguna = ?");
$stmtCur->bind_param('i', $id);
$stmtCur->execute();
$cur = $stmtCur->get_result()->fetch_assoc();
$stmtCur->close();

$newVal = ($cur['aktif'] == 1) ? 0 : 1;
$label  = $newVal ? 'diaktifkan' : 'dinonaktifkan';

$upd = $conn->prepare("UPDATE akun_pengguna SET is_active = ? WHERE id_pengguna = ?");
$upd->bind_param('ii', $newVal, $id);
$ok = $upd->execute();
$upd->close();

echo json_encode([
    'success'   => $ok,
    'is_active' => $newVal,
    'message'   => $ok ? "Akun berhasil $label." : 'Gagal: '.$conn->error,
]);
