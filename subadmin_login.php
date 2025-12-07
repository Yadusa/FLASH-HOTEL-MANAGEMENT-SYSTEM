<?php
session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subadmin Login</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="login-container">
    <form action="subadmin_login_process.php" method="POST" class="login-box">
        <h2>Subadmin Login</h2>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>

        <p class="note">Only subadmins can access this login.</p>
    </form>
</div>

</body>
</html>
