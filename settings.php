<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$id = $_SESSION['user_id'];
$user = $conn->query("SELECT name, email, created_at FROM users WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Settings</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="section">
    <div class="container">
        <h2>Settings</h2>
        <p class="muted" style="margin-top:6px;margin-bottom:16px;">Manage your MyDoctor account.</p>

        <div class="settings-list">
            <div class="settings-item">
                <div>
                    <div class="settings-label">Account</div>
                    <span><?= htmlspecialchars($user['name']) ?></span>
                </div>
                <a href="profile.php" class="btn small outline">Edit profile</a>
            </div>
            <div class="settings-item">
                <div>
                    <div class="settings-label">Email</div>
                    <span><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <span class="badge badge-blue">Verified</span>
            </div>
            <div class="settings-item">
                <div>
                    <div class="settings-label">Notifications</div>
                    <span>Enabled</span>
                </div>
                <span class="badge badge-gray">Default</span>
            </div>
            <div class="settings-item">
                <div>
                    <div class="settings-label">Member since</div>
                    <span><?= htmlspecialchars(substr($user['created_at'], 0, 10)) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Settings</span>
    </div>
</footer>
</body>
</html>
