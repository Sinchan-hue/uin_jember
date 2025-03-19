<?php
session_start(); // Mulai session
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
include 'koneksi.php'; // Sertakan file koneksi database

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data rekap desertasi dengan pencarian
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.promotor = d.id_dosen THEN 1 END) AS promotor,
        COUNT(CASE WHEN k.copromotor = d.id_dosen THEN 1 END) AS copromotor,
        COUNT(CASE WHEN k.penguji_utama = d.id_dosen THEN 1 END) AS penguji_utama,
        COUNT(CASE WHEN k.sekretaris_penguji = d.id_dosen THEN 1 END) AS sekretaris_penguji,
        COUNT(CASE WHEN k.penguji_1 = d.id_dosen THEN 1 END) AS penguji_1,
        COUNT(CASE WHEN k.penguji_2 = d.id_dosen THEN 1 END) AS penguji_2,
        COUNT(CASE WHEN k.penguji_3 = d.id_dosen THEN 1 END) AS penguji_3,
        COUNT(CASE WHEN k.penguji_4 = d.id_dosen THEN 1 END) AS penguji_4
    FROM 
        dosen d
    LEFT JOIN 
        kusus_desertasi k ON d.id_dosen = k.promotor 
        OR d.id_dosen = k.copromotor 
        OR d.id_dosen = k.penguji_utama 
        OR d.id_dosen = k.sekretaris_penguji 
        OR d.id_dosen = k.penguji_1 
        OR d.id_dosen = k.penguji_2 
        OR d.id_dosen = k.penguji_3 
        OR d.id_dosen = k.penguji_4
    WHERE 
        d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
    GROUP BY 
        d.id_dosen
    HAVING 
        promotor != 0 
        OR copromotor != 0 
        OR penguji_utama != 0 
        OR sekretaris_penguji != 0 
        OR penguji_1 != 0 
        OR penguji_2 != 0 
        OR penguji_3 != 0 
        OR penguji_4 != 0
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
            kusus_desertasi k ON d.id_dosen = k.promotor 
            OR d.id_dosen = k.copromotor 
            OR d.id_dosen = k.penguji_utama 
            OR d.id_dosen = k.sekretaris_penguji 
            OR d.id_dosen = k.penguji_1 
            OR d.id_dosen = k.penguji_2 
            OR d.id_dosen = k.penguji_3 
            OR d.id_dosen = k.penguji_4
        WHERE 
            d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
        GROUP BY 
            d.id_dosen
        HAVING 
            COUNT(CASE WHEN k.promotor = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.copromotor = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.penguji_utama = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.sekretaris_penguji = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.penguji_1 = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.penguji_2 = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.penguji_3 = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.penguji_4 = d.id_dosen THEN 1 END) != 0
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
    <title>Rekap Desertasi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Gaya untuk container tabel */
        .table-container {
            width: 100%;
            overflow-x: auto; /* Tambahkan scroll horizontal */
            margin-top: 20px; /* Jarak dari elemen di atasnya */
        }

        /* Gaya untuk tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            min-width: 1200px; /* Lebar minimum tabel */
        }

        table thead {
            background-color: #007bff;
            color: #fff;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap; /* Mencegah teks dari wrapping */
        }

        table th {
            font-weight: bold;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Gaya untuk pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background 0.3s, color 0.3s;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination a:hover:not(.active) {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Rekap Desertasi</h2>

            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari nama dosen atau golongan..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Tabel Rekap Desertasi -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA DOSEN</th>
                            <th>GOLONGAN</th>
                            <th>PROMOTOR</th>
                            <th>CO-PROMOTOR</th>
                            <th>PENGUJI UTAMA</th>
                            <th>SEKRETARIS PENGUJI</th>
                            <th>PENGUJI 1</th>
                            <th>PENGUJI 2</th>
                            <th>PENGUJI 3</th>
                            <th>PENGUJI 4</th>
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
                            <td><?php echo $row['promotor']; ?></td>
                            <td><?php echo $row['copromotor']; ?></td>
                            <td><?php echo $row['penguji_utama']; ?></td>
                            <td><?php echo $row['sekretaris_penguji']; ?></td>
                            <td><?php echo $row['penguji_1']; ?></td>
                            <td><?php echo $row['penguji_2']; ?></td>
                            <td><?php echo $row['penguji_3']; ?></td>
                            <td><?php echo $row['penguji_4']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

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