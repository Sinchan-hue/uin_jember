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
            background: linear-gradient(135deg, #ffffff, #f4f4f4); /* Gradien putih ke abu-abu muda */
            padding: 40px;
            border-radius: 12px; /* Sudut lebih melengkung */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Shadow lebih tebal */
            text-align: center;
            margin-top: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, color 0.3s ease; /* Efek hover termasuk warna teks */
        }

        .welcome-section:hover {
            transform: translateY(-5px); /* Naikkan sedikit saat hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); /* Shadow lebih tebal saat hover */
            color: #228B22; /* Warna teks berubah saat hover */
        }

        .welcome-section h2 {
            color: #228B22; /* Warna hijau yang lebih cerah */
            font-size: 32px; /* Ukuran font lebih besar */
            margin-bottom: 20px;
            font-weight: 700; /* Tebalkan font */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Shadow untuk teks */
            transition: color 0.3s ease; /* Efek hover pada warna teks */
        }

        .welcome-section:hover h2 {
            color:rgb(169, 232, 163); /* Warna teks berubah menjadi emas saat hover */
        }

        .welcome-section p {
            color: #444; /* Warna teks lebih gelap */
            font-size: 18px; /* Ukuran font lebih besar */
            line-height: 1.8; /* Jarak antar baris lebih lebar */
            max-width: 800px; /* Batasi lebar teks */
            margin: 0 auto; /* Pusatkan teks */
            transition: color 0.3s ease; /* Efek hover pada warna teks */
        }

        .welcome-section:hover p {
            color: #228B22; /* Warna teks berubah menjadi hijau saat hover */
        }

        .features {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            flex-wrap: wrap;
            gap: 20px; /* Jarak antar fitur */
        }

        .feature {
            background: linear-gradient(135deg, #ffffff, #f4f4f4); /* Gradien putih ke abu-abu muda */
            padding: 20px;
            border-radius: 12px; /* Sudut lebih melengkung */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Shadow lebih tebal */
            width: 27%;
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, color 0.3s ease; /* Efek hover termasuk warna teks */
        }

        .feature:hover {
            transform: translateY(-5px); /* Naikkan sedikit saat hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); /* Shadow lebih tebal saat hover */
            color: #228B22; /* Warna teks berubah saat hover */
        }

        .feature i {
            font-size: 50px; /* Ukuran ikon lebih besar */
            color:rgb(175, 226, 176); /* Warna emas untuk ikon */
            margin-bottom: 15px;
            transition: color 0.3s ease; /* Efek hover pada ikon */
        }

        .feature:hover i {
            color: #228B22; /* Warna hijau saat hover */
        }

        .feature h3 {
            color: #228B22; /* Warna hijau untuk judul */
            font-size: 24px; /* Ukuran font lebih besar */
            margin-bottom: 10px;
            font-weight: 600; /* Tebalkan font */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Shadow untuk teks */
            transition: color 0.3s ease; /* Efek hover pada warna teks */
        }

        .feature:hover h3 {
            color:rgb(166, 234, 175); /* Warna teks berubah menjadi emas saat hover */
        }

        .feature p {
            color: #555; /* Warna teks lebih gelap */
            font-size: 16px; /* Ukuran font lebih besar */
            line-height: 1.6; /* Jarak antar baris lebih lebar */
            transition: color 0.3s ease; /* Efek hover pada warna teks */
        }

        .feature:hover p {
            color: #228B22; /* Warna teks berubah menjadi hijau saat hover */
        }

        /* Responsif untuk Perangkat Mobile */
        @media (max-width: 768px) {
            .features {
                flex-direction: column;
                align-items: center;
            }

            .feature {
                width: 80%; /* Lebar lebih besar di mobile */
            }

            .welcome-section h2 {
                font-size: 28px; /* Ukuran font lebih kecil di mobile */
            }

            .welcome-section p {
                font-size: 16px; /* Ukuran font lebih kecil di mobile */
            }

            .feature h3 {
                font-size: 20px; /* Ukuran font lebih kecil di mobile */
            }

            .feature p {
                font-size: 14px; /* Ukuran font lebih kecil di mobile */
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