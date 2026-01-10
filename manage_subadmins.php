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

// Fetch only subadmins
$result = $conn->query("SELECT * FROM admins WHERE role='subadmin' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subadmins | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="admin.css"> <style>
        :root { --obsidian-gold: #b89241; --dark-sidebar: #2c3e50; }
        
        /* Professional Status Badges */
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .active-status { background: #e3fcef; color: #006644; border: 1px solid #abf5d1; }
        .pending-status { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

        /* Temporary Password Box Styling */
        .otp-display {
            background: #fdf6e3;
            border-left: 5px solid #b89241;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
        <a href="manage_staff.php"> Staff</a>
        <a href="bookings.php"> Bookings</a>
        <a href="reports.php"> Reports</a>
    <?php } ?>
    
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3>Subadmin Administration</h3>
        <p>Current User: <strong><?php echo htmlspecialchars($adminName); ?></strong></p>
    </div>

    <div style="margin: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h4 style="margin: 0; font-size: 1.2rem; color: #1f2933;">Administrative Accounts</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 0.9rem;">Manage permissions and access for sub-level administrators.</p>
            </div>
            <a href="add_subadmin.php" class="btn-add">+ Create New Account</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div style="padding: 15px; border-radius: 5px; margin-bottom: 20px; <?php echo ($_GET['msg'] == 'added') ? 'background: #e3fcef; color: #006644;' : 'background: #ffebe6; color: #bf2600;'; ?>">
                <?php 
                    if($_GET['msg'] == 'added') echo "✔ New subadmin account created successfully.";
                    if($_GET['msg'] == 'exists') echo "✖ Error: Username or ID already exists in the system.";
                    if($_GET['msg'] == 'weak_password') echo "✖ Error: Provided password does not meet security standards.";
                    if($_GET['msg'] == 'deleted') echo "✔ Account removed successfully.";
                ?>
            </div>

            <?php if($_GET['msg'] == 'added' && isset($_SESSION['temp_pw_display'])): ?>
                <div class="otp-display">
                    <strong style="color: #b89241;">SECURITY ACTION REQUIRED:</strong><br>
                    <p style="margin: 10px 0;">Please share this generated temporary password with the subadmin. They will be required to update it immediately upon entry.</p>
                    <span class="otp-code"><?php echo $_SESSION['temp_pw_display']; ?></span>
                    <p style="margin-top: 10px; font-size: 0.8rem; color: #888;">* This code is only visible once. Do not refresh the page until the code is saved.</p>
                </div>
                <?php unset($_SESSION['temp_pw_display']); ?>
            <?php endif; ?>
        <?php endif; ?>

        <table class="table" style="width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 15px;">Admin ID</th>
                    <th>Username</th>
                    <th>Account Status</th>
                    <th style="text-align: center;">Control Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">#<?php echo htmlspecialchars($row["id"]); ?></td>
                            <td><strong><?php echo htmlspecialchars($row["username"]); ?></strong></td>
                            <td>
                                <?php if(isset($row['is_first_login']) && $row['is_first_login'] == 1): ?>
                                    <span class="status-pill pending-status">Pending First Login</span>
                                <?php else: ?>
                                    <span class="status-pill active-status">Active Account</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="edit_subadmins.php?id=<?php echo $row['id']; ?>" class="btn-edit" style="color: #4c8bf5; text-decoration: none; font-weight: 600; margin-right: 15px;">Edit</a>
                                <a href="delete_subadmins.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   style="color: #d9534f; text-decoration: none; font-weight: 600;"
                                   onclick="return confirm('WARNING: Are you sure you want to permanently remove this subadmin access?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999; padding: 50px;">
                            No active subadmin accounts found. Click '+ Create New Account' to add one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>