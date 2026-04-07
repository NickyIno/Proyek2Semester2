<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "warung_tahu_kupat";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if(!$koneksi){
    die("koneksi gagal");
}

?>