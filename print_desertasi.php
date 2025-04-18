<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Ambil parameter
$search = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Query untuk data desertasi
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.promotor = d.id_dosen THEN 1 END) AS jumlah_promotor,
        COUNT(CASE WHEN k.copromotor = d.id_dosen THEN 1 END) AS jumlah_copromotor,
        GROUP_CONCAT(DISTINCT DATE_FORMAT(k.tanggal_ujian, '%d-%m-%Y') ORDER BY k.tanggal_ujian SEPARATOR ', ') AS tanggal_ujian_list
    FROM 
        dosen d
    LEFT JOIN 
        kusus_desertasi k ON (d.id_dosen = k.promotor OR d.id_dosen = k.copromotor)";
        
// Filter tanggal jika ada
if (!empty($start_date)) {
    $query .= " AND k.tanggal_ujian >= '$start_date'";
}
if (!empty($end_date)) {
    $query .= " AND k.tanggal_ujian <= '$end_date'";
}

$query .= "
    WHERE 
        d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
    GROUP BY 
        d.id_dosen
    HAVING 
        jumlah_promotor != 0 OR jumlah_copromotor != 0
    ORDER BY 
        d.nama ASC";

$result = $koneksi->query($query);

// Judul berdasarkan filter
$judul = "Rekap Desertasi";
if (!empty($start_date) || !empty($end_date)) {
    $judul .= " (";
    if (!empty($start_date)) $judul .= "Dari: " . date('d/m/Y', strtotime($start_date));
    if (!empty($end_date)) $judul .= " Sampai: " . date('d/m/Y', strtotime($end_date));
    $judul .= ")";
}
if (!empty($search)) {
    $judul .= " - Pencarian: " . urldecode($search);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $judul; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4b6cb7;
            padding-bottom: 10px;
        }
        .print-header h2 {
            color: #4b6cb7;
            margin: 0;
        }
        .print-header p {
            margin: 5px 0 0;
            color: #666;
        }
        .print-info {
            margin-bottom: 15px;
            font-size: 14px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }
        th {
            background-color: #4b6cb7;
            color: white;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h2>REKAP DESERTASI</h2>
        <p>UIN Kiai Haji Achmad Siddiq</p>
    </div>
    
    <div class="print-info">
        <?php if (!empty($start_date) || !empty($end_date)): ?>
            <p><strong>Periode:</strong> 
                <?php if (!empty($start_date)) echo date('d/m/Y', strtotime($start_date)); ?>
                <?php if (!empty($end_date)) echo " - " . date('d/m/Y', strtotime($end_date)); ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($search)): ?>
            <p><strong>Kata Kunci:</strong> <?php echo htmlspecialchars(urldecode($search)); ?></p>
        <?php endif; ?>
        <p><strong>Tanggal Cetak:</strong> <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA DOSEN</th>
                <th>GOLONGAN</th>
                <th>JUMLAH PROMOTOR</th>
                <th>JUMLAH CO-PROMOTOR</th>
                <th>TANGGAL UJIAN</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama_dosen']; ?></td>
                <td><?php echo $row['golongan']; ?></td>
                <td><?php echo $row['jumlah_promotor']; ?></td>
                <td><?php echo $row['jumlah_copromotor']; ?></td>
                <td><?php echo $row['tanggal_ujian_list']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: <?php echo $_SESSION['username'] ?? 'Admin'; ?> pada <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="background: #4b6cb7; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak Dokumen
        </button>
        <button onclick="window.close()" style="background: #666; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print();
        });
    </script>
</body>
</html>