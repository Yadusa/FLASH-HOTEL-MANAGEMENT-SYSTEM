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
    <title>Add Subadmin | The Obsidian</title>
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>The Obsidian</h2>
        <p class="role">SuperAdmin</p>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Add New Subadmin</h2>
            <p>The system will automatically generate a secure temporary password for this account.</p>
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

            <div class="info-box" style="background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 5px solid #2196F3; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 0.9rem; color: #0c5460;">
                    <strong>Note:</strong> Upon creation, you will see a temporary password once. Please copy and provide it to the subadmin. They will be forced to change it when they first log in.
                </p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Generate Subadmin Account</button>
                <a href="manage_subadmins.php" class="btn-link">Cancel and Go Back</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>