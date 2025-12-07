<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$old_id = $_POST["old_id"];
$new_id = $_POST["id"];
$username = $_POST["username"];
$password = $_POST["password"];

$stmt = $conn->prepare("UPDATE admins SET id=?, username=?, password=? WHERE id=?");
$stmt->bind_param("issi", $new_id, $username, $password, $old_id);

if ($stmt->execute()) {
    header("Location: manage_subadmins.php?msg=updated");
} else {
    header("Location: manage_subadmins.php?msg=error");
}
exit;
?>
