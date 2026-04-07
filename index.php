<?php
include 'aksi/koneksi.php';
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$error = false; 

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // PERBAIKAN: Tambahkan kolom 'id' dan 'role' dalam SELECT
    $stmt = mysqli_prepare($koneksi, "SELECT id, role, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            header("Location: user/dashboard.php");
            exit();
        } else {
            $error = true; 
        }
    } else {
        $error = true; 
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buku Kas</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/components.css">
</head>
<body class="full-center-layout">

    <div class="auth-card">
        <div class="auth-title">
            <i class="fas fa-table" style="color: var(--excel-green); font-size: 32px; margin-bottom: 8px;"></i>
            <h2>Buku Kas</h2>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" type="text" id="username" name="username" placeholder="Masukkan username..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Masukkan password..." required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary btn-block-mobile" style="width: 100%; margin-top: var(--space-sm);">
                <i class="fas fa-arrow-right-to-bracket"></i> Login
            </button>
        </form>
    </div>

    <script src="style/app.js"></script>
    <?php if ($error){?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Akses Ditolak",
                text: "Username atau Password salah!",
                confirmButtonColor: '#C62828'
            });
        });
    </script>
    <?php }; ?>
</body>
</html>