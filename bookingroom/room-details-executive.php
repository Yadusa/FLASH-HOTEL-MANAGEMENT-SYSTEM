<?php
require_once "../config/db.php";

$sql = "SELECT COUNT(*) AS available_rooms
        FROM rooms
        WHERE room_type = 'Executive Suite'
        AND room_status = 'Available'";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$available = $row['available_rooms'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Suite | The Obsidian KL</title>
    <link rel="stylesheet" href="../bookingroom.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="booking-header">
    <a href="select-room.html" class="back-button">← Back to Rooms</a>
</header>

<div class="room-details-container">

    <div class="room-detail-hero">
        <img src="suiteroom.jpg" class="room-detail-image" alt="Executive Suite">

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
                    <li>🛏 1 King Size Bed</li>
                    <li>👤 Max Occupancy: 2 Adults</li>
                </ul>
            </div>

            <div class="section">
                <h3>Room Facilities</h3>
                <ul>
                    <li>✔ Jacuzzi Bathtub</li>
                    <li>✔ Hot Water Shower</li>
                    <li>✔ Smart TV & Streaming</li>
                    <li>✔ Free High-Speed Wi-Fi</li>
                    <li>✔ Mini Bar</li>
                    <li>✔ Air Conditioning</li>
                </ul>
            </div>

            <?php if ($available > 0): ?>
                <div class="availability-box available">
                    ✅ <?php echo $available; ?> Executive Suite(s) Available
                </div>
            <?php else: ?>
                <div class="availability-box full">
                    ❌ Fully Booked
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>

