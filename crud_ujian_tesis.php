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
$searchCondition = $search ? "WHERE id_ujian LIKE '%$search%' OR id_tesis LIKE '%$search%' OR jenis_ujian LIKE '%$search%' OR tanggal_ujian LIKE '%$search%' OR id_dosen LIKE '%$search%' OR peran_dosen LIKE '%$search%'" : '';

// Query untuk mengambil data ujian tesis
$query = "SELECT * FROM ujian_tesis $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM ujian_tesis $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data tesis untuk dropdown (hanya id_prodi <= 8)
$queryTesis = "SELECT t.id_tesis, t.judul 
               FROM tesis t
               LEFT JOIN prodi p ON t.id_prodi = p.id
               WHERE p.id <= 8";
$resultTesis = $koneksi->query($queryTesis);

// Ambil data dosen untuk dropdown
$queryDosen = "SELECT id_dosen, nama FROM dosen";
$resultDosen = $koneksi->query($queryDosen);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $id_tesis = $_POST['id_tesis'];
        $jenis_ujian = $_POST['jenis_ujian'];
        $tanggal_ujian = $_POST['tanggal_ujian'];
        $id_dosen = $_POST['id_dosen'];
        $peran_dosen = $_POST['peran_dosen'];
        $sql = "INSERT INTO ujian_tesis (id_tesis, jenis_ujian, tanggal_ujian, id_dosen, peran_dosen) VALUES ('$id_tesis', '$jenis_ujian', '$tanggal_ujian', '$id_dosen', '$peran_dosen')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_ujian = $_POST['id_ujian'];
        $id_tesis = $_POST['id_tesis'];
        $jenis_ujian = $_POST['jenis_ujian'];
        $tanggal_ujian = $_POST['tanggal_ujian'];
        $id_dosen = $_POST['id_dosen'];
        $peran_dosen = $_POST['peran_dosen'];
        $sql = "UPDATE ujian_tesis SET id_tesis='$id_tesis', jenis_ujian='$jenis_ujian', tanggal_ujian='$tanggal_ujian', id_dosen='$id_dosen', peran_dosen='$peran_dosen' WHERE id_ujian=$id_ujian";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_ujian = $_POST['id_ujian'];
        $sql = "DELETE FROM ujian_tesis WHERE id_ujian=$id_ujian";
        $koneksi->query($sql);
    }
    header("Location: crud_ujian_tesis.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Ujian Tesis</title>
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
            <h2>Data Ujian Tesis</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_ujian_tesis.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Ujian</button>
            </div>

            <!-- Tabel Data Ujian Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>ID Ujian</th>
                        <th>Judul Tesis</th>
                        <th>Jenis Ujian</th>
                        <th>Tanggal Ujian</th>
                        <th>Nama Dosen</th>
                        <th>Peran Dosen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        // Ambil judul tesis
                        $queryJudulTesis = "SELECT judul FROM tesis WHERE id_tesis = " . $row['id_tesis'];
                        $resultJudulTesis = $koneksi->query($queryJudulTesis);
                        $judulTesis = $resultJudulTesis->fetch_assoc()['judul'];

                        // Ambil nama dosen
                        $queryNamaDosen = "SELECT nama FROM dosen WHERE id_dosen = " . $row['id_dosen'];
                        $resultNamaDosen = $koneksi->query($queryNamaDosen);
                        $namaDosen = $resultNamaDosen->fetch_assoc()['nama'];
                    ?>
                    <tr>
                        <td><?php echo $row['id_ujian']; ?></td>
                        <td><?php echo $judulTesis; ?></td>
                        <td><?php echo $row['jenis_ujian']; ?></td>
                        <td><?php echo $row['tanggal_ujian']; ?></td>
                        <td><?php echo $namaDosen; ?></td>
                        <td><?php echo $row['peran_dosen']; ?></td>
                        <td>
                            <form method="POST" action="crud_ujian_tesis.php" style="display:inline;">
                                <input type="hidden" name="id_ujian" value="<?php echo $row['id_ujian']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button onclick="openEditModal(<?php echo $row['id_ujian']; ?>, '<?php echo $row['id_tesis']; ?>', '<?php echo $row['jenis_ujian']; ?>', '<?php echo $row['tanggal_ujian']; ?>', '<?php echo $row['id_dosen']; ?>', '<?php echo $row['peran_dosen']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                <h2 id="modalTitle">Tambah Ujian</h2>
                <form method="POST" action="crud_ujian_tesis.php">
                    <input type="hidden" name="id_ujian" id="modalIdUjian">
                    <div class="form-group">
                        <label for="id_tesis">Tesis:</label>
                        <select name="id_tesis" id="modalIdTesis" required>
                            <option value="">Pilih Tesis</option> <!-- Opsi default -->
                            <?php while ($rowTesis = $resultTesis->fetch_assoc()): ?>
                                <option value="<?php echo $rowTesis['id_tesis']; ?>"><?php echo $rowTesis['judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jenis_ujian">Jenis Ujian:</label>
                        <select name="jenis_ujian" id="modalJenisUjian" required>
                            <option value="">Pilih Jenis Ujian</option> <!-- Opsi default -->
                            <option value="ujian_kualifikasi">Ujian Kualifikasi</option>
                            <option value="ujian_proposal">Ujian Proposal</option>
                            <option value="seminar_hasil">Seminar Hasil</option>
                            <option value="ujian_tertutup">Ujian Tertutup</option>
                            <option value="ujian_terbuka">Ujian Terbuka</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_ujian">Tanggal Ujian:</label>
                        <input type="date" name="tanggal_ujian" id="modalTanggalUjian" required>
                    </div>

                    <div class="form-group">
                        <label for="id_dosen">Dosen:</label>
                        <select name="id_dosen" id="modalIdDosen" required>
                            <option value="">Pilih Dosen</option> <!-- Opsi default -->
                            <?php while ($rowDosen = $resultDosen->fetch_assoc()): ?>
                                <option value="<?php echo $rowDosen['id_dosen']; ?>"><?php echo $rowDosen['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="peran_dosen">Peran Dosen:</label>
                        <select name="peran_dosen" id="modalPeranDosen" required>
                            <option value="">Pilih Peran Dosen</option> <!-- Opsi default -->

                            <option value="pembimbing_penguji_1">Pembimbing/Penguji 1</option>
                            <option value="pembimbing_penguji_2">Pembimbing/Penguji 2</option>
                            <option value="penguji">Penguji</option>
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
            document.getElementById('modalTitle').innerText = 'Tambah Ujian';
            document.getElementById('modalIdUjian').value = '';
            document.getElementById('modalIdTesis').selectedIndex = 0;
            document.getElementById('modalJenisUjian').selectedIndex = 0;
            document.getElementById('modalTanggalUjian').value = '';
            document.getElementById('modalIdDosen').selectedIndex = 0;
            document.getElementById('modalPeranDosen').selectedIndex = 0;
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';

            // Reset Select2 (jika menggunakan Select2)
            $('#modalIdTesis').val(null).trigger('change');
            $('#modalIdDosen').val(null).trigger('change');
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_ujian, id_tesis, jenis_ujian, tanggal_ujian, id_dosen, peran_dosen) {
            document.getElementById('modalTitle').innerText = 'Edit Ujian';
            document.getElementById('modalIdUjian').value = id_ujian;
            document.getElementById('modalIdTesis').value = id_tesis;
            document.getElementById('modalJenisUjian').value = jenis_ujian;
            document.getElementById('modalTanggalUjian').value = tanggal_ujian;
            document.getElementById('modalIdDosen').value = id_dosen;
            document.getElementById('modalPeranDosen').value = peran_dosen;
            document.getElementById('modalSubmit').name = 'edit';
            document.getElementById('modal').style.display = 'flex';

            // Set nilai dropdown menggunakan Select2
            $('#modalIdTesis').val(id_tesis).trigger('change');
            $('#modalIdDosen').val(id_dosen).trigger('change');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Inisialisasi Select2 untuk dropdown
        $(document).ready(function() {
            $('#modalIdTesis').select2({
                placeholder: "Cari tesis...",
                allowClear: true,
                width: '100%'
            });

            $('#modalIdDosen').select2({
                placeholder: "Cari dosen...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>