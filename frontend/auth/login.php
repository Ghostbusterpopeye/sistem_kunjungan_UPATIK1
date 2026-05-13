<?php
/**
 * frontend/auth/login.php
 * Halaman login terpadu: tab ADMIN dan SISWA/STAF
 */
require_once '../../includes/auth.php';

// Jika sudah login sebagai admin → ke admin dashboard
if (isAdminLoggedIn()) {
    header('Location: ' . BASE_URL . '/frontend/admin/admin-dashboard.php');
    exit;
}
// Jika sudah login sebagai pengguna → ke beranda pengguna
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';
$mode  = $_POST['mode'] ?? $_GET['mode'] ?? 'pengguna'; // 'admin' atau 'pengguna'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $pass       = $_POST['password']        ?? '';
    $mode       = $_POST['mode']            ?? 'pengguna';

    if (empty($identifier) || empty($pass)) {
        $error = 'Semua field wajib diisi.';
    } else {
        $authenticated = false;

        if ($mode === 'admin') {
            // ── Login Admin ───────────────────────────────────────────────
            if ($conn) {
                $stmt = $conn->prepare(
                    "SELECT id_admin, nama_lengkap, nip, password
                     FROM akun_admin WHERE nip = ? LIMIT 1"
                );
                $stmt->bind_param('s', $identifier);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($row && password_verify($pass, $row['password'])) {
                    $_SESSION['admin_id']   = $row['id_admin'];
                    $_SESSION['admin_nama'] = $row['nama_lengkap'];
                    $_SESSION['admin_nip']  = $row['nip'];
                    $authenticated = true;
                }
            } else {
                // Demo mode
                if ($identifier === 'admin001' && $pass === 'admin123') {
                    $_SESSION['admin_id']   = 1;
                    $_SESSION['admin_nama'] = 'Admin UPA TIK';
                    $_SESSION['admin_nip']  = 'admin001';
                    $authenticated = true;
                }
            }
            if ($authenticated) {
                header('Location: ' . BASE_URL . '/frontend/admin/admin-dashboard.php');
                exit;
            } else {
                $error = 'NIP atau Password admin tidak sesuai.';
            }

        } else {
            // ── Login Pengguna ────────────────────────────────────────────
            if ($conn) {
                $stmt = $conn->prepare(
                    "SELECT id_pengguna, nama_lengkap, nim_nip, email, password, status,
                            COALESCE(is_active,1) AS is_active
                     FROM akun_pengguna
                     WHERE nim_nip = ? OR email = ?
                     LIMIT 1"
                );
                $stmt->bind_param('ss', $identifier, $identifier);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($row) {
                    if (!(int)$row['is_active']) {
                        $error = 'Akun Anda telah dinonaktifkan. Hubungi admin UPA TIK.';
                    } elseif (password_verify($pass, $row['password'])) {
                        $_SESSION['pengguna_id']     = $row['id_pengguna'];
                        $_SESSION['pengguna_nama']   = $row['nama_lengkap'];
                        $_SESSION['pengguna_nim']    = $row['nim_nip'];
                        $_SESSION['pengguna_email']  = $row['email'];
                        $_SESSION['pengguna_status'] = $row['status'];
                        $authenticated = true;
                    } else {
                        $error = 'NIM/NIP atau Password tidak sesuai.';
                    }
                } else {
                    $error = 'NIM/NIP atau Password tidak sesuai.';
                }
            } else {
                // Demo mode
                if ($identifier === 'demo001' && $pass === 'password123') {
                    $_SESSION['pengguna_id']     = 1;
                    $_SESSION['pengguna_nama']   = 'Demo Mahasiswa';
                    $_SESSION['pengguna_nim']    = 'demo001';
                    $_SESSION['pengguna_email']  = 'demo@polije.ac.id';
                    $_SESSION['pengguna_status'] = 'mahasiswa';
                    $authenticated = true;
                } else {
                    $error = 'NIM/NIP atau Password tidak sesuai.';
                }
            }
            if ($authenticated) {
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Sistem Kunjungan UPA TIK</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    /* ── Auth page layout ─────────────────────────────── */
    .auth-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      background:
        linear-gradient(rgba(15,20,60,0.60), rgba(15,20,60,0.60)),
        url('../../assets/polije.png') center/cover no-repeat;
    }

    .auth-card {
      background: #fff;
      border-radius: 24px;
      padding: 44px 40px 36px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 24px 64px rgba(0,0,0,0.28);
      animation: fadeSlideUp 0.45s ease;
    }

    .auth-back {
      font-size: 0.85rem;
      color: var(--primary);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 24px;
      font-weight: 600;
      transition: .2s;
    }
    .auth-back:hover { color: var(--accent); }

    .auth-card h1 {
      font-family: 'Sora', sans-serif;
      font-size: 2rem;
      font-weight: 800;
      color: var(--primary);
      margin-bottom: 6px;
    }

    .auth-subtitle {
      color: var(--text-muted);
      font-size: 0.93rem;
      margin-bottom: 24px;
    }

    /* ── Tab switcher ─────────────────────────────────── */
    .auth-tabs {
      display: flex;
      background: #f1f5f9;
      border-radius: 50px;
      padding: 5px;
      margin-bottom: 28px;
      gap: 4px;
    }

    .auth-tab {
      flex: 1;
      padding: 11px 0;
      border: none;
      border-radius: 50px;
      font-size: 0.88rem;
      font-weight: 700;
      cursor: pointer;
      transition: all .25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      color: var(--text-muted);
      background: transparent;
    }

    .auth-tab.active {
      background: white;
      color: var(--primary);
      box-shadow: 0 2px 12px rgba(45,58,140,0.15);
    }

    .auth-tab .tab-icon {
      font-size: 1rem;
    }

    /* ── Info box ─────────────────────────────────────── */
    .auth-info {
      background: rgba(45,58,140,0.06);
      border: 1px solid rgba(45,58,140,0.14);
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 0.85rem;
      color: var(--primary);
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
      gap: 8px;
      line-height: 1.5;
    }

    .auth-footer {
      text-align: center;
      margin-top: 20px;
      font-size: 0.88rem;
      color: var(--text-muted);
    }

    .auth-footer a {
      color: var(--primary);
      font-weight: 700;
      text-decoration: none;
    }
    .auth-footer a:hover { color: var(--accent); }

    /* Hide/show panel */
    .auth-panel { display: none; }
    .auth-panel.visible { display: block; }

    @keyframes fadeSlideUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <a href="<?= BASE_URL ?>/" class="auth-back">← Kembali ke Beranda</a>

    <h1>Selamat Datang</h1>
    <p class="auth-subtitle">Silakan masuk untuk mengakses layanan.</p>

    <!-- Tab Switcher -->
    <div class="auth-tabs" role="tablist">
      <button class="auth-tab <?= ($mode !== 'admin') ? 'active' : '' ?>"
              id="tab-pengguna" role="tab" onclick="switchTab('pengguna')">
        <span class="tab-icon">SISWA / STAF</span> 
      </button>
      <button class="auth-tab <?= ($mode === 'admin') ? 'active' : '' ?>"
              id="tab-admin" role="tab" onclick="switchTab('admin')">
        <span class="tab-icon">ADMIN</span> 
      </button>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ── Panel Pengguna ────────────────────────────────── -->
    <div class="auth-panel <?= ($mode !== 'admin') ? 'visible' : '' ?>" id="panel-pengguna">
      <div class="auth-info">
        ℹ️ Login menggunakan NIM / NIP atau Email dan Password Anda
      </div>
      <form method="POST" action="login.php">
        <input type="hidden" name="mode" value="pengguna">
        <div class="form-group">
          <div class="input-wrapper">
            <input type="text" name="identifier" class="form-control"
                   id="nim-input"
                   placeholder="NIM / NIP / Email"
                   value="<?= ($mode !== 'admin') ? htmlspecialchars($_POST['identifier'] ?? '') : '' ?>"
                   autocomplete="username" required>
          </div>
        </div>
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" name="password" class="form-control"
                   placeholder="Password" autocomplete="current-password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk Sekarang</button>
      </form>
      <div class="auth-footer">
        Belum punya akun? <a href="register.php">Daftar sekarang</a>
      </div>
    </div>

    <!-- ── Panel Admin ──────────────────────────────────── -->
    <div class="auth-panel <?= ($mode === 'admin') ? 'visible' : '' ?>" id="panel-admin">
      <div class="auth-info">
        🛡️ Login menggunakan NIP dan Password akun Admin UPA TIK
      </div>
      <form method="POST" action="login.php">
        <input type="hidden" name="mode" value="admin">
        <div class="form-group">
          <div class="input-wrapper">
            <input type="text" name="identifier" class="form-control"
                   id="nip-input"
                   placeholder="NIP Admin"
                   value="<?= ($mode === 'admin') ? htmlspecialchars($_POST['identifier'] ?? '') : '' ?>"
                   autocomplete="username" required>
          </div>
        </div>
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" name="password" class="form-control"
                   placeholder="Password" autocomplete="current-password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk Sekarang</button>
      </form>
    </div>

  </div><!-- end auth-card -->
</div><!-- end auth-page -->

<script>
function switchTab(mode) {
  // Update active tab style
  document.getElementById('tab-pengguna').classList.toggle('active', mode === 'pengguna');
  document.getElementById('tab-admin').classList.toggle('active', mode === 'admin');

  // Show/hide panels
  document.getElementById('panel-pengguna').classList.toggle('visible', mode === 'pengguna');
  document.getElementById('panel-admin').classList.toggle('visible', mode === 'admin');
}
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>
