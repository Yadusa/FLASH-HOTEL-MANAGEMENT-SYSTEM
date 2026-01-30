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

// --- HANDLE DELETE ROOM (Superadmin Only) ---
if (isset($_POST['delete_room_trigger']) && $adminRole === 'superadmin') {
    $room_id = $_POST['room_id'];

    // Check if there are any active bookings for this room first
    $check_bookings = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_name = (SELECT room_name FROM rooms WHERE id = ?) AND payment_status != 'Cancelled'");
    $check_bookings->bind_param("i", $room_id);
    $check_bookings->execute();
    $booking_count = $check_bookings->get_result()->fetch_assoc()['count'];

    if ($booking_count > 0) {
        $error_message = "Cannot delete: This room type has $booking_count active bookings. Cancel them first.";
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
        .btn-add { background: var(--success); color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .btn-restore { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        input[type="text"], input[type="number"], input[type="date"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><h2>FLASH HOTEL</h2><p class="role"><?php echo ucfirst($adminRole); ?></p></div>
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
            <form method="GET" style="display: flex; align-items: center; gap: 10px;">
                <label>Check Date: </label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
            </form>
            <p>Status for: <strong><?php echo date('M d, Y', strtotime($selected_date)); ?></strong></p>
        </div>

        <?php if ($adminRole === "superadmin"): ?>
        <div class="add-room-box">
            <h4 style="margin:0 0 10px 0;">Add New Room Type</h4>
            <form method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="add_room_trigger" value="1">
                <input type="text" name="new_room_name" placeholder="Room Name" required>
                <input type="number" name="new_total_slots" placeholder="Total Slots" required>
                <button type="submit" class="btn-add">Add Room</button>
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
                    // Calculate bookings for the SPECIFIC date
                    $count_query = "SELECT COUNT(*) as booked_count FROM bookings WHERE room_name = ? AND ? BETWEEN checkin AND checkout AND payment_status != 'Cancelled'";
                    $stmt_count = $conn->prepare($count_query);
                    $stmt_count->bind_param("ss", $row['room_name'], $selected_date);
                    $stmt_count->execute();
                    $booked = $stmt_count->get_result()->fetch_assoc()['booked_count'];
                    
                    $display_avail = $row['total_slots'] - $booked;
                    $is_date_blocked = ($display_avail <= 0 || $row['room_status'] == 'Maintenance');
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <span style="color: <?php echo $display_avail > 0 ? 'var(--success)' : 'var(--danger)'; ?>; font-weight:bold;">
                            <?php echo max(0, $display_avail); ?> / <?php echo $row['total_slots']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-pill <?php echo $is_date_blocked ? 'maintenance' : strtolower($row['room_status']); ?>">
                            <?php echo $is_date_blocked ? ($row['room_status'] == 'Maintenance' ? 'Blocked' : 'Full') : $row['room_status']; ?>
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
                        </form>

                        <?php if ($adminRole === 'superadmin'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this room type? This cannot be undone.');">
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