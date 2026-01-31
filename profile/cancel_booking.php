<?php
session_start();
include "../db_customer.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id  = intval($_POST['booking_id']);
    $customer_id = $_SESSION['customer_id'];

    // Security check: booking must belong to this customer
    $stmt = $conn->prepare("
        UPDATE booking 
        SET status='cancelled' 
        WHERE id=? AND customer_id=? AND status <> 'cancelled'
    ");
    $stmt->bind_param("ii", $booking_id, $customer_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?cancelled=1");
exit();

    } else {
        die("Failed to cancel booking.");
    }
}
