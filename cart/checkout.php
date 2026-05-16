<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil data keranjang
$query_keranjang = mysqli_query($conn, "
    SELECT k.produk_id, k.jumlah, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = '$user_id'
");

if (mysqli_num_rows($query_keranjang) == 0) {
    header("Location: keranjang.php");
    exit;
}

// Hitung total harga
$total_harga = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_keranjang)) {
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_harga += $subtotal;
    
    // Simpan di array untuk di-insert ke detail_pesanan nantinya
    $row['subtotal'] = $subtotal;
    $items[] = $row;
}

// 1. Masukkan ke tabel pesanan
mysqli_query($conn, "INSERT INTO pesanan(user_id, total_harga, status) VALUES('$user_id', '$total_harga', 'pending')");
$pesanan_id = mysqli_insert_id($conn);

// 2. Masukkan ke detail_pesanan dan kurangi stok
foreach ($items as $item) {
    $produk_id = $item['produk_id'];
    $jumlah = $item['jumlah'];
    $harga = $item['harga'];
    $subtotal = $item['subtotal'];
    
    // Insert detail
    mysqli_query($conn, "INSERT INTO detail_pesanan(pesanan_id, produk_id, jumlah, harga, subtotal) VALUES('$pesanan_id', '$produk_id', '$jumlah', '$harga', '$subtotal')");
    
    // Kurangi stok produk
    mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id = '$produk_id'");
}

// 3. Kosongkan keranjang di database
mysqli_query($conn, "DELETE FROM keranjang WHERE user_id = '$user_id'");

// Selesai
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Berhasil - Toko Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">TokoOnline</a>
        </div>
    </header>

    <main class="container">
        <div style="text-align: center; padding: 5rem 2rem; background: var(--surface); border-radius: 1rem; border: 1px solid var(--border); max-width: 600px; margin: 4rem auto;">
            <div style="width: 80px; height: 80px; background: #D1FAE5; color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem;">
                ✓
            </div>
            <h1 style="margin-bottom: 1rem; color: var(--text-primary);">Checkout Berhasil!</h1>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.125rem;">
                ID Pesanan Anda: <strong>#<?= $pesanan_id; ?></strong>
            </p>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                Terima kasih telah berbelanja. Total pembayaran Anda adalah <strong style="color: var(--primary);">Rp <?= number_format($total_harga, 0, ',', '.'); ?></strong>.
            </p>
            <a href="../index.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </main>

</body>
</html>
