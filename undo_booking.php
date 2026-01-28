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

// 2. Check if booking ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: bookings.php");
    exit;
}

$booking_id = (int)$_GET['id'];

// 3. Fetch booking details to verify it's cancelled
$sql = "SELECT payment_status FROM bookings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: bookings.php");
    exit;
}

$booking = $result->fetch_assoc();

// 4. Only allow undo if status is 'Cancelled'
if ($booking['payment_status'] !== 'Cancelled') {
    header("Location: bookings.php");
    exit;
}

// 5. Update payment_status to 'Pending'
$update_sql = "UPDATE bookings SET payment_status = 'Pending' WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $booking_id);
$update_stmt->execute();

// 6. Redirect back to bookings page with success message
header("Location: bookings.php?undone=1");
exit;
?>
