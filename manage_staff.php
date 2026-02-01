<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"] ?? "Admin";
$adminRole = $_SESSION["admin_role"];

// 2. Fetch Staff Data
$sql = "SELECT * FROM staffs ORDER BY staff_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | The Obsidian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;    /* Dark Blue Sidebar */
            --accent: #b89241;     /* Gold Brand Color */
            --bg-light: #f4f6f9;   /* Light Gray Background */
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

        /* Top Bar */
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

        /* --- TABLE & PAGE SPECIFIC STYLES --- */
        .table-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .staff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .staff-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        .staff-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            color: #444;
        }
        .staff-table tr:hover { background-color: #fafafa; }
        
        /* Add Button */
        .btn-add {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .btn-add:hover { background: #34495e; }

        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active { background: #e3fcef; color: #006644; border: 1px solid #abf5d1; }
        .status-inactive { background: #ffebe6; color: #bf2600; border: 1px solid #ffbdad; }

        /* Action Links */
        .action-link {
            text-decoration: none;
            margin-right: 15px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }
        .edit-link { color: #4c8bf5; }
        .edit-link:hover { color: #2a62c0; }
        .delete-link { color: #d9534f; }
        .delete-link:hover { color: #c9302c; }

        /* Text Utilities */
        .text-muted { color: #888; font-size: 0.85em; }
        .font-bold { font-weight: 600; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>The Obsidian</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php"><i class="fas fa-bed"></i> Manage Rooms</a>

    <?php if ($adminRole === "superadmin") { ?>
        <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
        <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_staff.php" class="active"><i class="fas fa-id-badge"></i> All Staff</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <?php } ?>

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3><i class="fas fa-users"></i> Staff Management</h3>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <div class="table-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h4 style="margin: 0; font-size: 1.1rem; color: #2c3e50;">Employee Directory</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 0.9rem;">Manage your hotel team members and their roles.</p>
            </div>
            <a href="add_staff.php" class="btn-add">
                <i class="fas fa-plus-circle"></i> Add New Staff
            </a>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Staff Info</th>
                    <th>Position</th>
                    <th>Contact Details</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        // Determine status class
                        $statusClass = (strtolower($row["staff_status"]) === 'active') ? 'status-active' : 'status-inactive';
                    ?>
                    <tr>
                        <td>
                            <div class="font-bold"><?php echo htmlspecialchars($row["staff_name"]); ?></div>
                            <div class="text-muted">ID: <?php echo htmlspecialchars($row["staff_id"]); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($row["staff_position"]); ?></td>
                        <td>
                            <div><i class="fas fa-envelope text-muted"></i> <?php echo htmlspecialchars($row["staff_email"]); ?></div>
                            <div class="text-muted"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($row["staff_phone"]); ?></div>
                        </td>
                        <td class="font-bold">$<?php echo number_format($row["staff_salary"], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst($row["staff_status"]); ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_staff.php?id=<?php echo $row['staff_id']; ?>" class="action-link edit-link">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="delete_staff.php?id=<?php echo $row['staff_id']; ?>" 
                               class="action-link delete-link"
                               onclick="return confirm('WARNING: Are you sure you want to delete this staff member?');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999; padding: 40px;">
                            <i class="fas fa-user-slash" style="font-size: 30px; margin-bottom: 10px; display: block;"></i>
                            No staff members found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>