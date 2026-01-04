<?php
session_start();
require "db.php";

// Check authorization
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT * FROM admins WHERE role='subadmin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subadmins | FLASH Hotel</title>
    <link rel="stylesheet" href="subadmin.css">
    <style>
        /* Extra styling for the temporary password box */
        .otp-display {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 5px solid #ffc107;
        }
        .otp-code {
            font-family: monospace;
            font-size: 1.3rem;
            font-weight: bold;
            color: #d9534f;
            background: #eee;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH Hotel Admin</h2>
        <p class="role">SuperAdmin</p>
    </div>
    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_subadmins.php" class="active"> Manage Subadmin</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="content-header-wrapper">
        <div class="content-header">
            <h2>Subadmin List</h2>
            <p>Overview of all administrative staff with sub-level permissions.</p>
        </div>
        <div class="action-bar-bottom">
            <a href="add_subadmin.php" class="btn">+ Add Subadmin</a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="<?php echo ($_GET['msg'] == 'weak_password' || $_GET['msg'] == 'exists' || $_GET['msg'] == 'error') ? 'msg-error' : 'msg-success'; ?>">
            <?php 
                if($_GET['msg'] == 'weak_password') echo "<strong>Error:</strong> Password requirements not met.";
                if($_GET['msg'] == 'added') echo "Subadmin account created successfully!";
                if($_GET['msg'] == 'exists') echo "<strong>Error:</strong> ID or Username already exists.";
                if($_GET['msg'] == 'error') echo "<strong>Error:</strong> Something went wrong.";
            ?>
        </div>

        <?php if($_GET['msg'] == 'added' && isset($_SESSION['temp_pw_display'])): ?>
            <div class="otp-display">
                <strong>IMPORTANT:</strong> Please provide this Temporary Password to the subadmin: 
                <span class="otp-code"><?php echo $_SESSION['temp_pw_display']; ?></span>
                <br><small>This password will only be shown once for security reasons. The subadmin will be forced to change it upon their first login.</small>
            </div>
            <?php unset($_SESSION['temp_pw_display']); // Remove it from session after displaying ?>
        <?php endif; ?>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row["id"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($row["username"]); ?></td>
                        <td>
                            <?php if(isset($row['is_first_login']) && $row['is_first_login'] == 1): ?>
                                <span style="color: #f0ad4e;">● Pending First Login</span>
                            <?php else: ?>
                                <span style="color: #5cb85c;">● Active</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="edit_subadmins.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_subadmins.php?id=<?php echo $row['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this subadmin?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #999; padding: 40px;">
                        No subadmins found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>