<?php
session_start();
require_once "db.php";

// 1. Security Check
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
    $new_total_slots = !empty($_POST['new_total_slots']) ? (int)$_POST['new_total_slots'] : 5;
    
    $add_sql = "INSERT INTO rooms (room_name, total_slots, room_status) VALUES (?, ?, 'Available')";
    $stmt = $conn->prepare($add_sql);
    $stmt->bind_param("si", $new_room_name, $new_total_slots);
    $stmt->execute();
    header("Location: manage_rooms.php?date=$selected_date");
    exit;
}

// --- HANDLE DELETE ROOM ---
if (isset($_POST['delete_room_trigger']) && $adminRole === 'superadmin') {
    $room_id = $_POST['room_id'];
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

    $clear_blocks = $conn->prepare("DELETE FROM bookings WHERE room_name = ? AND customer_username = 'SYSTEM_BLOCK' AND checkin <= ? AND checkout >= ?");
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
    <title>Manage Rooms | The Obsidian Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50; 
            --accent: #b89241; 
            --bg-light: #f4f6f9; 
            --text-dark: #333;
            --white: #ffffff;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
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

        /* --- CONTENT BOXES --- */
        .content-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .add-section { background: #f8f9fa; border: 1px solid #e9ecef; }

        /* --- TABLE STYLES --- */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .custom-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            color: #444;
        }

        /* --- BUTTONS & PILLS --- */
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .available { background: #e3fcef; color: #006644; }
        .blocked { background: #ffebe6; color: #bf2600; }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn:hover { opacity: 0.9; }

        input, select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>The Obsidian</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php" class="active"><i class="fas fa-bed"></i> Manage Rooms</a>
    <?php if ($adminRole === "superadmin"): ?>
        <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <?php endif; ?>

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

    <?php if ($adminRole === "superadmin"): ?>
    <div class="content-box add-section">
        <h4 style="margin:0 0 15px 0;"><i class="fas fa-plus-circle"></i> Add New Room Category</h4>
        <form method="POST" style="display: flex; gap: 15px; align-items: center;">
            <input type="hidden" name="add_room_trigger" value="1">
            <input type="text" name="new_room_name" placeholder="Room Name (e.g. Deluxe Suite)" required style="flex: 1;">
            <input type="number" name="new_total_slots" value="5" min="1" style="width: 100px;" title="Total Slots">
            <button type="submit" class="btn btn-success">Create Room</button>
        </form>
    </div>

    <div class="content-box" style="border-left: 5px solid var(--danger);">
        <h4 style="margin:0 0 15px 0; color: var(--danger);"><i class="fas fa-calendar-minus"></i> Emergency Date Blocking</h4>
        <form method="POST" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <select name="room_name" required style="min-width: 200px;">
                <option value="">Select Room Type...</option>
                <?php 
                $room_list = $conn->query("SELECT room_name FROM rooms");
                while($rn = $room_list->fetch_assoc()) echo "<option value='".$rn['room_name']."'>".$rn['room_name']."</option>";
                ?>
            </select>
            <input type="date" name="block_start" required min="<?php echo date('Y-m-d'); ?>">
            <input type="date" name="block_end" required min="<?php echo date('Y-m-d'); ?>">
            <button type="submit" name="block_date_range" class="btn btn-danger">Block Range</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <form method="GET">
                <label style="font-weight: 600; color: #666;">View Inventory For: </label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()" style="border: 1px solid var(--accent);">
            </form>
            <h4 style="margin:0; color: var(--primary);">Live Status: <?php echo date('D, M d, Y', strtotime($selected_date)); ?></h4>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Available Slots</th>
                    <th>Status</th>
                    <th>Global Operations</th>
                    <th>Management</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    // Count real bookings
                    $stmt_c = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE room_name = ? AND customer_username != 'SYSTEM_BLOCK' AND payment_status != 'Cancelled' AND (checkin <= ? AND checkout > ?)");
                    $stmt_c->bind_param("sss", $row['room_name'], $selected_date, $selected_date);
                    $stmt_c->execute();
                    $booked_count = $stmt_c->get_result()->fetch_assoc()['c'];

                    // Check blocks
                    $stmt_b = $conn->prepare("SELECT COUNT(*) as b FROM bookings WHERE room_name = ? AND customer_username = 'SYSTEM_BLOCK' AND checkin <= ? AND checkout >= ?");
                    $stmt_b->bind_param("sss", $row['room_name'], $selected_date, $selected_date);
                    $stmt_b->execute();
                    $is_date_blocked = $stmt_b->get_result()->fetch_assoc()['b'] > 0;

                    $is_global_maint = ($row['room_status'] == 'Maintenance');
                    $current_avail = ($is_global_maint || $is_date_blocked) ? 0 : ($row['total_slots'] - $booked_count);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_name']); ?></strong></td>
                    <td>
                        <b style="font-size: 1.1rem; color: <?php echo ($current_avail > 0) ? 'var(--success)' : 'var(--danger)'; ?>">
                            <?php echo max(0, $current_avail); ?> / <?php echo $row['total_slots']; ?>
                        </b>
                    </td>
                    <td>
                        <?php if($is_global_maint): ?>
                            <span class="status-pill blocked">Maintenance</span>
                        <?php elseif($is_date_blocked): ?>
                            <span class="status-pill blocked">Date Blocked</span>
                        <?php elseif($current_avail <= 0): ?>
                            <span class="status-pill blocked">Sold Out</span>
                        <?php else: ?>
                            <span class="status-pill available">Active</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_status_trigger" value="1">
                            <select name="new_status" onchange="this.form.submit()" style="font-size: 12px; padding: 4px;">
                                <option value="Available" <?php if(!$is_global_maint) echo 'selected'; ?>>Operational</option>
                                <option value="Maintenance" <?php if($is_global_maint) echo 'selected'; ?>>Maintenance</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <form method="POST">
                                <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="restore_slot" class="btn btn-primary" style="padding: 5px 10px;" title="Unlock this date">Reset Date</button>
                            </form>

                            <?php if ($adminRole === "superadmin"): ?>
                            <form method="POST" onsubmit="return confirm('Delete this entire room category?');">
                                <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="delete_room_trigger" value="1">
                                <button type="submit" class="btn btn-danger" style="padding: 5px 10px;"><i class="fas fa-trash"></i></button>
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