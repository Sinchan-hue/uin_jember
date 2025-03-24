<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : 'ketua-sidang'; // Default: Ketua Sidang

// Base query
$query = "
    SELECT 
        k.id,
        t.judul,
        m.nim,
        m.nama AS nama_mahasiswa,
        d.nama AS nama_dosen
    FROM 
        kusus_tesis k
    JOIN 
        tesis t ON k.id_tesis = t.id_tesis
    JOIN 
        mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    JOIN 
        dosen d ON ";

// Modify query based on selected role
switch($role) {
    case 'penguji-utama':
        $query .= "k.id_penguji_utama = d.id_dosen";
        $role_title = "Penguji Utama";
        break;
    case 'pembimbing1':
        $query .= "k.id_pembimbing1_penguji = d.id_dosen";
        $role_title = "Pembimbing 1 / Penguji";
        break;
    case 'pembimbing2':
        $query .= "k.id_pembimbing2_penguji = d.id_dosen";
        $role_title = "Pembimbing 2 / Penguji";
        break;
    default: // ketua-sidang
        $query .= "k.ketua_sidang = d.id_dosen";
        $role_title = "Ketua Sidang";
}

// Add search conditions
$query .= "
    WHERE 
        t.judul LIKE '%$search%' OR 
        m.nim LIKE '%$search%' OR 
        m.nama LIKE '%$search%' OR 
        d.nama LIKE '%$search%'
    ORDER BY 
        k.tanggal_ujian DESC";

$result = $koneksi->query($query);

// Pagination
$rows_per_page = 10;
$total_rows = $result->num_rows;
$total_pages = ceil($total_rows / $rows_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rows_per_page;

// Query dengan pagination
$query .= " LIMIT $offset, $rows_per_page";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar <?php echo $role_title; ?> Tesis</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .role-buttons {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        
        .role-button {
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .role-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .role-button.penguji-utama {
            background: linear-gradient(135deg, #FF5733, #C70039);
        }
        
        .role-button.pembimbing1 {
            background: linear-gradient(135deg, #3498DB, #2874A6);
        }
        
        .role-button.pembimbing2 {
            background: linear-gradient(135deg, #2ECC71, #239B56);
        }
        
        .role-button.ketua-sidang {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
        }
        
        .role-button.active {
            border: 3px solid #FFD700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
        
        .search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 2px solid #228B22;
            border-radius: 4px;
        }
        .search-form button {
            padding: 10px 20px;
            background-color: #228B22;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #1a6d1a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #228B22;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e6ffe6;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: #228B22;
            border: 1px solid #228B22;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #228B22;
            color: white;
        }
        .pagination a:hover:not(.active) {
            background-color: #e6ffe6;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="container">
            <h2>Daftar <?php echo $role_title; ?> Tesis</h2>
            
            <!-- Tombol Peran -->
            <div class="role-buttons">
                <a href="?role=penguji-utama&search=<?php echo urlencode($search); ?>" class="role-button penguji-utama <?php echo $role == 'penguji-utama' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> PENGUJI UTAMA
                </a>
                <a href="?role=pembimbing1&search=<?php echo urlencode($search); ?>" class="role-button pembimbing1 <?php echo $role == 'pembimbing1' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i> PEMBIMBING 1 / PENGUJI
                </a>
                <a href="?role=pembimbing2&search=<?php echo urlencode($search); ?>" class="role-button pembimbing2 <?php echo $role == 'pembimbing2' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i> PEMBIMBING 2 / PENGUJI
                </a>
                <a href="?role=ketua-sidang&search=<?php echo urlencode($search); ?>" class="role-button ketua-sidang <?php echo $role == 'ketua-sidang' ? 'active' : ''; ?>">
                    <i class="fas fa-gavel"></i> KETUA SIDANG
                </a>
            </div>
            
            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari judul, NIM, nama mahasiswa, atau nama dosen..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="role" value="<?php echo $role; ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Tabel Data -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Tesis</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Nama <?php echo $role_title; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = ($current_page - 1) * $rows_per_page + 1;
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td><?php echo htmlspecialchars($row['nim']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_dosen']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Tidak ada data ditemukan</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>