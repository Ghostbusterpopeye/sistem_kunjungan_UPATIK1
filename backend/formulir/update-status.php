<?php
/**
 * backend/formulir/update-status.php
 * API endpoint: ubah status_layanan formulir
 * Method: POST
 * Params: id_formulir (int), status (string: menunggu|diproses|selesai)
 */
require_once '../../includes/auth.php';

header('Content-Type: application/json');

// Hanya admin yang boleh
if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
    exit;
}

$idFormulir = intval($_POST['id_formulir'] ?? 0);
$status     = trim($_POST['status'] ?? '');
$allowed    = ['menunggu', 'diproses', 'selesai'];

if (!$idFormulir || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid.']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE formulir_layanan SET status_layanan = ? WHERE id_formulir = ?"
);
$stmt->bind_param('si', $status, $idFormulir);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    // Jika selesai, catat tindakan admin
    if ($status === 'selesai') {
        $admin    = getCurrentAdmin();
        $today    = date('Y-m-d');
        $detail   = 'Formulir ditandai selesai oleh admin melalui dashboard.';
        $stmtT = $conn->prepare(
            "INSERT INTO tindakan_layanan (id_formulir, id_admin, tanggal_tindakan, detail_tindakan)
             VALUES (?, ?, ?, ?)"
        );
        $stmtT->bind_param('iiss', $idFormulir, $admin['id'], $today, $detail);
        $stmtT->execute();
        $stmtT->close();
    }
    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status: ' . $conn->error]);
}
