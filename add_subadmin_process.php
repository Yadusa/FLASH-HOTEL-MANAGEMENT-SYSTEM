<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$id = $_POST["id"];
$username = $_POST["username"];
$password = $_POST["password"];

$stmt = $conn->prepare("INSERT INTO admins (id, username, password, role) VALUES (?, ?, ?, 'subadmin')");
$stmt->bind_param("iss", $id, $username, $password);

if ($stmt->execute()) {
    header("Location: manage_subadmins.php?msg=added");
} else {
    header("Location: manage_subadmins.php?msg=error");
}
exit;
?>
