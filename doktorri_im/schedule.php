<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$prefillDoctor = isset($_GET['doctor']) ? trim($_GET['doctor']) : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor = trim($_POST['doctor'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $uid = intval($_SESSION['user_id']);

    if ($doctor === '' || $date === '' || $time === '') {
        $error = 'Please fill all fields.';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_name, date, time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $uid, $doctor, $date, $time);
            $stmt->execute();
            header("Location: appointments.php?success=1");
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = 'Database error. Please restart MySQL in XAMPP and try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment | MyDoctor</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body { background: #f8fafc; }
        .schedule-container {
            max-width: 620px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 14px;
            padding: 35px 40px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        .schedule-title {
            font-size: 28px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
        }
        .schedule-subtitle {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 14px;
            color: #475569;
            margin-bottom: 6px;
        }
        input, select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
            font-size: 15px;
            color: #0f172a;
            transition: 0.2s ease;
        }
        input:focus, select:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.3);
            outline: none;
        }
        .submit-btn {
            width: 100%;
            margin-top: 10px;
            padding: 12px;
            border-radius: 999px;
            background: linear-gradient(to right, #2563eb, #1d4ed8);
            color: #ffffff;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: 0.2s ease;
        }
        .submit-btn:hover {
            background: linear-gradient(to right, #1e40af, #1d4ed8);
        }
        .schedule-icon { text-align: center; margin-bottom: 18px; }
        .schedule-icon img { width: 70px; opacity: 0.95; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #475569;
            font-size: 14px;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 14px;
        }
    </style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="schedule-container">

    <div class="schedule-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/3209/3209265.png">
    </div>

    <div class="schedule-title">Schedule Appointment</div>
    <div class="schedule-subtitle">Choose doctor, date, and time for your visit.</div>

    <?php if ($error !== ''): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Doctor</label>
            <input type="text" name="doctor" placeholder="Enter doctor name" value="<?= htmlspecialchars($prefillDoctor) ?>">
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" min="<?= date('Y-m-d') ?>">
        </div>

        <div class="form-group">
            <label>Time</label>
            <input type="time" name="time">
        </div>

        <button type="submit" class="submit-btn">Confirm Appointment</button>

    </form>

    <a href="index.php" class="back-link">Back to homepage</a>

</div>

</body>
</html>
