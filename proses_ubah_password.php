<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include file koneksi database
include 'koneksi.php';

$user_id = $_SESSION['user_id'];
$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi_password = $_POST['konfirmasi_password'];

// Validasi input
if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)) {
    $_SESSION['error'] = "Semua field harus diisi!";
    header("Location: ubah_password.php");
    exit();
}

if ($password_baru !== $konfirmasi_password) {
    $_SESSION['error'] = "Password baru dan konfirmasi password tidak cocok!";
    header("Location: ubah_password.php");
    exit();
}

// Ambil password lama dari database
$query = $koneksi->prepare("SELECT password FROM user WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Verifikasi password lama (tanpa enkripsi)
if ($password_lama !== $user['password']) {
    $_SESSION['error'] = "Password lama salah!";
    header("Location: ubah_password.php");
    exit();
}

// Update password di database (tanpa enkripsi)
$query = $koneksi->prepare("UPDATE user SET password = ? WHERE id = ?");
$query->bind_param("si", $password_baru, $user_id);
$query->execute();

$_SESSION['success'] = "Password berhasil diubah!";
header("Location: index.php");
exit();
?>