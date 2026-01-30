<?php
session_start();
include "../db_customer.php";

$customer_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username       = trim($_POST['username']);
    $cust_name      = trim($_POST['cust_name']);
    $cust_email     = trim($_POST['cust_email']);
    $contact_number = trim($_POST['contact_number']);
    $address        = trim($_POST['address']);

    // Optional: check if username/email already exists for another user
    $stmtCheck = $conn->prepare("SELECT id FROM customer WHERE (username=? OR cust_email=?) AND id<>?");
    $stmtCheck->bind_param("ssi", $username, $cust_email, $customer_id);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows > 0) {
        die("Username or email already used by another account.");
    }

    $stmt = $conn->prepare("UPDATE customer 
        SET username=?, cust_name=?, cust_email=?, contact_number=?, address=?
        WHERE id=?");
    $stmt->bind_param("sssssi", $username, $cust_name, $cust_email, $contact_number, $address, $customer_id);
    
    if ($stmt->execute()) {
        $_SESSION['customer_username'] = $username; // update session
        header("Location: edit_profile.php?success=1");
        exit();
    } else {
        die("Failed to update profile: " . $conn->error);
    }
}
?>
