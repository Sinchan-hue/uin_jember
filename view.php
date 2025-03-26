<?php
require_once 'init.php'; // Ganti dari config.php ke init.php


// Fungsi untuk memastikan input aman
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk memvalidasi file yang didekripsi
function validateFile($filename) {
    // Daftar file yang diizinkan
    $allowedFiles = [
        // File umum
        'ubah_password.php',
        
        // File admin
        'kusus_tesis.php', 'rekap_tesis.php', 'peran_tesis.php',
        'kusus_desertasi.php', 'rekap_desertasi.php', 'peran_desertasi.php',
        'crud_tesis.php', 'crud_mahasiswa.php', 'crud_dosen.php', 'crud_user.php',
        
        // File dosen
        'view_tesis.php', 'view_desertasi.php'
    ];
    
    // Cek ekstensi file
    if (!preg_match('/^[a-zA-Z0-9_-]+\.php$/', $filename)) {
        return false;
    }
    
    // Cek apakah file diizinkan
    return in_array($filename, $allowedFiles);
}

// Fungsi untuk memeriksa izin akses berdasarkan role
function checkPermission($filename, $role) {
    // File yang bisa diakses semua role
    $commonFiles = ['ubah_password.php'];
    
    if (in_array($filename, $commonFiles)) {
        return true;
    }
    
    // File khusus admin
    $adminFiles = [
        'kusus_tesis.php', 'rekap_tesis.php', 'peran_tesis.php',
        'kusus_desertasi.php', 'rekap_desertasi.php', 'peran_desertasi.php',
        'crud_tesis.php', 'crud_mahasiswa.php', 'crud_dosen.php', 'crud_user.php'
    ];
    
    // File khusus dosen
    $dosenFiles = ['view_tesis.php', 'view_desertasi.php'];
    
    if ($role == 'admin' && in_array($filename, $adminFiles)) {
        return true;
    }
    
    if ($role == 'dosen' && in_array($filename, $dosenFiles)) {
        return true;
    }
    
    return false;
}

// Proses utama
try {
    if (!isset($_GET['file'])) {
        throw new Exception('Parameter file tidak ditemukan');
    }

    // Verifikasi session dan role
    if (!isset($_SESSION['role'])) {
        throw new Exception('Akses ditolak. Silakan login terlebih dahulu.');
    }

    $role = $_SESSION['role'];
    $encryptedFile = sanitizeInput($_GET['file']);
    $decryptedFile = decryptFile($encryptedFile);

    if (!$decryptedFile) {
        throw new Exception('Gagal mendekripsi file');
    }

    if (!validateFile($decryptedFile)) {
        throw new Exception('File tidak valid');
    }

    if (!checkPermission($decryptedFile, $role)) {
        throw new Exception('Anda tidak memiliki izin untuk mengakses file ini');
    }

    // Jika semua validasi berhasil, include file yang diminta
    if (file_exists($decryptedFile)) {
        // Tampilkan header
        header('Content-Type: text/html; charset=utf-8');
        
        // Include file yang diminta
        include $decryptedFile;
    } else {
        throw new Exception('File tidak ditemukan');
    }

} catch (Exception $e) {
    // Handle error dengan aman
    header('HTTP/1.1 400 Bad Request');
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        <div class='error-container'>
            <h1>Terjadi Kesalahan</h1>
            <p>".sanitizeInput($e->getMessage())."</p>
            <a href='index.php' class='btn'>Kembali ke halaman utama</a>
        </div>
    </body>
    </html>";
    exit;
}
?>