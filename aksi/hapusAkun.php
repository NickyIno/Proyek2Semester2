<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) { exit; }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "DELETE FROM master_kas WHERE id = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../user/dashboard.php?pesan=hapus_berhasil");
    } else {
        echo "Gagal hapus: " . mysqli_error($koneksi);
    }
}
?>