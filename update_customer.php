<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

// 1. Fixed the key name to match your HTML form (contact_number)
$id      = $_POST['customer_id'];
$name    = $_POST['cust_name'];
$email   = $_POST['cust_email'];
$contact = $_POST['contact_number']; // Fixed typo here

/* Backend validation */
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
    die("Invalid email format");
}

// Ensure the regex is checking the corrected $contact variable
if (!preg_match('/^\d{10,}$/', $contact)) {
    die("Contact number must be at least 10 digits");
}

/* Update */
// 2. Fixed 'contact_numer' to 'contact_number' in the SQL string
$stmt = $conn->prepare("
    UPDATE customer 
    SET cust_name = ?, cust_email = ?, contact_number = ? 
    WHERE id = ?
");

// 3. Match the data types (s = string, i = integer)
$stmt->bind_param("sssi", $name, $email, $contact, $id);

if ($stmt->execute()) {
    header("Location: customers.php?updated=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}