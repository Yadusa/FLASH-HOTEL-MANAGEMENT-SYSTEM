<?php
// 1. Start session to check if user is logged in
session_start();
require_once "../db.php";

// 2. Fetch the correct column 'available_slots' based on your database screenshot
// Modified query for room_details_executive.php
$sql = "SELECT available_slots, room_status FROM rooms WHERE room_name = 'Deluxe King Room'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$available = $row['available_slots'];
$status = $row['room_status'];

// The room is only "Bookable" if slots > 0 AND status is 'Available'
$is_bookable = ($available > 0 && $status == 'Available');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deluxe King Room | The Obsidian KL</title>
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
        <img src="deluxeroom.jpg" class="room-detail-image" alt="Deluxe King Room">

        <div class="room-detail-content">
            <h2>Deluxe King Room</h2>
            <div class="price-tag">RM 950 / night</div>

            <p>
                Experience refined luxury in our Deluxe King Room, offering a
                spacious living area, premium furnishings, and exclusive
                amenities for superior comfort.
            </p>

            <div class="section">
                <h3>Beds & Occupancy</h3>
                <ul>
                    <li>1 King Size Bed</li>
                    <li>Max Occupancy: 2 Adults</li>
                </ul>
            </div>

            <div class="section">
                <h3>Room Facilities</h3>
                <ul>
                    <li>Ensuite Marble Bathroom with Hot Water Shower</li>
                    <li>Sauna</li>
                    <li>Smart TV & Streaming</li>
                    <li>Free High-Speed Wi-Fi</li>
                    <li>Mini Bar</li>
                    <li>Work Desk & Chair</li>
                </ul>
            </div>

            <div class="status-container" style="margin-top: 20px;">
             <?php if ($is_bookable): ?>
             <div class="availability-box available">
              <h3 style="color: #006644;"><?php echo $available; ?> EXECUTIVE DELUXE KING ROOM(S) AVAILABLE</h3>
             </div>

             <div class="cta-group" style="margin-top: 20px;">
             <?php if(isset($_SESSION['customer_username'])): ?>
                <a href="booking.php?room_name=Executive Suite&room_price=1000" class="btn btn-primary">Book Now →</a>
             <?php else: ?>
                <a href="../customer_login.php?redirect=bookingroom/booking.php&room_name=Executive Suite&room_price=1000" class="btn btn-primary">Book Now →</a>
             <?php endif; ?>
           </div>

             <?php else: ?>
             <div class="availability-box out-of-stock" style="padding: 20px; background: #ffebe6; border-radius: 8px; border: 1px solid #ffbdad;">
              <h3 style="color: #bf2600; margin: 0;">
                <?php 
                    if ($status == 'Maintenance') {
                        echo "UNDER MAINTENANCE";
                        echo "<p style='font-size: 0.9rem; font-weight: normal; margin-top: 5px;'>This room is temporarily unavailable due to scheduled maintenance.</p>";
                    } else {
                        echo "CURRENTLY FULLY BOOKED";
                        echo "<p style='font-size: 0.9rem; font-weight: normal; margin-top: 5px;'>Please check back later or explore our other luxury suites.</p>";
                    }
                ?>
              </h3>
           </div>
        
           <div class="cta-group" style="margin-top: 20px; opacity: 0.5;">
             <button class="btn btn-secondary" disabled>Booking Unavailable</button>
            </div>
           <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>