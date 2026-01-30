<?php
session_start();
require_once "db.php";

// Security check
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: customers.php");
    exit;
}

$customerId = $_GET['id'];

// Soft delete (deactivate)
$sql = "UPDATE customer SET status = 'inactive' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);
$stmt->execute();

header("Location: customers.php?deleted=1");
exit;
