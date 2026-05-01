<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "Kas";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if(!$koneksi){
    die("koneksi gagal");
}

?>
