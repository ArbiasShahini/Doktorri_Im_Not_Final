<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "doktorri_im";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error");
}
?>