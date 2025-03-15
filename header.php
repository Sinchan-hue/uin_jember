<?php
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Tesis dan Disertasi</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
           
            <img src="image/logo_pemenanng-fotor-2025031605033.png" 
                alt="Logo UIN Kiai Haji Achmad Siddiq" 
                class="logo" 
                width="30" 
                height="30"> <!-- Lebar dan tinggi diatur menjadi 50 piksel -->
            <h1>SYSCA - Sistem Smart Layanan Akademik Pascasarjana</h1>
            
            <div class="user-info">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars($nama); ?></span>
                
            </div>
        </div>
    </header>
</body>
</html>