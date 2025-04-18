<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Determine which report type to show (thesis or dissertation)
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'tesis'; // Default: thesis
$search = isset($_GET['search']) ? $_GET['search'] : '';

// For thesis reports
$jenis_seminar = isset($_GET['jenis']) ? $_GET['jenis'] : 'proposal'; // Default: Seminar Proposal

// For dissertation reports
$type = isset($_GET['type']) ? $_GET['type'] : ''; // Default: all types

// Pagination settings
$rows_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rows_per_page;

if ($report_type == 'tesis') {
    // Thesis report query
    $query = "
        SELECT 
            d.id_dosen,
            d.nama AS nama_dosen,
            d.bidang_keahlian AS golongan,
            COUNT(CASE WHEN k.id_penguji_utama = d.id_dosen THEN 1 END) AS penguji_utama,
            COUNT(CASE WHEN k.id_pembimbing1_penguji = d.id_dosen THEN 1 END) AS pembimbing1_penguji,
            COUNT(CASE WHEN k.id_pembimbing2_penguji = d.id_dosen THEN 1 END) AS pembimbing2_penguji,
            COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) AS ketua_sidang,
            'tesis' AS report_type
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
            OR ketua_sidang != 0";

    // Count query for thesis
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
} else {
    // Dissertation report query
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
            COUNT(CASE WHEN k.ketua_sidang = d.id_dosen THEN 1 END) AS ketua_sidang,
            'desertasi' AS report_type
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
            OR ketua_sidang != 0";

    // Count query for dissertation
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
}

// Get total rows
$count_result = $koneksi->query($count_query);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $rows_per_page);

