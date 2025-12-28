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
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>

<div class="sidebar">
    <h2>Flash Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Add New Subadmin</h2>
            <p>Assign a new administrator to manage hotel operations.</p>
        </div>

        <form action="add_subadmin_process.php" method="POST" class="admin-form">
            <div class="form-group">
                <label for="id">Admin ID</label>
                <input type="number" id="id" name="id" placeholder="e.g., 102" required>
                <small>Unique identification number for the staff.</small>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <ul class="password-requirements">
                   <li>Minimum 8 characters</li>
                   <li>Must include at least one uppercase letter</li>
                   <li>Must include at least one lowercase letter</li>
                   <li>Must include at least one number</li>
                   <li>Must include at least one symbol (e.g., @, #, $, !)</li>
                </ul>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Subadmin</button>
                <a href="manage_subadmins.php" class="btn-link">Cancel and Go Back</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>