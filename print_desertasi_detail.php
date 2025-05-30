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
        k.id,
        k.tanggal_ujian,
        k.nilai,
        k.masa_berlaku,
        m.nama AS nama_mahasiswa,
        m.nim,
        m.email,
        m.telepon,
        t.judul AS judul_tesis,
        p.Program AS prodi,
        d1.nama AS nama_promotor,
        d2.nama AS nama_copromotor,
        d3.nama AS nama_penguji_utama,
        d4.nama AS nama_sekretaris_penguji,
        d5.nama AS nama_penguji_1,
        d6.nama AS nama_penguji_2,
        d7.nama AS nama_penguji_3,
        d8.nama AS nama_penguji_4,
        d9.nama AS nama_ketua_sidang
    FROM 
        kusus_desertasi k
    JOIN 
        tesis t ON k.id_tesis = t.id_tesis
    JOIN 
        mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN 
        prodi p ON t.id_prodi = p.id
    LEFT JOIN 
        dosen d1 ON k.promotor = d1.id_dosen
    LEFT JOIN 
        dosen d2 ON k.copromotor = d2.id_dosen
    LEFT JOIN 
        dosen d3 ON k.penguji_utama = d3.id_dosen
    LEFT JOIN 
        dosen d4 ON k.sekretaris_penguji = d4.id_dosen
    LEFT JOIN 
        dosen d5 ON k.penguji_1 = d5.id_dosen
    LEFT JOIN 
        dosen d6 ON k.penguji_2 = d6.id_dosen
    LEFT JOIN 
        dosen d7 ON k.penguji_3 = d7.id_dosen
    LEFT JOIN 
        dosen d8 ON k.penguji_4 = d8.id_dosen
    LEFT JOIN 
        dosen d9 ON k.ketua_sidang = d9.id_dosen
    WHERE 
        1=1";

// Filter pencarian
if (!empty($search)) {
    $query .= " AND (m.nama LIKE '%$search%' OR t.judul LIKE '%$search%' OR p.Program LIKE '%$search%')";
}

// Filter tanggal
if (!empty($start_date)) {
    $query .= " AND k.tanggal_ujian >= '$start_date'";
}
if (!empty($end_date)) {
    $query .= " AND k.tanggal_ujian <= '$end_date'";
}

$query .= " ORDER BY k.tanggal_ujian DESC";
$result = $koneksi->query($query);

// Judul berdasarkan filter
$judul = "Data Desertasi";
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
        <h2>DATA DESERTASI</h2>
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
                <th>NAMA MAHASISWA</th>
                <th>NIM</th>
                <th>JUDUL TESIS</th>
                <th>PRODI</th>
                <th>EMAIL</th>
                <th>TELEPON</th>
                <th>TANGGAL UJIAN</th>
                <th>KETUA SIDANG</th>
                <th>PENGUJI UTAMA</th>
                <th>SEKRETARIS PENGUJI</th>
                <th>PENGUJI 1</th>
                <th>PENGUJI 2</th>
                <th>PENGUJI 3</th>
                <th>PENGUJI 4</th>
                <th>PROMOTOR</th>
                <th>CO-PROMOTOR</th>
                <th>NILAI</th>
                <th>MASA BERLAKU</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama_mahasiswa']; ?></td>
                <td><?php echo $row['nim']; ?></td>
                <td><?php echo $row['judul_tesis']; ?></td>
                <td><?php echo $row['prodi']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telepon']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['tanggal_ujian'])); ?></td>
                <td><?php echo $row['nama_ketua_sidang']; ?></td>
                <td><?php echo $row['nama_penguji_utama']; ?></td>
                <td><?php echo $row['nama_sekretaris_penguji']; ?></td>
                <td><?php echo $row['nama_penguji_1']; ?></td>
                <td><?php echo $row['nama_penguji_2']; ?></td>
                <td><?php echo $row['nama_penguji_3']; ?></td>
                <td><?php echo $row['nama_penguji_4']; ?></td>
                <td><?php echo $row['nama_promotor']; ?></td>
                <td><?php echo $row['nama_copromotor']; ?></td>
                <td><?php echo $row['nilai']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['masa_berlaku'])); ?></td>
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