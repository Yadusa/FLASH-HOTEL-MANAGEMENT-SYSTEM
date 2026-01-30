<?php
session_start();
require_once "db.php";

// 1. Security Check
if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Handle form submission to update adults/children
if (isset($_POST['update_people'])) {
    $booking_id = (int)$_POST['booking_id'];
    $adults = (int)$_POST['adults'];
    $children = (int)$_POST['children'];

    // Validate input
    if ($adults < 1 || $adults > 10 || $children < 0 || $children > 10) {
        $error_message = "Invalid number of guests. Adults: 1-10, Children: 0-10.";
    } else {
        // Update the booking
        $update_sql = "UPDATE bookings SET adults = ?, children = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iii", $adults, $children, $booking_id);
        $stmt->execute();

        $success_message = "Guest count updated successfully!";
    }
}

// Get customer username from URL
$customer_username = isset($_GET['username']) ? trim($_GET['username']) : '';

// Fetch customer details
$customer_sql = "SELECT * FROM customer WHERE username = ?";
$customer_stmt = $conn->prepare($customer_sql);
$customer_stmt->bind_param("s", $customer_username);
$customer_stmt->execute();
$customer_result = $customer_stmt->get_result();

if ($customer_result->num_rows === 0) {
    header("Location: customers.php");
    exit;
}

$customer = $customer_result->fetch_assoc();

// Fetch all bookings for this customer
$bookings_sql = "SELECT * FROM bookings WHERE customer_username = ? ORDER BY created_at DESC";
$bookings_stmt = $conn->prepare($bookings_sql);
$bookings_stmt->bind_param("s", $customer_username);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guest Count | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #2c3e50;    /* Dark Blue Sidebar */
            --accent: #b89241;     /* Gold Brand Color */
            --bg-light: #f4f6f9;   /* Light Gray Background */
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

        /* --- FORM STYLES --- */
        .form-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

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

        /* --- TABLE STYLES --- */
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
    <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
    <a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a>
    <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3><i class="fas fa-user-edit"></i> Edit Guest Count for <?php echo htmlspecialchars($customer['cust_name']); ?></h3>
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

    <div class="form-box">
        <h4 style="margin: 0 0 20px 0; color: #555;">Select Booking to Edit</h4>

        <?php if ($bookings_result->num_rows > 0): ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Type</th>
                        <th>Dates</th>
                        <th>Current Guests</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = $bookings_result->fetch_assoc()):
                        $statusRaw = isset($booking['payment_status']) ? strtolower($booking['payment_status']) : 'pending';
                        $statusClass = 'status-pending';
                        if ($statusRaw == 'paid' || $statusRaw == 'confirmed') {
                            $statusClass = 'status-paid';
                        } elseif ($statusRaw == 'cancelled') {
                            $statusClass = 'status-cancelled';
                        }
                    ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                            <td>
                                <div style="font-size: 0.9em; color: #666;">
                                    <?php echo date('M d', strtotime($booking['checkin'])); ?> - <?php echo date('M d', strtotime($booking['checkout'])); ?>
                                </div>
                            </td>
                            <td><?php echo $booking['adults']; ?> <i class="fas fa-user"></i>, <?php echo $booking['children']; ?> <i class="fas fa-child"></i></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($booking['payment_status'] ?? 'Pending'); ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="editBooking(<?php echo $booking['id']; ?>, <?php echo $booking['adults']; ?>, <?php echo $booking['children']; ?>)" class="btn btn-success" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit People
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px;">
                <i class="fas fa-calendar-times" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                No bookings found for this customer.
            </p>
        <?php endif; ?>
    </div>

    <!-- Edit Form (Hidden by default) -->
    <div id="editForm" class="form-box" style="display: none;">
        <h4 style="margin: 0 0 20px 0; color: #555;">Update Guest Count</h4>
        <form method="POST">
            <input type="hidden" name="booking_id" id="booking_id">
            <div class="form-group">
                <label for="adults">Number of Adults:</label>
                <input type="number" name="adults" id="adults" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label for="children">Number of Children:</label>
                <input type="number" name="children" id="children" min="0" max="10" required>
            </div>
            <button type="submit" name="update_people" class="btn btn-success">Update Guest Count</button>
            <button type="button" onclick="cancelEdit()" class="btn" style="background: #6c757d; margin-left: 10px;">Cancel</button>
        </form>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="customers.php" class="btn" style="background: #6c757d;">Back to Customers</a>
    </div>
</div>

<script>
function editBooking(bookingId, currentAdults, currentChildren) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('adults').value = currentAdults;
    document.getElementById('children').value = currentChildren;
    document.getElementById('editForm').style.display = 'block';
    document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
}

function cancelEdit() {
    document.getElementById('editForm').style.display = 'none';
}
</script>
</body>
</html>
