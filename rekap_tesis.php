<?php
require_once 'init.php'; // Ganti dari config.php ke init.php
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
include 'koneksi.php'; // Sertakan file koneksi database

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Tentukan jenis seminar yang dipilih
$jenis_seminar = isset($_GET['jenis']) ? $_GET['jenis'] : 'proposal'; // Default: Seminar Proposal

// Query dasar untuk mengambil data rekap tesis dengan pencarian
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) AS penguji_utama,
        COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) AS pembimbing1_penguji,
        COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) AS pembimbing2_penguji,
        COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) AS ketua_sidang
    FROM 
        dosen d
    LEFT JOIN 
        kusus_tesis k ON d.id_dosen = k.id_penguji_utama 
        OR d.id_dosen = k.id_pembimbing1_penguji 
        OR d.id_dosen = k.id_pembimbing2_penguji
        OR d.id_dosen = k.ketua_sidang
    WHERE 
        d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
    GROUP BY 
        d.id_dosen
    HAVING 
        penguji_utama != 0 
        OR pembimbing1_penguji != 0 
        OR pembimbing2_penguji != 0
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
            kusus_tesis k ON d.id_dosen = k.id_penguji_utama 
            OR d.id_dosen = k.id_pembimbing1_penguji 
            OR d.id_dosen = k.id_pembimbing2_penguji
            OR d.id_dosen = k.ketua_sidang
        WHERE 
            d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%'
        GROUP BY 
            d.id_dosen
        HAVING 
            COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) != 0 
            OR COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) != 0
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
    <title>Rekap Tesis</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .button-container {
            margin: 25px 0;
            display: flex;
            gap: 15px;
            justify-content: left;
        }
        
        .button-container a {
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        
        /* Hover effects */
        .button-container a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Active state */
        .button-container a.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
        }
        
        /* Ripple effect */
        .button-container a::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        .button-container a:focus:not(:active)::after {
            animation: ripple 0.6s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Rekap Tesis</h2>
            <!-- Tombol Seminar Proposal, Seminar Hasil, dan Ujian Tesis -->
            <div class="button-container">
                <a href="?jenis=proposal" class="<?php echo ($jenis_seminar == 'proposal') ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt" style="margin-right: 8px;"></i> Seminar Proposal
                </a>
                <a href="?jenis=hasil" class="<?php echo ($jenis_seminar == 'hasil') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line" style="margin-right: 8px;"></i> Seminar Hasil
                </a>
                <a href="?jenis=ujian" class="<?php echo ($jenis_seminar == 'ujian') ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap" style="margin-right: 8px;"></i> Ujian Tesis
                </a>
            </div>
            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari nama dosen atau golongan..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="jenis" value="<?php echo $jenis_seminar; ?>">
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
                        <?php if ($jenis_seminar != 'proposal'): ?>
                            <th>KETUA SIDANG</th>
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
                        <td><?php echo $row['penguji_utama']; ?></td>
                        <td><?php echo $row['pembimbing1_penguji']; ?></td>
                        <td><?php echo $row['pembimbing2_penguji']; ?></td>
                        <?php if ($jenis_seminar != 'proposal'): ?>
                            <td><?php echo $row['ketua_sidang']; ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>