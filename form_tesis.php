<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$judul = '';
$abstrak = '';
$tahun = '';
$id_mahasiswa = '';
$status = 'diajukan';

// Ambil data tesis jika dalam mode edit
if ($id > 0) {
    $query = "SELECT * FROM tesis WHERE id_tesis = $id";
    $result = $koneksi->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $judul = $row['judul'];
        $abstrak = $row['abstrak'];
        $tahun = $row['tahun'];
        $id_mahasiswa = $row['id_mahasiswa'];
        $status = $row['status'];
    }
}

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $koneksi->real_escape_string($_POST['judul']);
    $abstrak = $koneksi->real_escape_string($_POST['abstrak']);
    $tahun = (int)$_POST['tahun'];
    $id_mahasiswa = (int)$_POST['id_mahasiswa'];
    $status = $koneksi->real_escape_string($_POST['status']);

    if ($id > 0) {
        // Update data
        $query = "UPDATE tesis SET judul = '$judul', abstrak = '$abstrak', tahun = $tahun, id_mahasiswa = $id_mahasiswa, status = '$status' WHERE id_tesis = $id";
    } else {
        // Tambah data baru
        $query = "INSERT INTO tesis (judul, abstrak, tahun, id_mahasiswa, status) VALUES ('$judul', '$abstrak', $tahun, $id_mahasiswa, '$status')";
    }

    if ($koneksi->query($query)) {
        echo "<script>alert('Data tesis berhasil disimpan.'); window.location.href = 'crud_tesis.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data tesis.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tesis</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (diperlukan oleh Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="content">
        <div class="container">
            <h1><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Data Tesis</h1>

            <!-- Form Tesis -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" value="<?php echo $judul; ?>" required>
                </div>
                <div class="form-group">
                    <label for="abstrak">Abstrak</label>
                    <textarea id="abstrak" name="abstrak" rows="5" placeholder="Masukkan abstrak tesis..." required><?php echo $abstrak; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tahun">Tahun</label>
                    <input type="number" id="tahun" name="tahun" value="<?php echo $tahun; ?>" required>
                </div>
                <div class="form-group">
                    <label for="id_mahasiswa">Mahasiswa</label>
                    <select id="id_mahasiswa" name="id_mahasiswa" required>
                        <option value="">Pilih Mahasiswa</option>
                        <?php
                        $query_mahasiswa = "SELECT id_mahasiswa, nama FROM mahasiswa";
                        $result_mahasiswa = $koneksi->query($query_mahasiswa);
                        while ($row_mahasiswa = $result_mahasiswa->fetch_assoc()) {
                            $selected = $row_mahasiswa['id_mahasiswa'] == $id_mahasiswa ? 'selected' : '';
                            echo "<option value='{$row_mahasiswa['id_mahasiswa']}' $selected>{$row_mahasiswa['nama']}</option>";
                        }
                        ?>
                    </select>

                    <script>
                        $(document).ready(function() {
                            $('#id_mahasiswa').select2({
                                placeholder: "Cari mahasiswa...", // Teks placeholder
                                allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                                width: '100%' // Lebar dropdown
                            });
                        });
                    </script>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="diajukan" <?php echo $status == 'diajukan' ? 'selected' : ''; ?>>Diajukan</option>
                        <option value="diterima" <?php echo $status == 'diterima' ? 'selected' : ''; ?>>Diterima</option>
                        <option value="ditolak" <?php echo $status == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                        <option value="selesai" <?php echo $status == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Simpan</button>
                <a href="crud_tesis.php" class="btn-batal">Batal</a>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
// Tutup koneksi
$koneksi->close();
?>