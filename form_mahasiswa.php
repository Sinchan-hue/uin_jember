<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nim = '';
$nama = '';
$program_studi = '';
$email = '';
$telepon = '';

// Ambil data mahasiswa jika dalam mode edit
if ($id > 0) {
    $query = "SELECT * FROM mahasiswa WHERE id_mahasiswa = $id";
    $result = $koneksi->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nim = $row['nim'];
        $nama = $row['nama'];
        $program_studi = $row['program_studi'];
        $email = $row['email'];
        $telepon = $row['telepon'];
    }
}

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $koneksi->real_escape_string($_POST['nim']);
    $nama = $koneksi->real_escape_string($_POST['nama']);
    $program_studi = $koneksi->real_escape_string($_POST['program_studi']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $telepon = $koneksi->real_escape_string($_POST['telepon']);

    if ($id > 0) {
        // Update data
        $query = "UPDATE mahasiswa SET nim = '$nim', nama = '$nama', program_studi = '$program_studi', email = '$email', telepon = '$telepon' WHERE id_mahasiswa = $id";
    } else {
        // Tambah data baru
        $query = "INSERT INTO mahasiswa (nim, nama, program_studi, email, telepon) VALUES ('$nim', '$nama', '$program_studi', '$email', '$telepon')";
    }

    if ($koneksi->query($query)) {
        echo "<script>alert('Data mahasiswa berhasil disimpan.'); window.location.href = 'crud_mahasiswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data mahasiswa.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Mahasiswa</title>
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
            <h1><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Data Mahasiswa</h1>

            <!-- Form Mahasiswa -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" value="<?php echo $nim; ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $nama; ?>" required>
                </div>
                <div class="form-group">
                    <label for="program_studi">Program Studi</label>
                    <input type="text" id="program_studi" name="program_studi" value="<?php echo $program_studi; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                </div>
                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" id="telepon" name="telepon" value="<?php echo $telepon; ?>">
                </div>
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Simpan</button>
                <a href="crud_mahasiswa.php" class="btn-batal">Batal</a>
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