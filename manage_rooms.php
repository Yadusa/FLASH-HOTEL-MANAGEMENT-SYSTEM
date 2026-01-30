<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"] ?? 'staff';
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Handle room blocking/unblocking
if (isset($_POST['toggle_room_booking'])) {
    $room_id = (int)$_POST['room_id'];
    $new_status = $_POST['new_status']; // 'Available' or 'Unavailable for Booking'
    
    $update_sql = "UPDATE rooms SET room_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $room_id);
    $stmt->execute();
    $stmt->close();
    
    $success_message = "Room status updated successfully!";
}

// Handle blocked date removal
if (isset($_POST['delete_blocked_date'])) {
    $blocked_id = (int)$_POST['blocked_id'];
    $delete_sql = "DELETE FROM room_blocked_dates WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $blocked_id);
    $stmt->execute();
    $stmt->close();
    $success_message = "Blocked date removed successfully!";
}

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

// Fetch all rooms for the room status section
$rooms_sql = "SELECT id, room_name, room_status, available_slots, total_slots FROM rooms ORDER BY room_name";
$rooms_result = $conn->query($rooms_sql);

// Fetch all blocked dates
$blocked_sql = "SELECT id, room_name, blocked_date FROM room_blocked_dates ORDER BY blocked_date ASC";
$blocked_result = $conn->query($blocked_sql);
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
            --primary: #2c3e50;    /* Dark Blue Sidebar */
            --accent: #b89241;     /* Gold Brand Color */
            --bg-light: #f4f6f9;   /* Light Gray Background */
            --text-dark: #333;
            --white: #ffffff;
            --success: #28a745;    /* Green */
            --warning: #ffc107;    /* Yellow/Orange */
            --danger: #dc3545;     /* Red */
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            display: flex;
        }

        /* --- SIDEBAR STYLE --- */
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
        .topbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
        }
        .topbar h3 { margin: 0; font-size: 24px; color: var(--text-dark); }
        .user-profile {
            display: flex; align-items: center; gap: 15px; 
            background: white; padding: 8px 15px; 
            border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* Alerts */
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .admin-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .table-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #f8f9fa;
            color: #555;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            color: #444;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fafafa; }

        /* Status Badges */
        .status-badge, .status-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-available, .available { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-unavailable { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-unavailableforbooking { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-maintenance, .maintenance { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-occupied, .occupied { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-restore { background: #6c757d; color: white; }
        .btn-restore:hover { background: #5a6268; }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .form-section h3 {
            margin-top: 0;
            color: #555;
            font-size: 1.1rem;
        }

        .form-section label {
            display: inline-block;
            margin-right: 10px;
            font-weight: 600;
        }

        .form-section input, .form-section select {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

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

    <?php if ($adminRole === "superadmin") { ?>
        <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <?php } ?>

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

    <div class="admin-card">
        <h2>Room Inventory Management</h2>

        <!-- Add New Room Form -->
        <div class="form-section">
            <h3><i class="fas fa-plus-circle"></i> Add New Room</h3>
            <form method="POST">
                <label>Room Name: </label>
                <input type="text" name="room_name" required>
                <label>Total Slots: </label>
                <input type="number" name="total_slots" min="1" required>
                <button type="submit" name="add_room" class="btn btn-success">Add Room</button>
            </form>
        </div>

        <!-- Block Dates Form -->
        <div class="form-section">
            <h3><i class="fas fa-calendar-times"></i> Block Dates for Rooms</h3>
            <form method="POST">
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
                <button type="submit" name="block_date" class="btn btn-danger">Block Date</button>
            </form>
        </div>

        <form method="GET" style="margin-bottom: 20px;">
            <label><strong>Check Date: </strong></label>
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
                <?php 
                // Reset result pointer for the main room table
                $result->data_seek(0);
                while($row = $result->fetch_assoc()): 
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
                        <span class="status-pill <?php echo strtolower(str_replace(' ', '', $row['room_status'])); ?>">
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
                            <button type="submit" name="restore_slot" class="btn btn-restore" <?php echo ($row['available_slots'] >= $row['total_slots']) ? 'disabled' : ''; ?>>
                                + Restore Slot
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Room Availability Status Section -->
    <?php if ($adminRole === "superadmin") { ?>
    <div class="table-box">
        <h4 style="margin: 0 0 15px; font-size: 1.1rem; color: #555;">
            <i class="fas fa-door-open"></i> Room Booking Availability
        </h4>
        <table>
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Available Slots</th>
                    <th>Current Status</th>
                    <th>Booking Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($room = $rooms_result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($room['room_name']); ?></strong></td>
                    <td><?php echo $room['available_slots']; ?> / <?php echo $room['total_slots']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $room['room_status'])); ?>">
                            <?php echo htmlspecialchars($room['room_status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($room['room_status'] == 'Unavailable for Booking'): ?>
                            <span style="color: #dc3545; font-weight: bold;">
                                <i class="fas fa-ban"></i> UNAVAILABLE FOR BOOKING
                            </span>
                        <?php else: ?>
                            <span style="color: #28a745; font-weight: bold;">
                                <i class="fas fa-check-circle"></i> Open for Booking
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            <?php if ($room['room_status'] == 'Unavailable for Booking'): ?>
                                <input type="hidden" name="new_status" value="Available">
                                <button type="submit" name="toggle_room_booking" class="btn btn-success" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="fas fa-unlock"></i> Enable Booking
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="new_status" value="Unavailable for Booking">
                                <button type="submit" name="toggle_room_booking" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" 
                                        onclick="return confirm('Block this room from customer bookings?');">
                                    <i class="fas fa-ban"></i> Block Booking
                                </button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <!-- Blocked Dates Section -->
    <?php if ($adminRole === "superadmin") { ?>
    <div class="table-box">
        <h4 style="margin: 0 0 15px; font-size: 1.1rem; color: #555;">
            <i class="fas fa-calendar-times"></i> Blocked Dates Management
        </h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Name</th>
                    <th>Blocked Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($blocked_result && $blocked_result->num_rows > 0): ?>
                    <?php while($blocked = $blocked_result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $blocked['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($blocked['room_name']); ?></strong></td>
                        <td><?php echo date('M d, Y', strtotime($blocked['blocked_date'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="blocked_id" value="<?php echo $blocked['id']; ?>">
                                <button type="submit" name="delete_blocked_date" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;"
                                        onclick="return confirm('Are you sure you want to unblock this date?');">
                                    <i class="fas fa-trash"></i> Remove Block
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px; color: #999;">
                            <i class="fas fa-check-circle" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                            No blocked dates found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

</div>

</body>
</html>