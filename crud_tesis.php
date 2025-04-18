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
$searchCondition = $search ? "WHERE judul LIKE '%$search%' OR abstrak LIKE '%$search%' OR tahun LIKE '%$search%' OR id_mahasiswa LIKE '%$search%' OR id_prodi LIKE '%$search%' OR status LIKE '%$search%'" : '';

// Query untuk mengambil data tesis
$query = "SELECT * FROM tesis $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM tesis $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data mahasiswa untuk dropdown
$queryMahasiswa = "SELECT id_mahasiswa, nama FROM mahasiswa";
$resultMahasiswa = $koneksi->query($queryMahasiswa);

// Ambil data prodi untuk dropdown
$queryProdi = "SELECT id, Program FROM prodi";
$resultProdi = $koneksi->query($queryProdi);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $judul = $_POST['judul'];
        $abstrak = $_POST['abstrak'];
        $tahun = $_POST['tahun'];
        $id_mahasiswa = $_POST['id_mahasiswa'];
        $id_prodi = $_POST['id_prodi'];
        $status = $_POST['status'];
        $sql = "INSERT INTO tesis (judul, abstrak, tahun, id_mahasiswa, id_prodi, status) VALUES ('$judul', '$abstrak', '$tahun', '$id_mahasiswa', '$id_prodi', '$status')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_tesis = $_POST['id_tesis'];
        $judul = $_POST['judul'];
        $abstrak = $_POST['abstrak'];
        $tahun = $_POST['tahun'];
        $id_mahasiswa = $_POST['id_mahasiswa'];
        $id_prodi = $_POST['id_prodi'];
        $status = $_POST['status'];
        $sql = "UPDATE tesis SET judul='$judul', abstrak='$abstrak', tahun='$tahun', id_mahasiswa='$id_mahasiswa', id_prodi='$id_prodi', status='$status' WHERE id_tesis=$id_tesis";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_tesis = $_POST['id_tesis'];
        $sql = "DELETE FROM tesis WHERE id_tesis=$id_tesis";
        $koneksi->query($sql);
    }
    header("Location: crud_tesis.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Tesis</title>
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
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Data Tesis</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_tesis.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Tesis</button>
            </div>

            <!-- Tabel Data Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>ID Tesis</th>
                        <th>Judul</th>
                        <th>Abstrak</th>
                        <th>Tahun</th>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        // Ambil nama mahasiswa
                        $queryNamaMahasiswa = "SELECT nama FROM mahasiswa WHERE id_mahasiswa = " . $row['id_mahasiswa'];
                        $resultNamaMahasiswa = $koneksi->query($queryNamaMahasiswa);
                        $namaMahasiswa = $resultNamaMahasiswa->fetch_assoc()['nama'];

                        // Ambil nama program studi
                        $queryNamaProdi = "SELECT Program FROM prodi WHERE id = " . $row['id_prodi'];
                        $resultNamaProdi = $koneksi->query($queryNamaProdi);
                        $namaProdi = $resultNamaProdi->fetch_assoc()['Program'];
                    ?>
                    <tr>
                        <td><?php echo $row['id_tesis']; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['abstrak']; ?></td>
                        <td><?php echo $row['tahun']; ?></td>
                        <td><?php echo $namaMahasiswa; ?></td>
                        <td><?php echo $namaProdi; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <form method="POST" action="crud_tesis.php" style="display:inline;">
                                <input type="hidden" name="id_tesis" value="<?php echo $row['id_tesis']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button onclick="openEditModal(<?php echo $row['id_tesis']; ?>, '<?php echo $row['judul']; ?>', '<?php echo $row['abstrak']; ?>', '<?php echo $row['tahun']; ?>', '<?php echo $row['id_mahasiswa']; ?>', '<?php echo $row['id_prodi']; ?>', '<?php echo $row['status']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                <h2 id="modalTitle">Tambah Tesis</h2>
                <form method="POST" action="crud_tesis.php">
                    <input type="hidden" name="id_tesis" id="modalIdTesis">
                    <div class="form-group">
                        <label for="judul">Judul:</label>
                        <input type="text" name="judul" id="modalJudul" required>
                    </div>
                    <div class="form-group">
                        <label for="abstrak">Abstrak:</label>
                        <textarea name="abstrak" id="modalAbstrak" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tahun">Tahun:</label>
                        <input type="text" name="tahun" id="modalTahun" required>
                    </div>
                    <div class="form-group">
                        <label for="id_mahasiswa">Mahasiswa:</label>
                        <select name="id_mahasiswa" id="modalIdMahasiswa" required>
                            <option value="">Pilih Mahasiswa</option> <!-- Opsi default -->
                            <?php while ($rowMahasiswa = $resultMahasiswa->fetch_assoc()): ?>
                                <option value="<?php echo $rowMahasiswa['id_mahasiswa']; ?>"><?php echo $rowMahasiswa['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_prodi">Program Studi:</label>
                        <select name="id_prodi" id="modalIdProdi" required>
                            <option value="">Pilih Program Studi</option> <!-- Opsi default -->
                            <?php while ($rowProdi = $resultProdi->fetch_assoc()): ?>
                                <option value="<?php echo $rowProdi['id']; ?>"><?php echo $rowProdi['Program']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="modalStatus" required>
                            <option value="diajukan">Diajukan</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="selesai">Selesai</option>
                            <option value="lulus">Lulus</option>
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
            document.getElementById('modalTitle').innerText = 'Tambah Tesis';
            document.getElementById('modalIdTesis').value = '';
            document.getElementById('modalJudul').value = '';
            document.getElementById('modalAbstrak').value = '';
            document.getElementById('modalTahun').value = '';
            
            // Reset dropdown mahasiswa dan prodi
            document.getElementById('modalIdMahasiswa').selectedIndex = 0; // Pilih opsi pertama (default)
            document.getElementById('modalIdProdi').selectedIndex = 0; // Pilih opsi pertama (default)
            
            document.getElementById('modalStatus').value = 'diajukan';
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';

            // Reset Select2 (jika menggunakan Select2)
            $('#modalIdMahasiswa').val(null).trigger('change');
            $('#modalIdProdi').val(null).trigger('change');
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_tesis, judul, abstrak, tahun, id_mahasiswa, id_prodi, status) {
            document.getElementById('modalTitle').innerText = 'Edit Tesis';
            document.getElementById('modalIdTesis').value = id_tesis;
            document.getElementById('modalJudul').value = judul;
            document.getElementById('modalAbstrak').value = abstrak;
            document.getElementById('modalTahun').value = tahun;
            document.getElementById('modalIdMahasiswa').value = id_mahasiswa;
            document.getElementById('modalIdProdi').value = id_prodi;
            document.getElementById('modalStatus').value = status;
            document.getElementById('modalSubmit').name = 'edit';
            document.getElementById('modal').style.display = 'flex';

            // Set nilai dropdown menggunakan Select2
            $('#modalIdMahasiswa').val(id_mahasiswa).trigger('change');
            $('#modalIdProdi').val(id_prodi).trigger('change');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Inisialisasi Select2 untuk dropdown
        $(document).ready(function() {
            $('#modalIdMahasiswa').select2({
                placeholder: "Cari mahasiswa...",
                allowClear: true,
                width: '100%'
            });

            $('#modalIdProdi').select2({
                placeholder: "Cari program studi...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>