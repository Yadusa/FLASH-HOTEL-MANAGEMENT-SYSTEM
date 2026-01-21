<?php
session_start();
require "db.php"; // Database connection ($conn)

// 1. Security Check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];
$currentDate = date('Y-m-d');

// ===============================
// DATE FILTER
// ===============================
$startDate = $_GET['start_date'] ?? $currentDate;
$endDate   = $_GET['end_date']   ?? date('Y-m-d', strtotime($startDate . ' +1 day'));

// =========================================================
// 2. LIVE DATABASE QUERIES
// =========================================================

// A. CALCULATE ROOM STATISTICS
$roomSql = "
SELECT 
    r.room_name,
    r.total_slots,
    (r.total_slots - COUNT(b.id)) AS available_slots
FROM rooms r
LEFT JOIN bookings b 
    ON r.room_name = b.room_name
    AND b.checkin < '$endDate'
    AND b.checkout > '$startDate'
GROUP BY r.room_name
";

$roomResult = $conn->query($roomSql);
if (!$roomResult) {
    die("Room Query Failed: " . $conn->error);
}

$total_rooms = 0;
$available_rooms = 0;
$roomDetails = [];

while ($row = $roomResult->fetch_assoc()) {
    $total_rooms += (int)$row['total_slots'];
    $available_rooms += max(0, (int)$row['available_slots']);
    $roomDetails[] = $row;
}

$occupied_rooms = $total_rooms - $available_rooms;
$occupancy_rate = ($total_rooms > 0) ? round(($occupied_rooms / $total_rooms) * 100) : 0;

// B. GET TODAY'S ARRIVALS
$arrSql = "SELECT COUNT(*) as count FROM bookings WHERE checkin = '$currentDate'";
$arrResult = $conn->query($arrSql);
$today_arrivals = $arrResult ? $arrResult->fetch_assoc()['count'] : 0;

// C. GET TODAY'S DEPARTURES
$depSql = "SELECT COUNT(*) as count FROM bookings WHERE checkout = '$currentDate'";
$depResult = $conn->query($depSql);
$today_departures = $depResult ? $depResult->fetch_assoc()['count'] : 0;

// D. GET PENDING BOOKINGS
$pendingSql = "SELECT COUNT(*) as count FROM bookings WHERE payment_status = 'Pending'";
$pendingResult = $conn->query($pendingSql);
$pending_bookings = $pendingResult ? $pendingResult->fetch_assoc()['count'] : 0;

