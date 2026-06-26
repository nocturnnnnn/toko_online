<?php
session_start();
require_once __DIR__ . "/../config/koneksi.php";
/** @var mysqli $conn */

$success = false;
$error = '';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username atau Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users(nama, username, email, password) VALUES('$nama', '$username', '$email', '$password')";
        if(mysqli_query($conn, $query)) {
            $success = true;
        } else {
            $error = "Terjadi kesalahan saat menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nocturn Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Nocturn Shop</a>
        </div>
    </header>

    <div class="auth-container">
        <h2 class="auth-title">Buat Akun Baru</h2>
        
        <?php if($success): ?>
            <div style="background: var(--success); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                Register berhasil! Silakan <a href="login.php" style="color: white; text-decoration: underline;">Login</a>.
            </div>
        <?php else: ?>
            <?php if($error): ?>
                <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address (Opsional/Hanya Profil)</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <button type="submit" name="register" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Daftar Akun
                </button>
            </form>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </div>
</body>
</html>