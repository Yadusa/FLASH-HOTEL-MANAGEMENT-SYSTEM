<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

$id      = $_POST['customer_id'];
$name    = $_POST['cust_name'];
$email   = $_POST['cust_email'];
$contact = $_POST['contact_numer'];

/* Backend validation */
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
    die("Invalid email format");
}

if (!preg_match('/^\d{10,}$/', $contact)) {
    die("Contact number must be at least 10 digits");
}

/* Update */
$stmt = $conn->prepare("
    UPDATE customer 
    SET cust_name = ?, cust_email = ?, contact_numer = ?
    WHERE customer_id = ?
");
$stmt->bind_param("sssi", $name, $email, $contact, $id);

$stmt->execute();

header("Location: customers.php?updated=1");
exit;
