<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminRole = $_SESSION["admin_role"] ?? 'staff';
$selected_date = $_GET['date'] ?? date('Y-m-d');

// --- HANDLE SLOT RESTORATION ---
if (isset($_POST['restore_slot'])) {
    $room_id = $_POST['room_id'];
    $restore_sql = "UPDATE rooms SET available_slots = available_slots + 1 WHERE id = ? AND available_slots < total_slots";
    $stmt = $conn->prepare($restore_sql);
    $stmt->bind_param("i", $room_id);
    
    if($stmt->execute()) {
        $auto_avail = "UPDATE rooms SET room_status = 'Available' WHERE id = ? AND available_slots > 0 AND room_status = 'Occupied'";
        $stmt2 = $conn->prepare($auto_avail);
        $stmt2->bind_param("i", $room_id);
        $stmt2->execute();
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

// --- HANDLE ADD NEW ROOM ---
if (isset($_POST['add_room'])) {
    $room_name = trim($_POST['room_name']);
    $total_slots = (int)$_POST['total_slots'];
    if (!empty($room_name) && $total_slots > 0) {
        $insert_sql = "INSERT INTO rooms (room_name, total_slots, available_slots, room_status) VALUES (?, ?, ?, 'Available')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sii", $room_name, $total_slots, $total_slots);
        $stmt->execute();
    }
}

// --- HANDLE BLOCK DATE ---
if (isset($_POST['block_date'])) {
    $room_name = $_POST['block_room_name'];
    $block_date = $_POST['block_date'];
    $insert_block = "INSERT INTO room_blocked_dates (room_name, blocked_date) VALUES (?, ?) ON DUPLICATE KEY UPDATE blocked_date = blocked_date";
    $stmt = $conn->prepare($insert_block);
    $stmt->bind_param("ss", $room_name, $block_date);
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
    <title>Manage Rooms | FLASH Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Updated Styles for a cleaner look like your first image */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 240px; background: #2c3e50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin-bottom: 5px; border-radius: 4px; }
        .sidebar a.active { background: #34495e; border-left: 4px solid #3498db; }
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 320px); }
        .admin-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #34495e; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .status-pill { padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .available { background: #d4edda; color: #155724; }
        .occupied { background: #f8d7da; color: #721c24; }
        .btn-restore { background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .btn-restore:hover { background: #5a6268; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>FLASH HOTEL</h2>
    <p>Logged as: <?php echo ucfirst($adminRole); ?></p>
    <hr>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_rooms.php" class="active">Manage Rooms</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="admin-card">
        <h2>Room Inventory Management</h2>

        <!-- Add New Room Form -->
        <h3>Add New Room</h3>
        <form method="POST" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <label>Room Name: </label>
            <input type="text" name="room_name" required>
            <label>Total Slots: </label>
            <input type="number" name="total_slots" min="1" required>
            <button type="submit" name="add_room" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Add Room</button>
        </form>

        <!-- Block Dates Form -->
        <h3>Block Dates for Rooms</h3>
        <form method="POST" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <label>Room: </label>
            <select name="block_room_name" required>
                <?php
                $room_result = $conn->query("SELECT room_name FROM rooms");
                while($room = $room_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($room['room_name']) . "'>" . htmlspecialchars($room['room_name']) . "</option>";
                }
                ?>
            </select>
            <label>Block Date: </label>
            <input type="date" name="block_date" required>
            <button type="submit" name="block_date" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Block Date</button>
        </form>

        <form method="GET" style="margin-bottom: 20px;">
            <label>Check Date: </label>
            <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
        </form>

        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Inventory (Avail/Total)</th>
                    <th>Status</th>
                    <th>Manual Control</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    // Calculate bookings safely
                    $count_query = "SELECT COUNT(*) as booked_count FROM bookings WHERE room_id = ? AND ? BETWEEN start_date AND end_date";
                    $stmt_count = $conn->prepare($count_query);
                    
                    if ($stmt_count) {
                        $stmt_count->bind_param("is", $row['id'], $selected_date);
                        $stmt_count->execute();
                        $count_res = $stmt_count->get_result()->fetch_assoc();
                        $booked = $count_res['booked_count'];
                    } else {
                        $booked = 0; // Fallback if query fails
                    }
                ?>
                <tr>
                    <td><strong><?php echo $row['room_name']; ?></strong></td>
                    <td><?php echo $row['available_slots']; ?> / <?php echo $row['total_slots']; ?></td>
                    <td>
                        <span class="status-pill <?php echo strtolower($row['room_status']); ?>">
                            <?php echo $row['room_status']; ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_status_trigger" value="1">
                            <select name="new_status" onchange="this.form.submit()">
                                <option value="Available" <?php if($row['room_status'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Occupied" <?php if($row['room_status'] == 'Occupied') echo 'selected'; ?>>Full</option>
                                <option value="Maintenance" <?php if($row['room_status'] == 'Maintenance') echo 'selected'; ?>>Block</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="restore_slot" class="btn-restore" <?php echo ($row['available_slots'] >= $row['total_slots']) ? 'disabled' : ''; ?>>
                                + Restore Slot
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