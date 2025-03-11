<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_tesis = '';
$id_dosen = '';
$peran = 'pembimbing_utama';

// Ambil data pembimbing jika dalam mode edit
if ($id > 0) {
    $query = "SELECT * FROM pembimbing WHERE id_pembimbing = $id";
    $result = $koneksi->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_tesis = $row['id_tesis'];
        $id_dosen = $row['id_dosen'];
        $peran = $row['peran'];
    }
}

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tesis = (int)$_POST['id_tesis'];
    $id_dosen = (int)$_POST['id_dosen'];
    $peran = $koneksi->real_escape_string($_POST['peran']);

    if ($id > 0) {
        // Update data
        $query = "UPDATE pembimbing SET id_tesis = $id_tesis, id_dosen = $id_dosen, peran = '$peran' WHERE id_pembimbing = $id";
    } else {
        // Tambah data baru
        $query = "INSERT INTO pembimbing (id_tesis, id_dosen, peran) VALUES ($id_tesis, $id_dosen, '$peran')";
    }

    if ($koneksi->query($query)) {
        echo "<script>alert('Data pembimbing berhasil disimpan.'); window.location.href = 'crud_pembimbing.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data pembimbing.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembimbing</title>
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
            <h1><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Data Pembimbing</h1>

            <!-- Form Pembimbing -->
            <form method="POST" action="">
            <div class="form-group">
                <label for="id_tesis">Tesis</label>
                <select id="id_tesis" name="id_tesis" required>
                    <option value="">Pilih Tesis</option>
                    <?php
                    $query_tesis = "SELECT id_tesis, judul FROM tesis";
                    $result_tesis = $koneksi->query($query_tesis);
                    while ($row_tesis = $result_tesis->fetch_assoc()) {
                        $selected = $row_tesis['id_tesis'] == $id_tesis ? 'selected' : '';
                        echo "<option value='{$row_tesis['id_tesis']}' $selected>{$row_tesis['judul']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_dosen">Dosen</label>
                <select id="id_dosen" name="id_dosen" required>
                    <option value="">Pilih Dosen</option>
                    <?php
                    $query_dosen = "SELECT id_dosen, nama FROM dosen";
                    $result_dosen = $koneksi->query($query_dosen);
                    while ($row_dosen = $result_dosen->fetch_assoc()) {
                        $selected = $row_dosen['id_dosen'] == $id_dosen ? 'selected' : '';
                        echo "<option value='{$row_dosen['id_dosen']}' $selected>{$row_dosen['nama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <script>
                $(document).ready(function() {
                    // Inisialisasi Select2 untuk Tesis
                    $('#id_tesis').select2({
                        placeholder: "Cari tesis...", // Teks placeholder
                        allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                        width: '100%' // Lebar dropdown
                    });

                    // Inisialisasi Select2 untuk Dosen
                    $('#id_dosen').select2({
                        placeholder: "Cari dosen...", // Teks placeholder
                        allowClear: true, // Memungkinkan pengguna untuk menghapus pilihan
                        width: '100%' // Lebar dropdown
                    });
                });
            </script>
                <div class="form-group">
                    <label for="peran">Peran</label>
                    <select id="peran" name="peran" required>
                        <option value="pembimbing_utama" <?php echo $peran == 'pembimbing_utama' ? 'selected' : ''; ?>>Pembimbing Utama</option>
                        <option value="pembimbing_pendamping" <?php echo $peran == 'pembimbing_pendamping' ? 'selected' : ''; ?>>Pembimbing Pendamping</option>
                    </select>
                </div>
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Simpan</button>
                <a href="crud_pembimbing.php" class="btn-batal">Batal</a>
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