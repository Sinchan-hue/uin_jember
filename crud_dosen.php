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
$searchCondition = $search ? "WHERE nama LIKE '%$search%' OR nidn LIKE '%$search%' OR email LIKE '%$search%'" : '';

// Query untuk mengambil data dosen
$query = "SELECT * FROM dosen $searchCondition LIMIT $start, $limit";
$result = $koneksi->query($query);

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM dosen $searchCondition";
$totalResult = $koneksi->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['nama'];
        $nidn = $_POST['nidn'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $bidang_keahlian = $_POST['bidang_keahlian'];
        $max = $_POST['max'];
        $sql = "INSERT INTO dosen (nama, nidn, email, telepon, bidang_keahlian, max) VALUES ('$nama', '$nidn', '$email', '$telepon', '$bidang_keahlian', '$max')";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_dosen = $_POST['id_dosen'];
        $nama = $_POST['nama'];
        $nidn = $_POST['nidn'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $bidang_keahlian = $_POST['bidang_keahlian'];
        $max = $_POST['max'];
        $sql = "UPDATE dosen SET nama='$nama', nidn='$nidn', email='$email', telepon='$telepon', bidang_keahlian='$bidang_keahlian', max='$max' WHERE id_dosen=$id_dosen";
        $koneksi->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id_dosen = $_POST['id_dosen'];
        $sql = "DELETE FROM dosen WHERE id_dosen=$id_dosen";
        $koneksi->query($sql);
    }
    header("Location: crud_dosen.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Dosen</title>
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
            <h2>Data Dosen</h2>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <form method="GET" action="crud_dosen.php" class="search-form">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
                <button onclick="openAddModal()" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Dosen</button>
            </div>

            <!-- Tabel Data Dosen -->
            <table>
                <thead>
                    <tr>
                        <th>ID Dosen</th>
                        <th>Nama</th>
                        <th>NIDN</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Bidang Keahlian</th>
                        <th>Max</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_dosen']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['nidn']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['telepon']; ?></td>
                        <td><?php echo $row['bidang_keahlian']; ?></td>
                        <td><?php echo $row['max']; ?></td>
                        <td>
                            <form method="POST" action="crud_dosen.php" style="display:inline;">
                                <input type="hidden" name="id_dosen" value="<?php echo $row['id_dosen']; ?>">
                                <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button onclick="openEditModal(<?php echo $row['id_dosen']; ?>, '<?php echo $row['nama']; ?>', '<?php echo $row['nidn']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['telepon']; ?>', '<?php echo $row['bidang_keahlian']; ?>', '<?php echo $row['max']; ?>')" class="btn-edit"><i class="fas fa-edit"></i></button>
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
                <h2 id="modalTitle">Tambah Dosen</h2>
                <form method="POST" action="crud_dosen.php">
                    <input type="hidden" name="id_dosen" id="modalIdDosen">
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" id="modalNama" required>
                    </div>
                    <div class="form-group">
                        <label for="nidn">NIDN:</label>
                        <input type="text" name="nidn" id="modalNidn" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="modalEmail">
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" id="modalTelepon">
                    </div>
                    <div class="form-group">
                        <label for="bidang_keahlian">Bidang Keahlian:</label>
                        <input type="text" name="bidang_keahlian" id="modalBidangKeahlian">
                    </div>
                    <div class="form-group">
                        <label for="max">Max:</label>
                        <input type="number" name="max" id="modalMax" required>
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
            document.getElementById('modalTitle').innerText = 'Tambah Dosen';
            document.getElementById('modalIdDosen').value = '';
            document.getElementById('modalNama').value = '';
            document.getElementById('modalNidn').value = '';
            document.getElementById('modalEmail').value = '';
            document.getElementById('modalTelepon').value = '';
            document.getElementById('modalBidangKeahlian').value = '';
            document.getElementById('modalMax').value = '';
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_dosen, nama, nidn, email, telepon, bidang_keahlian, max) {
            document.getElementById('modalTitle').innerText = 'Edit Dosen';
            document.getElementById('modalIdDosen').value = id_dosen;
            document.getElementById('modalNama').value = nama;
            document.getElementById('modalNidn').value = nidn;
            document.getElementById('modalEmail').value = email;
            document.getElementById('modalTelepon').value = telepon;
            document.getElementById('modalBidangKeahlian').value = bidang_keahlian;
            document.getElementById('modalMax').value = max;
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