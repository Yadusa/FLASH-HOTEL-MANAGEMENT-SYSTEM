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
    $contact_name = $conn->real_escape_string($_POST['contact_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $event_title = $conn->real_escape_string($_POST['event_title']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $attendees = (int)$_POST['attendees'];
    $event_type = $conn->real_escape_string($_POST['event_type']);
    $space_type = $conn->real_escape_string($_POST['space_type']);
    $equipment_setup = isset($_POST['equipment_setup']) ? implode(", ", $_POST['equipment_setup']) : '';
    $catering = isset($_POST['catering']) ? implode(", ", $_POST['catering']) : '';
    $urgency = $conn->real_escape_string($_POST['urgency']);
    $special_requests = $conn->real_escape_string($_POST['special_requests']);

    // Insert into database
    $sql = "INSERT INTO EventSpaceBooking
        (contact_name, email, phone_number, event_title, event_date, start_time, end_time, attendees, event_type, space_type, equipment_setup, catering_requirements, urgency_level, special_requests)
        VALUES
        ('$contact_name', '$email', '$phone_number', '$event_title', '$event_date', '$start_time', '$end_time', $attendees, '$event_type', '$space_type', '$equipment_setup', '$catering', '$urgency', '$special_requests')";

    if ($conn->query($sql) === TRUE) {
        $message = "Event space booking submitted successfully!";
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
    <title>Obsidian KL Event Space Booking</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f7f7f7; }
        h2 { text-align: center; color: #333; }
        form { max-width: 700px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;}
        button { padding: 12px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .message { text-align: center; margin-bottom: 20px; color: green; font-weight: bold; }
        label { font-weight: bold; margin-top: 10px; display: block; }
    </style>
</head>
<body>

<h2>Obsidian KL Event Space Booking</h2>

<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<form method="post" action="">
    <label>Contact Person Full Name:</label>
    <input type="text" name="contact_name" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Phone Number:</label>
    <input type="text" name="phone_number" required>

    <label>Event/Meeting Title:</label>
    <input type="text" name="event_title" required>

    <label>Date of Event/Meeting:</label>
    <input type="date" name="event_date" required>

    <label>Start Time:</label>
    <input type="time" name="start_time" required>

    <label>End Time:</label>
    <input type="time" name="end_time" required>

    <label>Estimated Number of Attendees:</label>
    <input type="number" name="attendees" min="1" required>

    <label>Type of Event:</label>
    <select name="event_type" required>
        <option value="Internal Team Meeting">Internal Team Meeting</option>
        <option value="Client Presentation">Client Presentation</option>
        <option value="Workshop/Training Session">Workshop/Training Session</option>
        <option value="Conference/Seminar">Conference/Seminar</option>
        <option value="Social Event/Reception">Social Event/Reception</option>
        <option value="Other">Other</option>
    </select>

    <label>Preferred Space Type:</label>
    <select name="space_type" required>
        <option value="Small Meeting Room (1-8 people)">Small Meeting Room (1-8 people)</option>
        <option value="Medium Meeting Room (9-20 people)">Medium Meeting Room (9-20 people)</option>
        <option value="Large Conference Hall (21+ people)">Large Conference Hall (21+ people)</option>
        <option value="Flexible Event Space">Flexible Event Space</option>
        <option value="No Preference">Do not have a preference</option>
    </select>

    <label>Required Equipment/Setup:</label>
    <input type="checkbox" name="equipment_setup[]" value="Projector and Screen"> Projector and Screen
    <input type="checkbox" name="equipment_setup[]" value="Whiteboard/Flipchart"> Whiteboard/Flipchart
    <input type="checkbox" name="equipment_setup[]" value="Video Conferencing System"> Video Conferencing System
    <input type="checkbox" name="equipment_setup[]" value="Microphone and Speakers"> Microphone and Speakers
    <input type="checkbox" name="equipment_setup[]" value="Laptop/Computer Access"> Laptop/Computer Access
    <input type="checkbox" name="equipment_setup[]" value="Specialized Seating Arrangement"> Specialized Seating Arrangement (U-shape, Classroom)

    <label>Catering Requirements:</label>
    <input type="checkbox" name="catering[]" value="Coffee/Tea Service"> Coffee/Tea Service
    <input type="checkbox" name="catering[]" value="Light Snacks"> Light Snacks (pastries, cookies)
    <input type="checkbox" name="catering[]" value="Lunch Buffet"> Lunch Buffet
    <input type="checkbox" name="catering[]" value="Dinner Service"> Dinner Service
    <input type="checkbox" name="catering[]" value="None"> None

    <label>Level of Urgency for this Booking:</label>
    <select name="urgency">
        <option value="Not Urgent">Not Urgent</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="Immediate Need">Immediate Need</option>
    </select>

    <label>Special Requests or Additional Notes:</label>
    <textarea name="special_requests" rows="4"></textarea>

    <button type="submit">Submit Booking</button>
</form>

</body>
</html>
