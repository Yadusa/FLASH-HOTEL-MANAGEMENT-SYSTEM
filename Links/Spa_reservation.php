<?php
// ---------------------------
// Database credentials
// ---------------------------
$host = "localhost";       // Usually localhost
$dbname = "flash_hotel"; // Replace with your database name
$username = "root";        // Usually root
$password = "";            // Usually empty in XAMPP

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
    // Sanitize inputs to prevent SQL injection
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

    // Insert data into database
    $sql = "INSERT INTO Spa 
        (full_name, contact_number, email, room_number, appointment_date, time_slot, service, duration, guests, concerns)
        VALUES 
        ('$full_name', '$contact_number', '$email', '$room_number', '$appointment_date', '$time_slot', '$service', '$duration', $guests, '$concerns')";

    if ($conn->query($sql) === TRUE) {
        $message = "Reservation submitted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Obsidian KL Spa Reservation</title>
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

<h2>Obsidian KL Spa Reservation</h2>

<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<form method="post" action="">
    <label>Full Name:</label>
    <input type="text" name="full_name" required>

    <label>Contact Number:</label>
    <input type="text" name="contact_number" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Room Number:</label>
    <input type="text" name="room_number" required>

    <label>Preferred Date of Appointment:</label>
    <input type="date" name="appointment_date" required 
           min="2026-01-01" max="2026-05-31">

    <label>Preferred Time Slot:</label>
    <select name="time_slot" required>
        <option value="10:00 AM - 12:00 PM">10:00 AM - 12:00 PM</option>
        <option value="12:00 PM - 2:00 PM">12:00 PM - 2:00 PM</option>
        <option value="2:00 PM - 4:00 PM">2:00 PM - 4:00 PM</option>
        <option value="4:00 PM - 6:00 PM">4:00 PM - 6:00 PM</option>
        <option value="6:00 PM - 8:00 PM">6:00 PM - 8:00 PM</option>
    </select>

    <label>Select Desired Service:</label>
    <select name="service" required>
        <option value="Traditional Malay Massage">Traditional Malay Massage</option>
        <option value="Aromatherapy Oil Massage">Aromatherapy Oil Massage</option>
        <option value="Deep Tissue Massage">Deep Tissue Massage</option>
        <option value="Obsidian Signature Spa Package (2 hours)">Obsidian Signature Spa Package (2 hours)</option>
        <option value="Foot Reflexology">Foot Reflexology</option>
        <option value="Facial Treatment">Facial Treatment</option>
    </select>

    <label>Duration of Service:</label>
    <select name="duration" required>
        <option value="60 Minutes">60 Minutes</option>
        <option value="90 Minutes">90 Minutes</option>
        <option value="120 Minutes (2 hours)">120 Minutes (2 hours)</option>
    </select>

    <label>Number of Guests (including yourself):</label>
    <input type="number" name="guests" min="1" required>

    <label>Are there any specific concerns or focus areas for your treatment?</label>
    <textarea name="concerns" rows="4"></textarea>

    <button type="submit">Submit Reservation</button>
</form>

</body>
</html>
