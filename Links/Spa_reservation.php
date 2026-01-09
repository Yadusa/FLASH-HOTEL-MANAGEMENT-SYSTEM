<?php
// ---------------------------
// Database credentials
// ---------------------------
$host = "localhost";
$dbname = "flashhotel"; 
$username = "root";
$password = "";



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// Handle form submission
// ---------------------------
$show_popup = false; 
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name       = $conn->real_escape_string($_POST['full_name']);
    $contact_number  = $conn->real_escape_string($_POST['contact_number']);
    $email           = $conn->real_escape_string($_POST['email']);
    $room_number     = $conn->real_escape_string($_POST['room_number']);
    $appointment_date= $conn->real_escape_string($_POST['appointment_date']);
    $time_slot       = $conn->real_escape_string($_POST['time_slot']);
    $service         = $conn->real_escape_string($_POST['service']);
    $duration        = $conn->real_escape_string($_POST['duration']);
    $guests          = (int)$_POST['guests'];
    $concerns        = $conn->real_escape_string($_POST['concerns']);

   
    if ($conn->query($sql) === TRUE) {
        $show_popup = true; 
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian KL Spa Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f4f4f4; color: #333; }
        
        .top-nav { max-width: 600px; margin: 0 auto 15px auto; }
        .back-link { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .back-link:hover { color: #1a1a1a; border-color: #1a1a1a; }

        h2 { text-align: center; color: #1a1a1a; font-family: 'Playfair Display', serif; font-size: 32px; margin-bottom: 20px; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 35px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);}
        
        label { font-weight: 600; margin-top: 15px; display: block; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; margin: 8px 0; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; font-family: inherit; }
        
        button.submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; font-weight: 600; }
        button.submit-btn:hover { background: #444; transform: translateY(-1px); }

        .error-message { text-align: center; color: #b91c1c; background: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 20px; }

        /* --- POPUP OVERLAY --- */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            display: <?php echo $show_popup ? 'flex' : 'none'; ?>; 
            justify-content: center; align-items: center; z-index: 2000;
        }
        .popup-card {
            background: white; padding: 40px; border-radius: 20px; text-align: center;
            max-width: 450px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        
        .popup-card h3 { font-size: 26px; margin-bottom: 10px; color: #1a1a1a; }
        .popup-card p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        .popup-btn { display: inline-block; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>The Obsidian Spa Reservation</h2>

<?php if ($error_msg) echo "<p class='error-message'>$error_msg</p>"; ?>

<form method="post" action="">
    <label>Full Name:</label>
    <input type="text" name="full_name" placeholder="E.g. Jane Doe" required>

    <label>Contact Number:</label>
    <input type="text" name="contact_number" placeholder="E.g. +60123456789" required>

    <label>Email Address:</label>
    <input type="email" name="email" placeholder="jane@example.com" required>

    <label>Room Number:</label>
    <input type="text" name="room_number" placeholder="E.g. 1204" required>

    <label>Preferred Date of Appointment:</label>
    <input type="date" name="appointment_date" required min="2026-01-01" max="2026-12-31">

    <label>Preferred Time Slot:</label>
    <select name="time_slot" required>
        <option value="" disabled selected>Select Time</option>
        <option value="10:00 AM - 12:00 PM">10:00 AM - 12:00 PM</option>
        <option value="12:00 PM - 2:00 PM">12:00 PM - 2:00 PM</option>
        <option value="2:00 PM - 4:00 PM">2:00 PM - 4:00 PM</option>
        <option value="4:00 PM - 6:00 PM">4:00 PM - 6:00 PM</option>
        <option value="6:00 PM - 8:00 PM">6:00 PM - 8:00 PM</option>
    </select>

    <label>Desired Service:</label>
    <select name="service" required>
        <option value="" disabled selected>Select Service</option>
        <option value="Traditional Malay Massage">Traditional Malay Massage</option>
        <option value="Aromatherapy Oil Massage">Aromatherapy Oil Massage</option>
        <option value="Deep Tissue Massage">Deep Tissue Massage</option>
        <option value="Obsidian Signature Spa Package">Obsidian Signature Spa Package</option>
        <option value="Foot Reflexology">Foot Reflexology</option>
        <option value="Facial Treatment">Facial Treatment</option>
    </select>

    <label>Duration:</label>
    <select name="duration" required>
        <option value="60 Minutes">60 Minutes</option>
        <option value="90 Minutes">90 Minutes</option>
        <option value="120 Minutes (2 hours)">120 Minutes (2 hours)</option>
    </select>

    <label>Number of Guests:</label>
    <input type="number" name="guests" min="1" value="1" required>

    <label>Focus Areas or Concerns:</label>
    <textarea name="concerns" rows="4" placeholder="E.g. neck tension, allergies, preferred pressure level..."></textarea>

    <button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size: 50px; color: #b8860b; margin-bottom: 15px;">🌿</div>
        <h3>Relaxation Awaits!</h3>
        <p>Your spa reservation at The Obsidian has been successfully submitted. We will confirm your appointment shortly.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>