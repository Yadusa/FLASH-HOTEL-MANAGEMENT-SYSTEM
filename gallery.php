<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> 
            The Obsidian Kuala Lumpur | Luxury Hotel
        </title>

        <link rel="stylesheet" href="hotelpage.css"> 
       
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    </head>

    <body>
        <div id="hotel-page">
    <header class="main-header">
       <div class="header-content">
           <div class="header-left-links">
            <a href="ourStory.html" class="nav-link">Our Story</a>
            <a href="specialoffers.html" class="nav-link special-offer-link"> Special Offers</a>
            <a href="gallery.php" class="nav-link Gallery">Gallery</a>
            </div>

            <div class="logo">

            <h1> The Obsidian </h1>
            <p> KUALA LUMPUR </p>
            </div>
         <div class="header-right-actions">
            <div class="auth-links">
                <a href="#" onclick="showAdminLogin()" class="nav-link">Sign In</a> 
                <span>|</span>
                <a href="#" class="nav-link">Join Now</a> 
            </div>
            
            <a href="bookingroom/roompage.html" class="cta-button book-now-button">BOOK NOW</a>
          </div>
       </div>
   </header>

    <div class="below-header-back">
    <a href="hotel.php" class="back-btn">← Back to Main</a>
    </div>

    <section class="gallery-container">


            <div class="gallery-intro">
                <p class="tagline">Visual Journey</p>
                <h2>Our Curated Spaces</h2>
            </div>
           
            <div class="photo-grid">
                <div class="gallery-item">
                    <img src="images/lobby1.jpg" alt="">
                    <div class="caption">The Obsidian KL</div>
                </div>
                <div class="gallery-item">
                    <img src="images/lobby2.jpg" alt="">
                    <div class="caption">Waiting area</div>
                </div>
                <div class="gallery-item">
                    <img src="images/lobby3.jpg" alt="">
                    <div class="caption">Walkway</div>
                </div>
                <div class="gallery-item">
                    <img src="images/lobby4.jpg" alt="City View from Window">
                    <div class="caption">Floors</div>
                </div>
            </div>
    </section>

    <!-- FOOTER (same as landing page) -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-logo">
                <h4>The Obsidian</h4>
                <p>KUALA LUMPUR</p>
            </div>

            <div class="footer-links">
                <h5>Hotel</h5>
                <a href="ourStory.html">About Us</a>
                <a href="#">Contact</a>
                <a href="#">Careers</a>
            </div>

            <div class="footer-links">
                <h5>Services</h5>
                <a href="#">Meetings & Events</a>
                <a href="https://forms.gle/J6sBpLRAp8hfJz9E8">Wellness & Spa</a>
                <a href="gallery.php">Gallery</a>
            </div>

            <div class="footer-contact">
                <h5>Contact Us</h5>
                <p>123 Obsidian Tower, KLCC, 50088 Kuala Lumpur, Malaysia</p>
                <p>+60 3-1234 5678</p>
                <p>reservations@obsidiankl.com</p>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2025 The Obsidian Kuala Lumpur. All Rights Reserved.
        </div>
    </footer>

</div>

<script src="landing.js" defer></script>
</body>
</html>
    