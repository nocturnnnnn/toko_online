<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username']; // Ubah dari email ke username
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: ../index.php");
        exit;
    } else {
        $error = "Username atau Password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nocturn Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Nocturn Shop</a>
        </div>
    </header>

    <div class="auth-container">
        <h2 class="auth-title">Login ke Akun Anda</h2>
        
        <?php if($error): ?>
            <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Login
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </p>
    </div>
</body>
</html>