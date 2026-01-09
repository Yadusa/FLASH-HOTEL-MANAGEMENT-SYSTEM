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
    <div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
    
    </div>
</head>
<body>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<h2>Event Reservations</h2>

<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<form method="post" action="">
    <div class="form-grid">
        <div class="full-width">
            <label>Contact Person Full Name</label>
            <input type="text" name="contact_name" placeholder="E.g. Alexander Vance" required>
        </div>

        <div>
            <label>Email Address</label>
            <input type="email" name="email" placeholder="vance@obsidian.com" required>
        </div>

        <div>
            <label>Phone Number</label>
            <input type="text" name="phone_number" placeholder="+60..." required>
        </div>

        <div class="full-width">
            <label>Event/Meeting Title</label>
            <input type="text" name="event_title" placeholder="Annual Strategy Summit" required>
        </div>

        <div>
            <label>Date of Event</label>
            <input type="date" name="event_date" required>
        </div>

        <div>
            <label>Attendees</label>
            <input type="number" name="attendees" min="1" placeholder="Number of Guests" required>
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
            <label>Type of Event</label>
            <select name="event_type" required>
                <option value="" disabled selected>Select Event Type</option>
                <option value="Internal Team Meeting">Internal Team Meeting</option>
                <option value="Client Presentation">Client Presentation</option>
                <option value="Workshop/Training Session">Workshop/Training Session</option>
                <option value="Conference/Seminar">Conference/Seminar</option>
                <option value="Social Event/Reception">Social Event/Reception</option>
            </select>
        </div>

        <div>
            <label>Preferred Space Type</label>
            <select name="space_type" required>
                <option value="" disabled selected>Select Space</option>
                <option value="Small Meeting Room">Small Meeting Room (1-8)</option>
                <option value="Medium Meeting Room">Medium Meeting Room (9-20)</option>
                <option value="Large Conference Hall">Large Conference Hall (21+)</option>
                <option value="Flexible Event Space">Flexible Event Space</option>
            </select>
        </div>

        <div class="full-width">
            <label>Required Equipment & Setup</label>
            <div class="checkbox-group">
                <div class="checkbox-item"><input type="checkbox" name="equipment_setup[]" value="Projector"> Projector & Screen</div>
                <div class="checkbox-item"><input type="checkbox" name="equipment_setup[]" value="Whiteboard"> Whiteboard</div>
                <div class="checkbox-item"><input type="checkbox" name="equipment_setup[]" value="Video Conf"> Video Conferencing</div>
                <div class="checkbox-item"><input type="checkbox" name="equipment_setup[]" value="Audio"> Audio/Mic System</div>
            </div>
        </div>

        <div class="full-width">
            <label>Catering Requirements</label>
            <div class="checkbox-group">
                <div class="checkbox-item"><input type="checkbox" name="catering[]" value="Coffee/Tea"> Coffee/Tea Service</div>
                <div class="checkbox-item"><input type="checkbox" name="catering[]" value="Snacks"> Light Snacks</div>
                <div class="checkbox-item"><input type="checkbox" name="catering[]" value="Lunch"> Lunch Buffet</div>
                <div class="checkbox-item"><input type="checkbox" name="catering[]" value="Dinner"> Dinner Service</div>
            </div>
        </div>

        <div>
            <label>Urgency Level</label>
            <select name="urgency">
                <option value="Not Urgent">Standard Processing</option>
                <option value="3">Priority (Level 3)</option>
                <option value="5">Urgent (Level 5)</option>
                <option value="Immediate Need">Immediate / Same Day</option>
            </select>
        </div>

        <div class="full-width">
            <label>Special Requests</label>
            <textarea name="special_requests" rows="3" placeholder="Dietary restrictions, specific layout needs..."></textarea>
        </div>
    </div>

    <button type="submit">Confirm Booking Request</button>
</form>

</body>
</html>
