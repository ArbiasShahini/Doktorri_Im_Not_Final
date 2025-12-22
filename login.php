<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-title">Log in to MyDoctor</div>
        <div class="form-subtitle">Access your appointments and doctors.</div>

        <form method="post" action="handle_login.php">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn primary" style="width:100%;margin-top:8px;">Login</button>
        </form>

        <div class="form-footer" style="margin-top:16px;">
            Don't have an account?
            <a href="register.php">Create one</a>
        </div>

        <div class="form-footer" style="margin-top:6px;">
            Admin?
            <a href="admin_login.php">Login as admin</a>
        </div>
    </div>
</div>

</body>
</html>
