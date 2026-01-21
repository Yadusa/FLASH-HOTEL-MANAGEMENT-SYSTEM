<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// 2. Get Admin Role for Sidebar Logic
$adminRole = $_SESSION["admin_role"] ?? 'staff';

// 3. Date filter for checking booked rooms
$selected_date = $_GET['date'] ?? date('Y-m-d'); // Default today if no date selected

// --- HANDLE SLOT RESTORATION ---
if (isset($_POST['restore_slot'])) {
    $room_id = $_POST['room_id'];
    $restore_sql = "UPDATE rooms SET available_slots = available_slots + 1 
                    WHERE id = ? AND available_slots < total_slots";
    $stmt = $conn->prepare($restore_sql);
    $stmt->bind_param("i", $room_id);
    
    if($stmt->execute()) {
        // If restoring a slot makes the room available again, update status automatically
        $auto_avail = "UPDATE rooms SET room_status = 'Available' 
                       WHERE id = ? AND available_slots > 0 AND room_status = 'Occupied'";
        $stmt2 = $conn->prepare($auto_avail);
        $stmt2->bind_param("i", $room_id);
        $stmt2->execute();
    }
}

// --- HANDLE MANUAL STATUS UPDATES (BLOCKING/UNBLOCKING) ---
if (isset($_POST['update_status_trigger'])) {
    $room_id = $_POST['room_id'];
    $new_status = $_POST['new_status'];
    
    $status_sql = "UPDATE rooms SET room_status = ? WHERE id = ?";
    $stmt = $conn->prepare($status_sql);
    $stmt->bind_param("si", $new_status, $room_id);
    $stmt->execute();
}

// Fetch all rooms
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles same as before, omitted for brevity */
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        .available { background: #e3fcef; color: #006644; }
        .occupied { background: #ffebe6; color: #bf2600; }
        .maintenance { background: #fff3cd; color: #856404; }
        .btn-status { padding: 8px; border-radius: 4px; border: 1px solid #ddd; background: white; cursor: pointer; }
        .btn-restore { background-color: #2ecc71; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-weight: 600; margin-left: 10px; }
        .btn-restore:disabled { background-color: #ccc; cursor: not-allowed; }
        .room-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">
        <h2>FLASH HOTEL</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php" class="active"><i class="fas fa-bed"></i> Manage Rooms</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="topbar"><h3><i class="fas fa-door-open"></i> Room Inventory Management</h3></div>

    <div class="admin-card" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0; margin-bottom: 20px; color: #666;">Active Inventory & Room Control</h4>

        <!-- Date Picker -->
        <form method="GET" style="margin-bottom: 15px;">
            <label>Select Date: </label>
            <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
        </form>

        <table class="room-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th>Room Type</th>
                    <th>Inventory (Avail/Total)</th>
                    <th>Available on Selected Date</th>
                    <th>Current Status</th>
                    <th>Manual Control</th>
                    <th>Inventory Recovery</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()):
                    // Calculate bookings for selected date
                    $stmt = $conn->prepare("SELECT COUNT(*) as booked_count FROM bookings WHERE room_id = ? AND ? BETWEEN start_date AND end_date");
                    $stmt->bind_param("is", $row['id'], $selected_date);
                    $stmt->execute();
                    $book_result = $stmt->get_result()->fetch_assoc();
                    $booked_count = $book_result['booked_count'] ?? 0;
                    $real_available = max(0, $row['available_slots'] - $booked_count);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <span style="font-weight: 600; color: var(--accent); font-size: 1.1rem;">
                            <?php echo $row['available_slots']; ?>
                        </span> 
                        <span style="color: #999;">/ <?php echo $row['total_slots']; ?></span>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: #007bff;">
                            <?php echo $real_available; ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-pill <?php echo strtolower($row['room_status'] ?? 'available'); ?>">
                            <?php echo $row['room_status'] ?? 'Available'; ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_status_trigger" value="1">
                            <select name="new_status" class="btn-status" onchange="this.form.submit()">
                                <option value="Available" <?php if($row['room_status'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Occupied" <?php if($row['room_status'] == 'Occupied') echo 'selected'; ?>>Occupied (Full)</option>
                                <option value="Maintenance" <?php if($row['room_status'] == 'Maintenance') echo 'selected'; ?>>Maintenance (Block)</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Restore 1 room slot?');">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="restore_slot" class="btn-restore" 
                                <?php echo ($row['available_slots'] >= $row['total_slots']) ? 'disabled' : ''; ?>>
                                <i class="fas fa-plus"></i> Restore Slot
                            </button>
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
