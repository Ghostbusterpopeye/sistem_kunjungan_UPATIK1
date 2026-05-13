<?php
/**
 * backend/layanan/tambah.php
 * API: tambah layanan baru
 * POST: nama_layanan (string)
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Akses ditolak.']);
    exit;
}

$nama   = trim($_POST['nama_layanan'] ?? '');
$detail = trim($_POST['detail_layanan'] ?? '');
if (empty($nama) || strlen($nama) > 50) {
    echo json_encode(['success'=>false,'message'=>'Nama layanan wajib diisi dan maksimal 50 karakter.']);
    exit;
}
if (strlen($detail) > 500) {
    echo json_encode(['success'=>false,'message'=>'Detail layanan maksimal 500 karakter.']);
    exit;
}
if (!$conn) {
    echo json_encode(['success'=>false,'message'=>'Koneksi database gagal.']);
    exit;
}

// Cek duplikat
$chk = $conn->prepare("SELECT id_layanan FROM layanan WHERE nama_layanan = ?");
$chk->bind_param('s', $nama);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close();
    echo json_encode(['success'=>false,'message'=>'Layanan dengan nama tersebut sudah ada.']);
    exit;
}
$chk->close();

$ins = $conn->prepare("INSERT INTO layanan (nama_layanan, detail_layanan) VALUES (?, ?)");
$ins->bind_param('ss', $nama, $detail);
$ok  = $ins->execute();
$newId = $conn->insert_id;
$ins->close();

echo json_encode([
    'success'    => $ok,
    'id_layanan' => $newId,
    'message'    => $ok ? 'Layanan berhasil ditambahkan.' : 'Gagal: '.$conn->error,
]);