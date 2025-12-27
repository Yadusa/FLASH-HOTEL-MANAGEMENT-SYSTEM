<?php
session_start();
require "db.php";

// 1. Security Check: Only superadmin can create subadmins
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST["id"]);
    $username = trim($_POST["username"]);
    $plain_password = $_POST["password"];

    // 2. FIXED REGEX PATTERN
    // This now correctly checks the whole string for each requirement
    $pattern = "/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/";

    if (!preg_match($pattern, $plain_password)) {
        header("Location: manage_subadmins.php?msg=weak_password");
        exit;
    }

    // 3. Check for existing ID or Username to avoid database errors
    $check = $conn->prepare("SELECT id FROM admins WHERE id = ? OR username = ?");
    $check->bind_param("is", $id, $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        header("Location: manage_subadmins.php?msg=exists");
        exit;
    }

    // 4. Hash the password
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // 5. Insert into database
    $stmt = $conn->prepare("INSERT INTO admins (id, username, password, role) VALUES (?, ?, ?, 'subadmin')");
    $stmt->bind_param("iss", $id, $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: manage_subadmins.php?msg=added");
    } else {
        header("Location: manage_subadmins.php?msg=error");
    }
    exit;
}
?>