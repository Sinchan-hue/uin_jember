<?php
// init.php
session_start();
require_once 'config.php';

// Validasi session dasar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
?>