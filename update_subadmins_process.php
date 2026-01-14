<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_id = $_POST['old_id'];
    $new_id = $_POST['id'];
    $username = $_POST['username'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE admins SET id = ?, username = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issi", $new_id, $username, $status, $old_id);

    if ($stmt->execute()) {
        header("Location: manage_subadmins.php?msg=updated");
    } else {
        header("Location: manage_subadmins.php?msg=error");
    }
    $stmt->close();
}
?>