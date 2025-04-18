<?php
session_start();
include 'koneksi.php';

$id_tesis_edit = isset($_GET['id_tesis']) ? (int)$_GET['id_tesis'] : 0;

// Query untuk mengambil data tesis
$queryTesis = "
    SELECT t.id_tesis, t.judul 
    FROM tesis t
    WHERE t.id_prodi <= 8 
    AND (t.id_tesis NOT IN (SELECT k.id_tesis FROM kusus_tesis k) OR t.id_tesis = ?)
";
$stmt = $koneksi->prepare($queryTesis);
$stmt->bind_param("i", $id_tesis_edit);
$stmt->execute();
$resultTesis = $stmt->get_result();

$dataTesis = [];
while ($row = $resultTesis->fetch_assoc()) {
    $dataTesis[] = $row;
}

echo json_encode($dataTesis);
?>