// E. FETCH TODAY'S ARRIVALS LIST
$listSql = "
SELECT b.id, c.cust_name, b.customer_username, b.room_name, b.payment_status 
FROM bookings b
LEFT JOIN customer c ON b.customer_username = c.username
WHERE b.checkin = '$currentDate'
ORDER BY b.id DESC
";
$arrivalsResult = $conn->query($listSql);
if (!$arrivalsResult) {
    die("Arrivals Query Failed: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #2c3e50;
            --accent: #b89241;
            --bg-light: #f4f6f9;
            --text-dark: #333;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg-light); display: flex; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: var(--primary); color: white; min-height: 100vh; display: flex; flex-direction: column; position: fixed; }
        .brand { padding: 25px; background: rgba(0,0,0,0.1); text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand h2 { margin: 0; font-size: 24px; color: var(--accent); letter-spacing: 1px; }
        .brand .role { margin: 5px 0 0; font-size: 12px; opacity: 0.7; text-transform: uppercase; }
        
        .sidebar a { padding: 15px 25px; text-decoration: none; color: #b0b8c1; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: white; border-left-color: var(--accent); }
        .sidebar .logout { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); color: #ffadad; }
        .sidebar .logout:hover { background: #3d2a2a; border-left-color: #dc3545; }

        /* MAIN CONTENT */
        .main-content { margin-left: 260px; flex: 1; padding: 25px; }

        /* Top Bar */
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .welcome-text h3 { margin: 0; font-size: 24px; color: var(--text-dark); }
        .welcome-text p { margin: 5px 0 0; color: #666; font-size: 14px; }
        
        .user-profile { display: flex; align-items: center; gap: 15px; background: white; padding: 8px 15px; border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .avatar-circle { width: 35px; height: 35px; background: var(--accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }

        /* Form */
        .filter-form { margin-bottom:20px; display:flex; gap:10px; }
        .btn-sm { padding: 6px 12px; font-size: 12px; background: var(--primary); color: white; border-radius: 4px; text-decoration: none; cursor: pointer; }

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; border-bottom: 4px solid transparent; transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card-info h4 { margin: 0 0 5px; color: #666; font-size: 14px; font-weight: normal; }
        .card-info h2 { margin: 0; font-size: 28px; color: var(--text-dark); }
        .card-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

        .card.blue { border-color: var(--info); } .card.blue .card-icon { background: #e0f7fa; color: var(--info); }
        .card.green { border-color: var(--success); } .card.green .card-icon { background: #d4edda; color: var(--success); }
        .card.orange { border-color: var(--warning); } .card.orange .card-icon { background: #fff3cd; color: var(--warning); }
        .card.red { border-color: var(--danger); } .card.red .card-icon { background: #f8d7da; color: var(--danger); }

        /* Dashboard Sections */
        .dashboard-row { display: flex; gap: 25px; flex-wrap: wrap; }
        .section-box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); flex: 1; min-width: 300px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }

        /* Tables */
        .table-clean { width: 100%; border-collapse: collapse; }
        .table-clean th { text-align: left; color: #888; font-size: 12px; padding: 10px 5px; font-weight: 600; }
        .table-clean td { padding: 12px 5px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .table-clean tr:last-child td { border-bottom: none; }
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .status-confirmed { background: #e3fcef; color: #006644; }
        .status-pending { background: #fff8c5; color: #856404; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH HOTEL</h2>
        <p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_rooms.php"><i class="fas fa-bed"></i> Manage Rooms</a>
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
        <div class="welcome-text">
            <h3>Good Evening, <?php echo htmlspecialchars($adminName); ?>.</h3>
            <p><?php echo date("l, d F Y"); ?> | System Status: <span style="color:var(--success)">● Online</span></p>
        </div>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div class="avatar-circle">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <!-- DATE FILTER FORM -->
    <form class="filter-form" method="GET">
        <input type="date" name="start_date" value="<?php echo $startDate; ?>" required>
        <input type="date" name="end_date" value="<?php echo $endDate; ?>" required>
        <button class="btn-sm">Apply</button>
    </form>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="card blue">
            <div class="card-info">
                <h4>Total Occupancy</h4>
                <h2><?php echo $occupancy_rate; ?>%</h2>
                <small><?php echo $occupied_rooms; ?> / <?php echo $total_rooms; ?> Rooms</small>
            </div>
            <div class="card-icon"><i class="fas fa-chart-pie"></i></div>
        </div>

        <div class="card green">
            <div class="card-info">
                <h4>Arriving Today</h4>
                <h2><?php echo $today_arrivals; ?></h2>
                <small>Check-ins due</small>
            </div>
            <div class="card-icon"><i class="fas fa-suitcase-rolling"></i></div>
        </div>

        <div class="card orange">
            <div class="card-info">
                <h4>Departing Today</h4>
                <h2><?php echo $today_departures; ?></h2>
                <small>Check-outs due</small>
            </div>
            <div class="card-icon"><i class="fas fa-door-open"></i></div>
        </div>

        <div class="card red">
            <div class="card-info">
                <h4>Pending Bookings</h4>
                <h2><?php echo $pending_bookings; ?></h2>
                <small>Need payment/action</small>
            </div>
            <div class="card-icon"><i class="fas fa-bell"></i></div>
        </div>
    </div>

    <!-- DASHBOARD ROW -->
    <div class="dashboard-row">
        <!-- Today's Arrivals Table -->
        <div class="section-box" style="flex: 2;">
            <div class="section-header">
                <h3><i class="fas fa-concierge-bell"></i> Today's Arrivals</h3>
                <a href="bookings.php" class="btn-sm">View All Bookings</a>
            </div>
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest Name</th>
                        <th>Room Assigned</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($arrivalsResult->num_rows > 0): ?>
                        <?php while($row = $arrivalsResult->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['cust_name'] ?? $row['customer_username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($row['payment_status'] == 'Paid') ? 'status-confirmed' : 'status-pending'; ?>">
                                        <?php echo htmlspecialchars($row['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="bookings.php?checkin_id=<?php echo $row['id']; ?>" style="color:var(--primary); font-size:14px;">
                                        <i class="fas fa-check-circle"></i> Check In
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#999; padding:20px;">
                                No arrivals scheduled for today (<?php echo $currentDate; ?>).
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Room Status -->
        <div class="section-box" style="flex: 1;">
            <div class="section-header">
                <h3><i class="fas fa-bed"></i> Room Status</h3>
                <a href="manage_rooms.php" class="btn-sm">Manage</a>
            </div>
            <div style="margin-bottom: 15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>Available</span>
                    <strong><?php echo $available_rooms; ?></strong>
                </div>
                <div style="height:8px; background:#eee; border-radius:4px; overflow:hidden;">
                    <div style="width: <?php echo ($total_rooms > 0) ? (100 - $occupancy_rate) : 0; ?>%; background: var(--success); height:100%;"></div>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>Occupied</span>
                    <strong><?php echo $occupied_rooms; ?></strong>
                </div>
                <div style="height:8px; background:#eee; border-radius:4px; overflow:hidden;">
                    <div style="width: <?php echo $occupancy_rate; ?>%; background: var(--info); height:100%;"></div>
                </div>
            </div>
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; text-align: center;">
                <h4 style="margin:0 0 5px; color: #333;">Total Capacity</h4>
                <p style="margin:0; font-size:16px; color:#555;"><?php echo $total_rooms; ?> Total Rooms</p>
            </div>
        </div>
    </div>

</div>
</body>
</html>
