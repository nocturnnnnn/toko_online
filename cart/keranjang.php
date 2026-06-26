<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Harus login untuk melihat keranjang (sesuai db)
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil data keranjang beserta detail produknya
$query = mysqli_query($conn, "
    SELECT k.id as keranjang_id, k.jumlah, p.id as produk_id, p.nama_produk, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = '$user_id'
");

$total_keseluruhan = 0;
$ada_barang = mysqli_num_rows($query) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Nocturn Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Nocturn Shop</a>
            <div class="nav-links">
                <a href="../produk.php">Produk</a>
                <a href="../index.php">Beranda</a>
            </div>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-bottom: 2rem;">Keranjang Belanja Anda</h1>

        <?php if(!$ada_barang): ?>
            <div style="text-align: center; padding: 4rem; background: var(--surface); border-radius: 1rem; border: 1px solid var(--border);">
                <h3 style="margin-bottom: 1rem; color: var(--text-secondary);">Keranjang masih kosong</h3>
                <a href="../produk.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                            $subtotal = $row['harga'] * $row['jumlah'];
                            $total_keseluruhan += $subtotal;
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= $row['jumlah']; ?></td>
                                <td style="font-weight: 600; color: var(--primary);">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                                <td>
                                    <a href="hapus_keranjang.php?id=<?= $row['keranjang_id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;" onclick="return confirm('Hapus item ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <div>
                    <span style="color: var(--text-secondary);">Total Pembayaran:</span>
                    <div class="total-amount">Rp <?= number_format($total_keseluruhan, 0, ',', '.'); ?></div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="hapus_keranjang.php?clear=1" class="btn btn-danger" style="font-size: 1.125rem;" onclick="return confirm('Yakin ingin membatalkan semua pesanan di keranjang?');">Batalkan Pesanan</a>
                    <a href="checkout.php" class="btn btn-primary" style="font-size: 1.125rem;">Checkout Sekarang</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
