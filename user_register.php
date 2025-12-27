<?php
session_start();
require_once "db.php";

/* Get form data safely */
$username    = $_POST['username'];
$cust_name   = $_POST['cust_name'];
$cust_email  = $_POST['cust_email'];
$contact     = $_POST['contact_numer'];
$password    = $_POST['password'];
$room        = $_POST['room'] ?? '';

if (!filter_var($cust_email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $cust_email)) {
    die("Email must be a valid email address ending with .com");
}

if (!preg_match('/^\d{10,}$/', $contact)) {
    die("Contact number must be at least 10 digits.");
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
    die("Password must contain uppercase, lowercase, number, symbol and be at least 8 characters long.");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* Prepared statement (SECURE) */
$sql = "INSERT INTO customer 
(username, cust_name, cust_email, contact_numer, password_hash) 
VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssss",
    $username,
    $cust_name,
    $cust_email,
    $contact,
    $password_hash
);

if ($stmt->execute()) {

    $_SESSION['customer'] = $username;

    if (!empty($room)) {
        header("Location: booking-form.html?room=" . urlencode($room));
    } else {
        header("Location: hotel.html");
    }
    exit;

} else {
    echo "Username or Email already exists!";
}

$stmt->close();
$conn->close();
?>
