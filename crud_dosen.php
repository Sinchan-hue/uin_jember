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
        // Data utama
        $nama = $_POST['nama'];
        $nidn = $_POST['nidn'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $bidang_keahlian = $_POST['bidang_keahlian'];
        
        // Data peran dosen
        $ketua_sidang = isset($_POST['ketua_sidang']) ? (int)$_POST['ketua_sidang'] : 0;
        $penguji_utama = isset($_POST['penguji_utama']) ? (int)$_POST['penguji_utama'] : 0;
        $pembimbing1_penguji = isset($_POST['pembimbing1_penguji']) ? (int)$_POST['pembimbing1_penguji'] : 0;
        $pembimbing2_penguji = isset($_POST['pembimbing2_penguji']) ? (int)$_POST['pembimbing2_penguji'] : 0;
        $penguji1 = isset($_POST['penguji1']) ? (int)$_POST['penguji1'] : 0;
        $penguji2 = isset($_POST['penguji2']) ? (int)$_POST['penguji2'] : 0;
        $penguji3 = isset($_POST['penguji3']) ? (int)$_POST['penguji3'] : 0;
        $penguji4 = isset($_POST['penguji4']) ? (int)$_POST['penguji4'] : 0;
        $promotor = isset($_POST['promotor']) ? (int)$_POST['promotor'] : 0;
        $co_promotor = isset($_POST['co_promotor']) ? (int)$_POST['co_promotor'] : 0;
        $sekretaris = isset($_POST['sekretaris']) ? (int)$_POST['sekretaris'] : 0;
        
        $sql = "INSERT INTO dosen (nama, nidn, email, telepon, bidang_keahlian, 
                ketua_sidang, penguji_utama, pembimbing1_penguji, pembimbing2_penguji, 
                penguji1, penguji2, penguji3, penguji4, promotor, co_promotor, sekretaris) 
                VALUES ('$nama', '$nidn', '$email', '$telepon', '$bidang_keahlian',
                $ketua_sidang, $penguji_utama, $pembimbing1_penguji, $pembimbing2_penguji,
                $penguji1, $penguji2, $penguji3, $penguji4, $promotor, $co_promotor, $sekretaris)";
        $koneksi->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id_dosen = $_POST['id_dosen'];
        $nama = $_POST['nama'];
        $nidn = $_POST['nidn'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $bidang_keahlian = $_POST['bidang_keahlian'];
        
        // Data peran dosen
        $ketua_sidang = isset($_POST['ketua_sidang']) ? (int)$_POST['ketua_sidang'] : 0;
        $penguji_utama = isset($_POST['penguji_utama']) ? (int)$_POST['penguji_utama'] : 0;
        $pembimbing1_penguji = isset($_POST['pembimbing1_penguji']) ? (int)$_POST['pembimbing1_penguji'] : 0;
        $pembimbing2_penguji = isset($_POST['pembimbing2_penguji']) ? (int)$_POST['pembimbing2_penguji'] : 0;
        $penguji1 = isset($_POST['penguji1']) ? (int)$_POST['penguji1'] : 0;
        $penguji2 = isset($_POST['penguji2']) ? (int)$_POST['penguji2'] : 0;
        $penguji3 = isset($_POST['penguji3']) ? (int)$_POST['penguji3'] : 0;
        $penguji4 = isset($_POST['penguji4']) ? (int)$_POST['penguji4'] : 0;
        $promotor = isset($_POST['promotor']) ? (int)$_POST['promotor'] : 0;
        $co_promotor = isset($_POST['co_promotor']) ? (int)$_POST['co_promotor'] : 0;
        $sekretaris = isset($_POST['sekretaris']) ? (int)$_POST['sekretaris'] : 0;
        
        $sql = "UPDATE dosen SET 
                nama='$nama', nidn='$nidn', email='$email', telepon='$telepon', 
                bidang_keahlian='$bidang_keahlian',
                ketua_sidang=$ketua_sidang, penguji_utama=$penguji_utama, 
                pembimbing1_penguji=$pembimbing1_penguji, pembimbing2_penguji=$pembimbing2_penguji,
                penguji1=$penguji1, penguji2=$penguji2, penguji3=$penguji3, penguji4=$penguji4,
                promotor=$promotor, co_promotor=$co_promotor, sekretaris=$sekretaris
                WHERE id_dosen=$id_dosen";
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
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
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
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="number"] {
            width: 80px;
        }
        .role-badge {
            display: inline-block;
            background-color: #e0e0e0;
            padding: 2px 8px;
            border-radius: 10px;
            margin: 2px;
            font-size: 12px;
        }
        .role-badge.active {
            background-color: #4CAF50;
            color: white;
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
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
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
                            <td>
                                <?php 
                                $roles = [
                                    'ketua_sidang' => 'Ketua Sidang',
                                    'penguji_utama' => 'Penguji Utama',
                                    'pembimbing1_penguji' => 'Pembimbing 1',
                                    'pembimbing2_penguji' => 'Pembimbing 2',
                                    'penguji1' => 'Penguji 1',
                                    'penguji2' => 'Penguji 2',
                                    'penguji3' => 'Penguji 3',
                                    'penguji4' => 'Penguji 4',
                                    'promotor' => 'Promotor',
                                    'co_promotor' => 'Co-Promotor',
                                    'sekretaris' => 'Sekretaris'
                                ];
                                
                                foreach ($roles as $key => $label) {
                                    if ($row[$key] > 0) {
                                        echo '<span class="role-badge active">'.$label.' = '.$row[$key].'</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <form method="POST" action="crud_dosen.php" style="display:inline;">
                                    <input type="hidden" name="id_dosen" value="<?php echo $row['id_dosen']; ?>">
                                    <button type="submit" name="delete" class="btn-hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <button onclick="openEditModal(
                                    <?php echo $row['id_dosen']; ?>, 
                                    '<?php echo addslashes($row['nama']); ?>', 
                                    '<?php echo $row['nidn']; ?>', 
                                    '<?php echo $row['email']; ?>', 
                                    '<?php echo $row['telepon']; ?>', 
                                    '<?php echo addslashes($row['bidang_keahlian']); ?>',
                                    <?php echo $row['ketua_sidang']; ?>,
                                    <?php echo $row['penguji_utama']; ?>,
                                    <?php echo $row['pembimbing1_penguji']; ?>,
                                    <?php echo $row['pembimbing2_penguji']; ?>,
                                    <?php echo $row['penguji1']; ?>,
                                    <?php echo $row['penguji2']; ?>,
                                    <?php echo $row['penguji3']; ?>,
                                    <?php echo $row['penguji4']; ?>,
                                    <?php echo $row['promotor']; ?>,
                                    <?php echo $row['co_promotor']; ?>,
                                    <?php echo $row['sekretaris']; ?>
                                )" class="btn-edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
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

        <!-- Modal untuk Tambah/Edit Data -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Tambah Dosen</h2>
                <form method="POST" action="crud_dosen.php">
                    <input type="hidden" name="id_dosen" id="modalIdDosen">
                    <div class="form-grid">
                        <!-- Kolom 1 - Data Utama -->
                        <div>
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
                        </div>
                        
                        <!-- Kolom 2 - Peran Dosen -->
                        <div>
                            <div class="form-group">
                                <label for="ketua_sidang">Ketua Sidang:</label>
                                <input type="number" name="ketua_sidang" id="modalKetuaSidang" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="penguji_utama">Penguji Utama:</label>
                                <input type="number" name="penguji_utama" id="modalPengujiUtama" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="pembimbing1_penguji">Pembimbing 1 / Penguji:</label>
                                <input type="number" name="pembimbing1_penguji" id="modalPembimbing1Penguji" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="pembimbing2_penguji">Pembimbing 2 / Penguji:</label>
                                <input type="number" name="pembimbing2_penguji" id="modalPembimbing2Penguji" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="penguji1">Penguji 1:</label>
                                <input type="number" name="penguji1" id="modalPenguji1" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="penguji2">Penguji 2:</label>
                                <input type="number" name="penguji2" id="modalPenguji2" min="0" value="0">
                            </div>
                        </div>
                        
                        <!-- Kolom 3 - Peran Dosen Lanjutan -->
                        <div>
                            <div class="form-group">
                                <label for="penguji3">Penguji 3:</label>
                                <input type="number" name="penguji3" id="modalPenguji3" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="penguji4">Penguji 4:</label>
                                <input type="number" name="penguji4" id="modalPenguji4" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="promotor">Promotor:</label>
                                <input type="number" name="promotor" id="modalPromotor" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="co_promotor">Co-Promotor:</label>
                                <input type="number" name="co_promotor" id="modalCoPromotor" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="sekretaris">Sekretaris:</label>
                                <input type="number" name="sekretaris" id="modalSekretaris" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="submit" name="add" id="modalSubmit" class="btn-simpan">Simpan</button>
                        <button type="button" onclick="closeModal()" class="btn-batal">Batal</button>
                    </div>
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
            
            // Reset semua input peran ke 0
            document.querySelectorAll('.modal-content input[type="number"]').forEach(input => {
                input.value = 0;
            });
            
            document.getElementById('modalSubmit').name = 'add';
            document.getElementById('modal').style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(id_dosen, nama, nidn, email, telepon, bidang_keahlian,
                            ketua_sidang, penguji_utama, pembimbing1_penguji, pembimbing2_penguji,
                            penguji1, penguji2, penguji3, penguji4, promotor, co_promotor, sekretaris) {
            document.getElementById('modalTitle').innerText = 'Edit Dosen';
            document.getElementById('modalIdDosen').value = id_dosen;
            document.getElementById('modalNama').value = nama;
            document.getElementById('modalNidn').value = nidn;
            document.getElementById('modalEmail').value = email;
            document.getElementById('modalTelepon').value = telepon;
            document.getElementById('modalBidangKeahlian').value = bidang_keahlian;
            
            // Set nilai peran
            document.getElementById('modalKetuaSidang').value = ketua_sidang;
            document.getElementById('modalPengujiUtama').value = penguji_utama;
            document.getElementById('modalPembimbing1Penguji').value = pembimbing1_penguji;
            document.getElementById('modalPembimbing2Penguji').value = pembimbing2_penguji;
            document.getElementById('modalPenguji1').value = penguji1;
            document.getElementById('modalPenguji2').value = penguji2;
            document.getElementById('modalPenguji3').value = penguji3;
            document.getElementById('modalPenguji4').value = penguji4;
            document.getElementById('modalPromotor').value = promotor;
            document.getElementById('modalCoPromotor').value = co_promotor;
            document.getElementById('modalSekretaris').value = sekretaris;
            
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