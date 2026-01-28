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
    // Capture and Sanitize inputs
    $reservation_date = $_POST['reservation_date'] ?? '';
    $time_slot        = $_POST['time_slot'] ?? '';
    $guests           = (int)($_POST['guests'] ?? 0);
    $full_name        = htmlspecialchars($_POST['full_name'] ?? '');
    $contact_number   = htmlspecialchars($_POST['contact_number'] ?? '');
    $email            = htmlspecialchars($_POST['email'] ?? '');
    $room_number      = htmlspecialchars($_POST['room_number'] ?? '');
    $special_requests = htmlspecialchars($_POST['special_requests'] ?? '');
    $referral         = htmlspecialchars($_POST['referral'] ?? '');

    // Database Insert Logic
    $stmt = $conn->prepare("INSERT INTO dinner_reservations (reservation_date, time_slot, guests, full_name, contact_number, email, room_number, special_requests, referral) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssss", $reservation_date, $time_slot, $guests, $full_name, $contact_number, $email, $room_number, $special_requests, $referral);

    if ($stmt->execute()) {
        $show_popup = true; // ✅ SHOW POPUP only on success
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
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
        .top-nav { max-width: 600px; margin: 0 auto 15px; }
        .back-link { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; transition: 0.3s; }
        .back-link:hover { color: #000; border-color: #000; }
        h2 { text-align: center; margin-bottom: 25px; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        label { font-weight: bold; margin-top: 15px; display: block; font-size: 13px; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 8px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        .submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; }
        .submit-btn:hover { background: #444; }
        .popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); display: <?php echo $show_popup ? 'flex' : 'none'; ?>; justify-content: center; align-items: center; z-index: 2000; }
        .popup-card { background: white; padding: 40px; border-radius: 20px; text-align: center; max-width: 450px; width: 90%; animation: popDown 0.4s ease-out; }
        @keyframes popDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .popup-btn { padding: 12px 35px; background: #1a1a1a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .popup-btn:hover { background: #b8860b; }
    </style>
</head>

<body>

<h2>The Obsidian KL Reservation</h2>
<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main</a>
</div>

<form method="post">
    <label>Date of Reservation</label>
    <input type="date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>">

    <label>Time Slot</label>
    <select name="time_slot" required>
        <option value="6:00 PM">6:00 PM</option>
        <option value="7:00 PM">7:00 PM</option>
        <option value="8:00 PM">8:00 PM</option>
        <option value="9:00 PM">9:00 PM</option>
    </select>

    <label>Guests</label>
    <input type="number" name="guests" min="1" value="2" required>

    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Contact Number</label>
    <input type="text" name="contact_number" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Room Number</label>
    <input type="text" name="room_number" placeholder="Enter N/A if not staying">

    <label>Special Requests</label>
    <textarea name="special_requests" rows="3" placeholder="Allergies, seating preference..."></textarea>

    <label>How did you hear about us?</label>
    <select name="referral">
        <option>Social Media</option>
        <option>Search Engine</option>
        <option>Word of Mouth</option>
    </select>

    <button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size:50px;color:#b8860b;">✧</div>
        <h3>Thank You!</h3>
        <p>Your dinner reservation has been successfully submitted.</p>
        <button class="popup-btn" onclick="closePopup()">Back to Main</button>
    </div>
</div>

<script>
function closePopup() {
    window.location.href = "../hotel.php";
}
</script>

</body>
</html>