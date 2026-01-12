<?php
session_start();
require_once('db.php');

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Initialize variables
$startDate = "";
$endDate = "";
$totalBookings = 0;
$totalRevenue = 0.00;
$results = [];

// --- NEW CODE: TXT DOWNLOAD LOGIC ---
if (isset($_POST['download_txt'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $fullEndDate = $endDate . " 23:59:59";

    $query = "SELECT * FROM bookings WHERE created_at BETWEEN ? AND ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $fullEndDate);
    $stmt->execute();
    $res = $stmt->get_result();

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="Hotel_Report_'.$startDate.'.txt"');

    echo "FLASH HOTEL - BOOKING REPORT\r\n";
    echo "Period: $startDate to $endDate\r\n";
    echo "------------------------------------------------------------\r\n";
    
    $fileTotalRev = 0;
    $fileTotalCount = 0;
    $dataRows = "";

    while ($row = $res->fetch_assoc()) {
        $fileTotalCount++;
        $fileTotalRev += $row['total_price'];
        $dataRows .= $row['created_at'] . " | " . $row['customer_username'] . " | " . $row['room_name'] . " | $" . $row['total_price'] . "\r\n";
    }

    echo "Total Bookings: $fileTotalCount\r\n";
    echo "Total Revenue:  $" . number_format($fileTotalRev, 2) . "\r\n";
    echo "------------------------------------------------------------\r\n";
    echo "Date                | Customer        | Room                | Amount\r\n";
    echo "------------------------------------------------------------\r\n";
    echo $dataRows;
    exit;
}
// --- END TXT LOGIC ---

if (isset($_POST['generate_report'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $query = "SELECT * FROM bookings WHERE created_at BETWEEN ? AND ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($query);
    $fullEndDate = $endDate . " 23:59:59";
    $stmt->bind_param("ss", $startDate, $fullEndDate);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $results[] = $row;
        $totalBookings++;
        $totalRevenue += $row['total_price'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Reports | FLASH Hotel</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .report-form { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { display: inline-block; margin-right: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-generate { padding: 9px 20px; background: #4c8bf5; color: white; border: none; border-radius: 4px; cursor: pointer; }
        
        /* NEW STYLES FOR BUTTONS */
        .btn-txt { padding: 9px 20px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        .btn-print { padding: 9px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        
        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; text-align: center; border-left: 5px solid #4c8bf5; }
        .stat-card h3 { margin: 0; color: #555; font-size: 14px; }
        .stat-card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #333; }
        .report-table { width: 100%; border-collapse: collapse; background: #fff; }
        .report-table th, .report-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .report-table th { background: #f8f9fa; }

        /* PRINT LOGIC: Hide sidebar and forms when printing */
        @media print {
            .sidebar, .report-form, .topbar, .btn-print, .btn-txt, .btn-generate { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .table-box, .stat-card { border: 1px solid #ccc; box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2> FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>
    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_rooms.php"> Manage Rooms</a>
    <a href="manage_subadmins.php"> Manage Subadmins</a>
    <a href="bookings.php"> Bookings</a>
    <a href="customers.php"> Customers</a>
    <a href="manage_staff.php"> Staff</a>
    <a href="reports.php" class="active"> Reports</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3>Financial & Booking Reports</h3>
    </div>

    <div class="report-form">
        <form method="POST" action="">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>" required>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="<?php echo $endDate; ?>" required>
            </div>
            <button type="submit" name="generate_report" class="btn-generate">Generate Report</button>
            
            <?php if (isset($_POST['generate_report']) && !empty($results)): ?>
                <button type="submit" name="download_txt" class="btn-txt">Download .txt</button>
                <button type="button" onclick="window.print()" class="btn-print">Print Report</button>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($_POST['generate_report'])): ?>
        <div class="summary-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <p><?php echo $totalBookings; ?></p>
            </div>
            <div class="stat-card" style="border-left-color: #2ecc71;">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>

        <div class="table-box">
            <h3>Details for <?php echo $startDate; ?> to <?php echo $endDate; ?></h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_username']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($results)) echo "<tr><td colspan='4'>No records found for these dates.</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>