<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get & sanitize inputs
    $username        = trim($_POST['username']);
    $cust_name       = trim($_POST['cust_name']);
    $cust_email      = trim($_POST['cust_email']);
    $contact_number  = trim($_POST['contact_number']);
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    /* 1️⃣ Basic validation */
    if (
        empty($username) || empty($cust_name) || empty($cust_email) ||
        empty($contact_number) || empty($password) || empty($confirmPassword)
    ) {
        header("Location: customer_register.php?error=empty");
        exit;
    }

    /* 2️⃣ Password match check */
    if ($password !== $confirmPassword) {
        header("Location: customer_register.php?error=password_mismatch");
        exit;
    }

    /* 3️⃣ Check if username or email already exists */
    $checkStmt = $conn->prepare(
        "SELECT id FROM customer WHERE username = ? OR cust_email = ?"
    );
    $checkStmt->bind_param("ss", $username, $cust_email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        header("Location: customer_register.php?error=exists");
        exit;
    }

    /* 4️⃣ Hash password */
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    /* 5️⃣ Insert customer */
    $stmt = $conn->prepare(
        "INSERT INTO customer 
        (username, cust_name, cust_email, contact_number, password_hash, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())"
    );

    $stmt->bind_param(
        "sssss",
        $username,
        $cust_name,
        $cust_email,
        $contact_number,
        $hashedPassword
    );

    if ($stmt->execute()) {
        // Success → redirect to login
        header("Location: customer_login.php?registered=1");
        exit;
    } else {
        // DB error
        header("Location: customer_register.php?error=failed");
        exit;
    }

} else {
    // Direct access not allowed
    header("Location: customer_register.php");
    exit;
}
