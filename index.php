<?php
session_start(); // Mulai session di sini
if (!isset($_SESSION['user_id'])) { // Periksa apakah user sudah login
    header("Location: login.php"); // Redirect ke halaman login jika belum
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Rekap Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Gaya tambahan untuk halaman beranda */
        .welcome-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        .welcome-section h2 {
            color: #006400;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .welcome-section p {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }

        .features {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .feature {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            margin-bottom: 20px;
        }

        .feature i {
            font-size: 40px;
            color: #006400;
            margin-bottom: 15px;
        }

        .feature h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .feature p {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .features {
                flex-direction: column;
                align-items: center;
            }

            .feature {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="content">
        <div class="container">
            <div class="welcome-section">
                <h2>Selamat Datang di Sistem Smart Layanan Akademik Pascasarjana</h2>
                <p>
                    Sistem ini dirancang untuk memudahkan Anda dalam mengelola dan merekap data dosen. 
                    Anda dapat melihat, menambah, mengedit, dan menghapus data dosen dengan mudah.
                </p>
            </div>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-user-plus"></i>
                    <h3>Tambah Dosen</h3>
                    <p>Tambahkan data dosen baru ke dalam sistem dengan mudah dan cepat.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-edit"></i>
                    <h3>Edit Data</h3>
                    <p>Perbarui informasi dosen yang sudah ada dengan fitur edit yang intuitif.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-trash-alt"></i>
                    <h3>Hapus Data</h3>
                    <p>Hapus data dosen yang tidak diperlukan lagi dari sistem.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>