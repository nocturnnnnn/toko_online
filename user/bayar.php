<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: pesanan_saya.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

// Pastikan pesanan adalah milik user yang login dan statusnya pending
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id='$id' AND user_id='$user_id' AND status='pending'");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    header("Location: pesanan_saya.php");
    exit;
}

$error = '';
$success = false;

if (isset($_POST['upload'])) {
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $nama_file = uniqid('bukti_') . '.' . $ext;
        $tujuan = "../assets/img/bukti/" . $nama_file;
        
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $tujuan)) {
            // Update database, ganti status jadi dibayar (bisa juga 'menunggu konfirmasi' tapi kita asumsikan 'dibayar')
            mysqli_query($conn, "UPDATE pesanan SET bukti_pembayaran='$nama_file' WHERE id='$id'");
            $success = true;
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    } else {
        $error = "Silakan pilih gambar bukti transfer yang valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #<?= htmlspecialchars($id); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Nocturn Shop</a>
            <div class="nav-links">
                <a href="pesanan_saya.php">Kembali ke Pesanan</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="auth-container" style="max-width: 600px; margin-top: 2rem;">
            
            <?php if($success): ?>
                <div style="text-align: center;">
                    <div style="width: 64px; height: 64px; background: #D1FAE5; color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">✓</div>
                    <h2 style="margin-bottom: 1rem;">Upload Berhasil!</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">Terima kasih, bukti pembayaran Anda telah kami terima dan sedang diproses (menunggu konfirmasi Admin).</p>
                    <a href="pesanan_saya.php" class="btn btn-primary">Kembali ke Pesanan Saya</a>
                </div>
            <?php else: ?>
                <h2 class="auth-title">Konfirmasi Pembayaran</h2>
                
                <div style="background: #F3F4F6; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid var(--border);">
                    <p style="margin-bottom: 0.5rem; color: var(--text-secondary);">Total Tagihan Anda:</p>
                    <h3 style="color: var(--primary); font-size: 1.5rem;">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.'); ?></h3>
                    <hr style="border:none; border-top: 1px solid var(--border); margin: 1rem 0;">
                    <p style="margin-bottom: 0.5rem; font-weight: 500;">Silakan transfer ke salah satu rekening berikut:</p>
                    <ul style="color: var(--text-secondary); line-height: 1.8; padding-left: 1.5rem;">
                        <li><strong>Dana</strong>: 085185021163 | Zala Risky Pratama</li>
                        <li><strong>Mandiri</strong>: 1850010723952 | Zala Risky Pratama</li>
                    </ul>
                </div>

                <?php if($error): ?>
                    <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="bukti">Unggah Bukti Transfer (Foto/Screenshot)</label>
                        <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" name="upload" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>
