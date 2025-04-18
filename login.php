<?php
session_start();
include 'koneksi.php';

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
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #228B22;
            --primary-dark: #1A6D1A;
            --secondary: #FFD700;
            --secondary-dark: #E5C100;
            --light: #F8F9FA;
            --dark: #212529;
            --danger: #DC3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .logo-container {
            margin-bottom: 1rem; /* Diubah dari 1.5rem */
            position: relative;
        }

        .logo-container img {
            width: 120px;
            height: auto;
            margin-bottom: 1rem;
        }

        .title {
            color: var(--primary);
            margin-bottom: 0.3rem; /* Diubah dari 0.5rem */
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 0.5px;
            line-height: 1.2; /* Ditambahkan */
        }

        .subtitle {
            color: var(--dark);
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 0.2rem; /* Diubah dari 0.3rem */
            line-height: 1.2; /* Diubah dari 1.4 */
        }

        .university {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.2rem; /* Diubah dari 1.8rem */
            position: relative;
            display: inline-block;
            line-height: 1.2; /* Ditambahkan */
        }

        .university::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--secondary);
            border-radius: 3px;
        }

        .error-message {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-field {
            position: relative;
        }

        .input-field i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: var(--light);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(34, 139, 34, 0.2);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.8rem;
            }
            
            .title {
                font-size: 1.5rem;
            }
            
            .subtitle {
                font-size: 0.9rem;
            }
            
            .university {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="image/logo_pemenanng-Photoroom.png" alt="Logo UIN KHAS">
            <h1 class="title">SYSCA</h1>
            <p class="subtitle">System Smart Layanan Akademik Pascasarjana</p>
            <p class="university">UIN Kiai Haji Achmad Siddiq</p>
            <p class="subtitle">Mas_dir25</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message show"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="footer">
            &copy; 2025 SYSCA. All rights reserved.
        </div>
    </div>

    <script>
        // Animasi untuk input fields
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i').style.color = '#228B22';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i').style.color = '#228B22';
            });
        });
    </script>
</body>
</html>