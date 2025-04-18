<?php
$role = $_SESSION['role']; // Ambil role dari session
$user_id = $_SESSION['user_id']; // Ambil user_id dari session
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
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav>
            <ul>
                <!-- Menu Beranda (Tampil untuk semua role) -->
                <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>

                <?php if ($role == 'admin'): ?>
                    <!-- Menu Admin -->
                    <li>
                        <a href="#"><i class="fas fa-book"></i> Ujian Tesis</a>
                        <ul>
                            <li><a href="kusus_tesis.php"><i class="fas fa-list"></i> Data Tesis</a></li>
                            <li><a href="rekap_tesis.php"><i class="fas fa-file-alt"></i> Rekap Tesis</a></li>
                            <li><a href="peran_tesis.php"><i class="fas fa-clipboard-list"></i> Peran Tesis</a></li>
                            <li><a href="rekap_pembimbing.php"><i class="fas fa-users"></i> Pembimbing 1 & 2</a></li>
                        </ul>
                    </li>

                    <!-- Submenu Ujian Disertasi -->
                    <li>
                        <a href="#"><i class="fas fa-book"></i> Ujian Disertasi</a>
                        <ul>
                            <li><a href="kusus_desertasi.php"><i class="fas fa-list"></i> Data Disertasi</a></li>
                            <li><a href="rekap_desertasi.php"><i class="fas fa-file-alt"></i> Rekap Disertasi</a></li>
                            <li><a href="peran_desertasi.php"><i class="fas fa-clipboard-list"></i> Peran Desertasi</a></li>
                            <li><a href="rekap_co_promotor.php"><i class="fas fa-users"></i> Rekap Co/Promotor</a></li>
                        </ul>
                    </li>

                    <!-- Rekap Total Menu -->
                    <li>
                        <a href="rekap_total.php"><i class="fas fa-file-contract"></i> Rekap Total</a>
                    </li>

                    <li><a href="crud_tesis.php"><i class="fas fa-book"></i> Tesis</a></li>
                    <li><a href="crud_mahasiswa.php"><i class="fas fa-users"></i> Mahasiswa</a></li>
                    <li><a href="crud_dosen.php"><i class="fas fa-chalkboard-teacher"></i> Dosen</a></li>                    
                    <li><a href="crud_user.php"><i class="fas fa-user"></i> User</a></li>
                <?php elseif ($role == 'dosen'): ?>
                    <!-- Menu Dosen -->
                    <li>
                        <a href="#"><i class="fas fa-book"></i> Ujian Tesis</a>
                        <ul>
                            <li><a href="view_tesis.php"><i class="fas fa-list"></i> Data Tesis</a></li>
                            <li><a href="rekap_tesis.php"><i class="fas fa-file-alt"></i> Rekap Tesis</a></li>
                            <li><a href="peran_tesis.php"><i class="fas fa-clipboard-list"></i> Peran Tesis</a></li>
                            <li><a href="rekap_pembimbing.php"><i class="fas fa-users"></i> Pembimbing 1 & 2</a></li>
                        </ul>
                    </li>
                    
                    <!-- Submenu Ujian Disertasi -->
                    <li>
                        <a href="#"><i class="fas fa-book"></i> Ujian Disertasi</a>
                        <ul>
                            <li><a href="view_desertasi.php"><i class="fas fa-list"></i> Data Disertasi</a></li>
                            <li><a href="rekap_desertasi.php"><i class="fas fa-file-alt"></i> Rekap Disertasi</a></li>
                            <li><a href="peran_desertasi.php"><i class="fas fa-clipboard-list"></i> Peran Desertasi</a></li>
                            <li><a href="rekap_co_promotor.php"><i class="fas fa-users"></i> Rekap Co/Promotor</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Menu Ganti Password (Tampil untuk semua role) -->
                <li><a href="ubah_password.php"><i class="fas fa-key"></i> Ganti Password</a></li>

                <!-- Menu Logout (Tampil untuk semua role) -->
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
</body>
</html>