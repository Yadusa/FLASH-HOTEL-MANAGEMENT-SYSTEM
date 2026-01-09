<?php
// ---------------------------
// Handle form submission ONLY
// ---------------------------
$show_popup = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // No database — just trigger popup
    $show_popup = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian KL Spa Reservation</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            background: #f4f4f4;
            color: #333;
        }

        .top-nav {
            max-width: 600px;
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
            color: #1a1a1a;
            border-color: #1a1a1a;
        }

        h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
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
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
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



<h2>The Obsidian Spa Reservation</h2>
<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<form method="post" action="">
    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Contact Number</label>
    <input type="text" name="contact_number" required>

    <label>Email Address</label>
    <input type="email" name="email" required>

    <label>Room Number</label>
    <input type="text" name="room_number" required>

    <label>Appointment Date</label>
    <input type="date" name="appointment_date" required>

    <label>Preferred Time Slot</label>
    <select name="time_slot" required>
        <option disabled selected>Select Time</option>
        <option>10:00 AM - 12:00 PM</option>
        <option>12:00 PM - 2:00 PM</option>
        <option>2:00 PM - 4:00 PM</option>
        <option>4:00 PM - 6:00 PM</option>
        <option>6:00 PM - 8:00 PM</option>
    </select>

    <label>Service</label>
    <select name="service" required>
        <option disabled selected>Select Service</option>
        <option>Traditional Malay Massage</option>
        <option>Aromatherapy Oil Massage</option>
        <option>Deep Tissue Massage</option>
        <option>Foot Reflexology</option>
    </select>

    <label>Number of Guests</label>
    <input type="number" name="guests" min="1" value="1" required>

    <label>Concerns</label>
    <textarea name="concerns" rows="4"></textarea>

    <button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <button class="popup-close" onclick="closePopup()">✕</button>

        <div style="font-size:50px;">🌿</div>
        <h3>Relaxation Awaits!</h3>
        <p>Your reservation has been submitted successfully.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>
