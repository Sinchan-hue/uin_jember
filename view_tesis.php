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

// Query untuk mengambil data kusus_tesis
$query = "
    SELECT 
        k.id,
        k.id_tesis,
        k.id_penguji_utama,
        k.id_pembimbing1_penguji,
        k.id_pembimbing2_penguji,
        k.nilai,
        k.masa_berlaku,
        m.nama AS nama_mahasiswa,
        m.nim,
        t.judul AS judul_tesis,
        p.Program AS prodi,
        k.tanggal_ujian,
        d1.nama AS penguji_utama,
        d2.nama AS pembimbing1_penguji,
        d3.nama AS pembimbing2_penguji
    FROM 
        kusus_tesis k
    JOIN 
        tesis t ON k.id_tesis = t.id_tesis
    JOIN 
        mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN 
        prodi p ON t.id_prodi = p.id
    JOIN 
        dosen d1 ON k.id_penguji_utama = d1.id_dosen
    JOIN 
        dosen d2 ON k.id_pembimbing1_penguji = d2.id_dosen
    JOIN 
        dosen d3 ON k.id_pembimbing2_penguji = d3.id_dosen
    $searchCondition
    LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "
    SELECT COUNT(*) as total 
    FROM kusus_tesis k
    JOIN tesis t ON k.id_tesis = t.id_tesis
    JOIN mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN prodi p ON t.id_prodi = p.id
    $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data tesis untuk dropdown dengan kondisi id_prodi <= 8
$queryTesis = "
    SELECT t.id_tesis, t.judul 
    FROM tesis t
    WHERE t.id_prodi <= 8 
    AND (t.id_tesis NOT IN (SELECT k.id_tesis FROM kusus_tesis k) OR t.id_tesis = ?)
";
$stmt = $koneksi->prepare($queryTesis);
$stmt->bind_param("i", $id_tesis_edit); // $id_tesis_edit adalah id_tesis yang sedang dipilih
$stmt->execute();
$resultTesis = $stmt->get_result();

// Ambil data dosen untuk dropdown dan simpan dalam array
$queryDosen = "SELECT id_dosen, nama FROM dosen";
$resultDosen = $koneksi->query($queryDosen);
$dosenList = []; // Array untuk menyimpan data dosen
while ($rowDosen = $resultDosen->fetch_assoc()) {
    $dosenList[] = $rowDosen; // Simpan data dosen ke dalam array
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Delete Operation
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM kusus_tesis WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            header("Location: kusus_tesis.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $koneksi->error;
        }
    }

    // Handle Add/Edit Operation
    if (isset($_POST['id_tesis'], $_POST['tanggal_ujian'], $_POST['id_penguji_utama'], $_POST['id_pembimbing1_penguji'], $_POST['id_pembimbing2_penguji'], $_POST['nilai'], $_POST['masa_berlaku'])) {
        $id_tesis = $_POST['id_tesis'];
        $tanggal_ujian = $_POST['tanggal_ujian'];
        $id_penguji_utama = $_POST['id_penguji_utama'];
        $id_pembimbing1_penguji = $_POST['id_pembimbing1_penguji'];
        $id_pembimbing2_penguji = $_POST['id_pembimbing2_penguji'];
        $nilai = $_POST['nilai'];
        $masa_berlaku = $_POST['masa_berlaku'];
    
        if (isset($_POST['add'])) {
            $sql = "INSERT INTO kusus_tesis (id_tesis, tanggal_ujian, id_penguji_utama, id_pembimbing1_penguji, id_pembimbing2_penguji, nilai, masa_berlaku) 
                    VALUES ('$id_tesis', '$tanggal_ujian', '$id_penguji_utama', '$id_pembimbing1_penguji', '$id_pembimbing2_penguji', '$nilai', '$masa_berlaku')";
            $koneksi->query($sql);
        } elseif (isset($_POST['edit'])) {
            $id = $_POST['id'];
            $sql = "UPDATE kusus_tesis 
                    SET id_tesis='$id_tesis', tanggal_ujian='$tanggal_ujian', id_penguji_utama='$id_penguji_utama', 
                        id_pembimbing1_penguji='$id_pembimbing1_penguji', id_pembimbing2_penguji='$id_pembimbing2_penguji', 
                        nilai='$nilai', masa_berlaku='$masa_berlaku' 
                    WHERE id=$id";
            $koneksi->query($sql);
        }
        header("Location: kusus_tesis.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Kusus Tesis</title>
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
            <h2>Data Kusus Tesis</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="kusus_tesis.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Data</button>
            </div>

            <!-- Tabel Data Kusus Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Judul Tesis</th>
                        <th>Program Studi</th>
                        <th>Tanggal Ujian</th>
                        <th>Penguji Utama</th>
                        <th>Pembimbing 1</th>
                        <th>Pembimbing 2</th>
                        <th>Nilai</th>
                        <th>Masa Berlaku</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $no = 1; // Inisialisasi variabel counter untuk nomor urut
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $no; ?></td>
                        <td><?php echo $row['nama_mahasiswa']; ?></td>
                        <td><?php echo $row['nim']; ?></td>
                        <td><?php echo $row['judul_tesis']; ?></td>
                        <td><?php echo $row['prodi']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['tanggal_ujian'])); ?></td>
                        <td><?php echo $row['penguji_utama']; ?></td>
                        <td><?php echo $row['pembimbing1_penguji']; ?></td>
                        <td><?php echo $row['pembimbing2_penguji']; ?></td>
                        <td><?php echo $row['nilai']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['masa_berlaku'])); ?></td>
                        
                    </tr>
                    <?php 
                        $no++; // Increment counter untuk nomor urut
                        endwhile; 
                    ?>
                </tbody>
            </table>

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