<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT * FROM admins WHERE role='subadmin'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subadmins</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>FLASH Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Subadmin List</h2>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'weak_password'): ?>
        <p style="color: red; background: #fee; padding: 10px; border-radius: 5px;">
            Error: Password must include uppercase, lowercase, a number, and a symbol.
        </p>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <p style="color: green;">Subadmin added successfully!</p>
    <?php endif; ?>

    <a href="add_subadmin.php" class="btn">+ Add Subadmin</a>

    <table border="1" cellpadding="10" class="admin-table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["username"]; ?></td>
                <td><code style="color: #888;">**** (Hashed)</code></td>

                <td>
                    <a href="edit_subadmins.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="delete_subadmins.php?id=<?php echo $row['id']; ?>"
                    class="btn-delete"
                    onclick="return confirm('Are you sure you want to delete this subadmin?');">
                    Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>