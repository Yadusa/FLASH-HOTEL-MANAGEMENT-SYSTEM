<?php
session_start();
require_once "db.php";

// 1. Check if the user is a superadmin
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

// 2. Check if the 'id' is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ✅ FIX: Changed 'customer_id' to 'id' to match your database schema
    $stmt = $conn->prepare("DELETE FROM customer WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to customers list with a success message
        header("Location: customers.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // Redirect if no ID was provided
    header("Location: customers.php");
    exit;
}
?>