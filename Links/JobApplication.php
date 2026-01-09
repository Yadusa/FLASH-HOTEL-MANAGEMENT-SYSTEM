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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// Handle form submission
// ---------------------------
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $positions = isset($_POST['positions']) ? implode(", ", $_POST['positions']) : '';
    $work_experience = $conn->real_escape_string($_POST['work_experience']);
    $availability_date = $conn->real_escape_string($_POST['availability_date']);
    $english_proficiency = $conn->real_escape_string($_POST['english_proficiency']);
    $malay_proficiency = $conn->real_escape_string($_POST['malay_proficiency']);
    $mandarin_proficiency = $conn->real_escape_string($_POST['mandarin_proficiency']);
    $other_language = $conn->real_escape_string($_POST['other_language']);

    // Handle CV upload
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $cv_file = $upload_dir . basename($_FILES['cv_file']['name']);
    if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $cv_file)) {
        // Insert into database
        $sql = "INSERT INTO JobApplications
            (full_name, email, phone_number, cv_file, positions, work_experience, availability_date, english_proficiency, malay_proficiency, mandarin_proficiency, other_language)
            VALUES
            ('$full_name', '$email', '$phone_number', '$cv_file', '$positions', '$work_experience', '$availability_date', '$english_proficiency', '$malay_proficiency', '$mandarin_proficiency', '$other_language')";

        if ($conn->query($sql) === TRUE) {
            $message = "Application submitted successfully!";
        } else {
            $message = "Database error: " . $conn->error;
        }
    } else {
        $message = "Failed to upload CV. Make sure the file is less than 10 MB.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Work with Us - Obsidian KL</title>
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

<h2>Work with Us - Obsidian Kuala Lumpur</h2>
<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<form method="post" action="" enctype="multipart/form-data">
    <label>Full Name *</label>
    <input type="text" name="full_name" required>

    <label>Email Address *</label>
    <input type="email" name="email" required>

    <label>Phone Number *</label>
    <input type="text" name="phone_number" required>

    <label>Upload CV/Resume (Max 10MB) *</label>
    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required>

    <label>Which position(s) are you interested in? *</label>
    <input type="checkbox" name="positions[]" value="Front Desk"> Front Desk
    <input type="checkbox" name="positions[]" value="Housekeeping"> Housekeeping
    <input type="checkbox" name="positions[]" value="Kitchen"> Kitchen
    <input type="checkbox" name="positions[]" value="Management"> Management
    <input type="checkbox" name="positions[]" value="Other"> Other

    <label>Brief work experience</label>
    <textarea name="work_experience" rows="4"></textarea>

    <label>Earliest availability to start (if hired)</label>
    <input type="date" name="availability_date">

    <label>English Proficiency</label>
    <select name="english_proficiency">
        <option value="">Select</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Malay Proficiency</label>
    <select name="malay_proficiency">
        <option value="">Select</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Mandarin Proficiency</label>
    <select name="mandarin_proficiency">
        <option value="">Select</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Other Language (Please specify)</label>
    <input type="text" name="other_language">

    <button type="submit">Submit Application</button>
</form>

</body>
</html>
