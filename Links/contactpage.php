<?php
session_start();

$show_popup = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // You can save feedback to DB here later
    $show_popup = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | The Obsidian Kuala Lumpur</title>

<link rel="stylesheet" href="../Links/contactpage.css">

<style>
/* ===== POPUP ===== */
.popup-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    display: <?php echo $show_popup ? 'flex' : 'none'; ?>;
    justify-content: center;
    align-items: center;
    z-index: 3000;
}

.popup-card {
    position: relative;
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    max-width: 420px;
    width: 90%;
    animation: popDown 0.4s ease-out;
}

@keyframes popDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.check-icon {
    font-size: 48px;
    color: #b8860b;
    margin-bottom: 15px;
}

/* Close X button */
.popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 35px;
    height: 35px;
    border: 2px solid #1a1a1a;
    background: white;
    font-size: 18px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}
.popup-close:hover {
    background: #1a1a1a;
    color: white;
}

/* Bottom button */
.popup-btn {
    margin-top: 20px;
    padding: 12px 35px;
    background: #1a1a1a;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.popup-btn:hover {
    background: #b8860b;
}
</style>
</head>

<body>
<div id="hotel-page">

<header class="main-header">
    <div class="header-content">
        <div class="header-left-links">
            <a href="../ourStory.html" class="nav-link">Our Story</a>
            <a href="../specialoffers.php" class="nav-link">Special Offers</a>
            <a href="../gallery.php" class="nav-link">Gallery</a>
        </div>

        <div class="logo">
            <h1>The Obsidian</h1>
            <p>KUALA LUMPUR</p>
        </div>

        <div class="header-right-actions">
            <a href="../bookingroom/roompage.html" class="cta-button book-now-button">BOOK NOW</a>
        </div>
    </div>
</header>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to main</a>
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
            <form method="post" class="modern-form">

                <div class="input-group">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Your Email" required>
                </div>

                <div class="input-group">
                    <textarea name="feedback" placeholder="Your Feedback" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Feedback</button>

            </form>
        </div>

    </div>
</main>

<!-- POPUP -->
<div class="popup-overlay">
    <div class="popup-card">
        <button class="popup-close" onclick="closePopup()">✕</button>
        <div class="check-icon">✓</div>
        <h3>Thank You!</h3>
        <p>Your feedback has been submitted successfully. We will get back to you soon.</p>
        <button class="popup-btn" onclick="closePopup()">Back to Main</button>
    </div>
</div>

</div>

<script>
function closePopup() {
    window.location.href = "../hotel.php"; 
    // change to "contact.php" if you want to stay on contact page
}
</script>

</body>
</html>
