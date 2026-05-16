<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Proses update status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'konfirmasi') {
        mysqli_query($conn, "UPDATE pesanan SET status='dibayar' WHERE id='$id'");
    } elseif ($action == 'kirim') {
        mysqli_query($conn, "UPDATE pesanan SET status='dikirim' WHERE id='$id'");
    } elseif ($action == 'selesai') {
        mysqli_query($conn, "UPDATE pesanan SET status='selesai' WHERE id='$id'");
    }
    header("Location: pesanan.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT p.*, u.nama, u.username 
    FROM pesanan p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="brand">Admin Panel</a>
            <div class="nav-links">
                <a href="index.php">Kelola Produk</a>
                <a href="pesanan.php" style="color: var(--primary); font-weight: 600;">Kelola Pesanan</a>
                <a href="../index.php">Lihat Website</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Daftar Pesanan Masuk</h1>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pembeli</th>
                        <th>Total Harga</th>
                        <th>Bukti Transfer</th>
                        <th>Status</th>
                        <th>Aksi / Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                        <tr>
                            <td style="font-weight: 600;">#<?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?> <br><small style="color: var(--text-secondary);">@<?= htmlspecialchars($row['username']); ?></small></td>
                            <td style="font-weight: 600; color: var(--primary);">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if($row['bukti_pembayaran']): ?>
                                    <a href="../assets/img/bukti/<?= htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank" style="color: var(--primary); text-decoration: underline;">Lihat Bukti</a>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary); font-style: italic;">Belum ada</span>
                                <?php endif; ?>
                            </td>
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
                                <?php if($row['status'] == 'pending' && $row['bukti_pembayaran']): ?>
                                    <a href="pesanan.php?action=konfirmasi&id=<?= $row['id']; ?>" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;" onclick="return confirm('Konfirmasi pembayaran valid?');">Konfirmasi Bayar</a>
                                <?php elseif($row['status'] == 'dibayar'): ?>
                                    <a href="pesanan.php?action=kirim&id=<?= $row['id']; ?>" class="btn" style="background: #8B5CF6; color: white; padding: 0.25rem 0.5rem; font-size: 0.875rem;">Tandai Dikirim</a>
                                <?php elseif($row['status'] == 'dikirim'): ?>
                                    <a href="pesanan.php?action=selesai&id=<?= $row['id']; ?>" class="btn" style="background: #10B981; color: white; padding: 0.25rem 0.5rem; font-size: 0.875rem;">Tandai Selesai</a>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
