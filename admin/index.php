<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nocturn Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Admin Panel</a>
            <div class="nav-links">
                <a href="index.php" style="color: var(--primary); font-weight: 600;">Kelola Produk</a>
                <a href="pesanan.php">Kelola Pesanan</a>
                <a href="../index.php">Lihat Website</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Kelola Produk</h1>
            <a href="tambah_produk.php" class="btn btn-primary">+ Tambah Produk Baru</a>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($query)) : ?>
                        <tr>
                            <td>
                                <?php if($p['gambar']): ?>
                                    <img src="../assets/img/<?= htmlspecialchars($p['gambar']); ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: var(--border); border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: var(--text-secondary);">No Img</div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($p['nama_produk'] ?? ''); ?></td>
                            <td>Rp <?= number_format($p['harga'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($p['stok'] ?? ''); ?></td>
                            <td>
                                <a href="edit_produk.php?id=<?= $p['id']; ?>" class="btn" style="border: 1px solid var(--border); padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</a>
                                <a href="hapus_produk.php?id=<?= $p['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; margin-left: 0.5rem;" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>