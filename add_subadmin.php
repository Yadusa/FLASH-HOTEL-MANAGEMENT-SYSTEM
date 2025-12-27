<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Subadmin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Add New Subadmin</h2>

    <form action="add_subadmin_process.php" method="POST" class="form-box">
        <label>Admin ID</label>
        <input type="number" name="id" placeholder="e.g. 102" required>

        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Min 8 chars, Uppercase, Number, Symbol" required>

        <div class="button-group">
            <button type="submit" class="btn-save">Create Subadmin</button>
            <a href="manage_subadmins.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>