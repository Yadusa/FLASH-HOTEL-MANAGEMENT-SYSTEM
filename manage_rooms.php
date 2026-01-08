<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Handle Status Updates
if (isset($_POST['update_status'])) {
    $room_id = $_POST['room_id'];
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE rooms SET room_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $room_id);
    $stmt->execute();
}

// Fetch all rooms from database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        :root { --obsidian-gold: #b89241; --dark-sidebar: #2c3e50; }
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .available { background: #e3fcef; color: #006644; }
        .occupied { background: #ffebe6; color: #bf2600; }
        .maintenance { background: #fff3cd; color: #856404; }
        .btn-status { padding: 5px 10px; cursor: pointer; border-radius: 4px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><h2> FLASH Hotel Admin</h2></div>
    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_rooms.php" class="active"> Manage Rooms</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar"><h3>Room Inventory Management</h3></div>

    <div class="admin-card" style="margin: 20px; background: #fff; padding: 25px; border-radius: 8px;">
        <h4 style="margin-bottom: 20px;">Active Inventory & Room Control</h4>
        <table class="room-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th>Room Type</th>
                    <th>Total Slots</th>
                    <th>Available Slots</th>
                    <th>Status</th>
                    <th>Action (Block/Unblock)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td><strong><?php echo $row['room_name']; ?></strong></td>
                    <td><?php echo $row['total_slots']; ?></td>
                    <td><?php echo $row['available_slots']; ?></td>
                    <td>
                        <span class="status-pill <?php echo strtolower($row['room_status'] ?? 'available'); ?>">
                            <?php echo $row['room_status'] ?? 'Available'; ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <select name="status" class="btn-status" onchange="this.form.submit()">
                                <option value="Available" <?php if($row['room_status'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Occupied" <?php if($row['room_status'] == 'Occupied') echo 'selected'; ?>>Occupied (Full)</option>
                                <option value="Maintenance" <?php if($row['room_status'] == 'Maintenance') echo 'selected'; ?>>Maintenance (Block)</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>