// Add pagination to main query
$query .= " ORDER BY d.nama ASC LIMIT $offset, $rows_per_page";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bimbingan dan Pengujian</title>
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
        
        .button-container a, .report-type-button {
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
            cursor: pointer;
            border: none;
        }
        
        /* Report type buttons */
        .report-type-button.tesis {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        
        .report-type-button.desertasi {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .report-type-button.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
        }
        
        /* Thesis buttons */
        .tesis-button {
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        }
        
        /* Dissertation buttons */
        .desertasi-button {
            background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);
        }
        
        /* Hover effects */
        .button-container a:hover, .report-type-button:hover {
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
        
        /* Search form */
        .search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex-grow: 1;
        }
        
        .search-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .search-form button:hover {
            background-color: #0056b3;
        }
        
        /* Print button */
        .print-button {
            background: linear-gradient(135deg, #5a67d8 0%, #4c51bf 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        
        .print-button:hover {
            background: linear-gradient(135deg, #4c51bf 0%, #434190 100%);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .button-container {
                flex-wrap: wrap;
            }
            
            .search-form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>REKAP BIMBINGAN DAN PENGUJIAN</h2>
            
            <!-- Report type selector -->
            <div class="button-container">
                <button class="report-type-button tesis <?php echo $report_type == 'tesis' ? 'active' : ''; ?>" 
                        onclick="window.location.href='?report_type=tesis&search=<?php echo urlencode($search); ?>'">
                    <i class="fas fa-file-alt"></i> Rekap Tesis
                </button>
                <button class="report-type-button desertasi <?php echo $report_type == 'desertasi' ? 'active' : ''; ?>" 
                        onclick="window.location.href='?report_type=desertasi&search=<?php echo urlencode($search); ?>'">
                    <i class="fas fa-graduation-cap"></i> Rekap Desertasi
                </button>
                <button onclick="printTable()" class="print-button">
                    <i class="fas fa-print"></i> Cetak Tabel
                </button>
            </div>
            
            <?php if ($report_type == 'tesis'): ?>
                <!-- Thesis report type -->
                <div class="button-container">
                    <a href="?report_type=tesis&jenis=proposal&search=<?php echo urlencode($search); ?>" 
                       class="tesis-button <?php echo $jenis_seminar == 'proposal' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt" style="margin-right: 8px;"></i> Seminar Proposal
                    </a>
                    <a href="?report_type=tesis&jenis=hasil&search=<?php echo urlencode($search); ?>" 
                       class="tesis-button <?php echo $jenis_seminar == 'hasil' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line" style="margin-right: 8px;"></i> Seminar Hasil
                    </a>
                    <a href="?report_type=tesis&jenis=ujian&search=<?php echo urlencode($search); ?>" 
                       class="tesis-button <?php echo $jenis_seminar == 'ujian' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap" style="margin-right: 8px;"></i> Ujian Tesis
                    </a>
                </div>
            <?php else: ?>
                <!-- Dissertation report type -->
                <div class="button-container">
                    <a href="?report_type=desertasi&type=kualifikasi&search=<?php echo urlencode($search); ?>" 
                       class="desertasi-button <?php echo $type == 'kualifikasi' ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check" style="margin-right: 8px;"></i> Ujian Kualifikasi
                    </a>
                    <a href="?report_type=desertasi&type=proposal&search=<?php echo urlencode($search); ?>" 
                       class="desertasi-button <?php echo $type == 'proposal' ? 'active' : ''; ?>">
                        <i class="fas fa-file-signature" style="margin-right: 8px;"></i> Ujian Proposal
                    </a>
                    <a href="?report_type=desertasi&type=seminar&search=<?php echo urlencode($search); ?>" 
                       class="desertasi-button <?php echo $type == 'seminar' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar" style="margin-right: 8px;"></i> Seminar Hasil
                    </a>
                    <a href="?report_type=desertasi&type=tertutup&search=<?php echo urlencode($search); ?>" 
                       class="desertasi-button <?php echo $type == 'tertutup' ? 'active' : ''; ?>">
                        <i class="fas fa-lock" style="margin-right: 8px;"></i> Ujian Tertutup
                    </a>
                    <a href="?report_type=desertasi&type=terbuka&search=<?php echo urlencode($search); ?>" 
                       class="desertasi-button <?php echo $type == 'terbuka' ? 'active' : ''; ?>">
                        <i class="fas fa-door-open" style="margin-right: 8px;"></i> Ujian Terbuka
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Search form -->
            <form method="GET" action="" class="search-form">
                <input type="hidden" name="report_type" value="<?php echo $report_type; ?>">
                <?php if ($report_type == 'tesis'): ?>
                    <input type="hidden" name="jenis" value="<?php echo $jenis_seminar; ?>">
                <?php else: ?>
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Cari nama dosen atau golongan..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA DOSEN</th>
                            <th>GOLONGAN</th>
                            <?php if ($report_type == 'tesis'): ?>
                                <!-- Thesis columns -->
                                <?php if ($jenis_seminar != 'proposal'): ?>
                                    <th>KETUA SIDANG</th>
                                <?php endif; ?>
                                <th>PENGUJI UTAMA</th>
                                <th>PEMBIMBING 1 / PENGUJI</th>
                                <th>PEMBIMBING 2 / PENGUJI</th>
                            <?php else: ?>
                                <!-- Dissertation columns -->
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
                                    <!-- Default columns when no specific type is selected -->
                                    <th>KETUA SIDANG</th>
                                    <th>PROMOTOR</th>                          
                                    <th>PENGUJI 1</th>
                                    <th>PENGUJI 2</th>
                                    <th>PENGUJI 3</th>
                                    <th>PENGUJI 4</th>
                                    <th>CO-PROMOTOR</th>
                                    <th>PENGUJI UTAMA</th>
                                <?php endif; ?>
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
                            
                            <?php if ($report_type == 'tesis'): ?>
                                <!-- Thesis data -->
                                <?php if ($jenis_seminar != 'proposal'): ?>
                                    <td><?php echo $row['ketua_sidang']; ?></td>
                                <?php endif; ?>
                                <td><?php echo $row['penguji_utama']; ?></td>
                                <td><?php echo $row['pembimbing1_penguji']; ?></td>
                                <td><?php echo $row['pembimbing2_penguji']; ?></td>
                            <?php else: ?>
                                <!-- Dissertation data -->
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
                                    <!-- Default columns when no specific type is selected -->
                                    <td><?php echo $row['ketua_sidang']; ?></td>                            
                                    <td><?php echo $row['promotor']; ?></td>
                                    <td><?php echo $row['copromotor']; ?></td>
                                    <td><?php echo $row['penguji_1']; ?></td>
                                    <td><?php echo $row['penguji_2']; ?></td>
                                    <td><?php echo $row['penguji_3']; ?></td>
                                    <td><?php echo $row['penguji_4']; ?></td>
                                    <td><?php echo $row['penguji_utama']; ?></td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?report_type=<?php echo $report_type; ?>&<?php echo $report_type == 'tesis' ? 'jenis='.$jenis_seminar : 'type='.$type; ?>&page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>">
                        &laquo; Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?report_type=<?php echo $report_type; ?>&<?php echo $report_type == 'tesis' ? 'jenis='.$jenis_seminar : 'type='.$type; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
                       <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?report_type=<?php echo $report_type; ?>&<?php echo $report_type == 'tesis' ? 'jenis='.$jenis_seminar : 'type='.$type; ?>&page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>">
                        Selanjutnya &raquo;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    function printTable() {
        // Clone the table container
        var printContent = document.querySelector('.table-container').cloneNode(true);
        
        // Create a new window
        var printWindow = window.open('', '_blank');
        
        // Add some styles to the new window
        printWindow.document.write('<html><head><title>Cetak Rekap</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
        printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
        printWindow.document.write('th { background-color: #f2f2f2; }');
        printWindow.document.write('h3 { color: #333; }');
        printWindow.document.write('@page { size: auto; margin: 5mm; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        
        // Add title
        printWindow.document.write('<h2>REKAP BIMBINGAN DAN PENGUJIAN</h2>');
        printWindow.document.write('<h3>' + (<?php echo $report_type == 'tesis' ? "'Rekap Tesis - '" : "'Rekap Desertasi - '"; ?> + 
            <?php 
            if ($report_type == 'tesis') {
                if ($jenis_seminar == 'proposal') echo "'Seminar Proposal'";
                elseif ($jenis_seminar == 'hasil') echo "'Seminar Hasil'";
                else echo "'Ujian Tesis'";
            } else {
                if ($type == 'kualifikasi') echo "'Ujian Kualifikasi'";
                elseif ($type == 'proposal') echo "'Ujian Proposal'";
                elseif ($type == 'seminar') echo "'Seminar Hasil'";
                elseif ($type == 'tertutup') echo "'Ujian Tertutup'";
                elseif ($type == 'terbuka') echo "'Ujian Terbuka'";
                else echo "'Semua Jenis'";
            }
            ?>) + '</h3>');
        
        printWindow.document.write(printContent.innerHTML);
        printWindow.document.write('</body></html>');
        
        printWindow.document.close();
        
        // Add a small delay to ensure content is loaded before printing
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 200);
    }
    </script>
</body>
</html>