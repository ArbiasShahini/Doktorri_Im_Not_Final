<?php
session_start();
if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}
require 'db.php';

$id = intval($_GET['id'] ?? 0);
$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();

    header("Location: admin_dashboard.php?tab=users&updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-wrapper">
  <div class="form-title">Edit User</div>

  <form method="post">
    <div class="form-group">
      <label>Name</label>
      <input name="name" value="<?= htmlspecialchars($user['name']) ?>">
    </div>
    <div class="form-group">
      <label>Email</label>
      <input name="email" value="<?= htmlspecialchars($user['email']) ?>">
    </div>
    <button class="btn">Save</button>
  </form>
</div>
</body>
</html>
