<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch session data for the sidebar display
$adminName = $_SESSION["admin_name"] ?? 'Admin';
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
        /* --- DESIGN SYSTEM (MATCHING BOOKING.PHP) --- */
        :root {
            --primary: #2c3e50;    /* Dark Blue/Obsidian Sidebar */
            --accent: #b89241;     /* Gold Brand Color */
            --bg-light: #f4f6f9;   /* Light Gray Background */
            --text-dark: #333;
            --white: #ffffff;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-light); 
            margin: 0; 
            display: flex; 
        }

        /* --- SIDEBAR STYLE --- */
        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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

        /* --- MAIN CONTENT AREA --- */
        .main-content { 
            margin-left: 260px; 
            padding: 40px; 
            width: calc(100% - 260px); 
            box-sizing: border-box;
        }

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

        .admin-card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
        }

        /* --- TABLE STYLING --- */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8f9fa; color: #555; padding: 15px; text-align: left; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; }
        
        .status-pill { padding: 5px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .available { background: #d4edda; color: #155724; }
        .occupied { background: #f8d7da; color: #721c24; }
        .maintenance { background: #fff3cd; color: #856404; }

        .btn-restore { 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 12px;
            transition: 0.3s;
        }
        .btn-restore:hover:not(:disabled) { background: var(--accent); }
        .btn-restore:disabled { opacity: 0.5; cursor: not-allowed; }

        select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
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

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    
    <div class="topbar">
        <h3><i class="fas fa-bed"></i> Room Inventory Management</h3>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

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
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <span style="font-weight: bold; color: var(--accent);">
                            <?php echo $row['available_slots']; ?>
                        </span> / <?php echo $row['total_slots']; ?>
                    </td>
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
                                <i class="fas fa-plus-circle"></i> Restore Slot
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