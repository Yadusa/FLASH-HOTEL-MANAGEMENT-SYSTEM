<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | The Obsidian Kuala Lumpur</title>

    <link rel="stylesheet" href="hotelpage.css">
</head>

<body>
<div id="hotel-page">

    <!-- HEADER (reuse your existing header) -->
    <header class="main-header">
        <div class="header-content">
            <div class="header-left-links">
                <a href="ourStory.html" class="nav-link">Our Story</a>
                <a href="specialoffers.html" class="nav-link">Special Offers</a>
                <a href="gallery.php" class="nav-link">Gallery</a>
            </div>

            <div class="logo">
                <h1>The Obsidian</h1>
                <p>KUALA LUMPUR</p>
            </div>

            <div class="header-right-actions">
                <a href="bookingroom/roompage.html" class="cta-button book-now-button">BOOK NOW</a>
            </div>
        </div>
    </header>

    <!-- BACK BUTTON -->
    <div class="below-header-back">
        <a href="hotel.php" class="back-btn">← Back to Main</a>
    </div>

    <!-- CONTACT SECTION -->
    <section class="contact-section">
        <div class="contact-container">

            <!-- LEFT: CONTACT INFO -->
            <div class="contact-info">
                <p class="tagline">Get In Touch</p>
                <h2>Contact Us</h2>

                <p class="contact-text">
                    123 Obsidian Tower, KLCC,<br>
                    50088 Kuala Lumpur, Malaysia
                </p>

                <p class="contact-text">
                    +60 3-1234 5678
                </p>

                <p class="contact-text">
                    reservations@obsidiankl.com
                </p>
            </div>

            <!-- RIGHT: FEEDBACK FORM -->
            <div class="contact-form">
                <form id="feedbackForm">
                    <input type="text" placeholder="Your Name" required>
                    <input type="email" placeholder="Your Email" required>
                    <textarea placeholder="Your Feedback" required></textarea>
                    <button type="submit" class="cta-button">Submit Feedback</button>
                </form>
            </div>

        </div>
    </section>

    <!-- SUCCESS POPUP -->
    <div class="popup-overlay" id="popup">
        <div class="popup">
            <h3>Thank You!</h3>
            <p>Your feedback has been submitted successfully.</p>
            <button onclick="closePopup()" class="cta-button">Close</button>
        </div>
    </div>

</div>

<script src="contact.js"></script>
</body>
</html>
