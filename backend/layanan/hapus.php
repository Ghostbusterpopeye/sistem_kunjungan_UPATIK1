<?php
/**
 * backend/layanan/hapus.php
 * API: hapus layanan
 * POST: id_layanan (int)
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Akses ditolak.']);
    exit;
}

$id = intval($_POST['id_layanan'] ?? 0);
if (!$id || !$conn) {
    echo json_encode(['success'=>false,'message'=>'Parameter tidak valid.']);
    exit;
}

// Cek apakah masih ada formulir terkait
$chk = $conn->prepare("SELECT COUNT(*) AS n FROM formulir_layanan WHERE id_layanan = ?");
$chk->bind_param('i', $id);
$chk->execute();
$n = $chk->get_result()->fetch_assoc()['n'];
$chk->close();

if ($n > 0) {
    echo json_encode([
        'success' => false,
        'message' => "Layanan tidak dapat dihapus karena masih ada $n formulir terkait.",
    ]);
    exit;
}

$del = $conn->prepare("DELETE FROM layanan WHERE id_layanan = ?");
$del->bind_param('i', $id);
$ok  = $del->execute();
$del->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Layanan berhasil dihapus.' : 'Gagal: '.$conn->error,
]);
