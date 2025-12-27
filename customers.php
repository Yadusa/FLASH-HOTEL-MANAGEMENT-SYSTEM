<?php
session_start();
require_once "db.php";

/* Only SuperAdmin allowed */
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

/* Alert messages */
if (isset($_GET['updated'])) {
    echo "<script>alert('Customer updated successfully');</script>";
}

if (isset($_GET['deleted'])) {
    echo "<script>alert('Customer deleted successfully');</script>";
}

/* ✅ FIX 1: Added 'id' to the SELECT statement */
$sql = "SELECT id, username, cust_name, cust_email, contact_number, created_at 
        FROM customer
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Customers</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH Hotel Admin</h2>
        <p class="role">SuperAdmin</p>
    </div>

    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_subadmins.php"> Manage Subadmins</a>
    <a href="customers.php" class="active"> Customers</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3>Registered Customers</h3>
    </div>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Registered On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['cust_name']); ?></td>
                    <td><?= htmlspecialchars($row['cust_email']); ?></td>
                    <td><?= htmlspecialchars($row['contact_number']); ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_customer.php?id=<?php echo $row['id']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Are you sure you want to delete this customer?');">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php 
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No customers found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>