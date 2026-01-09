<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ($adminRole === 'superadmin') ? 'SuperAdmin' : 'SubAdmin'; ?> Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <h2> FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>

    <a href="dashboard.php" class="active"> Dashboard</a>

    <a href="manage_rooms.php"> Manage Rooms</a>

    <?php if ($adminRole === "superadmin") { ?>
        <a href="manage_subadmins.php"> Manage Subadmins</a>
        <a href="bookings.php"> Bookings</a>
        <a href="customers.php"> Customers</a>
        <a href="manage_staff.php"> Staff</a>
        <a href="reports.php"> Reports</a>
    <?php } ?>

    <a href="logout.php" class="logout">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOP BAR -->
    <div class="topbar">
        <h3>Dashboard Overview</h3>

  
          <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:#4c8bf5;color:white;display:flex;align-items:center;justify-content:center;font-weight:600;">
                        <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                    </div>
                    
           </div>         
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="cards">

        <div class="card card-blue">
            <h4> Total Rooms</h4>
            <p>120 Rooms Available</p>
        </div>

        <div class="card card-green">
            <h4> Rooms Booked</h4>
            <p>75 Occupied</p>
        </div>

        <div class="card card-orange">
            <h4> Today's Guests</h4>
            <p>45 Check-ins</p>
        </div>

        <div class="card card-red">
            <h4> Pending Bookings</h4>
            <p>12 Pending</p>
        </div>

    </div>

    <!-- TABLE (EXAMPLE SECTION) -->
    <div class="table-box">
        <h3>Recent Activity</h3>
    </div>

</div>

</body>
</html>


