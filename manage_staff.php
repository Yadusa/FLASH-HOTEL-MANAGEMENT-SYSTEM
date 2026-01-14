<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"] ?? "Admin";
$adminRole = $_SESSION["admin_role"];

$sql = "SELECT * FROM staffs ORDER BY staff_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff | FLASH Hotel</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="staff-style.css">
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2> FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_rooms.php"> Manage Rooms</a>

    <?php if ($adminRole === "superadmin") { ?>
        <a href="manage_subadmins.php"> Manage Subadmins</a>
        <a href="bookings.php"> Bookings</a>
        <a href="customers.php"> Customers</a>
        <a href="manage_staff.php" class="active"> Manage Staff</a>
        <a href="reports.php"> Reports</a>
    <?php } ?>

    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3>Staff Management</h3>
        <div style="display:flex;align-items:center;gap:15px;">
             <a href="add_staff.php" class="btn">+ Add New Staff</a>
             <div class="avatar-circle">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
             </div>
        </div>
    </div>

    <div class="table-box">
        <div class="table-header">
            <div>
                <h3 style="margin-bottom:5px;">Employee Directory</h3>
                <p style="font-size: 13px; color: #666;">Manage your hotel team members and their roles.</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Staff Info</th>
                    <th>Position</th>
                    <th>Contact Details</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="staff-name-container">
                            <span class="staff-id-tag">ID: <?= $row["staff_id"] ?></span>
                            <span class="staff-name"><?= $row["staff_name"] ?></span>
                        </div>
                    </td>
                    <td><?= $row["staff_position"] ?></td>
                    <td>
                        <div style="font-size: 13px;">
                            <span style="display:block; color:#333;"><?= $row["staff_email"] ?></span>
                            <span style="color:#888;"><?= $row["staff_phone"] ?></span>
                        </div>
                    </td>
                    <td><strong>$<?= number_format($row["staff_salary"], 2) ?></strong></td>
                    <td>
                        <span class="status-badge status-<?= strtolower($row["staff_status"]) ?>">
                            <?= ucfirst($row["staff_status"]) ?>
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <div class="action-group">
                            <a href="edit_staff.php?id=<?= $row['staff_id'] ?>" class="edit-link">Edit</a>
                            <a href="delete_staff.php?id=<?= $row['staff_id'] ?>" 
                               class="delete-link" 
                               onclick="return confirm('Are you sure you want to delete this staff member?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>