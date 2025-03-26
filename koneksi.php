<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "sistem_tesis_disertasi";
$koneksi = new mysqli($host, $username, $password, $database);
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set charset untuk menghindari masalah karakter
$koneksi->set_charset("utf8mb4");
?>