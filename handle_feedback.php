<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5 || $comment === '') {
        header("Location: support.php?fberror=1");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $rating, $comment);
    $stmt->execute();

    header("Location: support.php?fbsuccess=1");
    exit;
}
header("Location: support.php");
?>