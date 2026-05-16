<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if (isset($_GET['id'])) {
    // Hapus 1 item
    $keranjang_id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id='$keranjang_id' AND user_id='$user_id'");
}

if (isset($_GET['clear'])) {
    // Kosongkan semua keranjang
    mysqli_query($conn, "DELETE FROM keranjang WHERE user_id='$user_id'");
}

header("Location: keranjang.php");
exit;
