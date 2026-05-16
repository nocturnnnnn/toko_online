<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Harus login untuk menambah keranjang
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $produk_id = $_GET['id'];
    $user_id = $_SESSION['user']['id'];

    // Cek apakah barang sudah ada di keranjang
    $cek_query = mysqli_query($conn, "SELECT * FROM keranjang WHERE user_id='$user_id' AND produk_id='$produk_id'");
    
    if (mysqli_num_rows($cek_query) > 0) {
        // Jika sudah ada, tambahkan jumlahnya
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE user_id='$user_id' AND produk_id='$produk_id'");
    } else {
        // Jika belum ada, insert baru dengan jumlah 1
        mysqli_query($conn, "INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES ('$user_id', '$produk_id', 1)");
    }
}

header("Location: keranjang.php");
exit;
