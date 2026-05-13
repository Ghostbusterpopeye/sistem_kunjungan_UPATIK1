<?php
/**
 * backend/pengguna/history.php
 * API: ambil riwayat kunjungan pengguna untuk admin
 * GET: id (int)
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid atau koneksi database gagal.']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT fl.id_formulir, fl.tanggal_isi, fl.detail_layanan, fl.status_layanan, l.nama_layanan
     FROM formulir_layanan fl
     LEFT JOIN layanan l ON fl.id_layanan = l.id_layanan
     WHERE fl.id_pengguna = ?
     ORDER BY fl.tanggal_isi DESC"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'history' => $history]);
