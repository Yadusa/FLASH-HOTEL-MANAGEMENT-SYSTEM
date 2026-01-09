<?php
// ---------------------------
// Handle form submission ONLY
// ---------------------------
$show_popup = false;
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Handle CV upload
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === 0) {

        $file_name = time() . "_" . basename($_FILES['cv_file']['name']);
        $cv_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $cv_file)) {
            // SUCCESS — show popup
            $show_popup = true;
        } else {
            $error_msg = "Failed to upload CV. Please try again.";
        }

    } else {
        $error_msg = "Please upload your CV.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work with Us - Obsidian KL</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            background: #f4f4f4;
            color: #333;
        }

        .top-nav {
            max-width: 700px;
            margin: 0 auto 15px auto;
        }

        .back-link {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        .back-link:hover {
            color: #000;
            border-color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border: 1px solid #eee;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .checkbox-item input {
            width: auto;
            margin: 0;
        }

        button.submit-btn {
            width: 100%;
            padding: 15px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            font-weight: 600;
        }

        button.submit-btn:hover {
            background: #444;
        }

        .error-banner {
            background: #fee2e2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* --- POPUP --- */
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
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .popup-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<h2>Work with Us – Obsidian Kuala Lumpur</h2>
<div class="top-nav">
    <a href="../hotel.php" class="back-link">← Back to Main Page</a>
</div>

<?php if ($error_msg) echo "<div class='error-banner'>$error_msg</div>"; ?>

<form method="post" action="" enctype="multipart/form-data">
    <label>Full Name *</label>
    <input type="text" name="full_name" required>

    <label>Email *</label>
    <input type="email" name="email" required>

    <label>Phone Number *</label>
    <input type="text" name="phone_number" required>

    <label>Upload CV / Resume *</label>
    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required>

    <label>Interested Position(s)</label>
    <div class="checkbox-container">
        <div class="checkbox-item"><input type="checkbox" name="positions[]"> Front Desk</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]"> Housekeeping</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]"> Kitchen</div>
        <div class="checkbox-item"><input type="checkbox" name="positions[]"> Management</div>
    </div>

    <button type="submit" class="submit-btn">Submit Application</button>
</form>

<div class="popup-overlay">
    <div class="popup-card">
        <button class="popup-close" onclick="closePopup()">✕</button>

        <div style="font-size:50px;">✉</div>
        <h3>Application Received!</h3>
        <p>Thank you for applying. Our HR team will review your application.</p>
        <a href="../hotel.php" class="popup-btn">Back to Main</a>
    </di
