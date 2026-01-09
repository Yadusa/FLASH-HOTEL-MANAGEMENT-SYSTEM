<?php
// ---------------------------
// Database credentials
// ---------------------------
$host = "localhost";
$dbname = "flashhotel"; 
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$show_popup = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get inputs
    $reservation_date = $conn->real_escape_string($_POST['reservation_date'] ?? '');
    $time_slot        = $conn->real_escape_string($_POST['time_slot'] ?? '');
    $guests           = (int)($_POST['guests'] ?? 0);
    $full_name        = $conn->real_escape_string($_POST['full_name'] ?? '');
    $contact_number   = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $email            = $conn->real_escape_string($_POST['email'] ?? '');
    $room_number      = $conn->real_escape_string($_POST['room_number'] ?? '');
    $special_requests = $conn->real_escape_string($_POST['special_requests'] ?? '');
    $referral         = $conn->real_escape_string($_POST['referral'] ?? '');

    // SQL Query
    $sql = "INSERT INTO DinnerReservation 
            (reservation_date, time_slot, guests, full_name, contact_number, email, room_number, special_requests, referral_source)
            VALUES 
            ('$reservation_date', '$time_slot', $guests, '$full_name', '$contact_number', '$email', '$room_number', '$special_requests', '$referral')";

    if ($conn->query($sql) === TRUE) {
        $show_popup = true; 
    } else {
        // This will only show if there is a database error
        echo "<script>alert('Database Error: " . $conn->error . "');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian KL Dinner Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f4f4f4; color: #333; }
        
        /* Top Navigation Link */
        .top-nav { max-width: 600px; margin: 0 auto 15px auto; }
        .back-link { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 2px; transition: 0.3s; }
        .back-link:hover { color: #000; border-color: #000; }

        h2 { text-align: center; color: #1a1a1a; margin-bottom: 25px; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        
        label { font-weight: bold; margin-top: 15px; display: block; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        input, select, textarea { width: 100%; padding: 12px; margin: 8px 0; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; font-family: inherit; }
        
        button.submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; font-weight: 600; }
        button.submit-btn:hover { background: #444; }

        /* --- POPUP OVERLAY --- */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            /* PHP controls if this is 'flex' or 'none' */
            display: <?php echo $show_popup ? 'flex' : 'none'; ?>; 
            justify-content: center; align-items: center; z-index: 2000;
        }
        .popup-card {
            background: white; padding: 40px; border-radius: 20px; text-align: center;
            max-width: 450px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: popDown 0.4s ease-out;
        }
        @keyframes popDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .popup-card h3 { font-size: 26px; margin-bottom: 10px; color: #1a1a1a; }
        .popup-card p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        
        /* Popup Button Style (Renamed to avoid conflict) */
        .popup-btn { 
            display: inline-block; padding: 12px 35px; background: #1a1a1a; 
            color: white !important; text-decoration: none; border-radius: 8px; 
            font-weight: bold; transition: 0.3s; 
        }
        .popup-btn:hover { background: #b8860b; transform: scale(1.05); }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>The Obsidian KL Reservation</h2>

<form method="post" action="">
    <label>Date of Reservation:</label>
    <input type="date" name="reservation_date" required>

    <label>Time Slot:</label>
    <select name="time_slot" required>
        <option value="6:00 PM">6:00 PM</option>
        <option value="7:00 PM">7:00 PM</option>
        <option value="8:00 PM">8:00 PM</option>
        <option value="9:00 PM">9:00 PM</option>
    </select>

    <label>Guests:</label>
    <input type="number" name="guests" min="1" required>

    <label>Full Name:</label>
    <input type="text" name="full_name" required>

    <label>Contact Number:</label>
    <input type="text" name="contact_number" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Room Number:</label>
    <input type="text" name="room_number">

    <label>Special Requests:</label>
    <textarea name="special_requests" rows="3"></textarea>

    <label>How did you hear about us?</label>
    <select name="referral">
        <option value="Social Media">Social Media</option>
        <option value="Search Engine">Search Engine</option>
        <option value="Word of Mouth">Word of Mouth</option>
    </select>

    <button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size: 60px; color: #b8860b; margin-bottom: 15px;">✧</div>
        <h3>Thank You!</h3>
        <p>Your dinner reservation has been successfully submitted. We look forward to hosting you at The Obsidian Kuala Lumpur.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>