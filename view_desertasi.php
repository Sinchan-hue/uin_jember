<?php
session_start(); // Mulai session
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
include 'koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = $search ? "WHERE m.nama LIKE '%$search%' OR t.judul LIKE '%$search%' OR p.Program LIKE '%$search%'" : '';

// Query untuk mengambil data kusus_desertasi
$query = "
    SELECT 
        k.id,
        k.id_tesis,
        k.tanggal_ujian,
        k.nilai,
        k.masa_berlaku,
        k.promotor,
        k.copromotor,
        k.penguji_utama,
        k.sekretaris_penguji,
        k.penguji_1,
        k.penguji_2,
        k.penguji_3,
        k.penguji_4,
        m.nama AS nama_mahasiswa,
        m.nim,
        t.judul AS judul_tesis,
        p.Program AS prodi,
        d1.nama AS nama_promotor,
        d2.nama AS nama_copromotor,
        d3.nama AS nama_penguji_utama,
        d4.nama AS nama_sekretaris_penguji,
        d5.nama AS nama_penguji_1,
        d6.nama AS nama_penguji_2,
        d7.nama AS nama_penguji_3,
        d8.nama AS nama_penguji_4
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
    $searchCondition
    LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "
    SELECT COUNT(*) as total 
    FROM kusus_desertasi k
    JOIN tesis t ON k.id_tesis = t.id_tesis
    JOIN mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN prodi p ON t.id_prodi = p.id
    $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data tesis untuk dropdown
$queryTesis = "SELECT id_tesis, judul FROM tesis";
$resultTesis = $koneksi->query($queryTesis);

// Ambil data dosen untuk dropdown
$queryDosen = "SELECT id_dosen, nama FROM dosen";
$resultDosen = $koneksi->query($queryDosen);
$dosenList = [];
while ($rowDosen = $resultDosen->fetch_assoc()) {
    $dosenList[] = $rowDosen;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Delete Operation
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM kusus_desertasi WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            header("Location: kusus_desertasi.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $koneksi->error;
        }
    }

    // Handle Add/Edit Operation
    if (isset($_POST['id_tesis'], $_POST['tanggal_ujian'], $_POST['nilai'], $_POST['masa_berlaku'], $_POST['promotor'], $_POST['penguji_utama'], $_POST['sekretaris_penguji'])) {
        $id_tesis = $_POST['id_tesis'];
        $tanggal_ujian = $_POST['tanggal_ujian'];
        $nilai = $_POST['nilai'];
        $masa_berlaku = $_POST['masa_berlaku'];
        $promotor = $_POST['promotor'];
        $copromotor = $_POST['copromotor'] ?? null;
        $penguji_utama = $_POST['penguji_utama'];
        $sekretaris_penguji = $_POST['sekretaris_penguji'];
        $penguji_1 = $_POST['penguji_1'] ?? null;
        $penguji_2 = $_POST['penguji_2'] ?? null;
        $penguji_3 = $_POST['penguji_3'] ?? null;
        $penguji_4 = $_POST['penguji_4'] ?? null;

        if (isset($_POST['add'])) {
            $sql = "INSERT INTO kusus_desertasi (id_tesis, tanggal_ujian, nilai, masa_berlaku, promotor, copromotor, penguji_utama, sekretaris_penguji, penguji_1, penguji_2, penguji_3, penguji_4) 
                    VALUES ('$id_tesis', '$tanggal_ujian', '$nilai', '$masa_berlaku', '$promotor', '$copromotor', '$penguji_utama', '$sekretaris_penguji', '$penguji_1', '$penguji_2', '$penguji_3', '$penguji_4')";
            $koneksi->query($sql);
        } elseif (isset($_POST['edit'])) {
            $id = $_POST['id'];
            $sql = "UPDATE kusus_desertasi 
                    SET id_tesis='$id_tesis', tanggal_ujian='$tanggal_ujian', nilai='$nilai', masa_berlaku='$masa_berlaku', 
                        promotor='$promotor', copromotor='$copromotor', penguji_utama='$penguji_utama', 
                        sekretaris_penguji='$sekretaris_penguji', penguji_1='$penguji_1', penguji_2='$penguji_2', 
                        penguji_3='$penguji_3', penguji_4='$penguji_4' 
                    WHERE id=$id";
            $koneksi->query($sql);
        }
        header("Location: kusus_desertasi.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Kusus Desertasi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (diperlukan oleh Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Data Kusus Desertasi</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="kusus_desertasi.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Data</button>
            </div>

            <!-- Tabel Data Kusus Desertasi -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Judul Tesis</th>
                            <th>Program Studi</th>
                            <th>Tanggal Ujian</th>
                            <th>Promotor</th>
                            <th>Co-Promotor</th>
                            <th>Penguji Utama</th>
                            <th>Sekretaris Penguji</th>
                            <th>Penguji 1</th>
                            <th>Penguji 2</th>
                            <th>Penguji 3</th>
                            <th>Penguji 4</th>
                            <th>Nilai</th>
                            <th>Masa Berlaku</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no = 1;
                            while ($row = $result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $row['nama_mahasiswa']; ?></td>
                            <td><?php echo $row['nim']; ?></td>
                            <td><?php echo $row['judul_tesis']; ?></td>
                            <td><?php echo $row['prodi']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_ujian'])); ?></td>
                            <td><?php echo $row['nama_promotor']; ?></td>
                            <td><?php echo $row['nama_copromotor']; ?></td>
                            <td><?php echo $row['nama_penguji_utama']; ?></td>
                            <td><?php echo $row['nama_sekretaris_penguji']; ?></td>
                            <td><?php echo $row['nama_penguji_1']; ?></td>
                            <td><?php echo $row['nama_penguji_2']; ?></td>
                            <td><?php echo $row['nama_penguji_3']; ?></td>
                            <td><?php echo $row['nama_penguji_4']; ?></td>
                            <td><?php echo $row['nilai']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['masa_berlaku'])); ?></td>
                            
                            </td>
                        </tr>
                        <?php 
                            $no++;
                            endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>

        
    </main>

    <?php include 'footer.php'; ?>

    
</body>
</html>