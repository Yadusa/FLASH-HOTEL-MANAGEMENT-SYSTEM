<?php
session_start();
include "../db_customer.php";

// Redirect if not logged in
if (!isset($_SESSION['customer_username'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_username = $_SESSION['customer_username'];

// Fetch bookings for this customer
$sql = "SELECT * FROM bookings 
        WHERE customer_username = ?
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $customer_username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f5f7;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1000px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #111827;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px;
    text-align: left;
}

th {
    background: #f9fafb;
    color: #374151;
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb;
}

td {
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
}

/* Status badge */
.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-block;
}

.status.paid {
    background: #dcfce7;
    color: #166534;
}

.status.pending {
    background: #fef3c7;
    color: #92400e;
}

.status.failed {
    background: #fee2e2;
    color: #991b1b;
}

/* Back link */
.back-link {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #4f46e5;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

/* Empty state */
.empty {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}
</style>
</head>

<body>

<div class="container">
    <h2>My Bookings</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Room</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Guests</th>
            <th>Total (RM)</th>
            <th>Payment</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['room_name']); ?></td>
            <td><?= htmlspecialchars($row['checkin']); ?></td>
            <td><?= htmlspecialchars($row['checkout']); ?></td>
            <td>
                <?= $row['adults']; ?> Adults,
                <?= $row['children']; ?> Children
            </td>
            <td>RM <?= number_format($row['total_price'], 2); ?></td>
            <td>
                <span class="status <?= strtolower($row['payment_status']); ?>">
                    <?= ucfirst($row['payment_status']); ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <div class="empty">You have no bookings yet.</div>
    <?php endif; ?>

    <a class="back-link" href="customer_profile.php">← Back to Profile</a>
</div>

</body>
</html>
