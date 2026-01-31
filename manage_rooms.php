<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"] ?? 'Admin';
$adminRole = $_SESSION["admin_role"] ?? 'staff';
$selected_date = $_GET['date'] ?? date('Y-m-d');

// --- NEW: HANDLE ADD NEW ROOM ---
if (isset($_POST['add_room_trigger']) && $adminRole === 'superadmin') {
    $new_room_name = $_POST['new_room_name'];
    $new_total_slots = !empty($_POST['new_total_slots']) ? (int)$_POST['new_total_slots'] : 5;
    
    $add_sql = "INSERT INTO rooms (room_name, total_slots, room_status) VALUES (?, ?, 'Available')";
    $stmt = $conn->prepare($add_sql);
    $stmt->bind_param("si", $new_room_name, $new_total_slots);
    $stmt->execute();
    header("Location: manage_rooms.php?date=$selected_date");
    exit;
}

// --- NEW: HANDLE DELETE ROOM ---
if (isset($_POST['delete_room_trigger']) && $adminRole === 'superadmin') {
    $room_id = $_POST['room_id'];
    // Delete the room (foreign key constraints in DB should handle related bookings if set to CASCADE, 
    // otherwise, manual cleanup of SYSTEM_BLOCKS for this room name might be needed)
    $del_sql = "DELETE FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($del_sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    header("Location: manage_rooms.php?date=$selected_date");
    exit;
}

// --- HANDLE DATE-RANGE BLOCKING ---
if (isset($_POST['block_date_range'])) {
    $room_name = $_POST['room_name'];
    $start_date = $_POST['block_start'];
    $end_date = $_POST['block_end'];
    
    // Logic fix: Ensure checkout is at least one day after checkin for the block to span correctly
    $block_sql = "INSERT INTO bookings (customer_username, room_name, checkin, checkout, payment_status, total_price) 
                  VALUES ('SYSTEM_BLOCK', ?, ?, ?, 'Blocked', 0)";
    $stmt = $conn->prepare($block_sql);
    $stmt->bind_param("sss", $room_name, $start_date, $end_date);
    
    if($stmt->execute()) {
        header("Location: manage_rooms.php?date=$selected_date&success=Blocked");
        exit;
    }
}

// --- HANDLE SLOT RESTORATION ---
if (isset($_POST['restore_slot'])) {
    $room_id = $_POST['room_id'];
    $room_name_q = $conn->prepare("SELECT room_name FROM rooms WHERE id = ?");
    $room_name_q->bind_param("i", $room_id);
    $room_name_q->execute();
    $r_name = $room_name_q->get_result()->fetch_assoc()['room_name'];

    // Updated DELETE logic to clear any block that covers the selected date
    $clear_blocks = $conn->prepare("DELETE FROM bookings WHERE room_name = ? AND customer_username = 'SYSTEM_BLOCK' AND checkin <= ? AND checkout >= ?");
    $clear_blocks->bind_param("sss", $r_name, $selected_date, $selected_date);
    $clear_blocks->execute();

    header("Location: manage_rooms.php?date=$selected_date&restored=1");
    exit;
}

// --- HANDLE MANUAL STATUS UPDATES (Global) ---
if (isset($_POST['update_status_trigger'])) {
    $room_id = $_POST['room_id'];
    $new_status = $_POST['new_status'];
    $status_sql = "UPDATE rooms SET room_status = ? WHERE id = ?";
    $stmt = $conn->prepare($status_sql);
    $stmt->bind_param("si", $new_status, $room_id);
    $stmt->execute();
}

