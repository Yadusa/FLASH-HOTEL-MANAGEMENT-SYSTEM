<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query only subadmins (role = 'subadmin')
    $sql = "SELECT * FROM admins WHERE username=? AND role='subadmin' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        // (Using plain password because you said NO hashing)
        if ($password === $row["password"]) {

            $_SESSION["admin_id"] = $row["id"];
            $_SESSION["admin_name"] = $row["username"];
            $_SESSION["admin_role"] = $row["role"];

            header("Location: dashboard.php");
            exit;
        }
    }

    header("Location: subadmin_login.php?error=Invalid credentials");
    exit;
}
?>
