<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, role, is_first_login FROM admins WHERE username = ? AND role = 'subadmin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION["admin_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["admin_role"] = $row["role"];

            // ✅ THE FIX: Check if it's the first time logging in
            if ($row['is_first_login'] == 1) {
                header("Location: first_login_reset.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            header("Location: subadmin_login.php?error=Invalid password");
        }
    } else {
        header("Location: subadmin_login.php?error=User not found");
    }
}