$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms | FLASH Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2c3e50; --accent: #b89241; --bg-light: #f4f6f9; --success: #28a745; --danger: #dc3545; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: var(--bg-light); display: flex; }
        .sidebar { width: 260px; background: var(--primary); color: white; min-height: 100vh; position: fixed; display: flex; flex-direction: column; }
        .sidebar a { padding: 15px 25px; color: #b0b8c1; text-decoration: none; display: flex; align-items: center; }
        .sidebar a.active { background: rgba(255,255,255,0.05); color: white; border-left: 4px solid var(--accent); }
        .main-content { margin-left: 260px; flex: 1; padding: 25px; }
        .admin-table-container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .available { background: #e3fcef; color: #006644; }
        .blocked { background: #ffebe6; color: #bf2600; }
        .btn-restore { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;}
        .btn-delete { background: var(--danger); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .add-section { background: #f0f4f8; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #d1d9e0; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand" style="padding:25px; text-align:center;"><h2>FLASH HOTEL</h2><p><?php echo ucfirst($adminRole); ?></p></div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php" class="active"><i class="fas fa-bed"></i> Manage Rooms</a>
    <?php if ($adminRole === "superadmin"): ?>
        <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <?php endif; ?>
    <a href="logout.php" style="margin-top:auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="admin-table-container">
        
        <?php if ($adminRole === "superadmin"): ?>
        <div class="add-section">
            <h4 style="margin-top:0;">Add New Room Type</h4>
            <form method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="add_room_trigger" value="1">
                <input type="text" name="new_room_name" placeholder="Room Name" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                <input type="number" name="new_total_slots" value="5" min="1" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 80px;">
                <button type="submit" class="btn-restore" style="background: var(--success);">Add Room</button>
            </form>
        </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <form method="GET">
                <label>Check Date: </label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
            </form>
            <h3>Inventory for: <?php echo date('M d, Y', strtotime($selected_date)); ?></h3>
        </div>

        <?php if ($adminRole === "superadmin"): ?>
        <div style="background: #fff5f5; padding: 20px; border: 1px dashed var(--danger); margin: 20px 0; border-radius: 8px;">
            <h4 style="color: var(--danger); margin-top:0;"><i class="fas fa-ban"></i> Block Date Range</h4>
            <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="room_name" required>
                    <?php 
                    $room_list = $conn->query("SELECT room_name FROM rooms");
                    while($rn = $room_list->fetch_assoc()) echo "<option value='".$rn['room_name']."'>".$rn['room_name']."</option>";
                    ?>
                </select>
                <input type="date" name="block_start" required min="<?php echo date('Y-m-d'); ?>">
                <input type="date" name="block_end" required min="<?php echo date('Y-m-d'); ?>">
                <button type="submit" name="block_date_range" class="btn-restore" style="background: var(--danger);">Apply Block</button>
            </form>
        </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Available Slots</th>
                    <th>Status Context</th>
                    <th>Global Setting</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    // 1. Count real customer bookings
                    $stmt_c = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE room_name = ? AND customer_username != 'SYSTEM_BLOCK' AND payment_status != 'Cancelled' AND (checkin <= ? AND checkout > ?)");
                    $stmt_c->bind_param("sss", $row['room_name'], $selected_date, $selected_date);
                    $stmt_c->execute();
                    $booked_count = $stmt_c->get_result()->fetch_assoc()['c'];

                    // 2. FIXED QUERY: Check if the selected date falls anywhere WITHIN the blocked range
                    $stmt_b = $conn->prepare("SELECT COUNT(*) as b FROM bookings WHERE room_name = ? AND customer_username = 'SYSTEM_BLOCK' AND checkin <= ? AND checkout >= ?");
                    $stmt_b->bind_param("sss", $row['room_name'], $selected_date, $selected_date);
                    $stmt_b->execute();
                    $is_date_blocked = $stmt_b->get_result()->fetch_assoc()['b'] > 0;

                    $is_global_maint = ($row['room_status'] == 'Maintenance');
                    
                    if ($is_global_maint || $is_date_blocked) {
                        $current_avail = 0;
                    } else {
                        $current_avail = $row['total_slots'] - $booked_count;
                    }
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <span style="font-weight:bold; color: <?php echo ($current_avail > 0) ? 'var(--success)' : 'var(--danger)'; ?>">
                            <?php echo max(0, $current_avail); ?> / <?php echo $row['total_slots']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($is_global_maint): ?>
                            <span class="status-pill blocked">GLOBAL MAINTENANCE</span>
                        <?php elseif($is_date_blocked): ?>
                            <span class="status-pill blocked">DATE BLOCKED</span>
                        <?php elseif($current_avail <= 0): ?>
                            <span class="status-pill blocked">FULLY BOOKED</span>
                        <?php else: ?>
                            <span class="status-pill available">AVAILABLE</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_status_trigger" value="1">
                            <select name="new_status" onchange="this.form.submit()">
                                <option value="Available" <?php if(!$is_global_maint) echo 'selected'; ?>>Operational</option>
                                <option value="Maintenance" <?php if($is_global_maint) echo 'selected'; ?>>Under Maintenance</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <div style="display: flex;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="restore_slot" class="btn-restore" title="Remove blocks for this specific date">Reset Date</button>
                            </form>

                            <?php if ($adminRole === "superadmin"): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this room type?');">
                                <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="delete_room_trigger" value="1">
                                <button type="submit" class="btn-delete">Delete Room</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>