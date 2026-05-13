<?php
/**
 * backend/pengguna/edit.php
 * API: edit nama, nim, email, status pengguna
 * POST: id_pengguna, nama_lengkap, nim_nip, email, status
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Akses ditolak.']);
    exit;
}

$id    = intval(  $_POST['id_pengguna']  ?? 0);
$nama  = trim(    $_POST['nama_lengkap'] ?? '');
$nim   = trim(    $_POST['nim_nip']      ?? '');
$email = trim(    $_POST['email']        ?? '');
$status= trim(    $_POST['status']       ?? '');

$allowed = ['mahasiswa','dosen'];

if (!$id || empty($nama) || empty($nim) || empty($email) || !in_array($status,$allowed)) {
    echo json_encode(['success'=>false,'message'=>'Data tidak lengkap atau tidak valid.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Format email tidak valid.']);
    exit;
}
if (!$conn) {
    echo json_encode(['success'=>false,'message'=>'Koneksi database gagal.']);
    exit;
}

// Cek duplikat nim/email (selain user ini sendiri)
$chk = $conn->prepare(
    "SELECT id_pengguna FROM akun_pengguna
     WHERE (nim_nip = ? OR email = ?) AND id_pengguna != ?"
);
$chk->bind_param('ssi', $nim, $email, $id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close();
    echo json_encode(['success'=>false,'message'=>'NIM/NIP atau Email sudah digunakan pengguna lain.']);
    exit;
}
$chk->close();

$upd = $conn->prepare(
    "UPDATE akun_pengguna SET nama_lengkap=?, nim_nip=?, email=?, status=? WHERE id_pengguna=?"
);
$upd->bind_param('ssssi', $nama, $nim, $email, $status, $id);
$ok = $upd->execute();
$upd->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Data pengguna berhasil diperbarui.' : 'Gagal: '.$conn->error,
]);
