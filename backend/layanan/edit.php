edit (1).php
<?php
/**
 * backend/layanan/edit.php
 * API: edit nama layanan
 * POST: id_layanan (int), nama_layanan (string)
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Akses ditolak.']);
    exit;
}

$id     = intval(trim($_POST['id_layanan']   ?? 0));
$nama   = trim(       $_POST['nama_layanan'] ?? '');
$detail = trim(       $_POST['detail_layanan'] ?? '');

if (!$id || empty($nama) || strlen($nama) > 50 || strlen($detail) > 500) {
    echo json_encode(['success'=>false,'message'=>'Data tidak valid.']);
    exit;
}


if (!$conn) {
    echo json_encode(['success'=>false,'message'=>'Koneksi database gagal.']);
    exit;
}

$upd = $conn->prepare("UPDATE layanan SET nama_layanan = ?, detail_layanan = ? WHERE id_layanan = ?");
$upd->bind_param('ssi', $nama, $detail, $id);
$ok  = $upd->execute();
$upd->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Layanan berhasil diperbarui.' : 'Gagal: '.$conn->error,
]);