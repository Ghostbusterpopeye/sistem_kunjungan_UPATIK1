<?php
/**
 * backend/layanan/toggle-status.php
 * API: toggle status aktif/nonaktif layanan
 * POST: id_layanan (int)
 * Returns: { success, is_active, message }
 */
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

$id = intval(trim($_POST['id_layanan'] ?? 0));

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID layanan tidak valid.']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}

// Ambil status sekarang
$sel = $conn->prepare("SELECT is_active FROM layanan WHERE id_layanan = ?");
$sel->bind_param('i', $id);
$sel->execute();
$sel->bind_result($current);
if (!$sel->fetch()) {
    $sel->close();
    echo json_encode(['success' => false, 'message' => 'Layanan tidak ditemukan.']);
    exit;
}
$sel->close();

// Toggle: 1 → 0, 0 → 1
$newStatus = $current ? 0 : 1;

$upd = $conn->prepare("UPDATE layanan SET is_active = ? WHERE id_layanan = ?");
$upd->bind_param('ii', $newStatus, $id);
$ok = $upd->execute();
$upd->close();

echo json_encode([
    'success'   => $ok,
    'is_active' => $newStatus,
    'message'   => $ok
        ? ($newStatus ? 'Layanan diaktifkan.' : 'Layanan dinonaktifkan.')
        : 'Gagal: ' . $conn->error,
]);
