<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online Premium</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="brand">TokoOnline</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user'])) : ?>
                    <span style="font-weight: 500;">Halo, <?= htmlspecialchars($_SESSION['user']['nama']); ?></span>
                    <?php if($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="admin/index.php" class="btn" style="border: 1px solid var(--border); padding: 0.25rem 0.75rem; font-size: 0.875rem;">Admin Panel</a>
                    <?php endif; ?>
                    <a href="produk.php" class="btn btn-primary">Belanja Sekarang</a>
                    <a href="cart/keranjang.php">Keranjang</a>
                    <a href="user/pesanan_saya.php">Pesanan Saya</a>
                    <a href="auth/logout.php" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Logout</a>
                <?php else : ?>
                    <a href="auth/login.php" class="btn">Login</a>
                    <a href="auth/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="text-align: center; padding: 4rem 0;">
            <h1 style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-primary);">Selamat Datang di Toko Online</h1>
            <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto 2rem;">Temukan berbagai produk berkualitas dengan harga terbaik. Mulai belanja sekarang juga!</p>
            <a href="produk.php" class="btn btn-primary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">Lihat Produk Kami</a>
        </div>
    </main>

</body>
</html>