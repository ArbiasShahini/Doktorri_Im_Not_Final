<?php
require "db.php";

$id = $_POST["id"];
$action = $_POST["action"];

if ($action === "resolve") {
    $stmt = $conn->prepare("UPDATE human_requests SET status='resolved' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php?tab=human_requests");
    exit;
}

if ($action === "reply") {
    $reply = $_POST["reply"];
    $email = $_POST["email"];

    $stmt = $conn->prepare("UPDATE human_requests SET status='resolved' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    mail(
        $email,
        "Response from MyDoctor Support",
        "Your request (ID $id) has been reviewed:\n\n$reply\n\nMyDoctor Team"
    );

    header("Location: admin_dashboard.php?tab=human_requests");
    exit;
}
?>
