<?php
session_start();
$err = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doktorri Im | Admin Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <div class="navbar">
    <div class="logo">Doktorri Im</div>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="login.php">User Login</a>
      <a href="admin_login.php" class="active">Admin</a>
    </nav>
  </div>
</header>

<div class="form-wrapper">
  <div class="form-title">Admin login</div>
  <div class="form-subtitle">Only for administrators</div>

  <div class="message error">
    <?php if ($err) echo "Invalid credentials."; ?>
  </div>

  <form method="post" action="admin_login_process.php">
    <div class="form-group">
      <label for="ad_name">Username</label>
      <input type="text" id="ad_name" name="username">
    </div>
    <div class="form-group">
      <label for="ad_pass">Password</label>
      <input type="password" id="ad_pass" name="password">
    </div>
    <button class="btn" type="submit">Log in</button>
  </form>
</div>

<footer>
  Doktorri Im · Student Project
</footer>
</body>
</html>