<?php
session_start();
include "config/koneksi.php";
/** @var mysqli $conn */

if(!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
$produk = mysqli_fetch_assoc($query);

if(!$produk) {
    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?= htmlspecialchars($produk['nama_produk'] ?? ''); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="brand">TokoOnline</a>
            <div class="nav-links">
                <a href="produk.php">Kembali ke Produk</a>
                <a href="cart/keranjang.php">Keranjang</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 1rem; padding: 2.5rem; max-width: 1000px; margin: 2rem auto; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            
            <!-- Kolom Gambar -->
            <div style="background: var(--background); border-radius: 1rem; overflow: hidden; display: flex; align-items: center; justify-content: center; min-height: 400px;">
                <?php if($produk['gambar']): ?>
                    <img src="assets/img/<?= htmlspecialchars($produk['gambar']); ?>" alt="Img" style="width: 100%; height: 100%; object-fit: contain;">
                <?php else: ?>
                    <div style="color: var(--text-secondary);">Tidak ada gambar tersedia</div>
                <?php endif; ?>
            </div>

            <!-- Kolom Info -->
            <div>
                <h1 style="margin-bottom: 1rem; font-size: 2.5rem; line-height: 1.2;"><?= htmlspecialchars($produk['nama_produk'] ?? ''); ?></h1>
                
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                    Rp <?= number_format($produk['harga'] ?? 0, 0, ',', '.'); ?>
                </div>
                
                <div style="display: inline-block; background: #EEF2FF; color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 500; font-size: 0.875rem; margin-bottom: 2rem;">
                    Sisa Stok: <?= htmlspecialchars($produk['stok'] ?? 0); ?>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 0.5rem; font-size: 1.25rem;">Deskripsi Produk</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap;"><?= htmlspecialchars($produk['deskripsi'] ?? 'Tidak ada deskripsi.'); ?></p>
                </div>

                <div style="padding-top: 2rem; border-top: 1px solid var(--border);">
                    <a href="cart/tambah_keranjang.php?id=<?= $produk['id']; ?>" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2rem; width: 100%; text-align: center;">
                        Tambahkan ke Keranjang Belanja
                    </a>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
