<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "kas";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if(!$koneksi){
    die("koneksi gagal");
}

?>
