<?php
session_start(); // Mulai session di sini
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
// Sisipkan file koneksi
require_once 'koneksi.php';

// Konfigurasi pagination
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil kata kunci pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data dosen yang memiliki id_ujian di tabel ujian_tesis atau ujian_disertasi
$query = "
    SELECT 
        d.id_dosen, 
        d.nidn AS nidn,
        d.telepon AS hp,
        d.nama AS nama_dosen, 
        d.max, 
        GROUP_CONCAT(DISTINCT t.judul SEPARATOR ', ') AS judul, 
        GROUP_CONCAT(DISTINCT p.Program SEPARATOR ', ') AS prodi, 
        COUNT(DISTINCT ut.id_tesis) + COUNT(DISTINCT ud.id_tesis) AS jumlah_diampu
    FROM dosen d
    LEFT JOIN ujian_tesis ut ON d.id_dosen = ut.id_dosen
    LEFT JOIN ujian_disertasi ud ON d.id_dosen = ud.id_dosen
    LEFT JOIN tesis t ON ut.id_tesis = t.id_tesis OR ud.id_tesis = t.id_tesis
    LEFT JOIN prodi p ON t.id_prodi = p.id
    WHERE (ut.id_ujian IS NOT NULL OR ud.id_ujian IS NOT NULL)
    AND (d.nama LIKE '%$search%' OR t.judul LIKE '%$search%' OR p.Program LIKE '%$search%')
    GROUP BY d.id_dosen, d.nama
    LIMIT $limit OFFSET $offset
";

// Hitung total data untuk pagination
$result_total = $koneksi->query("
    SELECT COUNT(DISTINCT d.id_dosen) as total 
    FROM dosen d
    LEFT JOIN ujian_tesis ut ON d.id_dosen = ut.id_dosen
    LEFT JOIN ujian_disertasi ud ON d.id_dosen = ud.id_dosen
    WHERE (ut.id_ujian IS NOT NULL OR ud.id_ujian IS NOT NULL)
    AND d.nama LIKE '%$search%'
");
$total_data = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Eksekusi query
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Gaya untuk baris yang melebihi max */
        .over-max {
            background-color: #ffcccc; /* Warna merah muda */
        }
        /* Gaya untuk pagination */
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="content">
        <div class="container">
            <h1>Rekap Dosen</h1>

            <!-- Form Pencarian -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Cari nama dosen, judul, atau prodi..." value="<?php echo $search; ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Tabel Rekap Dosen -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Telp/WA</th>
                        <th>Jumlah Diampu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            $class = ($row['jumlah_diampu'] > $row['max']) ? 'over-max' : '';
                            echo "<tr class='$class'>
                                <td>{$no}</td>
                                <td><a href='#' onclick=\"showDetails('{$row['nidn']}', '{$row['nama_dosen']}')\">{$row['nidn']}</a></td>
                                <td>{$row['nama_dosen']}</td>
                                <td>{$row['hp']}</td>
                                <td>{$row['jumlah_diampu']}</td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>Tidak ada data ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>">&laquo; Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <!-- Modal untuk menampilkan detail tesis/disertasi -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Detail Tesis/Disertasi</h2>
            <h3 id="dosenName"></h3>
            <div id="modalBody">
                <!-- Data akan dimuat di sini -->
            </div>
        </div>
    </div>

    <style>
        /* Gaya untuk modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .modal-content .close:hover {
            color: #000;
        }
    </style>
</body>
</html>
<script>
    // Fungsi untuk menampilkan modal dan memuat data
    function showDetails(nidn, namaDosen) {
        // Tampilkan modal
        document.getElementById('detailsModal').style.display = 'flex';

        // Tampilkan nama dosen di modal
        document.getElementById('dosenName').innerText = "Nama Dosen: " + namaDosen;

        // Kirim permintaan AJAX untuk mengambil data tesis/disertasi
        fetch(`get_details.php?nidn=${nidn}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modalBody').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
</script>
<?php
// Tutup koneksi
$koneksi->close();
?>