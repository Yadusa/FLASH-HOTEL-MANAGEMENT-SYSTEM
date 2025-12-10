<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM staffs ORDER BY staff_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>FLASH Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_staff.php" class="active">Staffs</a>

</div>

<div class="main-content">
    <div class="topbar">
        <h3>Manage Staff</h3>
    </div>

    <a href="add_staff.php" class="btn">+ Add New Staff</a>

    <table border="1" class="table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Salary</th>
            <th>Join Date</th>
            <th>Position</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["staff_id"] ?></td>
            <td><?= $row["staff_name"] ?></td>
            <td><?= $row["staff_dob"] ?></td>
            <td><?= $row["staff_salary"] ?></td>
            <td><?= $row["staff_join_date"] ?></td>
            <td><?= $row["staff_position"] ?></td>
            <td><?= $row["staff_email"] ?></td>
            <td><?= $row["staff_phone"] ?></td>
            <td><?= $row["staff_status"] ?></td>

            <td>
                <a class="btn-edit" href="edit_staff.php?id=<?= $row['staff_id'] ?>">Edit</a>
                <a class="btn-delete" 
                   href="delete_staff.php?id=<?= $row['staff_id'] ?>"
                   onclick="return confirm('Delete this staff?');">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
