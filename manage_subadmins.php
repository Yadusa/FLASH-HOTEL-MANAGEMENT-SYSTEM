<?php
session_start();
require "db.php";

// Check authorization
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Fetch subadmins AND receptionists
$result = $conn->query("SELECT * FROM admins WHERE role IN ('subadmin', 'receptionist') ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subadmin | The Obsidian</title>
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

        /* --- SIDEBAR STYLE (MATCHING DASHBOARD) --- */
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
        .topbar { margin-bottom: 30px; }
        .topbar h3 { margin: 0; font-size: 24px; color: var(--text-dark); }
        .topbar p { margin: 5px 0 0; color: #666; font-size: 14px; }

        /* --- PAGE SPECIFIC STYLES --- */
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border: 1px solid; }
        .status-active { background: #e3fcef; color: #006644; border-color: #abf5d1; }
        .status-pending { background: #fff3cd; color: #856404; border-color: #ffeeba; }
        .status-suspended { background: #fff0b3; color: #85660e; border-color: #ffe58f; }
        .status-terminated { background: #ffebe6; color: #bf2600; border-color: #ffbdad; }
        .status-inactive { background: #f4f5f7; color: #44546f; border-color: #dfe1e6; }

        .role-badge { font-size: 0.7rem; background: #f1f3f5; padding: 2px 8px; border-radius: 4px; color: #495057; border: 1px solid #dee2e6; }

        /* OTP Display Box */
        .otp-display {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .otp-code {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            background: #fff;
            padding: 10px 20px;
            border: 2px dashed #333;
            border-radius: 3px;
            display: inline-block;
            margin-top: 10px;
            letter-spacing: 2px;
        }

        .btn-add { background: var(--primary); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { background: #34495e; }
        
        /* Table Styles */
        .admin-table-container { background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; }
        .table th { background: #f8f9fa; border-bottom: 2px solid #eee; font-weight: 600; color: #555; }
        .table td { border-bottom: 1px solid #eee; vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }
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
        <a href="manage_subadmins.php" class="active"><i class="fas fa-user-shield"></i> Subadmins</a>
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <?php } ?>

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3><i class="fas fa-users-cog"></i> Staff Administration</h3>
        <p>Current User: <strong><?php echo htmlspecialchars($adminName); ?></strong></p>
    </div>

    <div>
        
        <?php if (isset($_SESSION['temp_pw_display'])): ?>
            <div class="otp-display">
                <h3 style="margin-top: 0; color: #856404;"> Account Created Successfully!</h3>
                <p style="color: #555;">Please copy the temporary password below and provide it to the staff member.<br>
                <strong>Warning: This password will disappear when you refresh this page.</strong></p>
                
                <div class="otp-code">
                    <?php 
                        echo htmlspecialchars($_SESSION['temp_pw_display']); 
                        unset($_SESSION['temp_pw_display']);
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h4 style="margin: 0; font-size: 1.2rem; color: #2c3e50;">Administrative & Reception Accounts</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 0.9rem;">Manage permissions and access levels.</p>
            </div>
            <a href="add_subadmin.php" class="btn-add"><i class="fas fa-plus"></i> Create New Account</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div style="padding: 15px; border-radius: 5px; margin-bottom: 20px; <?php echo ($_GET['msg'] == 'added' || $_GET['msg'] == 'updated') ? 'background: #e3fcef; color: #006644;' : 'background: #ffebe6; color: #bf2600;'; ?>">
                <?php 
                    if($_GET['msg'] == 'added') echo "New staff account created successfully.";
                    if($_GET['msg'] == 'updated') echo "Account details and status updated successfully.";
                    if($_GET['msg'] == 'exists') echo "Error: Username or ID already exists in the system.";
                    if($_GET['msg'] == 'deleted') echo "Account removed successfully.";
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Admin ID</th>
                        <th>Username & Role</th>
                        <th>Account Status</th>
                        <th style="text-align: center;">Control Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            // Determine Status Badge Logic
                            $status = isset($row['status']) ? $row['status'] : 'active';
                            $statusLabel = $status;
                            $statusClass = 'status-' . $status;

                            if(isset($row['is_first_login']) && $row['is_first_login'] == 1 && $status == 'active') {
                                $statusLabel = "Pending First Login";
                                $statusClass = "status-pending";
                            }
                        ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row["id"]); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row["username"]); ?></strong><br>
                                    <span class="role-badge"><?php echo ucfirst($row['role']); ?></span>
                                </td>
                                <td>
                                    <span class="status-pill <?php echo $statusClass; ?>">
                                        <?php echo ucwords($statusLabel); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="edit_subadmins.php?id=<?php echo $row['id']; ?>" style="color: #4c8bf5; text-decoration: none; font-weight: 600; margin-right: 15px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete_subadmins.php?id=<?php echo $row['id']; ?>" 
                                       style="color: #d9534f; text-decoration: none; font-weight: 600;"
                                       onclick="return confirm('WARNING: Are you sure you want to permanently remove this access?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999; padding: 50px;">
                                No staff accounts found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>