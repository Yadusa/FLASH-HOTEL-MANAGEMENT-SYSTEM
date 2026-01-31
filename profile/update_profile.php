<?php
session_start();
include "../db_customer.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cust_name      = trim($_POST['cust_name']);
    $cust_email     = trim($_POST['cust_email']);
    $contact_number = trim($_POST['contact_number']);
    $address        = trim($_POST['address']);

    // Check if email already used by another account
    $stmtCheck = $conn->prepare(
        "SELECT id FROM customer WHERE cust_email=? AND id<>?"
    );
    $stmtCheck->bind_param("si", $cust_email, $customer_id);
    $stmtCheck->execute();

    if ($stmtCheck->get_result()->num_rows > 0) {
        die("Email already used by another account.");
    }

    // Update profile (NO username)
    $stmt = $conn->prepare(
        "UPDATE customer 
         SET cust_name=?, cust_email=?, contact_number=?, address=? 
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssssi",
        $cust_name,
        $cust_email,
        $contact_number,
        $address,
        $customer_id
    );

    if ($stmt->execute()) {
        header("Location: edit_profile.php?success=1");
        exit();
    } else {
        die("Failed to update profile.");
    }
}
?>
