<?php
/**
 * backend/pengguna/hapus.php
 * API: hapus permanen akun pengguna
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
    echo json_encode(['success'=>false,'message'=>'Parameter tidak valid.']);
    exit;
}

$del = $conn->prepare("DELETE FROM akun_pengguna WHERE id_pengguna = ?");
$del->bind_param('i', $id);
$ok = $del->execute();
$del->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Akun berhasil dihapus.' : 'Gagal: '.$conn->error,
]);
