<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];

$sql = "DELETE FROM staffs WHERE staff_id='$id'";

if ($conn->query($sql)) {
    header("Location: manage_staff.php");
} else {
    echo "Error deleting staff: " . $conn->error;
}
?>
