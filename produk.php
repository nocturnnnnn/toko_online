<?php
session_start();
include "config/koneksi.php";
/** @var mysqli $conn */

$query = mysqli_query($conn, "SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="brand">Nocturn Shop</a>
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="cart/keranjang.php">Keranjang</a>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="user/pesanan_saya.php">Pesanan Saya</a>
                <?php endif; ?>
                <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <a href="admin/index.php" style="color: var(--primary); font-weight: 600;">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-bottom: 2rem;">Daftar Produk</h1>

        <div class="product-grid">
            <?php while ($produk = mysqli_fetch_assoc($query)) : ?>
                <div class="product-card">
                    <div style="height: 200px; margin: -1.5rem -1.5rem 1.5rem -1.5rem; background: var(--border); border-top-left-radius: 1rem; border-top-right-radius: 1rem; overflow: hidden;">
                        <?php if($produk['gambar']): ?>
                            <img src="assets/img/<?= htmlspecialchars($produk['gambar']); ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">Tidak ada gambar</div>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="product-title"><?= htmlspecialchars($produk['nama_produk'] ?? ''); ?></h3>
                    <p class="product-meta">Stok: <?= htmlspecialchars($produk['stok'] ?? 0); ?></p>
                    <p style="color: var(--text-secondary); flex-grow: 1;">
                        <?php 
                        $desc = $produk['deskripsi'] ?? '';
                        echo htmlspecialchars(substr($desc, 0, 100)) . (strlen($desc) > 100 ? '...' : ''); 
                        ?>
                    </p>
                    <div class="product-price">Rp <?= number_format($produk['harga'] ?? 0, 0, ',', '.'); ?></div>
                    <div class="card-actions">
                        <a href="detail.php?id=<?= $produk['id']; ?>" class="btn" style="border: 1px solid var(--border); flex: 1;">Detail</a>
                        <a href="cart/tambah_keranjang.php?id=<?= $produk['id']; ?>" class="btn btn-primary" style="flex: 1;">+ Keranjang</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

</body>
</html>