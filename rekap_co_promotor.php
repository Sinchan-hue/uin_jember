<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk rekap desertasi
// Query untuk rekap desertasi
$query = "
    SELECT 
        d.id_dosen,
        d.nama AS nama_dosen,
        d.bidang_keahlian AS golongan,
        COUNT(CASE WHEN k.promotor = d.id_dosen THEN 1 END) AS jumlah_promotor,
        COUNT(CASE WHEN k.copromotor = d.id_dosen THEN 1 END) AS jumlah_copromotor,
        GROUP_CONCAT(DISTINCT DATE_FORMAT(k.tanggal_ujian, '%d-%m-%Y') ORDER BY k.tanggal_ujian SEPARATOR ', ') AS tanggal_ujian_list,
        (COUNT(CASE WHEN k.promotor = d.id_dosen THEN 1 END) + 
         COUNT(CASE WHEN k.copromotor = d.id_dosen THEN 1 END)) AS total
    FROM 
        dosen d
    LEFT JOIN 
        kusus_desertasi k ON (d.id_dosen = k.promotor OR d.id_dosen = k.copromotor)
    LEFT JOIN
        tesis t ON k.id_tesis = t.id_tesis";  // Tambahkan JOIN dengan tabel tesis

// Filter tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND k.tanggal_ujian BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($start_date)) {
    $query .= " AND k.tanggal_ujian >= '$start_date'";
} elseif (!empty($end_date)) {
    $query .= " AND k.tanggal_ujian <= '$end_date'";
}

$query .= "
    WHERE 
        (d.nama LIKE '%$search%' OR d.bidang_keahlian LIKE '%$search%')
        AND (t.status IS NULL OR t.status != 'lulus')";  // Filter status tidak sama dengan 'selesai'

$query .= "
    GROUP BY 
        d.id_dosen
    HAVING 
        jumlah_promotor != 0 OR jumlah_copromotor != 0
    ORDER BY 
        total DESC";

$result = $koneksi->query($query);

// Query untuk data kuota
$kuota_query = "SELECT id_dosen, promotor, co_promotor FROM dosen";
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
    <title>Rekap Desertasi</title>
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
        /* CSS tetap sama seperti sebelumnya */
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

        .role-count.over-quota {
            background-color: #f44336;
            animation: pulse 1.5s infinite;
        }

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
            <h2>Rekap Desertasi</h2>
            
            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari nama dosen atau golongan..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>
            
            <!-- Form Filter Tanggal -->
            <div class="filter-container">
                <form method="GET" action="" class="filter-section">
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
                    
                    <a href="?search=<?php echo urlencode($search); ?>" class="reset-btn">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                    
                    <button onclick="printPreview()" class="print-btn">
                        <i class="fas fa-print"></i> Print Preview
                    </button>
                </form>
            </div>
            
            <!-- Tabel Rekap Desertasi -->
            <table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA DOSEN</th>
                        <th>GOLONGAN/HOME BASE</th>
                        <th>JUMLAH PROMOTOR</th>
                        <th>JUMLAH CO-PROMOTOR</th>
                        <th>TANGGAL UJIAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_dosen']; ?></td>
                        <td><?php echo $row['golongan']; ?></td>
                        <td>
                            <span class="role-count <?php 
                                $kuota = $kuota_data[$row['id_dosen']]['promotor'] ?? 0;
                                echo ($row['jumlah_promotor'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['jumlah_promotor']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-count <?php 
                                $kuota = $kuota_data[$row['id_dosen']]['co_promotor'] ?? 0;
                                echo ($row['jumlah_copromotor'] > $kuota) ? 'over-quota' : '';
                            ?>" 
                            title="Kuota: <?php echo $kuota; ?>">
                                <?php echo $row['jumlah_copromotor']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['tanggal_ujian_list']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
<script>
function printPreview() {
    const search = '<?php echo urlencode($search); ?>';
    const startDate = '<?php echo $start_date; ?>';
    const endDate = '<?php echo $end_date; ?>';
    
    let url = `print_desertasi.php`;
    
    if (search) url += `?search=${search}`;
    if (startDate) url += `${search ? '&' : '?'}start_date=${startDate}`;
    if (endDate) url += `&end_date=${endDate}`;
    
    window.open(url, '_blank');
}
</script>