<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) { exit; }

if (isset($_GET['id_transaksi']) && isset($_GET['back'])) {
    $id_trans  = $_GET['id_transaksi'];
    $id_master = $_GET['back']; 
    
    $query = "DELETE FROM transaksi_kas WHERE id = '$id_trans'";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../user/bukuKas.php?id=" . $id_master);
        exit();
    } else {
        echo "Gagal hapus transaksi: " . mysqli_error($koneksi);
    }
} else {
    echo "Parameter tidak lengkap!";
}
?>
