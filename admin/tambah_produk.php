<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_nama = null;

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_nama = uniqid() . '.' . $ext;
        $tujuan = "../assets/img/" . $gambar_nama;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
    }

    $gambar_val = $gambar_nama ? "'$gambar_nama'" : "NULL";

    mysqli_query(
        $conn,
        "INSERT INTO produk(nama_produk, deskripsi, harga, stok, gambar)
         VALUES('$nama', '$deskripsi', '$harga', '$stok', $gambar_val)"
    );

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="brand">Admin Panel</a>
            <div class="nav-links">
                <a href="index.php">Kelola Produk</a>
                <a href="pesanan.php">Kelola Pesanan</a>
                <a href="../index.php">Lihat Website</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="auth-container" style="max-width: 600px; margin-top: 2rem;">
            <h2 class="auth-title">Tambah Produk Baru</h2>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="gambar">Foto Produk (Opsional)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Lengkap</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" name="harga" id="harga" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok Tersedia</label>
                        <input type="number" name="stok" id="stok" class="form-control" required>
                    </div>
                </div>

                <button name="simpan" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Simpan Produk
                </button>
            </form>
        </div>
    </main>

</body>
</html>