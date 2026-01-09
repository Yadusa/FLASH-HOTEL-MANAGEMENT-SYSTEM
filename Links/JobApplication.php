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

// ---------------------------
// Handle form submission
// ---------------------------
$show_popup = false; 
$error_msg = "";

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
    
    $file_name = time() . "_" . basename($_FILES['cv_file']['name']); // Added timestamp to prevent overwriting
    $cv_file = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $cv_file)) {
        // Insert into database
        $sql = "INSERT INTO JobApplications
            (full_name, email, phone_number, cv_file, positions, work_experience, availability_date, english_proficiency, malay_proficiency, mandarin_proficiency, other_language)
            VALUES
            ('$full_name', '$email', '$phone_number', '$cv_file', '$positions', '$work_experience', '$availability_date', '$english_proficiency', '$malay_proficiency', '$mandarin_proficiency', '$other_language')";

        if ($conn->query($sql) === TRUE) {
            $show_popup = true; 
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
    } else {
        $error_msg = "Failed to upload CV. Make sure the file is less than 10 MB.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work with Us - Obsidian KL</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; padding: 20px; background: #f4f4f4; color: #333; }
        
        .top-nav { max-width: 700px; margin: 0 auto 15px auto; }
        .back-link { text-decoration: none; color: #666; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .back-link:hover { color: #000; border-color: #000; }

        h2 { text-align: center; color: #1a1a1a; margin-bottom: 30px; }
        form { max-width: 700px; margin: auto; background: #fff; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        
        label { font-weight: 600; margin-top: 15px; display: block; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; margin: 8px 0; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; font-family: inherit; }
        
        /* Checkbox Styling */
        .checkbox-container { display: flex; flex-wrap: wrap; gap: 15px; background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #eee; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .checkbox-item input { width: auto; margin: 0; }

        button.submit-btn { width: 100%; padding: 15px; background: #1a1a1a; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; font-weight: 600; }
        button.submit-btn:hover { background: #444; }

        .error-banner { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; max-width: 700px; margin-left: auto; margin-right: auto; }

        /* --- POPUP OVERLAY --- */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            display: <?php echo $show_popup ? 'flex' : 'none'; ?>; 
            justify-content: center; align-items: center; z-index: 2000;
        }
        .popup-card {
            background: white; padding: 40px; border-radius: 20px; text-align: center;
            max-width: 450px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .popup-card h3 { font-size: 24px; margin-bottom: 10px; color: #1a1a1a; }
        .popup-card p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        .popup-btn { display: inline-block; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<h2>Work with Us - Obsidian Kuala Lumpur</h2>

<?php if ($error_msg) echo "<div class='error-banner'>$error_msg</div>"; ?>

<form method="post" action="" enctype="multipart/form-data">
    <label>Full Name *</label>
    <input type="text" name="full_name" placeholder="John Doe" required>

    <label>Email Address *</label>
    <input type="email" name="email" placeholder="john@example.com" required>

    <label>Phone Number *</label>
    <input type="text" name="phone_number" placeholder="+60 12-345 6789" required>

    <label>Upload CV/Resume (PDF/DOCX) *</label>
    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required>

    <label>Interested Position(s) *</label>
    <div class="checkbox-container">
        <div class="checkbox-item"><input type="checkbox" name="positions[]" value="Front Desk"> Front Desk</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]" value="Housekeeping"> Housekeeping</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]" value="Kitchen"> Kitchen</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]" value="Management"> Management</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]" value="Other"> Other</div>
    </div>

    <label>Brief Work Experience</label>
    <textarea name="work_experience" rows="4" placeholder="Tell us about your previous roles..."></textarea>

    <label>Earliest Start Date</label>
    <input type="date" name="availability_date">

    <label>English Proficiency</label>
    <select name="english_proficiency">
        <option value="">Select Level</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Malay Proficiency</label>
    <select name="malay_proficiency">
        <option value="">Select Level</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Mandarin Proficiency</label>
    <select name="mandarin_proficiency">
        <option value="">Select Level</option>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
    </select>

    <label>Other Language</label>
    <input type="text" name="other_language" placeholder="E.g. French, Japanese">

    <button type="submit" class="submit-btn">Submit Application</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <div style="font-size: 50px; color: #1a1a1a; margin-bottom: 15px;">✉</div>
        <h3>Application Received!</h3>
        <p>Thank you for your interest in joining Obsidian KL. Our HR team will review your CV and contact you if you are shortlisted for an interview.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </div>
</div>

</body>
</html>