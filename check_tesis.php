<?php
include 'koneksi.php';

$id_tesis = $_GET['id_tesis'];

// Query untuk memeriksa apakah id_tesis sudah ada di tabel ujian_disertasi
$query = "SELECT COUNT(*) as total FROM ujian_disertasi WHERE id_tesis = $id_tesis";
$result = $koneksi->query($query);
$row = $result->fetch_assoc();

// Jika id_tesis sudah ada, kembalikan respons JSON
echo json_encode(['exists' => $row['total'] > 0]);
?>