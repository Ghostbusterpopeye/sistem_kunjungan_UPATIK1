-- ============================================================
-- FILE: database/seed_data.sql
-- Jalankan SETELAH database_upatik.sql
-- Berisi data awal: 1 akun admin + 9 layanan default
-- ============================================================

USE database_upatik;

-- ── Layanan default ────────────────────────────────────────────────────────────
INSERT IGNORE INTO `layanan` (`id_layanan`, `nama_layanan`) VALUES
(1,  'SSO Email'),
(2,  'Reset Password'),
(3,  'Pemasangan VPN'),
(4,  'Keluhan IT'),
(5,  'Maintenance'),
(6,  'Instalasi Software'),
(7,  'Konsultasi IT'),
(8,  'Keamanan Siber'),
(9,  'Jaringan Infra');

-- ── Akun admin default ─────────────────────────────────────────────────────────
-- Password: admin123  (hashed dengan PASSWORD_DEFAULT)
INSERT IGNORE INTO `akun_admin` (`id_admin`, `nip`, `nama_lengkap`, `password`) VALUES
(1, 'admin001', 'Admin UPA TIK',
 '$2y$12$9fqDKJ7Xt6W3pXZwWJzXaOfJZF6dVXJCxXtmIDyuL8MFgW2MjSbpi');

(2, 'admin002', 'Admin UPA TIK 2', '$2a$12$EQZwtxRZ6vJwyg13vWV4A.ZPNavhfq9nAbUvYZ5niGyO53jfLNwaS');

-- Jika hash di atas tidak cocok dengan versi PHP Anda, jalankan perintah berikut
-- di PHP untuk mendapatkan hash baru:
--   echo password_hash('admin123', PASSWORD_DEFAULT);
-- lalu UPDATE akun_admin SET password='HASH_BARU' WHERE id_admin=1;
