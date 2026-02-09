<?php
session_start();
include "db.php"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Added 'is_first_login' to the SELECT statement
    $stmt = $conn->prepare("SELECT id, username, password, role, is_first_login FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin["password"])) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["username"];
            $_SESSION["admin_role"] = $admin["role"];

            // --- NEW REDIRECTION LOGIC ---
            // If the user is a subadmin and it's their first time logging in
            if ($admin["role"] === "subadmin" && $admin["is_first_login"] == 1) {
                header("Location: first_login_change_pw.php");
                exit;
            } else {
                header("Location: dashboard.php");
                exit;
            }
            // -----------------------------

        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials!"; 
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login | The Obsidian</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required
         autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

        <input type="password" name="password" placeholder="Password" required
         autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

        <button type="submit">Login</button>
        <br><a href="hotel.php" class="back-btn">← Back to Hotel</a>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
</div>

</body>
</html>