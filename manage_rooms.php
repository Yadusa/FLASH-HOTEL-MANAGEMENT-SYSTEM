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

// --- HANDLE DATE-RANGE BLOCKING ---
if (isset($_POST['block_date_range'])) {
    $room_name = $_POST['room_name'];
    $start_date = $_POST['block_start'];
    $end_date = $_POST['block_end'];
    
    // We insert this into the bookings table with a special status 'Blocked'
    $block_sql = "INSERT INTO bookings (customer_username, room_name, checkin, checkout, payment_status, total_price) 
                  VALUES ('SYSTEM_BLOCK', ?, ?, ?, 'Blocked', 0)";
    $stmt = $conn->prepare($block_sql);
    $stmt->bind_param("sss", $room_name, $start_date, $end_date);
    
    if($stmt->execute()) {
        $success_message = "Room $room_name successfully blocked from $start_date to $end_date.";
    }
}

// --- HANDLE ADD NEW ROOM ---
if (isset($_POST['add_room_trigger']) && $adminRole === 'superadmin') {
    $new_room_name = $_POST['new_room_name'];
    $new_total_slots = $_POST['new_total_slots'];
    
    $add_sql = "INSERT INTO rooms (room_name, total_slots, available_slots, room_status) VALUES (?, ?, ?, 'Available')";
    $stmt = $conn->prepare($add_sql);
    $stmt->bind_param("sii", $new_room_name, $new_total_slots, $new_total_slots);
    $stmt->execute();
    header("Location: manage_rooms.php?date=$selected_date&success=1");
    exit;
}

// --- HANDLE DELETE ROOM ---
if (isset($_POST['delete_room_trigger']) && $adminRole === 'superadmin') {
    $room_id = $_POST['room_id'];

    $check_bookings = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_name = (SELECT room_name FROM rooms WHERE id = ?) AND payment_status != 'Cancelled'");
    $check_bookings->bind_param("i", $room_id);
    $check_bookings->execute();
    $booking_count = $check_bookings->get_result()->fetch_assoc()['count'];

    if ($booking_count > 0) {
        $error_message = "Cannot delete: This room type has $booking_count active bookings.";
    } else {
        $delete_sql = "DELETE FROM rooms WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $room_id);
        if($stmt->execute()) {
            header("Location: manage_rooms.php?date=$selected_date&success=Deleted");
            exit;
        }
    }
}

// --- HANDLE SLOT RESTORATION (Updated to clear blocks for that date) ---
if (isset($_POST['restore_slot'])) {
    $room_id = $_POST['room_id'];
    
    $room_name_q = $conn->query("SELECT room_name FROM rooms WHERE id = $room_id");
    $room_data = $room_name_q->fetch_assoc();
    $r_name = $room_data['room_name'];

    // Deletes system blocks overlapping the selected date to "Restore" the slot
    $clear_blocks = $conn->prepare("DELETE FROM bookings WHERE room_name = ? AND customer_username = 'SYSTEM_BLOCK' AND (checkin <= ? AND checkout > ?)");
    $clear_blocks->bind_param("sss", $r_name, $selected_date, $selected_date);
    $clear_blocks->execute();

    header("Location: manage_rooms.php?date=$selected_date&restored=1");
    exit;
}

