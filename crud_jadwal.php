<?php
session_start(); // Mulai session di sini
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
$searchCondition = $search ? "WHERE id_jadwal LIKE '%$search%' OR id_tesis LIKE '%$search%' OR tanggal LIKE '%$search%' OR waktu LIKE '%$search%' OR tempat LIKE '%$search%' OR status LIKE '%$search%'" : '';

// Query untuk mengambil data jadwal sidang
$query = "SELECT * FROM jadwal_sidang $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM jadwal_sidang $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data tesis untuk dropdown
$queryTesis = "SELECT id_tesis, judul FROM tesis";
$resultTesis = $koneksi->query($queryTesis);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $id_tesis = $_POST['id_tesis'];
        $tanggal = $_POST['tanggal'];
        $waktu = $_POST['waktu'];
        $tempat = $_POST['tempat'];
        $status = $_POST['status'];
        $sql = "INSERT INTO jadwal_sidang (id_tesis, tanggal, waktu, tempat, status) VALUES ('$id_tesis', '$tanggal', '$waktu', '$tempat', '$status')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_jadwal = $_POST['id_jadwal'];
        $id_tesis = $_POST['id_tesis'];
        $tanggal = $_POST['tanggal'];
        $waktu = $_POST['waktu'];
        $tempat = $_POST['tempat'];
        $status = $_POST['status'];
        $sql = "UPDATE jadwal_sidang SET id_tesis='$id_tesis', tanggal='$tanggal', waktu='$waktu', tempat='$tempat', status='$status' WHERE id_jadwal=$id_jadwal";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_jadwal = $_POST['id_jadwal'];
        $sql = "DELETE FROM jadwal_sidang WHERE id_jadwal=$id_jadwal";
        $koneksi->query($sql);
    }
    header("Location: crud_jadwal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Jadwal Sidang</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            width: 400px;
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
    </style>
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
            <h2>Data Jadwal Sidang</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_jadwal.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Jadwal</button>
            </div>

            <!-- Tabel Data Jadwal Sidang -->
            <table>
                <thead>
                    <tr>
                        <th>ID Jadwal</th>
                        <th>ID Tesis</th>
                        <th>Judul Tesis</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Tempat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        // Ambil judul tesis
                        $queryJudulTesis = "SELECT judul FROM tesis WHERE id_tesis = " . $row['id_tesis'];
                        $resultJudulTesis = $koneksi->query($queryJudulTesis);
                        $judulTesis = $resultJudulTesis->fetch_assoc()['judul'];
                    ?>
                    <tr>
                        <td><?php echo $row['id_jadwal']; ?></td>
                        <td><?php echo $row['id_tesis']; ?></td>
                        <td><?php echo $judulTesis; ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['waktu']; ?></td>
                        <td><?php echo $row['tempat']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <button onclick="openEditModal(<?php echo $row['id_jadwal']; ?>, '<?php echo $row['id_tesis']; ?>', '<?php echo $row['tanggal']; ?>', '<?php echo $row['waktu']; ?>', '<?php echo $row['tempat']; ?>', '<?php echo $row['status']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
                            <form method="POST" action="crud_jadwal.php" style="display:inline;">
                                <input type="hidden" name="id_jadwal" value="<?php echo $row['id_jadwal']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>                            
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Modal untuk Tambah/Edit Data -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Tambah Jadwal Sidang</h2>
                <form method="POST" action="crud_jadwal.php">
                    <input type="hidden" name="id_jadwal" id="modalIdJadwal">
                    <div class="form-group">
                        <label for="id_tesis">Tesis:</label>
                        <select name="id_tesis" id="modalIdTesis" required>
                            <option value="">Pilih Tesis</option> <!-- Tambahkan opsi default -->
                            <?php while ($rowTesis = $resultTesis->fetch_assoc()): ?>
                                <option value="<?php echo $rowTesis['id_tesis']; ?>"><?php echo $rowTesis['judul']; ?></option>
                            <?php endwhile; ?>
                        </select>

                        <script>
                            $(document).ready(function() {
                                // Inisialisasi Select2 untuk Tesis
                                $('#modalIdTesis').select2({
                                    placeholder: "Cari tesis...", // Teks placeholder
                                    allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                                    width: '100%' // Lebar dropdown
                                });
                            });
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal:</label>
                        <input type="date" name="tanggal" id="modalTanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu">Waktu:</label>
                        <input type="time" name="waktu" id="modalWaktu" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat">Tempat:</label>
                        <input type="text" name="tempat" id="modalTempat" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="modalStatus" required>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="selesai">Selesai</option>
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
            document.getElementById('modalTitle').innerText = 'Tambah Jadwal Sidang';
            document.getElementById('modalIdJadwal').value = ''; // Kosongkan nilai id_jadwal
            document.getElementById('modalTanggal').value = ''; // Kosongkan tanggal
            document.getElementById('modalWaktu').value = ''; // Kosongkan waktu
            document.getElementById('modalTempat').value = ''; // Kosongkan tempat
            document.getElementById('modalStatus').value = 'terjadwal'; // Set status default
            document.getElementById('modalSubmit').name = 'add'; // Set nama tombol submit ke 'add'

            // Kosongkan nilai dan pilihan pada elemen <select> menggunakan Select2
            $('#modalIdTesis').val('').trigger('change');

            // Tampilkan modal
            document.getElementById('modal').style.display = 'flex';
        }

        
        // Fungsi untuk membuka modal edit data
        function openEditModal(id_jadwal, id_tesis, tanggal, waktu, tempat, status) {
            document.getElementById('modalTitle').innerText = 'Edit Jadwal Sidang';
            document.getElementById('modalIdJadwal').value = id_jadwal;
            document.getElementById('modalTanggal').value = tanggal;
            document.getElementById('modalWaktu').value = waktu;
            document.getElementById('modalTempat').value = tempat;
            document.getElementById('modalStatus').value = status;
            document.getElementById('modalSubmit').name = 'edit';
            
            // Set nilai yang dipilih pada elemen <select> menggunakan Select2
            $('#modalIdTesis').val(id_tesis).trigger('change');
            
            // Inisialisasi ulang Select2 jika diperlukan
            $('#modalIdTesis').select2({
                placeholder: "Cari tesis...",
                allowClear: true,
                width: '100%'
            });
            
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>