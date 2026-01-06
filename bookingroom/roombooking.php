<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Select Your Room | The Obsidian Kuala Lumpur</title>
        <link rel="stylesheet" href="../bookingroom.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
         <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
         <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
         
    </head>

    <body>
            <header class="booking-header">
                <div class="header-content-inner">
                    <h1>Our Rooms & Suites</h1>
                    <p>Select your sanctuary of elegance in the heart of Kuala Lumpur.</p>
                </div>
            </header>
                <div class="below-header-back">
                  <a href="../hotel.php" class="back-btn">← Back to Main</a>
                </div>
            
            <div class="room-grid-container">

                <h2 class="section-title">Luxury Suites</h2>
                <div class="room-grid luxury-grid">
                    
                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="luxury_suite.jpg" alt="Image of Executive Suite">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Executive Suite</h3>
                            <p class="room-price">From RM 1,000 / night</p>
                            <p class="room-details-text">Luxury meets comfort in our elegantly designed spaces, complete with spacious living areas and modern amenities for a seamless, private stay.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
                                <a href="booking.php?room_name=Executive Suite&room_price=1000" class="btn btn-primary">View & Book →</a>
                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>

                                <a href="room-details-executive.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>

                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="deluxeroom.jpg" alt="Image of Deluxe King Room">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Deluxe King Room</h3>
                            <p class="room-price">From RM 950 / night</p>
                            <p class="room-details-text">Rest in a comfortable king-size bed, featuring a private marble bathroom, work desk, and complimentary Wi-Fi for superior comfort.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
                                <a href="booking.php?room_name=Deluxe King Room&room_price=950" class="btn btn-primary">View & Book →</a>                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>
                                <a href="room-details-deluxe_king_room.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>

                </div>

                <h2 class="section-title">Executive & Family Stays</h2>
                <div class="room-grid executive-grid">
                    
                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="familyroom.jpg" alt="Image of Family Room">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Family Room</h3>
                            <p class="room-price">From RM 500 / night</p>
                            <p class="room-details-text">Spacious and modern accommodations tailored for families, offering interconnected options and ample space for a relaxed holiday.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
<a href="booking.php?room_name=Family Room&room_price=500" class="btn btn-primary">View & Book →</a>
                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>
                                <a href="room-details-family_room.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>

                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="executive-deluxe-king.jpg" alt="Image of Executive Deluxe King Room">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Executive Deluxe King</h3>
                            <p class="room-price">From RM 420 / night</p>
                            <p class="room-details-text">Pamper yourself with premium toiletries and cozy bathrobes. Unwind with in-room entertainment and exclusive executive lounge access.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
<a href="booking.php?room_name=Executive Deluxe King&room_price=420" class="btn btn-primary">View & Book →</a>
                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>
                                <a href="room-details-executive_deluxe_king.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>

                </div>

                <h2 class="section-title">Budget Friendly Options</h2>
                <div class="room-grid budget-grid">

                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="standarddouble.jpg" alt="Image of Double Bed Budget Room">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Standard Double Room</h3>
                            <p class="room-price">From RM 150 / night</p>
                            <p class="room-details-text">Cozy and affordable accommodation. Enjoy a practical lodging experience without compromising on essential comfort and free Wi-Fi.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
<a href="booking.php?room_name=Standard Double Room&room_price=150" class="btn btn-primary">View & Book →</a>
                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>
                                <a href="room-details-standard_doubleroom.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>

                    <div class="room-card">
                        <div class="room-image-wrapper">
                            <img src="budget.jpg" alt="Image of Budget Room">
                        </div>
                        <div class="room-details">
                            <h3 class="room-name">Budget Twin Room</h3>
                            <p class="room-price">From RM 120 / night</p>
                            <p class="room-details-text">An economical choice offering clean twin beds, a private bathroom, and a work area, perfect for the efficient traveler.</p>
                            
                            <div class="cta-group">
                                <?php if(isset($_SESSION['customer_username'])): ?>
<a href="booking.php?room_name=Budget Twin Room&room_price=120" class="btn btn-primary">View & Book →</a>
                                <?php else: ?>
                                <a href="../customer_login.php" class="btn btn-primary">View & Book →</a>
                                <?php endif; ?>
                                <a href="room-details-budget_twinroom.php" class="btn btn-secondary">Room Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
