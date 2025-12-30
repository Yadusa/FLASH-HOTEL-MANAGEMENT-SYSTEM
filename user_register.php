<?php
session_start();
require_once "db.php";

/* 1. INSERT THE DATA RETRIEVAL HERE */
$username    = $_POST['username'] ?? '';
$cust_name   = $_POST['cust_name'] ?? '';
$cust_email  = $_POST['cust_email'] ?? '';
$password    = $_POST['password'] ?? '';
$room        = $_POST['room'] ?? '';

// This is the specific block you asked about:
$contact = $_POST['contact_number'] ?? ''; 

/* 2. INSERT THE VALIDATION HERE */
if (!filter_var($cust_email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $cust_email)) {
    die("Email must be a valid email address ending with .com");
}

// Validation for the 10-digit contact number
if (!preg_match('/^\d{10,}$/', $contact)) {
    die("Contact number must be at least 10 digits. (Received: " . htmlspecialchars($contact) . ")");
}

/* 3. THE REST OF YOUR CODE (Hasing and SQL) */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* Prepared statement */
$sql = "INSERT INTO customer (username, cust_name, cust_email, contact_number, password_hash) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $cust_name, $cust_email, $contact, $password_hash);

try {
    if ($stmt->execute()) {
        // Success Message and Redirect
        echo "<script>
                alert('Successfully registered! Please log in to continue.');
                window.location.href = 'user_login.php';
              </script>";
        exit;
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo "<script>
                alert('This username or email already exists. Please try another one.');
                window.history.back();
              </script>";
        exit;
    } else {
        die("Database error: " . $e->getMessage());
    }
}

$stmt->close();
$conn->close();
?>
