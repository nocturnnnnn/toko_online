<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE user_id = '$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Toko Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">TokoOnline</a>
            <div class="nav-links">
                <a href="../produk.php">Produk</a>
                <a href="../index.php">Beranda</a>
            </div>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-bottom: 2rem;">Riwayat Pesanan Saya</h1>

        <?php if(mysqli_num_rows($query) == 0): ?>
            <div style="text-align: center; padding: 4rem; background: var(--surface); border-radius: 1rem; border: 1px solid var(--border);">
                <h3 style="margin-bottom: 1rem; color: var(--text-secondary);">Belum ada pesanan</h3>
                <a href="../produk.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td style="font-weight: bold;">#<?= $row['id']; ?></td>
                                <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                <td style="font-weight: 600; color: var(--primary);">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                    $status_colors = [
                                        'pending' => '#F59E0B',
                                        'dibayar' => '#3B82F6',
                                        'dikirim' => '#8B5CF6',
                                        'selesai' => '#10B981'
                                    ];
                                    $color = $status_colors[$row['status']] ?? '#6B7280';
                                    ?>
                                    <span style="background: <?= $color ?>20; color: <?= $color ?>; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; text-transform: capitalize;">
                                        <?= $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a href="bayar.php?id=<?= $row['id']; ?>" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Bayar Sekarang</a>
                                    <?php else: ?>
                                        <a href="#" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; pointer-events: none; opacity: 0.5;">Selesai/Diproses</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
