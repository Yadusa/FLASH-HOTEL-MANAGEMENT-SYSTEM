<?php
session_start();
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "subadmin") {
    header("Location: subadmin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-container">
    <form action="update_first_password.php" method="POST" class="login-box">
        <h2>Set Permanent Password</h2>
        <p>This is your first login. Please create a new password to continue.</p>

        <?php if (isset($_GET['error'])) echo "<p class='error-msg'>".$_GET['error']."</p>"; ?>

        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="new_password" required minlength="8">
        </div>
        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required minlength="8">
        </div>
        <button type="submit" class="btn">Update & Login</button>
    </form>
</div>
</body>
</html>