// --- HANDLE MANUAL STATUS UPDATES ---
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms | FLASH Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50; --accent: #b89241; --bg-light: #f4f6f9;
            --text-dark: #333; --white: #ffffff; --success: #28a745; --danger: #dc3545;
        }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: var(--bg-light); display: flex; }
        .sidebar { width: 260px; background: var(--primary); color: white; min-height: 100vh; position: fixed; display: flex; flex-direction: column; }
        .brand { padding: 25px; text-align: center; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand h2 { margin: 0; color: var(--accent); }
        .sidebar a { padding: 15px 25px; color: #b0b8c1; text-decoration: none; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: white; border-left: 4px solid var(--accent); }
        .main-content { margin-left: 260px; flex: 1; padding: 25px; }
        .admin-table-container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .add-room-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px dashed #ccc; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; background: #fdfdfd; }
        .table td { padding: 15px; border-bottom: 1px solid #eee; }
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .available { background: #e3fcef; color: #006644; }
        .occupied { background: #ffebe6; color: #bf2600; }
        .maintenance { background: #fff3cd; color: #856404; }
        .btn-restore { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><h2>FLASH HOTEL</h2><p><?php echo ucfirst($adminRole); ?></p></div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php" class="active"><i class="fas fa-bed"></i> Manage Rooms</a>
    <?php if ($adminRole === "superadmin"): ?>
        <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <?php endif; ?>
    <a href="logout.php" style="margin-top:auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="topbar"><h3>Room Inventory Management</h3></div>

    <div class="admin-table-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <form method="GET">
                <label>Check Date: </label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
            </form>
            <p>Status for: <strong><?php echo date('M d, Y', strtotime($selected_date)); ?></strong></p>
        </div>

        <?php if ($adminRole === "superadmin"): ?>
        <div class="add-room-box" style="border-color: var(--danger); background: #fff5f5;">
            <h4 style="margin-top:0; color: var(--danger);"><i class="fas fa-ban"></i> Block Room for Specific Dates</h4>
            <form method="POST" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                <div>
                    <label style="display:block; font-size:12px;">Room Type</label>
                    <select name="room_name" required>
                        <?php 
                        $room_list = $conn->query("SELECT room_name FROM rooms");
                        while($rn = $room_list->fetch_assoc()) {
                            echo "<option value='".$rn['room_name']."'>".$rn['room_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:12px;">From</label>
                    <input type="date" name="block_start" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label style="display:block; font-size:12px;">Until</label>
                    <input type="date" name="block_end" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" name="block_date_range" class="btn-restore" style="background: var(--danger); height: 40px;">Apply Block</button>
            </form>
        </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Availability (Date Specific)</th>
                    <th>Global Status</th>
                    <th>Manual Control</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    // FIX: Prepared 3 placeholders, so we must bind 3 variables
                    $count_query = "SELECT COUNT(*) as booked_count FROM bookings 
                                    WHERE room_name = ? 
                                    AND payment_status != 'Cancelled' 
                                    AND (checkin <= ? AND checkout > ?)";
                    $stmt_count = $conn->prepare($count_query);
                    $stmt_count->bind_param("sss", $row['room_name'], $selected_date, $selected_date);
                    $stmt_count->execute();
                    $booked = $stmt_count->get_result()->fetch_assoc()['booked_count'];
                    
                    $display_avail = $row['total_slots'] - $booked;
                    $is_global_maint = ($row['room_status'] == 'Maintenance');
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <span style="color: <?php echo ($display_avail > 0 && !$is_global_maint) ? 'var(--success)' : 'var(--danger)'; ?>; font-weight:bold;">
                            <?php echo $is_global_maint ? 0 : max(0, $display_avail); ?> / <?php echo $row['total_slots']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-pill <?php echo ($is_global_maint || $display_avail <= 0) ? 'maintenance' : 'available'; ?>">
                            <?php 
                                if($is_global_maint) echo "Blocked (Global)";
                                elseif($display_avail <= 0) echo "Full/Blocked (Date)";
                                else echo "Available";
                            ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_status_trigger" value="1">
                            <select name="new_status" onchange="this.form.submit()">
                                <option value="Available" <?php if($row['room_status'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Maintenance" <?php if($row['room_status'] == 'Maintenance') echo 'selected'; ?>>Block (Global)</option>
                            </select>
                        </form>
                    </td>
                    <td>
                      <form method="POST" style="display:inline;">
                         <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                         <button type="submit" name="restore_slot" class="btn-restore">Reset Date Inventory</button>
                      </form>

                    <?php if ($adminRole === 'superadmin'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this room type?');">
                      <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                      <input type="hidden" name="delete_room_trigger" value="1">
                      <button type="submit" class="btn-restore" style="background: var(--danger); margin-left: 5px;">
                         <i class="fas fa-trash"></i>
                      </button>
                    </form>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>