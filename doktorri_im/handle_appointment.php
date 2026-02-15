<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $doctor_name = trim($_POST['doctor_name'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';

    if ($doctor_name === '' || $date === '' || $time === '') {
        header("Location: schedule.php?error=1");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_name, date, time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $doctor_name, $date, $time);
    $stmt->execute();

    header("Location: appointments.php?success=1");
    exit;
}
header("Location: schedule.php");
?>