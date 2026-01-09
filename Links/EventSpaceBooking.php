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

$show_popup = false; // Logic to trigger popup

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

    $sql = "INSERT INTO EventSpaceBooking
        (contact_name, email, phone_number, event_title, event_date, start_time, end_time, attendees, event_type, space_type, equipment_setup, catering_requirements, urgency_level, special_requests)
        VALUES
        ('$contact_name', '$email', '$phone_number', '$event_title', '$event_date', '$start_time', '$end_time', $attendees, '$event_type', '$space_type', '$equipment_setup', '$catering', '$urgency', '$special_requests')";

    if ($conn->query($sql) === TRUE) {
        $show_popup = true; // Set to true to show the overlay
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian KL Event Space Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f4f4f4; color: #333; }
        
        .top-nav { max-width: 700px; margin: 0 auto 15px auto; }
        .back-link { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .back-link:hover { color: #000; border-color: #000; }

        h2 { text-align: center; color: #1a1a1a; font-family: 'Playfair Display', serif; font-size: 32px; }
        form { max-width: 700px; margin: auto; background: #fff; padding: 35px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);}
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        
        label { font-weight: 600; margin-top: 10px; display: block; font-size: 13px; text-transform: uppercase; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; font-family: inherit;}
        
        .checkbox-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 10px 0; }
        .checkbox-item { font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .checkbox-item input { width: auto; margin: 0; }

        button.submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; font-weight: 600; }
        button.submit-btn:hover { background: #444; }

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
        }
        .popup-card h3 { font-size: 26px; margin-bottom: 10px; color: #1a1a1a; }
        .popup-card p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        .popup-btn { display: inline-block; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>Event Reservations</h2>

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
                <option value="Priority">Priority (Level 3)</option>
                <option value="Urgent">Urgent (Level 5)</option>
                <option value="Immediate Need">Immediate / Same Day</option>
            </select>
        </div>

        <div class="full-width">
            <label>Special Requests</label>
            <textarea name="special_requests" rows="3" placeholder="Dietary restrictions, specific layout needs..."></textarea>
        </div>
    </div>

    <button type="submit" class="submit-btn">Confirm Booking Request</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size: 60px; color: #b8860b; margin-bottom: 15px;">✧</div>
        <h3>Booking Received!</h3>
        <p>Your event space inquiry has been successfully submitted. Our events team will review your requirements and contact you within 24 hours.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>