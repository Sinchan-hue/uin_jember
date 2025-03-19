<?php
session_start(); // Mulai session
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
include 'koneksi.php'; // Sertakan file koneksi database

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data rekap tesis dengan pencarian
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) AS penguji_utama,
        COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) AS pembimbing1_penguji,
        COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) AS pembimbing2_penguji
    FROM 
        dosen d
    LEFT JOIN 
        kusus_tesis k ON d.id_dosen = k.id_penguji_utama 
        OR d.id_dosen = k.id_pembimbing1_penguji 
        OR d.id_dosen = k.id_pembimbing2_penguji
    WHERE 
        d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
    GROUP BY 
        d.id_dosen
    HAVING 
        penguji_utama != 0 
        OR pembimbing1_penguji != 0 
        OR pembimbing2_penguji != 0
    ORDER BY 
        d.nama ASC";

$result = $koneksi->query($query);

// Pagination
$rows_per_page = 10; // Jumlah baris per halaman

// Query untuk menghitung total baris yang memenuhi kriteria
$count_query = "
    SELECT 
        COUNT(*) AS total
    FROM (
        SELECT 
            d.id_dosen
        FROM 
            dosen d
        LEFT JOIN 
            kusus_tesis k ON d.id_dosen = k.id_penguji_utama 
            OR d.id_dosen = k.id_pembimbing1_penguji 
            OR d.id_dosen = k.id_pembimbing2_penguji
        WHERE 
            d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
        GROUP BY 
            d.id_dosen
        HAVING 
            COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) != 0
    ) AS filtered_data";

$count_result = $koneksi->query($count_query);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $rows_per_page);

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($current_page - 1) * $rows_per_page; // Offset untuk query

// Query dengan pagination
$query .= " LIMIT $offset, $rows_per_page";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Tesis</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Rekap Tesis</h2>

            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari nama dosen atau golongan..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Tabel Rekap Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA DOSEN</th>
                        <th>GOLONGAN</th>
                        <th>PENGUJI UTAMA</th>
                        <th>PEMBIMBING 1 / PENGUJI</th>
                        <th>PEMBIMBING 2 / PENGUJI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = ($current_page - 1) * $rows_per_page + 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_dosen']; ?></td>
                        <td><?php echo $row['golongan']; ?></td>
                        <td><?php echo $row['penguji_utama']; ?></td>
                        <td><?php echo $row['pembimbing1_penguji']; ?></td>
                        <td><?php echo $row['pembimbing2_penguji']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>