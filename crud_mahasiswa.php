<?php
require_once 'init.php'; // Ganti dari config.php ke init.php
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
$searchCondition = $search ? "WHERE nama LIKE '%$search%' OR nim LIKE '%$search%' OR email LIKE '%$search%' OR telepon LIKE '%$search%'" : '';

// Query untuk mengambil data mahasiswa
$query = "SELECT * FROM mahasiswa $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM mahasiswa $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['nama'];
        $nim = $_POST['nim'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $sql = "INSERT INTO mahasiswa (nama, nim, email, telepon) VALUES ('$nama', '$nim', '$email', '$telepon')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_mahasiswa = $_POST['id_mahasiswa'];
        $nama = $_POST['nama'];
        $nim = $_POST['nim'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $sql = "UPDATE mahasiswa SET nama='$nama', nim='$nim', email='$email', telepon='$telepon' WHERE id_mahasiswa=$id_mahasiswa";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_mahasiswa = $_POST['id_mahasiswa'];
        $sql = "DELETE FROM mahasiswa WHERE id_mahasiswa=$id_mahasiswa";
        $koneksi->query($sql);
    }
    header("Location: crud_mahasiswa.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Mahasiswa</title>
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
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Data Mahasiswa</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_mahasiswa.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Mahasiswa</button>
            </div>

            <!-- Tabel Data Mahasiswa -->
            <table>
                <thead>
                    <tr>
                        <th>ID Mahasiswa</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_mahasiswa']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['nim']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['telepon']; ?></td>
                        <td>
                            <form method="POST" action="crud_mahasiswa.php" style="display:inline;">
                                <input type="hidden" name="id_mahasiswa" value="<?php echo $row['id_mahasiswa']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button onclick="openEditModal(<?php echo $row['id_mahasiswa']; ?>, '<?php echo $row['nama']; ?>', '<?php echo $row['nim']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['telepon']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                <h2 id="modalTitle">Tambah Mahasiswa</h2>
                <form method="POST" action="crud_mahasiswa.php">
                    <input type="hidden" name="id_mahasiswa" id="modalIdMahasiswa">
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" id="modalNama" required>
                    </div>
                    <div class="form-group">
                        <label for="nim">NIM:</label>
                        <input type="text" name="nim" id="modalNim" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="modalEmail">
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" id="modalTelepon">
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
            document.getElementById('modalTitle').innerText = 'Tambah Mahasiswa';
            document.getElementById('modalIdMahasiswa').value = '';
            document.getElementById('modalNama').value = '';
            document.getElementById('modalNim').value = '';
            document.getElementById('modalEmail').value = '';
            document.getElementById('modalTelepon').value = '';
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_mahasiswa, nama, nim, email, telepon) {
            document.getElementById('modalTitle').innerText = 'Edit Mahasiswa';
            document.getElementById('modalIdMahasiswa').value = id_mahasiswa;
            document.getElementById('modalNama').value = nama;
            document.getElementById('modalNim').value = nim;
            document.getElementById('modalEmail').value = email;
            document.getElementById('modalTelepon').value = telepon;
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