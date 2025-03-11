<?php
// File: koneksi.php
$host = "localhost"; // Host database
$username = "root"; // Username database
$password = ""; // Password database
$database = "sistem_tesis_disertasi"; // Nama database

// Buat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>