<?php
session_start();
include "../db_customer.php";

// Redirect to login if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch bookings for logged-in customer
$sql = "SELECT * FROM bookings WHERE customer_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f5f7;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    padding-top: 50px;
}

h2 {
    text-align: center;
    color: #111827;
    margin-bottom: 30px;
}

/* Container card */
.bookings-card {
    background: #fff;
    padding: 30px 35px;
    border-radius: 15px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    font-size: 14px;
    color: #374151;
}

th {
    background-color: #f3f4f6;
    color: #111827;
    font-weight: 500;
}

tr:nth-child(even) {
    background-color: #f9fafb;
}

tr:hover {
    background-color: #e0e7ff;
}

.status {
    padding: 5px 10px;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    text-align: center;
    font-size: 13px;
}

/* Booking status colors */
.status-pending { background-color: #fbbf24; }  /* yellow */
.status-confirmed { background-color: #4ade80; } /* green */
.status-cancelled { background-color: #f87171; } /* red */

/* Responsive */
@media (max-width: 600px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }
    tr {
        margin-bottom: 15px;
    }
    th {
        display: none;
    }
    td {
        padding: 10px;
        position: relative;
    }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: 600;
        color: #111827;
    }
}
</style>
</head>
<body>

<div class="bookings-card">
    <h2>My Bookings</h2>

    <table>
        <thead>
            <tr>
                <th>Room</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Room"><?= htmlspecialchars($row['room_type']); ?></td>
                <td data-label="Check In"><?= htmlspecialchars($row['check_in']); ?></td>
                <td data-label="Check Out"><?= htmlspecialchars($row['check_out']); ?></td>
                <td data-label="Status">
                    <?php 
                        $statusClass = "";
                        switch(strtolower($row['status'])) {
                            case "pending": $statusClass = "status-pending"; break;
                            case "confirmed": $statusClass = "status-confirmed"; break;
                            case "cancelled": $statusClass = "status-cancelled"; break;
                        }
                    ?>
                    <span class="status <?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if($result->num_rows === 0): ?>
            <tr>
                <td colspan="4" style="text-align:center; padding: 20px; color:#6b7280;">No bookings found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
