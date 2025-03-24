<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Define role map
$role_map = [
    'ketua_sidang' => ['column' => 'ketua_sidang', 'title' => 'Ketua Sidang', 'icon' => 'fa-gavel'],
    'penguji_utama' => ['column' => 'penguji_utama', 'title' => 'Penguji Utama', 'icon' => 'fa-star'],
    'penguji_1' => ['column' => 'penguji_1', 'title' => 'Penguji 1', 'icon' => 'fa-user-graduate'],
    'penguji_2' => ['column' => 'penguji_2', 'title' => 'Penguji 2', 'icon' => 'fa-user-graduate'],
    'penguji_3' => ['column' => 'penguji_3', 'title' => 'Penguji 3', 'icon' => 'fa-user-graduate'],
    'penguji_4' => ['column' => 'penguji_4', 'title' => 'Penguji 4', 'icon' => 'fa-user-graduate'],
    'promotor' => ['column' => 'promotor', 'title' => 'Promotor', 'icon' => 'fa-user-tie'],
    'copromotor' => ['column' => 'copromotor', 'title' => 'Co-Promotor', 'icon' => 'fa-user-tie'],
    'sekretaris' => ['column' => 'sekretaris_penguji', 'title' => 'Sekretaris', 'icon' => 'fa-user-secret']
];

// Validate and set role
$valid_roles = array_keys($role_map);
$role = isset($_GET['role']) && in_array($_GET['role'], $valid_roles) ? $_GET['role'] : 'ketua_sidang';
$current_role = $role_map[$role];

// Search variable
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base query
$query = "
    SELECT 
        kd.id,
        t.judul,
        m.nim,
        m.nama AS nama_mahasiswa,
        d.nama AS nama_dosen,
        kd.tanggal_ujian,
        kd.nilai
    FROM 
        kusus_desertasi kd
    JOIN 
        tesis t ON kd.id_tesis = t.id_tesis
    JOIN 
        mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN 
        dosen d ON kd.".$current_role['column']." = d.id_dosen
    WHERE 
        (t.judul LIKE '%$search%' OR 
        m.nim LIKE '%$search%' OR 
        m.nama LIKE '%$search%' OR 
        d.nama LIKE '%$search%')
    ORDER BY 
        kd.tanggal_ujian DESC";

$result = $koneksi->query($query);

// Pagination
$rows_per_page = 10;
$total_rows = $result->num_rows;
$total_pages = ceil($total_rows / $rows_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rows_per_page;

$query .= " LIMIT $offset, $rows_per_page";
$result = $koneksi->query($query);

function getNilaiHuruf($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 55) return 'C';
    return 'D';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar <?php echo htmlspecialchars($current_role['title']); ?> Desertasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        /* Role Buttons Styles */
        .role-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        
        .role-button {
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .role-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .role-button.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
        }
        
        /* Button Colors */
        .button-ketua_sidang { background: linear-gradient(135deg, #8E44AD, #9B59B6); }
        .button-penguji_utama { background: linear-gradient(135deg, #E74C3C, #C0392B); }
        .button-penguji_1 { background: linear-gradient(135deg, #3498DB, #2980B9); }
        .button-penguji_2 { background: linear-gradient(135deg, #2ECC71, #27AE60); }
        .button-penguji_3 { background: linear-gradient(135deg, #F39C12, #D35400); }
        .button-penguji_4 { background: linear-gradient(135deg, #16A085, #1ABC9C); }
        .button-promotor { background: linear-gradient(135deg, #7F8C8D, #95A5A6); }
        .button-copromotor { background: linear-gradient(135deg, #34495E, #2C3E50); }
        .button-sekretaris { background: linear-gradient(135deg, #D35400, #E67E22); }
        
        .search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-form input {
            flex: 1;
            padding: 10px;
            border: 2px solid #3498db;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .search-form button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .search-form button:hover {
            background-color: #2980b9;
        }
        
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        table th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table tr:hover {
            background-color: #e8f4fc;
        }
        
        .nilai {
            font-weight: bold;
            text-align: center;
        }
        
        .nilai-A { color: #27ae60; }
        .nilai-B { color: #f39c12; }
        .nilai-C { color: #e74c3c; }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: #3498db;
            border: 1px solid #3498db;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .pagination a.active {
            background-color: #3498db;
            color: white;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #e8f4fc;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Daftar <?php echo htmlspecialchars($current_role['title']); ?> Desertasi</h2>
            
            <!-- Role Buttons -->
            <div class="role-buttons">
                <?php foreach ($role_map as $key => $value): ?>
                    <a href="?role=<?php echo $key; ?>&search=<?php echo urlencode($search); ?>" 
                       class="role-button button-<?php echo $key; ?> <?php echo $role == $key ? 'active' : ''; ?>">
                        <i class="fas <?php echo $value['icon']; ?>"></i>
                        <?php echo $value['title']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Search Form -->
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari judul, NIM, nama mahasiswa, atau nama <?php echo strtolower($current_role['title']); ?>..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="role" value="<?php echo $role; ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Desertasi</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Nama <?php echo htmlspecialchars($current_role['title']); ?></th>
                            <th>Tanggal Ujian</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = $offset + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_dosen']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_ujian'])); ?></td>
                                    <td class="nilai nilai-<?php echo getNilaiHuruf($row['nilai']); ?>">
                                        <?php echo getNilaiHuruf($row['nilai']); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Tidak ada data ditemukan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&role=<?php echo $role; ?>&search=<?php echo urlencode($search); ?>">
                        <i class="fas fa-chevron-left"></i> Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&role=<?php echo $role; ?>&search=<?php echo urlencode($search); ?>" 
                       <?php echo $i == $current_page ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&role=<?php echo $role; ?>&search=<?php echo urlencode($search); ?>">
                        Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>