<?php
session_start();
require 'db.php';
$doctors = $conn->query("SELECT id, name, specialization, description FROM doctors LIMIT 3");
$feedback = $conn->query("SELECT f.comment, f.rating, u.name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Welcome</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Welcome to MyDoctor</h1>
                <p>Your digital clinic for quick, simple and safe medical appointments.</p>
                <div class="hero-actions">
                    <a href="doctors.php" class="btn primary">Find a Doctor</a>
                    <a href="appointments.php" class="btn outline">Book Appointment</a>
                </div>
                <div class="hero-stats">
                    <div>
                        <h3>24/7</h3>
                        <span>Online access</span>
                    </div>
                    <div>
                        <h3>+50</h3>
                        <span>Verified doctors</span>
                    </div>
                    <div>
                        <h3>4.9/5</h3>
                        <span>Patient rating</span>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <h3>MyDoctor Clinic</h3>
                    <p>Trusted doctors, simple booking and clear communication between doctor and patient.</p>
                </div>
                <div class="hero-mini">
                    <span>New</span>
                    Online pharmacy and direct questions to your doctor.
                </div>
            </div>
        </div>
    </section>

    <section class="section gray">
        <div class="container about-grid">
            <div>
                <h2>About MyDoctor</h2>
                <p>MyDoctor helps patients in Kosovo book appointments, ask questions about medications and find nearby pharmacies in a few clicks.</p>
                <p>The platform is created as a student project with focus on real problems of patients and doctors.</p>
            </div>
            <div class="about-cards">
                <div class="info-card">
                    <h3>Fast access</h3>
                    <p>Book your doctor in seconds, without waiting lines.</p>
                </div>
                <div class="info-card">
                    <h3>Clear communication</h3>
                    <p>Ask questions to your doctor or assistant through MyDoctor.</p>
                </div>
                <div class="info-card">
                    <h3>Safe data</h3>
                    <p>Your data is stored securely in the system database.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Doctors</h2>
                <a href="doctors.php" class="link">See all doctors</a>
            </div>
            <div class="card-grid">
                <?php if ($doctors && $doctors->num_rows > 0): ?>
                    <?php while ($d = $doctors->fetch_assoc()): ?>
                        <article class="doctor-card">
                            <div class="doctor-avatar">
                                <span><?= substr($d['name'], 0, 1) ?></span>
                            </div>
                            <h3><?= htmlspecialchars($d['name']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($d['specialization']) ?></p>
                            <p class="small-text"><?= htmlspecialchars($d['description']) ?></p>
                            <a href="appointments.php" class="btn small">Book</a>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No doctors yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section gray">
        <div class="container two-col">
            <div>
                <h2>Pharmacy near you</h2>
                <p>Search for medicines and see which pharmacy has them available. MyDoctor helps you not to waste time from one place to another.</p>
                <a href="pharmacy.php" class="btn primary">Open Pharmacy</a>
            </div>
            <div>
                <h2>Ask your doctor</h2>
                <p>You can send questions to your doctor or assistant about medications and recommendations.</p>
                <a href="chat.php" class="btn outline">Ask a question</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>What patients say</h2>
            </div>
            <div class="card-grid">
                <?php if ($feedback && $feedback->num_rows > 0): ?>
                    <?php while ($f = $feedback->fetch_assoc()): ?>
                        <article class="feedback-card">
                            <div class="stars">
                                <?php for ($i = 0; $i < (int)$f['rating']; $i++): ?>
                                    <span>★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="small-text">"<?= htmlspecialchars($f['comment']) ?>"</p>
                            <p class="muted">Patient: <?= htmlspecialchars($f['name']) ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No feedback yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Student Project</span>
        <span>Built by Arbias Shahini</span>
    </div>
</footer>
</body>
</html>
