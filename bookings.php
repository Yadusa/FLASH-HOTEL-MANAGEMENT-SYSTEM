<?php
session_start();
require_once('db.php');

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Fetch all bookings from the database
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings | FLASH Hotel</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        /* Additional styling for the table */
        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        .booking-table th, .booking-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .booking-table th {
            background-color: #f8f9fa;
            color: #333;
        }
        .booking-table tr:hover {
            background-color: #f1f1f1;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            background: #e1f5fe;
            color: #01579b;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2> FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"> Dashboard</a>

    <?php if ($adminRole === "superadmin") { ?>
        <a href="bookings.php" class="active"> Bookings</a>
    <?php } ?>

    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3>Customer Bookings</h3>
        <div style="display:flex;align-items:center;gap:10px;">
            <span>Welcome, <?php echo $adminName; ?></span>
            <div style="width:36px;height:36px;border-radius:50%;background:#4c8bf5;color:white;display:flex;align-items:center;justify-content:center;font-weight:600;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <div class="table-box">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3>All Room Bookings</h3>
        </div>

        <table class="booking-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Room Type</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Total Price</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['id'] . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row['customer_username']) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row['room_name']) . "</td>";
                        echo "<td>" . $row['checkin'] . "</td>";
                        echo "<td>" . $row['checkout'] . "</td>";
                        echo "<td>" . $row['adults'] . "A, " . $row['children'] . "C</td>";
                        echo "<td>$" . number_format($row['total_price'], 2) . "</td>";
                        echo "<td>" . date('M d, Y H:i', strtotime($row['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>