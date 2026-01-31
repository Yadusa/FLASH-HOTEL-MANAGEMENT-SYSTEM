<?php
session_start();
require_once('db.php');

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Handle manual booking creation
if (isset($_POST['create_manual_booking'])) {
    $customer_username = trim($_POST['manual_customer_username']);
    $room_name = $_POST['manual_room_name'];
    $checkin = $_POST['manual_checkin'];
    $checkout = $_POST['manual_checkout'];
    $adults = (int)$_POST['manual_adults'];
    $children = (int)$_POST['manual_children'];
    $room_price = (float)$_POST['manual_room_price'];

    // Check if room is available for booking
    $room_check = $conn->prepare("SELECT room_status FROM rooms WHERE room_name = ?");
    if ($room_check === false) {
        $error_message = "Database error: " . $conn->error;
    } else {
        $room_check->bind_param("s", $room_name);
        $room_check->execute();
        $room_check_result = $room_check->get_result();
        $room_info = $room_check_result->fetch_assoc();

        if ($room_info['room_status'] == 'Unavailable for Booking') {
            $error_message = "This room is currently unavailable for booking!";
        } else {
            // Calculate total price
            $diff = strtotime($checkout) - strtotime($checkin);
            $nights = max(1, ceil($diff / (60*60*24)));
            $total_price = $nights * $room_price;

            // Insert booking
            $insert_sql = "INSERT INTO bookings (customer_username, room_name, room_price, checkin, checkout, adults, children, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssdssiii", $customer_username, $room_name, $room_price, $checkin, $checkout, $adults, $children, $total_price);
            $stmt->execute();

            // Update available slots
            $update_slots = "UPDATE rooms SET available_slots = available_slots - 1 WHERE room_name = ?";
            $update_stmt = $conn->prepare($update_slots);
            $update_stmt->bind_param("s", $room_name);
            $update_stmt->execute();

            // Set status if full
            $conn->query("UPDATE rooms SET room_status = 'Occupied' WHERE available_slots <= 0 AND room_status = 'Available'");

            $success_message = "Booking created successfully!";
        }
    }
}

// 2. Fetch all bookings
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings | FLASH Hotel Admin</title>
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

        /* --- SIDEBAR STYLE (MATCHING DASHBOARD) --- */
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

        /* --- TABLE STYLES --- */
        .table-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .booking-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        .booking-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            color: #444;
        }
        .booking-table tr:last-child td { border-bottom: none; }
        .booking-table tr:hover { background-color: #fafafa; }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Action Buttons */
        .action-link {
            text-decoration: none;
            margin-right: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }
        .edit-link { color: #f0ad4e; }
        .edit-link:hover { color: #d58512; }
        
        .delete-link { color: #d9534f; }
        .delete-link:hover { color: #c9302c; }

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

    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH HOTEL</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php"><i class="fas fa-bed"></i> Manage Rooms</a>
    <a href="bookings.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a>

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
        <h3><i class="fas fa-list-alt"></i> Booking Management</h3>
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

    <?php if (isset($_GET['undone'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Booking cancellation undone successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- All Bookings Section -->
    <div class="table-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 1.1rem; color: #555;">All Room Reservations</h4>
            <button onclick="toggleManualBookingForm()" class="btn">+ Manual Booking</button>
        </div>

        <!-- Manual Booking Form (Hidden by default) -->
        <div id="manualBookingForm" style="display: none; margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4>Create Manual Booking</h4>
            <form method="POST">
                <label>Customer Username: </label>
                <input type="text" name="manual_customer_username" required><br><br>
                <label>Room: </label>
                <select name="manual_room_name" required>
                    <?php
                    $room_result = $conn->query("SELECT room_name, room_status FROM rooms");
                    while($room = $room_result->fetch_assoc()) {
                        $disabled = ($room['room_status'] == 'Unavailable for Booking') ? 'disabled' : '';
                        $label = $room['room_name'];
                        if ($room['room_status'] == 'Unavailable for Booking') {
                            $label .= ' (UNAVAILABLE)';
                        }
                        echo "<option value='" . htmlspecialchars($room['room_name']) . "' $disabled>" . htmlspecialchars($label) . "</option>";
                    }
                    ?>
                </select><br><br>
                <label>Check-in Date: </label>
                <input type="date" name="manual_checkin" required><br><br>
                <label>Check-out Date: </label>
                <input type="date" name="manual_checkout" required><br><br>
                <label>Adults: </label>
                <input type="number" name="manual_adults" min="1" value="1" required>
                <label>Children: </label>
                <input type="number" name="manual_children" min="0" value="0" required><br><br>
                <label>Room Price (RM per night): </label>
                <input type="number" name="manual_room_price" min="0" step="0.01" required><br><br>
                <button type="submit" name="create_manual_booking" class="btn btn-success">Create Booking</button>
            </form>
        </div>

        <table class="booking-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Room Type</th>
                    <th>Dates (In - Out)</th>
                    <th>Guests</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Booked On</th>
                    <th>Actions</th> 
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()):
                        // Determine CSS class based on payment status
                        $payment_status = isset($row['payment_status']) ? $row['payment_status'] : 'Pending';
                        $statusRaw = strtolower($payment_status);
                        $statusClass = 'status-pending'; // default

                        if ($statusRaw == 'paid' || $statusRaw == 'confirmed') {
                            $statusClass = 'status-paid';
                        } elseif ($statusRaw == 'cancelled') {
                            $statusClass = 'status-cancelled';
                        }
                    ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['customer_username']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td>
                                <div style="font-size: 0.9em; color: #666;">
                                    <i class="fas fa-sign-in-alt"></i> <?php echo date('M d', strtotime($row['checkin'])); ?><br>
                                    <i class="fas fa-sign-out-alt"></i> <?php echo date('M d', strtotime($row['checkout'])); ?>
                                </div>
                            </td>
                            <td><?php echo $row['adults']; ?> <i class="fas fa-user"></i>, <?php echo $row['children']; ?> <i class="fas fa-child"></i></td>
                            <td style="font-weight: bold; color: #333;">RM<?php echo number_format($row['total_price'], 2); ?></td>
                            
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($payment_status); ?>
                                </span>
                            </td>
                            
                            <td style="font-size: 0.85em; color: #888;">
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>
                            
                            <td>
                                <a href="edit_booking.php?id=<?php echo $row['id']; ?>" class="action-link edit-link" title="Edit Booking">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($payment_status === 'Cancelled'): ?>
                                    <a href="undo_booking.php?id=<?php echo $row['id']; ?>" class="action-link" style="color: #28a745;" title="Undo Cancellation">
                                        <i class="fas fa-undo"></i> Undo
                                    </a>
                                <?php endif; ?>
                                <a href="delete_booking.php?id=<?php echo $row['id']; ?>" class="action-link delete-link"
                                   title="Delete Booking" onclick="return confirm('WARNING: Are you sure you want to delete Booking #<?php echo $row['id']; ?>?');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding: 40px; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                            No bookings found in the database.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleManualBookingForm() {
    var form = document.getElementById('manualBookingForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
</body>
</html>