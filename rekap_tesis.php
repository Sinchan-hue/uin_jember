<?php
session_start(); // Mulai session
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
// Ambil parameter tanggal dari URL
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Query dasar dengan penambahan filter tanggal
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) AS penguji_utama,
        COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) AS pembimbing1_penguji,
        COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) AS pembimbing2_penguji,
        COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) AS ketua_sidang,
        GROUP_CONCAT(DISTINCT DATE_FORMAT(k.tanggal_ujian, '%d-%m-%Y') ORDER BY k.tanggal_ujian SEPARATOR ', ') AS tanggal_ujian_list
    FROM 
        dosen d
    LEFT JOIN 
        kusus_tesis k ON (d.id_dosen = k.id_penguji_utama 
                       OR d.id_dosen = k.id_pembimbing1_penguji 
                       OR d.id_dosen = k.id_pembimbing2_penguji
                       OR d.id_dosen = k.ketua_sidang)";
        
    // Tambahkan kondisi WHERE untuk filter tanggal jika ada
    if (!empty($start_date) && !empty($end_date)) {
        $query .= " AND k.tanggal_ujian BETWEEN '$start_date' AND '$end_date'";
    } elseif (!empty($start_date)) {
        $query .= " AND k.tanggal_ujian >= '$start_date'";
    } elseif (!empty($end_date)) {
        $query .= " AND k.tanggal_ujian <= '$end_date'";
    }

    $query .= "
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
            kusus_tesis k ON (d.id_dosen = k.id_penguji_utama 
                           OR d.id_dosen = k.id_pembimbing1_penguji 
                           OR d.id_dosen = k.id_pembimbing2_penguji
                           OR d.id_dosen = k.ketua_sidang)";
        
// Tambahkan kondisi WHERE untuk filter tanggal jika ada
if (!empty($start_date) && !empty($end_date)) {
    $count_query .= " AND k.tanggal_ujian BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($start_date)) {
    $count_query .= " AND k.tanggal_ujian >= '$start_date'";
} elseif (!empty($end_date)) {
    $count_query .= " AND k.tanggal_ujian <= '$end_date'";
}

$count_query .= "
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

$kuota_query = "SELECT id_dosen, penguji_utama, pembimbing1_penguji, pembimbing2_penguji, ketua_sidang FROM dosen";
$kuota_result = $koneksi->query($kuota_query);
$kuota_data = [];
while ($row = $kuota_result->fetch_assoc()) {
    $kuota_data[$row['id_dosen']] = $row;
}
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
        /* Button Container Styles */
        .button-container {
            margin: 25px 0;
            display: flex;
            gap: 15px;
            justify-content: left;
        }

        /* Button Styles */
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

        /* Button Gradient Backgrounds */
        .button-container a:nth-child(1) {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .button-container a:nth-child(2) {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .button-container a:nth-child(3) {
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        }

        /* Button States */
        .button-container a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .button-container a.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
        }

        /* Ripple Effect */
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

        /* Filter Container Styles */
        .filter-container {
            margin: 20px 0;
        }

        .filter-section {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: #444;
            white-space: nowrap;
        }

        /* Date Picker Styles */
        input[type="date"] {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            width: 160px;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
            background-color: #fff;
        }

        /* Filter Button Styles */
        .filter-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #5a6fd1 0%, #6a3d9a 100%);
        }

        .filter-btn:active {
            transform: translateY(0);
        }

        .filter-btn i {
            font-size: 14px;
        }

        /* Reset Button Styles */
        .reset-btn {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #555;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #e6e9f0 0%, #b8c2d8 100%);
        }

        /* Action Buttons Styles */
        .action-buttons {
            margin: 15px 0;
            display: flex;
            justify-content: flex-end;
        }

        .print-btn {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #3a56a0 0%, #121f3d 100%);
        }

        .print-btn:active {
            transform: translateY(0);
        }

        /* Animation */
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
        /* Style untuk role count */
        .role-count {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Style ketika melebihi kuota */
        .role-count.over-quota {
            background-color: #f44336;
            animation: pulse 1.5s infinite;
        }

        /* Tooltip styling */
        .role-count:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 5px;
        }

        /* Animasi untuk peringatan kuota */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
            <!-- Form Filter Tanggal -->
            <div class="filter-container">
                <form method="GET" action="" class="filter-section">
                    <input type="hidden" name="jenis" value="<?php echo $jenis_seminar; ?>">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    
                    <div class="filter-group">
                        <label for="start_date">Dari:</label>
                        <input type="date" id="start_date" name="start_date" 
                            value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="end_date">Sampai:</label>
                        <input type="date" id="end_date" name="end_date" 
                            value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    </div>
                    
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    <a href="?jenis=<?php echo $jenis_seminar; ?>&search=<?php echo urlencode($search); ?>" 
                    class="reset-btn">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                    
                    <button onclick="printPreview()" class="print-btn">
                        <i class="fas fa-print"></i> Print Preview
                    </button>
                </form>
            </div>
            <!-- Tabel Rekap Tesis -->
            <table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA DOSEN</th>
                        <th>GOLONGAN/HOME BASE</th>
                        <th>PENGUJI UTAMA</th>
                        <th>PEMBIMBING 1 / PENGUJI</th>
                        <th>PEMBIMBING 2 / PENGUJI</th>
                        <?php if ($jenis_seminar != 'proposal'): ?>
                            <th>KETUA SIDANG</th>
                        <?php endif; ?>
                        <th>TANGGAL UJIAN</th>
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
                        <td>
                            <span class="role-count <?php 
                                // Cek apakah jumlah melebihi kuota
                                $kuota = $kuota_data[$row['id_dosen']]['penguji_utama'] ?? 0;
                                echo ($row['penguji_utama'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['penguji_utama']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-count <?php 
                                $kuota = $kuota_data[$row['id_dosen']]['pembimbing1_penguji'] ?? 0;
                                echo ($row['pembimbing1_penguji'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['pembimbing1_penguji']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-count <?php 
                                $kuota = $kuota_data[$row['id_dosen']]['pembimbing2_penguji'] ?? 0;
                                echo ($row['pembimbing2_penguji'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['pembimbing2_penguji']; ?>
                            </span>
                        </td>
                        <?php if ($jenis_seminar != 'proposal'): ?>
                        <td>
                            <span class="role-count <?php 
                                $kuota = $kuota_data[$row['id_dosen']]['ketua_sidang'] ?? 0;
                                echo ($row['ketua_sidang'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['ketua_sidang']; ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td><?php echo $row['tanggal_ujian_list']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&jenis=<?php echo $jenis_seminar; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
<script>
function printPreview() {
    // Ambil parameter yang aktif
    const jenis = '<?php echo $jenis_seminar; ?>';
    const search = '<?php echo urlencode($search); ?>';
    const startDate = '<?php echo $start_date; ?>';
    const endDate = '<?php echo $end_date; ?>';
    
    // Buat URL untuk print preview
    let url = `print_tesis.php?jenis=${jenis}`;
    
    // Tambahkan parameter jika ada
    if (search) url += `&search=${search}`;
    if (startDate) url += `&start_date=${startDate}`;
    if (endDate) url += `&end_date=${endDate}`;
    
    // Buka di tab baru
    window.open(url, '_blank');
}
</script>