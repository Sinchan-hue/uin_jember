<?php
session_start();
session_destroy(); // Hapus semua data session
header("Location: login.php"); // Redirect ke halaman login
exit();
?>