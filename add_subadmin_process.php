<?php
session_start();
require "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST["id"]);
    $username = trim($_POST["username"]);

    // 2. Generate a random 8-character Temporary Password
    // bin2hex(random_bytes(4)) creates an 8-char alphanumeric string
    $temp_password = bin2hex(random_bytes(4)); 

    // 3. Check for existing ID or Username
    $check = $conn->prepare("SELECT id FROM admins WHERE id = ? OR username = ?");
    $check->bind_param("is", $id, $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        header("Location: manage_subadmins.php?msg=exists");
        exit;
    }

    // 4. Hash the random password
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

    // 5. Insert into database with is_first_login = 1
    // Note: Ensure your table has the 'is_first_login' column
    $stmt = $conn->prepare("INSERT INTO admins (id, username, password, role, is_first_login) VALUES (?, ?, ?, 'subadmin', 1)");
    $stmt->bind_param("iss", $id, $username, $hashed_password);

    if ($stmt->execute()) {
        // Redirect back with the temporary password in the session to show it ONCE
        $_SESSION['temp_pw_display'] = $temp_password;
        header("Location: manage_subadmins.php?msg=added");
    } else {
        header("Location: manage_subadmins.php?msg=error");
    }
    exit;
}
?>