<?php
// ---------------------------
// Database credentials
// ---------------------------
$host = "localhost";
$dbname = "flashhotel"; // Replace with your DB name
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// Handle form submission
// ---------------------------
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_date = $conn->real_escape_string($_POST['reservation_date']);
    $time_slot = $conn->real_escape_string($_POST['time_slot']);
    $guests = (int)$_POST['guests'];
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $dietary = $conn->real_escape_string($_POST['dietary']);
    $other_dietary = $conn->real_escape_string($_POST['other_dietary']);
    $special_requests = $conn->real_escape_string($_POST['special_requests']);
    $referral = $conn->real_escape_string($_POST['referral']);

    // Insert into database
    $sql = "INSERT INTO DinnerReservation 
        (reservation_date, time_slot, guests, full_name, contact_number, email, room_number, dietary_restrictions, other_dietary, special_requests, referral_source)
        VALUES 
        ('$reservation_date', '$time_slot', $guests, '$full_name', '$contact_number', '$email', '$room_number', '$dietary', '$other_dietary', '$special_requests', '$referral')";

    if ($conn->query($sql) === TRUE) {
        $message = "Dinner reservation submitted successfully!";
    } else {
        $message = "Error: " . $conn->error;
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
        body { font-family: Arial, sans-serif; padding: 20px; background: #f7f7f7; }
        h2 { text-align: center; color: #333; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;}
        button { padding: 12px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .message { text-align: center; margin-bottom: 20px; color: green; font-weight: bold; }
        label { font-weight: bold; margin-top: 10px; display: block; }
    </style>
</head>
<body>

<h2>Obsidian KL Dinner Reservation</h2>

<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<form method="post" action="">
    <label>Date of Reservation:</label>
    <input type="date" name="reservation_date" required>

    <label>Preferred Time Slot:</label>
    <select name="time_slot" required>
        <option value="6:00 PM">6:00 PM</option>
        <option value="6:30 PM">6:30 PM</option>
        <option value="7:00 PM">7:00 PM</option>
        <option value="7:30 PM">7:30 PM</option>
        <option value="8:00 PM">8:00 PM</option>
        <option value="8:30 PM">8:30 PM</option>
        <option value="9:00 PM">9:00 PM</option>
        <option value="9:30 PM">9:30 PM</option>
    </select>

    <label>Number of Guests (including yourself):</label>
    <input type="number" name="guests" min="1" required>

    <label>Full Name for Reservation:</label>
    <input type="text" name="full_name" required>

    <label>Contact Number:</label>
    <input type="text" name="contact_number" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Room Number:</label>
    <input type="text" name="room_number">

    <label>Dietary Restrictions / Allergies:</label>
    <select name="dietary">
        <option value="None">None</option>
        <option value="Vegetarian">Vegetarian</option>
        <option value="Vegan">Vegan</option>
        <option value="Gluten-Free">Gluten-Free</option>
        <option value="Nut Allergy">Nut Allergy</option>
        <option value="Shellfish Allergy">Shellfish Allergy</option>
        <option value="Other">Other</option>
    </select>

    <label>If 'Other' dietary restrictions, please specify:</label>
    <input type="text" name="other_dietary">

    <label>Any special requests (occasion, seating, etc.):</label>
    <textarea name="special_requests" rows="3"></textarea>

    <label>How did you hear about The Obsidian KL?</label>
    <select name="referral">
        <option value="Social Media">Social Media</option>
        <option value="Search Engine">Search Engine</option>
        <option value="Word of Mouth">Word of Mouth</option>
        <option value="Online Review/Article">Online Review/Article</option>
        <option value="Other">Other</option>
    </select>

    <button type="submit">Submit Reservation</button>
</form>

</body>
</html>
