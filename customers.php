<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

/* Alert messages */
if (isset($_GET['updated'])) {
    echo "<script>alert('Customer updated successfully');</script>";
}
if (isset($_GET['deleted'])) {
    echo "<script>alert('Customer deleted successfully');</script>";
}

// 2. Fetch Customers - Removed 'status' column filter to fix SQL error
$sql = "SELECT id, username, cust_name, cust_email, contact_number, created_at 
        FROM customer
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Customers | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50; 
            --accent: #b89241; 
            --bg-light: #f4f6f9; 
            --text-dark: #333;
            --white: #ffffff;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            display: flex;
        }

        /* --- SIDEBAR STYLE --- */
        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
        }
        .brand {
            padding: 25px;
            background: rgba(0,0,0,0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .brand h2 { margin: 0; font-size: 24px; color: var(--accent); letter-spacing: 1px; }
        .brand .role { margin: 5px 0 0; font-size: 12px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            color: #b0b8c1;
            display: flex;
            align-items: center;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.05);
            color: white;
            border-left-color: var(--accent);
        }
        .sidebar .logout { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); color: #ffadad; }
        .sidebar .logout:hover { background: #3d2a2a; border-left-color: #dc3545; }

        /* --- MAIN CONTENT STYLE --- */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 25px;
        }

        .topbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
        }
        .topbar h3 { margin: 0; font-size: 24px; color: var(--text-dark); }
        .user-profile {
            display: flex; align-items: center; gap: 15px; 
            background: white; padding: 8px 15px; 
            border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* --- TABLE STYLES --- */
        .table-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .custom-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            color: #444;
        }
        .custom-table tr:hover { background-color: #fafafa; }

        /* --- ACTION LINKS (MATCHING MANAGE_BOOKING.PHP) --- */
        .action-link {
            text-decoration: none;
            margin-right: 15px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .edit-link { color: #f0ad4e; } /* Gold/Warning color */
        .edit-link:hover { color: #d58512; }
        
        .delete-link { color: #d9534f; } /* Red/Danger color */
        .delete-link:hover { color: #c9302c; }

    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH HOTEL</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php"><i class="fas fa-bed"></i> Manage Rooms</a>
    <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
    <a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a>
    <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3><i class="fas fa-address-book"></i> Registered Customers</h3>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <div class="table-box">
        <h4 style="margin: 0 0 20px 0; color: #555;">Client Database</h4>

        <table class="custom-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Contact</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['cust_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['cust_email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td style="color: #777; font-size: 0.9em;">
                        <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                    </td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="action-link edit-link" title="Edit Customer">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <a href="delete_customer.php?id=<?php echo $row['id']; ?>" 
                           class="action-link delete-link" 
                           title="Delete Customer" 
                           onclick="return confirm('WARNING: Are you sure you want to delete customer: <?php echo htmlspecialchars($row['username']); ?>?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php 
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding:40px; color:#999;'><i class='fas fa-users-slash' style='font-size:40px; margin-bottom:10px; display:block;'></i>No registered customers found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>