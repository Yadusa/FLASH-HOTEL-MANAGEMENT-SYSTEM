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

// --- HANDLE DELETE ROOM (Superadmin Only) ---
if (isset($_POST['delete_room_trigger']) && $adminRole === 'superadmin') {
    $room_id = $_POST['room_id'];

    // Check if there are any active bookings for this room first
    $check_bookings = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_name = (SELECT room_name FROM rooms WHERE id = ?)");
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
            $success_message = "Room deleted successfully!";
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

// --- HANDLE ADD NEW ROOM ---
if (isset($_POST['add_room'])) {
    $room_name = trim($_POST['room_name']);
    $total_slots = (int)$_POST['total_slots'];
    if (!empty($room_name) && $total_slots > 0) {
        // Check if room already exists
        $check_sql = "SELECT id FROM rooms WHERE room_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $room_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Room name already exists!";
        } else {
            $insert_sql = "INSERT INTO rooms (room_name, total_slots, available_slots, room_status) VALUES (?, ?, ?, 'Available')";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sii", $room_name, $total_slots, $total_slots);
            if ($stmt->execute()) {
                $success_message = "Room added successfully!";
            }
        }
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
    $success_message = "Date blocked successfully!";
}

// Fetch all rooms
$sql = "SELECT * FROM rooms ORDER BY room_name";
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
        .brand .role { margin: 5px 0 0; font-size: 12px; opacity: 0.7; text-transform: uppercase; }
        .sidebar a { padding: 15px 25px; color: #b0b8c1; text-decoration: none; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: white; border-left-color: var(--accent); }
        .sidebar .logout { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); color: #ffadad; }
        .sidebar .logout:hover { background: #3d2a2a; border-left-color: #dc3545; }
        
        .main-content { margin-left: 260px; flex: 1; padding: 25px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .topbar h3 { margin: 0; font-size: 24px; color: var(--text-dark); }
        .user-profile { display: flex; align-items: center; gap: 15px; background: white; padding: 8px 15px; border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .alert { padding: 12px 20px; margin-bottom: 20px; border-radius: 5px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .admin-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .form-section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .form-section h3 { margin-top: 0; color: #555; font-size: 1.1rem; }
        .form-section label { display: inline-block; margin-right: 10px; font-weight: 600; }
        .form-section input, .form-section select { padding: 8px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; }
        
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; background: #f8f9fa; color: #555; font-weight: 600; }
        .table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .table tr:hover { background-color: #fafafa; }
        
        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .available { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .occupied { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .maintenance { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        
        .btn { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
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
    <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <?php if ($adminRole === "superadmin"): ?>
        <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <?php endif; ?>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    
    <div class="topbar">
        <h3><i class="fas fa-bed"></i> Room Management</h3>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <h2>Room Inventory Management</h2>

        <!-- Add New Room Form -->
        <?php if ($adminRole === "superadmin"): ?>
        <div class="form-section">
            <h3><i class="fas fa-plus-circle"></i> Add New Room</h3>
            <form method="POST">
                <label>Room Name: </label>
                <input type="text" name="room_name" placeholder="e.g., Presidential Suite" required>
                <label>Total Slots: </label>
                <input type="number" name="total_slots" min="1" max="50" placeholder="5" required>
                <button type="submit" name="add_room" class="btn btn-success">Add Room</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Block Dates Form -->
        <div class="form-section">
            <h3><i class="fas fa-calendar-times"></i> Block Dates for Rooms</h3>
            <form method="POST">
                <label>Room: </label>
                <select name="block_room_name" required>
                    <?php
                    $room_result = $conn->query("SELECT room_name FROM rooms ORDER BY room_name");
                    while($room = $room_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($room['room_name']) . "'>" . htmlspecialchars($room['room_name']) . "</option>";
                    }
                    ?>
                </select>
                <label>Block Date: </label>
                <input type="date" name="block_date" required>
                <button type="submit" name="block_date" class="btn btn-danger">Block Date</button>
            </form>
        </div>

        <!-- Date Filter -->
        <form method="GET" style="margin-bottom: 20px;">
            <label><strong>Check Availability for Date: </strong></label>
            <input type="date" name="date" value="<?php echo $selected_date; ?>">
            <button type="submit" class="btn btn-sm">Apply Filter</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Availability (for <?php echo date('M d, Y', strtotime($selected_date)); ?>)</th>
                    <th>Global Status</th>
                    <th>Manual Control</th>
                    <?php if ($adminRole === "superadmin"): ?>
                    <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result->data_seek(0); // Reset pointer
                while($row = $result->fetch_assoc()): 
                    // Calculate bookings for the SPECIFIC date (removed payment_status check)
                    $count_query = "SELECT COUNT(*) as booked_count FROM bookings WHERE room_name = ? AND ? BETWEEN checkin AND checkout";
                    $stmt_count = $conn->prepare($count_query);
                    
                    if ($stmt_count) {
                        $stmt_count->bind_param("ss", $row['room_name'], $selected_date);
                        $stmt_count->execute();
                        $result_count = $stmt_count->get_result();
                        $booked = $result_count ? $result_count->fetch_assoc()['booked_count'] : 0;
                    } else {
                        $booked = 0;
                    }
                    
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
                    <?php if ($adminRole === "superadmin"): ?>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this room type? This cannot be undone.');">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="delete_room_trigger" value="1">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>