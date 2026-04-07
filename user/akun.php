<?php
include '../aksi/koneksi.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$pesan = '';
$tipe  = '';
// ===== UBAH AKUN =====
if (isset($_POST['ubah_akun'])) {
    $username_baru  = trim($_POST['username_baru']);
    $password_baru  = $_POST['password_baru'];
    $password_ulang = $_POST['password_ulang'];
    $password_lama  = $_POST['password_lama'];

    // Ambil data user saat ini
    $user_sesi = $_SESSION['username'];
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$user_sesi'");
    $user = mysqli_fetch_assoc($res);

    // Validasi password lama
    if (!password_verify($password_lama, $user['password'])) {
        $pesan = 'Password lama salah!';
        $tipe  = 'error';
    } elseif (empty($username_baru)) {
        $pesan = 'Username tidak boleh kosong!';
        $tipe  = 'error';
    } elseif (!empty($password_baru) && $password_baru !== $password_ulang) {
        $pesan = 'Konfirmasi password baru tidak cocok!';
        $tipe  = 'error';
    } else {
        // Cek username baru sudah dipakai orang lain belum
        $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username_baru' AND username != '$user_sesi'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = 'Username sudah digunakan akun lain!';
            $tipe  = 'error';
        } else {
            // Update username
            $update_username = "username = '$username_baru'";

            // Update password hanya kalau diisi
            $update_password = '';
            if (!empty($password_baru)) {
                $hash = password_hash($password_baru, PASSWORD_BCRYPT);
                $update_password = ", password = '$hash'";
            }

            mysqli_query($koneksi, "UPDATE users SET $update_username $update_password WHERE username = '$user_sesi'");

            // Perbarui session username
            $_SESSION['username'] = $username_baru;

            $pesan = 'Akun berhasil diperbarui!';
            $tipe  = 'sukses';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Buku Kas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/components.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="overlay" id="sidebar-overlay"></div>

    <header class="top-bar">
        <div class="top-bar-left">
            <button class="menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <span class="brand-title">Pengaturan Akun</span>
        </div>
        <div class="top-bar-right">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
        </div>
    </header>

    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-table" style="color: var(--excel-green-light);"></i>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="sidebar-item">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="tambahKas.php" class="sidebar-item">
                <i class="fas fa-plus"></i> Tambah Kas
            </a>
            <a href="akun.php" class="sidebar-item active">
                <i class="fas fa-user-gear"></i> Akun
            </a>
            
            <div style="margin-top: auto; padding: var(--space-md);">
                <a href="../aksi/logout.php" class="btn btn-danger" style="width: 100%; min-height: 38px;"
                onclick="konfirmasiLogout(event, this.href)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <div class="main-wrapper">
        <main class="page-content">
            
            <div class="mb-md">
                <h2><i class="fas fa-circle-user" style="color: var(--excel-green); margin-right: 8px;"></i>Detail Profil</h2>
                <p style="color: var(--gray-500); font-size: 14px;">Lihat info profil saat ini.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="stat-value" style="font-size: 18px;"><?php echo $_SESSION['username']; ?></div>
                        <div class="stat-label">Username</div>
                    </div>
                </div>
            </div>

            <div class="card card-form" style="margin-top: var(--space-lg);">
                <div class="card-header">
                    <i class="fas fa-id-card"></i> Informasi Personal
                </div>
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input class="form-control" type="text" value="<?php echo $_SESSION['username']; ?>" style="background: var(--gray-100);">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" value="********" style="background: var(--gray-100);">
                    <small style="color: var(--gray-500); margin-top: 4px; display: block;">Hubungi admin untuk perubahan password.]</small>
                </div>

                <hr style="border: none; border-top: var(--border-thin); margin: var(--space-lg) 0;">

                <a href="dashboard.php" class="btn btn-primary btn-block-mobile" style="width: 100%;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="kelolaAkun.php" class="btn btn-secondary btn-block-mobile" style="width: 100%; margin-top: var(--space-sm); border: 3px solid var(--excel-green);">
                        <i class="fas fa-user-gear"></i> Kelola Akun
                    </a>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="tambahKas.php" class="nav-item">
            <i class="fas fa-plus"></i>
            <span>Tambah</span>
        </a>
        <a href="akun.php" class="nav-item active">
            <i class="fas fa-circle-user"></i>
            <span>Akun</span>
        </a>
    </nav>

    <script>
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        }

        menuToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

    function konfirmasiLogout(event, url) {
    event.preventDefault(); 
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Anda akan keluar dari akun ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, logout!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
    </script>
</body>
</html>