<?php
session_start();
require 'db.php';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT id, name, specialization, description FROM doctors";
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $sql .= " WHERE name LIKE '%$s%' OR specialization LIKE '%$s%'";
}
$sql .= " ORDER BY name";
$doctors = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Doctors</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2>Doctors</h2>
                <p class="muted">Choose a specialist and book an appointment.</p>
            </div>
            <form method="get">
                <div class="form-group" style="margin-bottom:0;">
                    <input type="text" name="q" placeholder="Search by name or specialization" value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>
        </div>

        <div class="card-grid">
            <?php if ($doctors && $doctors->num_rows > 0): ?>
                <?php while ($d = $doctors->fetch_assoc()): ?>
                    <article class="doctor-card">
                        <div class="doctor-avatar">
                            <span><?= strtoupper(substr($d['name'], 0, 1)) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($d['name']) ?></h3>
                        <p class="muted"><?= htmlspecialchars($d['specialization']) ?></p>
                        <p class="small-text"><?= htmlspecialchars($d['description']) ?></p>
                        <a href="appointments.php" class="btn small primary">Book</a>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No doctors found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Doctors</span>
        <span>Student project</span>
    </div>
</footer> 
<!-- -->
</body>
</html>
