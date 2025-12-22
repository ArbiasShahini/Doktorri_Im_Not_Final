<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Mysafir';
$isAdmin = !empty($_SESSION['is_admin']);
?>
<header class="top-nav">
    <div class="container nav-container">
        <div class="brand">
            <a href="index.php">MyDoctor</a>
        </div>
        <nav class="main-nav">
            <a href="index.php">Home</a>
            <a href="doctors.php">Doctors</a>
            <a href="pharmacy.php">Pharmacy</a>
            <a href="appointments.php">My Appointments</a>
            <a href="chat.php">Ask Doctor</a>
            <a href="support.php">Support</a>
            <a href="settings.php">Settings</a>
            <?php if ($isAdmin): ?>
                <a href="admin_dashboard.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </nav>
        <div class="user-pill">
            <?= htmlspecialchars($userName) ?>
        </div>
    </div>
</header>
