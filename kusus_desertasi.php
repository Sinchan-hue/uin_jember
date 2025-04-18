<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Pencarian dan Filter Tanggal
$search = isset($_GET['search']) ? $_GET['search'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$searchCondition = $search ? "WHERE (m.nama LIKE '%$search%' OR t.judul LIKE '%$search%' OR p.Program LIKE '%$search%')" : 'WHERE 1=1';

// Tambahkan filter tanggal jika ada
if (!empty($start_date)) {
    $searchCondition .= " AND k.tanggal_ujian >= '$start_date'";
}
if (!empty($end_date)) {
    $searchCondition .= " AND k.tanggal_ujian <= '$end_date'";
}

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
        k.ketua_sidang,
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
$queryTesis = "SELECT id_tesis, judul FROM tesis WHERE id_prodi > 8";
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
        $ketua_sidang = $_POST['ketua_sidang'] ?? null;

        if (isset($_POST['add'])) {
            $sql = "INSERT INTO kusus_desertasi (id_tesis, tanggal_ujian, nilai, masa_berlaku, promotor, copromotor, penguji_utama, sekretaris_penguji, penguji_1, penguji_2, penguji_3, penguji_4, ketua_sidang) 
                    VALUES ('$id_tesis', '$tanggal_ujian', '$nilai', '$masa_berlaku', '$promotor', '$copromotor', '$penguji_utama', '$sekretaris_penguji', '$penguji_1', '$penguji_2', '$penguji_3', '$penguji_4', '$ketua_sidang')";
            $koneksi->query($sql);
        } elseif (isset($_POST['edit'])) {
            $id = $_POST['id'];
            $sql = "UPDATE kusus_desertasi 
                    SET id_tesis='$id_tesis', tanggal_ujian='$tanggal_ujian', nilai='$nilai', masa_berlaku='$masa_berlaku', 
                        promotor='$promotor', copromotor='$copromotor', penguji_utama='$penguji_utama', 
                        sekretaris_penguji='$sekretaris_penguji', penguji_1='$penguji_1', penguji_2='$penguji_2', 
                        penguji_3='$penguji_3', penguji_4='$penguji_4', ketua_sidang='$ketua_sidang' 
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
    <style>
        /* Gaya untuk Modal Popup */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .modal-content .close:hover {
            color: #000;
        }

        /* Gaya untuk Tombol Hapus */
        .btn-hapus {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-hapus:hover {
            background-color: #ff1a1a;
        }

        /* Filter Container Styles */
        .filter-container {
            margin: 20px 0;
        }

        .filter-section {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: #444;
            white-space: nowrap;
        }

        /* Date Picker Styles */
        input[type="date"] {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            width: 160px;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
            background-color: #fff;
        }

        /* Filter Button Styles */
        .filter-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #5a6fd1 0%, #6a3d9a 100%);
        }

        /* Reset Button Styles */
        .reset-btn {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #555;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #e6e9f0 0%, #b8c2d8 100%);
        }

        /* Print Button Styles */
        .print-btn {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #3a56a0 0%, #121f3d 100%);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Data Kusus Desertasi</h2>

            <!-- Form Pencarian dan Filter -->
            <div class="search-and-add">
                <form method="GET" action="kusus_desertasi.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Data</button>
            </div>

            <!-- Filter Tanggal -->
            <div class="filter-container">
                <form method="GET" action="kusus_desertasi.php" class="filter-section">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    
                    <div class="filter-group">
                        <label for="start_date">Dari:</label>
                        <input type="date" id="start_date" name="start_date" 
                            value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="end_date">Sampai:</label>
                        <input type="date" id="end_date" name="end_date" 
                            value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    </div>
                    
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    <a href="?search=<?php echo urlencode($search); ?>" class="reset-btn">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                    
                    <button type="button" onclick="printPreview()" class="print-btn">
                        <i class="fas fa-print"></i> Print Preview
                    </button>
                </form>
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
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Tanggal Ujian</th>
                            <th>Ketua Sidang</th>                                                     
                            <th>Penguji Utama</th> 
                            <th>Sekretaris Penguji</th>                              
                            <th>Penguji 1</th>
                            <th>Penguji 2</th>
                            <th>Penguji 3</th>
                            <th>Penguji 4</th>
                            <th>Promotor</th>
                            <th>Co-Promotor</th>                            
                            <th>Nilai</th>
                            <th>Masa Berlaku</th>
                            <th>Aksi</th>
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
                            <td>
                                <form method="POST" action="kusus_desertasi.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <button onclick="openEditModal(
                                    <?php echo $row['id']; ?>,
                                    '<?php echo $row['id_tesis']; ?>',
                                    '<?php echo date('Y-m-d', strtotime($row['tanggal_ujian'])); ?>',
                                    '<?php echo $row['nilai']; ?>',
                                    '<?php echo date('Y-m-d', strtotime($row['masa_berlaku'])); ?>',
                                    '<?php echo $row['promotor']; ?>',
                                    '<?php echo $row['copromotor']; ?>',
                                    '<?php echo $row['penguji_utama']; ?>',
                                    '<?php echo $row['sekretaris_penguji']; ?>',
                                    '<?php echo $row['penguji_1']; ?>',
                                    '<?php echo $row['penguji_2']; ?>',
                                    '<?php echo $row['penguji_3']; ?>',
                                    '<?php echo $row['penguji_4']; ?>',
                                    '<?php echo $row['ketua_sidang']; ?>'
                                )" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Modal untuk Tambah/Edit Data -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Tambah Kusus Desertasi</h2>
                <form method="POST" action="kusus_desertasi.php">
                    <input type="hidden" name="id" id="modalId">
                    <div class="form-group">
                        <label for="id_tesis">Tesis:</label>
                        <select name="id_tesis" id="modalIdTesis" required>
                            <option value="">Pilih Tesis</option>
                            <?php while ($rowTesis = $resultTesis->fetch_assoc()): ?>
                                <option value="<?php echo $rowTesis['id_tesis']; ?>"><?php echo $rowTesis['judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_ujian">Tanggal Ujian:</label>
                        <input type="date" name="tanggal_ujian" id="modalTanggalUjian" required>
                    </div>
                    <div class="form-group">
                        <label for="nilai">Nilai:</label>
                        <input type="number" name="nilai" id="modalNilai" required>
                    </div>
                    <div class="form-group">
                        <label for="masa_berlaku">Masa Berlaku:</label>
                        <input type="date" name="masa_berlaku" id="modalMasaBerlaku" required>
                    </div>
                    <div class="form-group">
                        <label for="sekretaris_penguji">Sekretaris Penguji:</label>
                        <select name="sekretaris_penguji" id="modalSekretarisPenguji" required>
                            <option value="">Pilih Sekretaris Penguji</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penguji_utama">Penguji Utama:</label>
                        <select name="penguji_utama" id="modalPengujiUtama" required>
                            <option value="">Pilih Penguji Utama</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penguji_1">Penguji 1:</label>
                        <select name="penguji_1" id="modalPenguji1">
                            <option value="">Pilih Penguji 1</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penguji_2">Penguji 2:</label>
                        <select name="penguji_2" id="modalPenguji2">
                            <option value="">Pilih Penguji 2</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penguji_3">Penguji 3:</label>
                        <select name="penguji_3" id="modalPenguji3">
                            <option value="">Pilih Penguji 3</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penguji_4">Penguji 4:</label>
                        <select name="penguji_4" id="modalPenguji4">
                            <option value="">Pilih Penguji 4</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="promotor">Promotor:</label>
                        <select name="promotor" id="modalPromotor" required>
                            <option value="">Pilih Promotor</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="copromotor">Co-Promotor:</label>
                        <select name="copromotor" id="modalCopromotor">
                            <option value="">Pilih Co-Promotor</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ketua_sidang">Ketua Sidang:</label>
                        <select name="ketua_sidang" id="modalKetuaSidang">
                            <option value="">Pilih Ketua Sidang</option>
                            <?php foreach ($dosenList as $dosen): ?>
                                <option value="<?php echo $dosen['id_dosen']; ?>"><?php echo $dosen['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add" id="modalSubmit" class="btn-simpan">Simpan</button>
                    <button type="button" onclick="closeModal()" class="btn-batal">Batal</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Fungsi untuk membuka modal tambah data
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Kusus Desertasi';
            document.getElementById('modalId').value = '';
            document.getElementById('modalTanggalUjian').value = '';
            document.getElementById('modalNilai').value = '';
            document.getElementById('modalMasaBerlaku').value = '';

            $('#modalIdTesis').val('').trigger('change');
            $('#modalPromotor').val('').trigger('change');
            $('#modalCopromotor').val('').trigger('change');
            $('#modalPengujiUtama').val('').trigger('change');
            $('#modalSekretarisPenguji').val('').trigger('change');
            $('#modalPenguji1').val('').trigger('change');
            $('#modalPenguji2').val('').trigger('change');
            $('#modalPenguji3').val('').trigger('change');
            $('#modalPenguji4').val('').trigger('change');
            $('#modalKetuaSidang').val('').trigger('change');

            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id, id_tesis, tanggal_ujian, nilai, masa_berlaku, promotor, copromotor, penguji_utama, sekretaris_penguji, penguji_1, penguji_2, penguji_3, penguji_4, ketua_sidang) {
            document.getElementById('modalTitle').innerText = 'Edit Kusus Desertasi';
            document.getElementById('modalId').value = id;
            document.getElementById('modalTanggalUjian').value = tanggal_ujian;
            document.getElementById('modalNilai').value = nilai;
            document.getElementById('modalMasaBerlaku').value = masa_berlaku;

            $('#modalIdTesis').val(id_tesis).trigger('change');
            $('#modalPromotor').val(promotor).trigger('change');
            $('#modalCopromotor').val(copromotor).trigger('change');
            $('#modalPengujiUtama').val(penguji_utama).trigger('change');
            $('#modalSekretarisPenguji').val(sekretaris_penguji).trigger('change');
            $('#modalPenguji1').val(penguji_1).trigger('change');
            $('#modalPenguji2').val(penguji_2).trigger('change');
            $('#modalPenguji3').val(penguji_3).trigger('change');
            $('#modalPenguji4').val(penguji_4).trigger('change');
            $('#modalKetuaSidang').val(ketua_sidang).trigger('change');

            document.getElementById('modalSubmit').name = 'edit';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Fungsi untuk print preview
        function printPreview() {
            const search = '<?php echo urlencode($search); ?>';
            const startDate = '<?php echo $start_date; ?>';
            const endDate = '<?php echo $end_date; ?>';
            
            let url = `print_desertasi_detail.php`;
            
            if (search) url += `?search=${search}`;
            if (startDate) url += `${search ? '&' : '?'}start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            
            window.open(url, '_blank');
        }

        // Inisialisasi Select2 untuk dropdown
        $(document).ready(function() {
            $('#modalIdTesis').select2({
                placeholder: "Cari tesis...",
                allowClear: true,
                width: '100%'
            });

            $('#modalPromotor, #modalCopromotor, #modalPengujiUtama, #modalSekretarisPenguji, #modalPenguji1, #modalPenguji2, #modalPenguji3, #modalPenguji4, #modalKetuaSidang').select2({
                placeholder: "Cari dosen...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>