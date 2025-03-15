<?php
session_start(); // Mulai session
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session
$nama = $_SESSION['nama']; // Ambil nama dari session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <h2>Ubah Password</h2>
            <?php
            // Tampilkan pesan error jika ada
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // Hapus pesan error setelah ditampilkan
            }
            // Tampilkan pesan sukses jika ada
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']); // Hapus pesan sukses setelah ditampilkan
            }
            ?>
            <form action="proses_ubah_password.php" method="POST">
                <div class="form-group">
                    <label for="password_lama">Password Lama</label>
                    <input type="password" id="password_lama" name="password_lama" required>
                </div>
                <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru" required>
                </div>
                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-simpan">Simpan</button>
                    <a href="index.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>