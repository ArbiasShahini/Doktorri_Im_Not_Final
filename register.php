<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-title">Create MyDoctor account</div>
        <div class="form-subtitle">Book appointments faster and keep everything in one place.</div>

        <form method="post" action="handle_register.php">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn primary" style="width:100%;margin-top:8px;">Register</button>
        </form>

        <div class="form-footer" style="margin-top:16px;">
            Already have an account?
            <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>
