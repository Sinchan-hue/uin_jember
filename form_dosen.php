<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nidn = '';
$nama = '';
$email = '';
$telepon = '';
$bidang_keahlian = '';

// Ambil data dosen jika dalam mode edit
if ($id > 0) {
    $query = "SELECT * FROM dosen WHERE id_dosen = $id";
    $result = $koneksi->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nidn = $row['nidn'];
        $nama = $row['nama'];
        $email = $row['email'];
        $telepon = $row['telepon'];
        $bidang_keahlian = $row['bidang_keahlian'];
    }
}

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nidn = $koneksi->real_escape_string($_POST['nidn']);
    $nama = $koneksi->real_escape_string($_POST['nama']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $telepon = $koneksi->real_escape_string($_POST['telepon']);
    $bidang_keahlian = $koneksi->real_escape_string($_POST['bidang_keahlian']);

    if ($id > 0) {
        // Update data
        $query = "UPDATE dosen SET nidn = '$nidn', nama = '$nama', email = '$email', telepon = '$telepon', bidang_keahlian = '$bidang_keahlian' WHERE id_dosen = $id";
    } else {
        // Tambah data baru
        $query = "INSERT INTO dosen (nidn, nama, email, telepon, bidang_keahlian) VALUES ('$nidn', '$nama', '$email', '$telepon', '$bidang_keahlian')";
    }

    if ($koneksi->query($query)) {
        echo "<script>alert('Data dosen berhasil disimpan.'); window.location.href = 'crud_dosen.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data dosen.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="content">
        <div class="container">
            <h1><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Data Dosen</h1>

            <!-- Form Dosen -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nidn">NIDN</label>
                    <input type="text" id="nidn" name="nidn" value="<?php echo $nidn; ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $nama; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                </div>
                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" id="telepon" name="telepon" value="<?php echo $telepon; ?>">
                </div>
                <div class="form-group">
                    <label for="bidang_keahlian">Bidang Keahlian</label>
                    <input type="text" id="bidang_keahlian" name="bidang_keahlian" value="<?php echo $bidang_keahlian; ?>">
                </div>
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Simpan</button>
                <a href="crud_dosen.php" class="btn-batal">Batal</a>
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