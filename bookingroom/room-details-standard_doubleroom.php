<?php
// 1. Start session to track login status
session_start();
require_once "../db.php";

// 2. Fetch 'available_slots' specifically for the 'Executive Deluxe King'
// Based on your database screenshot, the name must match exactly.
$sql = "SELECT available_slots FROM rooms WHERE room_name = 'Executive Deluxe King'";
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
        <img src="standarddouble.jpg" class="room-detail-image" alt="Executive Suite">

        <div class="room-detail-content">
            <h2>Standard Double Room</h2>
            <div class="price-tag">RM 150 / night</div>

            <p>
                Experience refined luxury in our Standard double room, offering a
                spacious living area, premium furnishings, and exclusive
                executive privileges.
            </p>

            <div class="section">
                <h3>Beds & Occupancy</h3>
                <ul>
                    <li>2 Super Single Size Bed</li>
            
                    <li>Max Occupancy: 2 Adults </li>

                </ul>
            </div>

            <div class="section">
                <h3>Room Facilities</h3>
                <ul>
                    <li>Private Bathroom with Hot Water Shower</li>
                    <li>Smart TV & Streaming</li>
                    <li>Free High-Speed Wi-Fi</li>
                    <li>Mini Bar</li>
                    <li>Work desk</li>
                    <li>Air Conditioning</li>
                </ul>
            </div>

            <?php if ($available > 0): ?>
                <div class="availability-box available">
                    <h3><?php echo $available; ?> EXECUTIVE DELUXE KING ROOM(S) AVAILABLE</h3>
                </div>

                <div class="cta-group" style="margin-top: 20px;">
                    <?php if(isset($_SESSION['customer_username'])): ?>
                        <a href="booking.php?room_name=Standard Double Room&room_price=150" class="btn btn-primary">Book Now →</a>
                    <?php else: ?>
                        <a href="../customer_login.php?redirect=bookingroom/booking.php&room_name=Standard Double Room&room_price=150" class="btn btn-primary">Book Now →</a>
                    <?php endif; ?>
                    </div>
                   <?php else: ?>
                  <div class="availability-box out-of-stock" style="color: #b91c1c; background: #fee2e2; padding: 10px; border-radius: 8px; text-align: center; font-weight: bold;">
                    Currently Fully Booked
                 </div>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>

