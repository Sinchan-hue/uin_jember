<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php sudah ada

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan username dan password
    $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama']; // Simpan nama pengguna ke session
        $_SESSION['role'] = $user['role'];
        header("Location: index.php"); // Redirect ke halaman beranda
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SYSCA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #228B22, #FFD700); /* Hijau dan Emas */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        .login-container img {
            width: 150px;
            
        }

        .login-container h1 {
            
            color: #228B22; /* Hijau */
            font-size: 24px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #228B22; /* Emas */
            font-size: 18px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #228B22; /* Hijau */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background: #1E7A1E; /* Hijau lebih gelap */
        }

        .login-container .error {
            color: red;
            margin-top: 10px;
        }

        .login-container .footer {
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="image/logo_pemenanng-Photoroom.png" alt="Logo UIN KHAS">
        <h1>SYSCA</h1>
        <h2>System Smart Layanan Akademik Pascasarjana</h2>
        <h2>UIN Kiai Haji Achmad Siddiq</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="footer">
            &copy; 2025 SYSCA. All rights reserved.
        </div>
    </div>
</body>
</html>