<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$messageSent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    if ($question === '') {
        $error = 'Please write your question.';
    } else {
        $rating = 5;
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $rating, $question);
        $stmt->execute();
        $messageSent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyDoctor | Ask Doctor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="section">
    <div class="container chat-layout">
        <div class="chat-info">
            <h1>Ask your doctor</h1>
            <p>Here you can send questions about medications, dosage or general recommendations. Doctor or assistant will answer you later.</p>
            <ul class="chat-points">
                <li>Do not write emergency cases.</li>
                <li>Describe clearly your question.</li>
                <li>Include medicine name if you ask about medication.</li>
            </ul>
        </div>
        <div class="chat-form">
            <?php if ($messageSent): ?>
                <div class="note success">Your question has been sent.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="note error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <label>Your question</label>
                <textarea name="question" rows="6" required></textarea>
                <button type="submit" class="btn primary">Send question</button>
            </form>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <span>MyDoctor · Ask Doctor</span>
    </div>
</footer>
</body>
</html>
