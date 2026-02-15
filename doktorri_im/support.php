<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$feedback = $conn->query("SELECT rating, comment, created_at FROM feedback WHERE user_id = $userId ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Support & Feedback</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="section">
    <div class="container two-col">
        <div>
            <h2>Support</h2>
            <p class="muted" style="margin-top:6px;">Write feedback about the platform or your experience.</p>

            <form method="post" action="handle_feedback.php" style="margin-top:16px;">
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="">Choose</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Weak</option>
                        <option value="1">1 - Very bad</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" rows="4" style="resize:vertical;padding:9px 11px;border-radius:12px;border:1px solid #d1d5db;background:#f9fafb;" required></textarea>
                </div>
                <button type="submit" class="btn primary">Send Feedback</button>
            </form>
        </div>

        <div>
            <h2>My last feedback</h2>
            <div class="card-grid" style="margin-top:12px;">
                <?php if ($feedback && $feedback->num_rows > 0): ?>
                    <?php while ($f = $feedback->fetch_assoc()): ?>
                        <article class="feedback-card">
                            <div class="stars">
                                <?php for ($i = 0; $i < (int)$f['rating']; $i++): ?>
                                    <span>★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="small-text" style="margin-top:6px;"><?= htmlspecialchars($f['comment']) ?></p>
                            <p class="muted" style="margin-top:4px;"><?= htmlspecialchars($f['created_at']) ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="muted">You have not sent any feedback yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Support</span>
    </div>
</footer>
</body>
</html>
