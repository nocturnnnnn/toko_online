<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Cek file gambar
    $query = mysqli_query($conn, "SELECT gambar FROM produk WHERE id='$id'");
    if ($produk = mysqli_fetch_assoc($query)) {
        if ($produk['gambar'] && file_exists("../assets/img/" . $produk['gambar'])) {
            unlink("../assets/img/" . $produk['gambar']);
        }
    }
    
    // Hapus dari database
    mysqli_query($conn, "DELETE FROM produk WHERE id='$id'");
}

header("Location: index.php");
exit;
