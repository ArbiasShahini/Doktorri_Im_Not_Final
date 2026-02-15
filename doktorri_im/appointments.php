<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT doctor_name, date, time, created_at FROM appointments WHERE user_id = ? ORDER BY date, time");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | My Appointments</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2>My Appointments</h2>
                <p class="muted">Overview of your upcoming and past visits.</p>
            </div>
            <a href="schedule.php" class="btn primary small">New appointment</a>
        </div>

        <div class  ="appointments-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <article class="appointment-card">
                        <h3><?= htmlspecialchars($row['doctor_name']) ?></h3>
                        <p class="muted"><?= htmlspecialchars($row['date']) ?> · <?= htmlspecialchars($row['time']) ?></p>
                        <p class="small-text">Created at: <?= htmlspecialchars($row['created_at']) ?></p>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No appointments yet.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Appointments</span>
    </div>
</footer>
</body>
</html>
