<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();

        $_SESSION['user_name'] = $name;

        header("Location: settings.php?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-wrapper">
    <div class="form-title">Edit Profile</div>

    <form method="post">

        <div class="form-group">
            <label>Name</label>
            <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <button type="submit" class="btn">Save</button>

    </form>
</div>

</body>
</html>
