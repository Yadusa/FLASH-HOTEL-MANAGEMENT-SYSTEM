<?php
session_start();
include "db.php"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $admin["password"])) {
            // Prevent Session Fixation
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["username"];
            $_SESSION["admin_role"] = $admin["role"];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials!"; // Keep message vague for security
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>



<div class="login-container">
    <h2>Admin Login</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username"
         autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">


        <input type="password" name="password" placeholder="Password"
         autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">


        <button type="submit">Login</button>
    </form>

    <p class="error"><?php echo $error; ?></p>
</div>

</body>
</html>
