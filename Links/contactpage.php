<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | The Obsidian Kuala Lumpur</title>
    <link rel="stylesheet" href="../contactpage.css">
</head>

<body>
<div id="hotel-page">

    <header class="main-header">
        <div class="header-content">
            <div class="header-left-links">
                <a href="../ourStory.html" class="nav-link">Our Story</a>
                <a href="../specialoffers.html" class="nav-link">Special Offers</a>
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

    <div class="below-header-back">
        <a href="hotel.php" class="back-btn">← Back to Main</a>
    </div>

    <main class="contact-wrapper">
        <div class="contact-container">
            <div class="contact-info">
                <p class="tagline">Get In Touch</p>
                <h2>Contact Us</h2>
                <div class="info-details">
                    <p class="contact-text">
                        <strong>Location</strong><br>
                        123 Obsidian Tower, KLCC,<br>
                        50088 Kuala Lumpur, Malaysia
                    </p>
                    <p class="contact-text">
                        <strong>Phone</strong><br>
                        +60 3-1234 5678
                    </p>
                    <p class="contact-text">
                        <strong>Email</strong><br>
                        reservations@obsidiankl.com
                    </p>
                </div>
            </div>

            <div class="contact-form-container">
                <form id="feedbackForm" class="modern-form">
                    <div class="input-group">
                        <input type="text" placeholder="Your Name" required>
                    </div>
                    <div class="input-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>
                    <div class="input-group">
                        <textarea placeholder="Your Feedback" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Feedback</button>
                </form>
            </div>
        </div>
    </main>

    <div class="popup-overlay" id="popup">
        <div class="popup-card">
            <div class="check-icon">✓</div>
            <h3>Thank You!</h3>
            <p>Your feedback has been submitted successfully. We will get back to you soon.</p>
            <button onclick="closePopup()" class="close-btn">Close</button>
        </div>
    </div>

</div>

<script src="../contact.js"></script>
</body>
</html>