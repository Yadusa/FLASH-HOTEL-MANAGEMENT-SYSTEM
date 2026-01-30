<?php
session_start();
require_once('db.php');

// 1. Security Check
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

// --- TXT DOWNLOAD LOGIC ---
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
        $dataRows .= $row['created_at'] . " | " . $row['customer_username'] . " | " . $row['room_name'] . " | RM" . $row['total_price'] . "\r\n";
    }

    echo "Total Bookings: $fileTotalCount\r\n";
    echo "Total Revenue:  RM" . number_format($fileTotalRev, 2) . "\r\n";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Reports | FLASH Hotel Admin</title>
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

        /* --- REPORT SPECIFIC STYLES --- */
        .report-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: flex-end;
            gap: 20px;
            flex-wrap: wrap;
        }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 14px; font-weight: 600; color: #555; }
        .form-group input { 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-family: inherit;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-generate { background: #4c8bf5; color: white; }
        .btn-generate:hover { background: #3b7ddd; }
        
        .btn-txt { background: #2ecc71; color: white; margin-left: auto; }
        .btn-txt:hover { background: #27ae60; }
        
        .btn-print { background: #6c757d; color: white; }
        .btn-print:hover { background: #5a6268; }

        /* Stat Cards */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 5px solid #4c8bf5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-card h3 { margin: 0; color: #777; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card p { margin: 10px 0 0; font-size: 28px; font-weight: bold; color: #333; }
        .stat-revenue { border-left-color: #2ecc71; }
        
        /* Table Styles */
        .table-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .report-table th { background: #f8f9fa; color: #555; font-weight: 600; padding: 15px; text-align: left; border-bottom: 2px solid #eee; }
        .report-table td { padding: 15px; border-bottom: 1px solid #eee; color: #444; }
        .report-table tr:hover { background-color: #fafafa; }

        /* PRINT LOGIC: Hide sidebar and forms when printing */
        @media print {
            .sidebar, .report-form, .topbar, .btn-print, .btn-txt, .btn-generate { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .table-box, .stat-card { border: 1px solid #ccc; box-shadow: none; }
            body { background: white; }
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
    <a href="manage_rooms.php"><i class="fas fa-bed"></i> Manage Rooms</a>
    <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>

    <?php if ($adminRole === "superadmin") { ?>
        <a href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Subadmins</a>
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_staff.php"><i class="fas fa-id-badge"></i> All Staff</a>
        <a href="reports.php" class="active"><i class="fas fa-chart-line"></i> Reports</a>
    <?php } ?>

    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">

    <div class="topbar">
        <h3><i class="fas fa-chart-pie"></i> Financial & Booking Reports</h3>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
        </div>
    </div>

    <div class="report-form">
        <form method="POST" action="" style="display:contents;">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>" required>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="<?php echo $endDate; ?>" required>
            </div>
            <button type="submit" name="generate_report" class="btn btn-generate">
                <i class="fas fa-search"></i> Generate Report
            </button>
            
            <?php if (isset($_POST['generate_report']) && !empty($results)): ?>
                <button type="submit" name="download_txt" class="btn btn-txt">
                    <i class="fas fa-file-alt"></i> Download .txt
                </button>
                <button type="button" onclick="window.print()" class="btn btn-print">
                    <i class="fas fa-print"></i> Print
                </button>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($_POST['generate_report'])): ?>
        
        <div class="summary-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <p><?php echo $totalBookings; ?></p>
            </div>
            <div class="stat-card stat-revenue">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>

        <div class="table-box">
            <h4 style="margin: 0; font-size: 1.1rem; color: #555;">
                <i class="fas fa-list"></i> Transaction Details (<?php echo $startDate; ?> to <?php echo $endDate; ?>)
            </h4>
            
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Room Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['customer_username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td style="font-weight:bold;">$<?php echo number_format($row['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($results)) echo "<tr><td colspan='4' style='text-align:center; padding:30px; color:#999;'>No transactions found for the selected dates.</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>