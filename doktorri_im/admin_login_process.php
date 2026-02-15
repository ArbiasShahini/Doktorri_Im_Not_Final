<?php
session_start();

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if ($user === 'admin' && $pass === 'Admin123!') {
    $_SESSION['is_admin'] = true;
    header("Location: admin_dashboard.php");
    exit;
}
header("Location: admin_login.php?error=1");
?>