<?php
session_start();
require "db.php";

// Check authorization: Only superadmin can access this page
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

// Fetch only subadmins from the database
$result = $conn->query("SELECT * FROM admins WHERE role='subadmin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subadmins | FLASH Hotel</title>
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH Hotel Admin</h2>
        <p class="role">SuperAdmin</p>
    </div>

    <a href="dashboard.php"> Dashboard</a>
    <a href="customers.php" class="active"> Customers</a>
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
            if($_GET['msg'] == 'weak_password') echo "<strong>Error:</strong> Password must include uppercase, lowercase, a number, and a symbol.";
            if($_GET['msg'] == 'added') echo "Subadmin added successfully!";
            if($_GET['msg'] == 'exists') echo "<strong>Error:</strong> ID or Username already exists.";
        ?>
    </div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Password Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row["id"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($row["username"]); ?></td>
                        <td><code style="color: #888; font-size: 0.85rem;">** (Encrypted)</code></td>
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
                        No subadmins found. Click "+ Add Subadmin" to get started.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>