<?php
// ---------------------------
// Handle form submission ONLY
// ---------------------------
$show_popup = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // No database — just confirm submission
    $show_popup = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian KL Event Space Booking</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            background: #f4f4f4;
            color: #333;
        }

        .top-nav {
            max-width: 700px;
            margin: 0 auto 15px auto;
        }

        .back-link {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        .back-link:hover {
            color: #000;
            border-color: #000;
        }

        h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 20px;
        }

        form {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 10px 0;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .checkbox-item input {
            width: auto;
            margin: 0;
        }

        button.submit-btn {
            width: 100%;
            padding: 15px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            font-weight: 600;
        }

        button.submit-btn:hover {
            background: #444;
        }

        /* --- POPUP --- */
        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            display: <?php echo $show_popup ? 'flex' : 'none'; ?>;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .popup-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .popup-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>Event Reservations</h2>

<form method="post" action="">
    <div class="form-grid">
        <div class="full-width">
            <label>Contact Person Full Name</label>
            <input type="text" name="contact_name" required>
        </div>

        <div>
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Phone Number</label>
            <input type="text" name="phone_number" required>
        </div>

        <div class="full-width">
            <label>Event Title</label>
            <input type="text" name="event_title" required>
        </div>

        <div>
            <label>Date of Event</label>
            <input type="date" name="event_date" required>
        </div>

        <div>
            <label>Attendees</label>
            <input type="number" name="attendees" min="1" required>
        </div>

        <div>
            <label>Start Time</label>
            <input type="time" name="start_time" required>
        </div>

        <div>
            <label>End Time</label>
            <input type="time" name="end_time" required>
        </div>

        <div>
            <label>Event Type</label>
            <select name="event_type" required>
                <option disabled selected>Select Type</option>
                <option>Internal Team Meeting</option>
                <option>Client Presentation</option>
                <option>Workshop / Training</option>
                <option>Conference / Seminar</option>
                <option>Social Event</option>
            </select>
        </div>

        <div>
            <label>Space Type</label>
            <select name="space_type" required>
                <option disabled selected>Select Space</option>
                <option>Small Meeting Room</option>
                <option>Medium Meeting Room</option>
                <option>Large Conference Hall</option>
                <option>Flexible Event Space</option>
            </select>
        </div>

        <div class="full-width">
            <label>Special Requests</label>
            <textarea name="special_requests" rows="3"></textarea>
        </div>
    </div>

    <button type="submit" class="submit-btn">Confirm Booking Request</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <button class="popup-close" onclick="closePopup()">✕</button>

        <div style="font-size:60px;">✧</div>
        <h3>Booking Received!</h3>
        <p>Your event space request has been submitted. Our team will contact you shortly.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>
