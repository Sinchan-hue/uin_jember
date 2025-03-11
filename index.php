<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

// Konfigurasi pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query dasar
$query = "SELECT t.judul, m.nama AS mahasiswa, t.status 
          FROM tesis t 
          JOIN mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa 
          WHERE t.status = 'diajukan'";

// Tambahkan pencarian jika ada input
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $koneksi->real_escape_string($_GET['search']);
    $query .= " AND (t.judul LIKE '%$search%' OR m.nama LIKE '%$search%')";
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
    <title>Daftar Tesis Diajukan</title>
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
            <h1>Daftar Tesis Diajukan</h1>

            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari judul atau nama mahasiswa..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Tabel Data Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Tesis</th>
                        <th>Nama Mahasiswa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['judul']}</td>
                                    <td>{$row['mahasiswa']}</td>
                                    <td>{$row['status']}</td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>Tidak ada data ditemukan.</td></tr>";
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