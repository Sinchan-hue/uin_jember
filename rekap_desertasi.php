<?php
session_start(); // Mulai session
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
include 'koneksi.php'; // Sertakan file koneksi database

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Variabel jenis ujian
$type = isset($_GET['type']) ? $_GET['type'] : '';

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
        COUNT(CASE WHEN k.penguji_4 = d.id_dosen THEN 1 END) AS penguji_4,
        COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) AS ketua_sidang
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
        OR d.id_dosen = k.ketua_sidang
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
        OR ketua_sidang != 0
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
            OR d.id_dosen = k.ketua_sidang
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
            OR COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) != 0
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
        /* Button Container Styles */
        .button-container {
            margin: 25px 0;
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
        }
        
        .button-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .button-container::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        
        .button-container a {
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Gradient backgrounds for each button */
        .button-container a:nth-child(1) {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        
        .button-container a:nth-child(2) {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .button-container a:nth-child(3) {
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        }
        
        .button-container a:nth-child(4) {
            background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);
        }
        
        .button-container a:nth-child(5) {
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
        }
        
        /* Hover effects */
        .button-container a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Active state */
        .button-container a.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
            transform: translateY(0);
        }
        
        /* Table Container Styles */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        
        table thead {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
        }
        
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            white-space: nowrap;
        }
        
        table th {
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            gap: 8px;
        }
        
        .pagination a {
            padding: 10px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .pagination a.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            border-color: #007bff;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #f1f8ff;
            border-color: #007bff;
        }
    
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Rekap Desertasi</h2>
            <div class="button-container">
                <a href="?type=kualifikasi" class="<?php echo ($_GET['type'] ?? '') == 'kualifikasi' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check" style="margin-right: 8px;"></i> Ujian Kualifikasi
                </a>
                <a href="?type=proposal" class="<?php echo ($_GET['type'] ?? '') == 'proposal' ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature" style="margin-right: 8px;"></i> Ujian Proposal Disertasi
                </a>
                <a href="?type=seminar" class="<?php echo ($_GET['type'] ?? '') == 'seminar' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar" style="margin-right: 8px;"></i> Ujian Seminar Hasil Disertasi
                </a>
                <a href="?type=tertutup" class="<?php echo ($_GET['type'] ?? '') == 'tertutup' ? 'active' : ''; ?>">
                    <i class="fas fa-lock" style="margin-right: 8px;"></i> Ujian Tertutup
                </a>
                <a href="?type=terbuka" class="<?php echo ($_GET['type'] ?? '') == 'terbuka' ? 'active' : ''; ?>">
                    <i class="fas fa-door-open" style="margin-right: 8px;"></i> Ujian Terbuka
                </a>
            </div>
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
                            <th>GOLONGAN/HOME BASE</th>
                            <?php if ($type == 'kualifikasi'): ?>
                                <th>KETUA SIDANG</th>
                                <th>PENGUJI 1</th>
                                <th>PENGUJI 2</th>
                                <th>SEKRETARIS</th>
                            <?php elseif ($type == 'proposal'): ?>
                                <th>KETUA SIDANG</th>
                                <th>PENGUJI UTAMA</th>
                                <th>PENGUJI 1</th>
                                <th>PROMOTOR</th>
                                <th>CO-PROMOTOR</th>
                            <?php elseif ($type == 'seminar'): ?>
                                <th>KETUA SIDANG</th>
                                <th>PENGUJI UTAMA</th>
                                <th>PENGUJI 1</th>
                                <th>PENGUJI 2</th>
                                <th>PROMOTOR</th>
                                <th>CO-PROMOTOR</th>
                            <?php elseif ($type == 'tertutup' || $type == 'terbuka'): ?>
                                <th>KETUA SIDANG</th>
                                <th>PENGUJI UTAMA</th>
                                <th>PENGUJI 1</th>
                                <th>PENGUJI 2</th>
                                <th>PENGUJI 3</th>
                                <th>PENGUJI 4</th>
                                <th>PROMOTOR</th>
                                <th>CO-PROMOTOR</th>
                            <?php else: ?>
                                <th>KETUA SIDANG</th>
                                <th>PROMOTOR</th>                          
                                <th>PENGUJI 1</th>
                                <th>PENGUJI 2</th>
                                <th>PENGUJI 3</th>
                                <th>PENGUJI 4</th>
                                <th>CO-PROMOTOR</th>
                                <th>PENGUJI UTAMA</th>
                            <?php endif; ?>
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
                            <?php if ($type == 'kualifikasi'): ?>
                                <td><?php echo $row['ketua_sidang']; ?></td>
                                <td><?php echo $row['penguji_1']; ?></td>
                                <td><?php echo $row['penguji_2']; ?></td>
                                <td><?php echo $row['sekretaris_penguji']; ?></td>
                            <?php elseif ($type == 'proposal'): ?>
                                <td><?php echo $row['ketua_sidang']; ?></td>
                                <td><?php echo $row['penguji_utama']; ?></td>
                                <td><?php echo $row['penguji_1']; ?></td>
                                <td><?php echo $row['promotor']; ?></td>
                                <td><?php echo $row['copromotor']; ?></td>
                            <?php elseif ($type == 'seminar'): ?>
                                <td><?php echo $row['ketua_sidang']; ?></td>
                                <td><?php echo $row['penguji_utama']; ?></td>
                                <td><?php echo $row['penguji_1']; ?></td>
                                <td><?php echo $row['penguji_2']; ?></td>
                                <td><?php echo $row['promotor']; ?></td>
                                <td><?php echo $row['copromotor']; ?></td>
                            <?php elseif ($type == 'tertutup' || $type == 'terbuka'): ?>
                                <td><?php echo $row['ketua_sidang']; ?></td>
                                <td><?php echo $row['penguji_utama']; ?></td>
                                <td><?php echo $row['penguji_1']; ?></td>
                                <td><?php echo $row['penguji_2']; ?></td>
                                <td><?php echo $row['penguji_3']; ?></td>
                                <td><?php echo $row['penguji_4']; ?></td>
                                <td><?php echo $row['promotor']; ?></td>
                                <td><?php echo $row['copromotor']; ?></td>
                            <?php else: ?>
                                <td><?php echo $row['ketua_sidang']; ?></td>                            
                                <td><?php echo $row['promotor']; ?></td>
                                <td><?php echo $row['copromotor']; ?></td>
                                <td><?php echo $row['penguji_1']; ?></td>
                                <td><?php echo $row['penguji_2']; ?></td>
                                <td><?php echo $row['penguji_3']; ?></td>
                                <td><?php echo $row['penguji_4']; ?></td>
                                <td><?php echo $row['penguji_utama']; ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>