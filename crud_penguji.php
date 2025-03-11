<?php
include 'koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = $search ? "WHERE id_penguji LIKE '%$search%' OR id_tesis LIKE '%$search%' OR id_dosen LIKE '%$search%' OR peran LIKE '%$search%'" : '';

// Query untuk mengambil data penguji
$query = "SELECT * FROM penguji $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM penguji $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data tesis untuk dropdown
$queryTesis = "SELECT id_tesis, judul FROM tesis";
$resultTesis = $koneksi->query($queryTesis);

// Ambil data dosen untuk dropdown
$queryDosen = "SELECT id_dosen, nama FROM dosen";
$resultDosen = $koneksi->query($queryDosen);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $id_tesis = $_POST['id_tesis'];
        $id_dosen = $_POST['id_dosen'];
        $peran = $_POST['peran'];
        $sql = "INSERT INTO penguji (id_tesis, id_dosen, peran) VALUES ('$id_tesis', '$id_dosen', '$peran')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_penguji = $_POST['id_penguji'];
        $id_tesis = $_POST['id_tesis'];
        $id_dosen = $_POST['id_dosen'];
        $peran = $_POST['peran'];
        $sql = "UPDATE penguji SET id_tesis='$id_tesis', id_dosen='$id_dosen', peran='$peran' WHERE id_penguji=$id_penguji";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_penguji = $_POST['id_penguji'];
        $sql = "DELETE FROM penguji WHERE id_penguji=$id_penguji";
        $koneksi->query($sql);
    }
    header("Location: crud_penguji.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Penguji</title>
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
            <h2>Data Penguji</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_penguji.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Penguji</button>
            </div>

            <!-- Tabel Data Penguji -->
            <table>
                <thead>
                    <tr>
                        <th>ID Penguji</th>
                        <th>ID Tesis</th>
                        <th>Judul Tesis</th>
                        <th>ID Dosen</th>
                        <th>Nama Dosen</th>
                        <th>Peran</th>
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
                        <td><?php echo $row['id_penguji']; ?></td>
                        <td><?php echo $row['id_tesis']; ?></td>
                        <td><?php echo $judulTesis; ?></td>
                        <td><?php echo $row['id_dosen']; ?></td>
                        <td><?php echo $namaDosen; ?></td>
                        <td><?php echo $row['peran']; ?></td>
                        <td>
                            <form method="POST" action="crud_penguji.php" style="display:inline;">
                                <input type="hidden" name="id_penguji" value="<?php echo $row['id_penguji']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button onclick="openEditModal(<?php echo $row['id_penguji']; ?>, '<?php echo $row['id_tesis']; ?>', '<?php echo $row['id_dosen']; ?>', '<?php echo $row['peran']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                <h2 id="modalTitle">Tambah Penguji</h2>
                <form method="POST" action="crud_penguji.php">
                    <input type="hidden" name="id_penguji" id="modalIdPenguji">
                    <div class="form-group">
                        <label for="id_tesis">Tesis:</label>
                        <select name="id_tesis" id="modalIdTesis" required>
                            <option value="">Pilih Tesis</option> <!-- Tambahkan opsi default -->
                            <?php while ($rowTesis = $resultTesis->fetch_assoc()): ?>
                                <option value="<?php echo $rowTesis['id_tesis']; ?>"><?php echo $rowTesis['judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_dosen">Dosen:</label>
                        <select name="id_dosen" id="modalIdDosen" required>
                            <option value="">Pilih Dosen</option> <!-- Tambahkan opsi default -->
                            <?php while ($rowDosen = $resultDosen->fetch_assoc()): ?>
                                <option value="<?php echo $rowDosen['id_dosen']; ?>"><?php echo $rowDosen['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <script>
                        $(document).ready(function() {
                            // Inisialisasi Select2 untuk Tesis
                            $('#modalIdTesis').select2({
                                placeholder: "Cari tesis...", // Teks placeholder
                                allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                                width: '100%' // Lebar dropdown
                            });

                            // Inisialisasi Select2 untuk Dosen
                            $('#modalIdDosen').select2({
                                placeholder: "Cari dosen...", // Teks placeholder
                                allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                                width: '100%' // Lebar dropdown
                            });
                        });
                    </script>
                    <div class="form-group">
                        <label for="peran">Peran:</label>
                        <select name="peran" id="modalPeran" required>
                            <option value="ketua_penguji">Ketua Penguji</option>
                            <option value="anggota_penguji">Anggota Penguji</option>
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
            document.getElementById('modalTitle').innerText = 'Tambah Penguji';
            document.getElementById('modalIdPenguji').value = '';
            document.getElementById('modalIdTesis').value = '';
            document.getElementById('modalIdDosen').value = '';
            document.getElementById('modalPeran').value = 'ketua_penguji';
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_penguji, id_tesis, id_dosen, peran) {
            document.getElementById('modalTitle').innerText = 'Edit Penguji';
            document.getElementById('modalIdPenguji').value = id_penguji;
            document.getElementById('modalIdTesis').value = id_tesis;
            document.getElementById('modalIdDosen').value = id_dosen;
            document.getElementById('modalPeran').value = peran;
            document.getElementById('modalSubmit').name = 'edit';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>