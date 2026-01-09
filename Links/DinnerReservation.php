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
    // Sanitize inputs
    $reservation_date = $conn->real_escape_string($_POST['reservation_date'] ?? '');
    $time_slot        = $conn->real_escape_string($_POST['time_slot'] ?? '');
    $guests           = (int)($_POST['guests'] ?? 0);
    $full_name        = $conn->real_escape_string($_POST['full_name'] ?? '');
    $contact_number   = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $email            = $conn->real_escape_string($_POST['email'] ?? '');
    $room_number      = $conn->real_escape_string($_POST['room_number'] ?? '');
    $special_requests = $conn->real_escape_string($_POST['special_requests'] ?? '');
    $referral         = $conn->real_escape_string($_POST['referral'] ?? '');

    // 👉 If you want DB insert later, put it here

    $show_popup = true; // ✅ SHOW POPUP
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
body {
    font-family: 'Poppins', sans-serif;
    padding: 20px;
    background: #f4f4f4;
    color: #333;
}

/* Top Navigation */
.top-nav {
    max-width: 600px;
    margin: 0 auto 15px;
}
.back-link {
    text-decoration: none;
    color: #666;
    font-size: 14px;
    border-bottom: 1px solid #ccc;
    transition: 0.3s;
}
.back-link:hover {
    color: #000;
    border-color: #000;
}

h2 {
    text-align: center;
    margin-bottom: 25px;
}

form {
    max-width: 600px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

label {
    font-weight: bold;
    margin-top: 15px;
    display: block;
    font-size: 13px;
    text-transform: uppercase;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.submit-btn {
    width: 100%;
    padding: 15px;
    background: #1a1a1a;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
    transition: 0.3s;
}
.submit-btn:hover {
    background: #444;
}

/* POPUP */
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
    position: relative;
    background: white;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    max-width: 450px;
    width: 90%;
    animation: popDown 0.4s ease-out;
}

@keyframes popDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Close Button */
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

.popup-btn {
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

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>The Obsidian KL Reservation</h2>

<form method="post" id="feedbackForm">

<label>Date of Reservation</label>
<input type="date" name="reservation_date" required>

<label>Time Slot</label>
<select name="time_slot" required>
    <option value="6:00 PM">6:00 PM</option>
    <option value="7:00 PM">7:00 PM</option>
    <option value="8:00 PM">8:00 PM</option>
    <option value="9:00 PM">9:00 PM</option>
</select>

<label>Guests</label>
<input type="number" name="guests" min="1" required>

<label>Full Name</label>
<input type="text" name="full_name" required>

<label>Contact Number</label>
<input type="text" name="contact_number" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Room Number</label>
<input type="text" name="room_number">

<label>Special Requests</label>
<textarea name="special_requests" rows="3"></textarea>

<label>How did you hear about us?</label>
<select name="referral">
    <option>Social Media</option>
    <option>Search Engine</option>
    <option>Word of Mouth</option>
</select>

<button type="submit" class="submit-btn">Submit Reservation</button>
</form>

<!-- POPUP -->
<div class="popup-overlay">
    <div class="popup-card">
        <button class="popup-close" onclick="closePopup()">✕</button>
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
