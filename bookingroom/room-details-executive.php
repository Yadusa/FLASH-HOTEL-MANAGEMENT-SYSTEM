<?php
// 1. Start session to check if user is logged in
session_start();
require_once "../db.php";

// 2. Fetch the correct column 'available_slots' based on your database screenshot
$sql = "SELECT available_slots FROM rooms WHERE room_name = 'Executive Suite'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $available = $row['available_slots'];
} else {
    $available = 0; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Suite | The Obsidian KL</title>
    <link rel="stylesheet" href="hotel_room.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="booking-header">
    <div class="header-content-inner">
        <h1>Room Details</h1>
    </div>
</header>

<div class="below-header-back">
    <a href="roombooking.php" class="back-button">← Back to Rooms</a>
</div>

<div class="room-details-container">
    <div class="room-detail-hero">
        <img src="luxury_suite.jpg" class="room-detail-image" alt="Executive Suite">

        <div class="room-detail-content">
            <h2>Executive Suite</h2>
            <div class="price-tag">RM 1,000 / night</div>

            <p>
                Experience refined luxury in our Executive Suite, offering a
                spacious living area, premium furnishings, and exclusive
                executive privileges.
            </p>

            <div class="section">
                <h3>Beds & Occupancy</h3>
                <ul>
                    <li>1 King Size Bed</li>
                    <li>1 Sofa Bed</li>
                    <li>Max Occupancy: 4 Adults</li>
                </ul>
            </div>

            <div class="section">
                <h3>Room Facilities</h3>
                <ul>
                    <li>Jacuzzi Bathtub</li>
                    <li>Hot Water Shower</li>
                    <li>Private Balcony</li>
                    <li>Smart TV & Streaming</li>
                    <li>Free High-Speed Wi-Fi</li>
                    <li>Mini Bar</li>
                    <li>Executive Lounge Access</li>
                </ul>
            </div>

            <?php if ($available > 0): ?>
                <div class="availability-box available">
                    <h3><?php echo $available; ?> EXECUTIVE SUITE(S) AVAILABLE</h3>
                </div>

                <div class="cta-group" style="margin-top: 20px;">
                    <?php if(isset($_SESSION['customer_username'])): ?>
                        <a href="booking.php?room_name=Executive Suite&room_price=1000" class="btn btn-primary">Book Now →</a>
                    <?php else: ?>
                        <a href="../customer_login.php?redirect=bookingroom/booking.php&room_name=Executive Suite&room_price=1000" class="btn btn-primary">Book Now →</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="availability-box out-of-stock" style="color: red; font-weight: bold;">
                    Currently Fully Booked
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>