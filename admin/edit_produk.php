<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Ambil data lama
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_baru = $produk['gambar']; // Default tetap yang lama

    // Cek upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Hapus file lama jika ada
        if ($produk['gambar'] && file_exists("../assets/img/" . $produk['gambar'])) {
            unlink("../assets/img/" . $produk['gambar']);
        }

        // Simpan file baru
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_baru = uniqid() . '.' . $ext;
        $tujuan = "../assets/img/" . $gambar_baru;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
    }

    $gambar_val = $gambar_baru ? "'$gambar_baru'" : "NULL";

    mysqli_query(
        $conn,
        "UPDATE produk SET 
            nama_produk='$nama', 
            kategori='$kategori',
            deskripsi='$deskripsi', 
            harga='$harga', 
            stok='$stok',
            gambar=$gambar_val
         WHERE id='$id'"
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
    <title>Edit Produk - Admin</title>
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
            <h2 class="auth-title">Edit Data Produk</h2>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Foto Produk Saat Ini</label>
                    <?php if($produk['gambar']): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="../assets/img/<?= htmlspecialchars($produk['gambar']); ?>" alt="img" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem; border: 1px solid var(--border);">
                        </div>
                    <?php endif; ?>
                    <label for="gambar">Ganti Foto (Biarkan kosong jika tidak ingin ganti)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($produk['nama_produk'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Elektronik" <?= ($produk['kategori'] ?? '') === 'Elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                        <option value="Fashion" <?= ($produk['kategori'] ?? '') === 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
                        <option value="Kecantikan" <?= ($produk['kategori'] ?? '') === 'Kecantikan' ? 'selected' : ''; ?>>Kecantikan</option>
                        <option value="Olahraga" <?= ($produk['kategori'] ?? '') === 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                        <option value="Rumah Tangga" <?= ($produk['kategori'] ?? '') === 'Rumah Tangga' ? 'selected' : ''; ?>>Rumah Tangga</option>
                        <option value="Lainnya" <?= ($produk['kategori'] ?? '') === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Lengkap</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($produk['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" name="harga" id="harga" class="form-control" value="<?= htmlspecialchars($produk['harga'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok Tersedia</label>
                        <input type="number" name="stok" id="stok" class="form-control" value="<?= htmlspecialchars($produk['stok'] ?? ''); ?>" required>
                    </div>
                </div>

                <button name="update" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Update Produk
                </button>
            </form>
        </div>
    </main>

</body>
</html>
