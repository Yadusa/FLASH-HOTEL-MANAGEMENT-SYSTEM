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
    // Sanitize and get inputs using the null coalescing operator to prevent warnings
    $reservation_date = $conn->real_escape_string($_POST['reservation_date'] ?? '');
    $time_slot        = $conn->real_escape_string($_POST['time_slot'] ?? '');
    $guests           = (int)($_POST['guests'] ?? 0);
    $full_name        = $conn->real_escape_string($_POST['full_name'] ?? '');
    $contact_number   = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $email            = $conn->real_escape_string($_POST['email'] ?? '');
    $room_number      = $conn->real_escape_string($_POST['room_number'] ?? '');
    $special_requests = $conn->real_escape_string($_POST['special_requests'] ?? '');
    $referral         = $conn->real_escape_string($_POST['referral'] ?? '');

    // SQL Query - Removed dietary_restrictions and other_dietary
    $sql = "INSERT INTO DinnerReservation 
            (reservation_date, time_slot, guests, full_name, contact_number, email, room_number, special_requests, referral_source)
            VALUES 
            ('$reservation_date', '$time_slot', $guests, '$full_name', '$contact_number', '$email', '$room_number', '$special_requests', '$referral')";

    if ($conn->query($sql) === TRUE) {
        $show_popup = true; 
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Obsidian KL Dinner Reservation</title>
    <style>
        body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f4f4f4; color: #333; }
        
        .top-nav { max-width: 600px; margin: 0 auto 15px auto; }
        .btn-back { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .btn-back:hover { color: #000; border-color: #000; }

        h2 { text-align: center; color: #1a1a1a; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        
        label { font-weight: bold; margin-top: 15px; display: block; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        input, select, textarea { width: 100%; padding: 12px; margin: 8px 0; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box;}
        
        button.submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; }
        button.submit-btn:hover { background: #444; }

        /* --- POPUP OVERLAY --- */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            display: <?php echo $show_popup ? 'flex' : 'none'; ?>; 
            justify-content: center; align-items: center; z-index: 1000;
        }
        .popup-card {
            background: white; padding: 40px; border-radius: 20px; text-align: center;
            max-width: 400px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .popup-card h3 { font-size: 24px; margin-bottom: 10px; color: #1a1a1a; }
        .popup-card p { color: #666; margin-bottom: 25px; }
        .btn-main { display: inline-block; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="hotel.php" class="btn-back">← Back to Main Page</a>
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

    <label>Referral:</label>
    <select name="referral">
        <option value="Social Media">Social Media</option>
        <option value="Search Engine">Search Engine</option>
        <option value="Word of Mouth">Word of Mouth</option>
    </select>

    <button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size: 50px; color: #b8860b; margin-bottom: 15px;">✧</div>
        <h3>Thank You!</h3>
        <p>Your dinner reservation has been successfully submitted. We look forward to hosting you at The Obsidian.</p>
        <a href="hotel.php" class="btn-main">Back to Main</a>
    </div>
</div>

</body>
</html>