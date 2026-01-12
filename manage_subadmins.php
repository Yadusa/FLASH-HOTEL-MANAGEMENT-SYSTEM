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

// MODIFIED: Fetch subadmins AND receptionists
$result = $conn->query("SELECT * FROM admins WHERE role IN ('subadmin', 'receptionist') ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="admin.css"> 
    <style>
        :root { --obsidian-gold: #b89241; --dark-sidebar: #2c3e50; }
        
        /* Professional Status Badges */
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border: 1px solid; }
        
        /* NEW: Comprehensive Status Styles */
        .status-active { background: #e3fcef; color: #006644; border-color: #abf5d1; }
        .status-pending { background: #fff3cd; color: #856404; border-color: #ffeeba; }
        .status-suspended { background: #fff0b3; color: #85660e; border-color: #ffe58f; }
        .status-terminated { background: #ffebe6; color: #bf2600; border-color: #ffbdad; }
        .status-inactive { background: #f4f5f7; color: #44546f; border-color: #dfe1e6; }

        .role-badge { font-size: 0.7rem; background: #f1f3f5; padding: 2px 8px; border-radius: 4px; color: #495057; border: 1px solid #dee2e6; }

        .otp-display {
            background: #fdf6e3;
            border-left: 5px solid #b89241;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .otp-code {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
            color: #d9534f;
            background: #fff;
            padding: 4px 10px;
            border: 1px dashed #d9534f;
            border-radius: 3px;
        }

        .admin-table td { padding: 15px; vertical-align: middle; }
        .btn-add { background: #1f2933; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { background: #374151; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>
    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_rooms.php"> Manage Rooms</a>
    
    <?php if ($adminRole === "superadmin") { ?>
        <a href="manage_subadmins.php" class="active"> Manage Subadmins</a>
        <a href="bookings.php"> Bookings</a>
        <a href="customers.php"> Customers</a>
        <a href="manage_staff.php"> Manage Staff</a>
        <a href="reports.php"> Reports</a>
    <?php } ?>
    
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3>Staff Administration</h3>
        <p>Current User: <strong><?php echo htmlspecialchars($adminName); ?></strong></p>
    </div>

    <div style="margin: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h4 style="margin: 0; font-size: 1.2rem; color: #1f2933;">Administrative & Reception Accounts</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 0.9rem;">Manage permissions and access for subadmins and receptionists.</p>
            </div>
            <a href="add_subadmin.php" class="btn-add">+ Create New Account</a>
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

        <table class="table" style="width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 15px;">Admin ID</th>
                    <th>Username & Role</th>
                    <th>Account Status</th>
                    <th style="text-align: center;">Control Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        // Determine the CSS class for the status badge
                        $status = isset($row['status']) ? $row['status'] : 'active';
                        $statusLabel = $status;
                        $statusClass = 'status-' . $status;

                        // Override label if it's the very first login
                        if(isset($row['is_first_login']) && $row['is_first_login'] == 1 && $status == 'active') {
                            $statusLabel = "Pending First Login";
                            $statusClass = "status-pending";
                        }
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">#<?php echo htmlspecialchars($row["id"]); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row["username"]); ?></strong><br>
                            </td>
                            <td>
                                <span class="status-pill <?php echo $statusClass; ?>">
                                    <?php echo ucwords($statusLabel); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="edit_subadmins.php?id=<?php echo $row['id']; ?>" class="btn-edit" style="color: #4c8bf5; text-decoration: none; font-weight: 600; margin-right: 15px;">Edit</a>
                                <a href="delete_subadmins.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   style="color: #d9534f; text-decoration: none; font-weight: 600;"
                                   onclick="return confirm('WARNING: Are you sure you want to permanently remove this access?');">
                                    Delete
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

</body>
</html>