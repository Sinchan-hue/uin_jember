<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

// Proses CRUD
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    switch ($action) {
        case 'delete':
            $query = "DELETE FROM pembimbing WHERE id_pembimbing = $id";
            if ($koneksi->query($query)) {
                echo "<script>alert('Data pembimbing berhasil dihapus.');</script>";
            } else {
                echo "<script>alert('Gagal menghapus data pembimbing.');</script>";
            }
            break;
    }
}

// Konfigurasi pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query dasar
$query = "SELECT p.id_pembimbing, t.judul AS tesis, d.nama AS dosen, p.peran 
          FROM pembimbing p
          JOIN tesis t ON p.id_tesis = t.id_tesis
          JOIN dosen d ON p.id_dosen = d.id_dosen";

// Tambahkan pencarian jika ada input
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $koneksi->real_escape_string($_GET['search']);
    $query .= " WHERE t.judul LIKE '%$search%' OR d.nama LIKE '%$search%' OR p.peran LIKE '%$search%'";
}

// Hitung total data untuk pagination
$result_total = $koneksi->query($query);
$total_data = $result_total->num_rows;
$total_pages = ceil($total_data / $limit);

// Tambahkan limit dan offset untuk pagination
$query .= " LIMIT $limit OFFSET $offset";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Pembimbing</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="content">
        <div class="container">
            <h1>CRUD Pembimbing</h1>

            <!-- Form Pencarian dan Tombol Tambah Data -->
            <div class="search-and-add">
                <!-- Form Pencarian -->
                <form method="GET" action="" class="search-form">
                    <input type="text" name="search" placeholder="Cari judul tesis, nama dosen, atau peran..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>

                <!-- Tombol Tambah Data -->
                <a href="form_pembimbing.php" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Data</a>
            </div>

            <!-- Tabel Data Pembimbing -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tesis</th>
                        <th>Dosen</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['tesis']}</td>
                                    <td>{$row['dosen']}</td>
                                    <td>{$row['peran']}</td>
                                    <td>
                                        <a href='form_pembimbing.php?id={$row['id_pembimbing']}' class='btn-edit'><i class='fas fa-edit'></i> Edit</a>
                                        <a href='crud_pembimbing.php?action=delete&id={$row['id_pembimbing']}' class='btn-hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'><i class='fas fa-trash'></i> Hapus</a>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>Tidak ada data ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
// Tutup koneksi
$koneksi->close();
?>