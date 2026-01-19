<?php
session_start();

// 1. If the user clicked "Confirm Logout" (The 'Yes' button)
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    // Actually destroy the session
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// 2. If the user clicked "Cancel" (The 'No' button)
if (isset($_GET['cancel'])) {
    // Send them back to the dashboard (or wherever they came from)
    header("Location: dashboard.php");
    exit;
}

// 3. Otherwise, show the Confirmation Popup
// (This HTML matches your Obsidian Theme style)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Logout | FLASH Hotel Admin</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f6f9; /* Light gray background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Overlay effect */
        .overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dimmed background */
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logout-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 90%;
            border-top: 5px solid #b89241; /* Gold Accent */
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .icon-box {
            font-size: 50px;
            color: #b89241;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        p {
            color: #666;
            margin-bottom: 30px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-yes {
            background-color: #dc3545; /* Red for Logout */
            color: white;
        }
        .btn-yes:hover { background-color: #c82333; }

        .btn-no {
            background-color: #e2e6ea;
            color: #333;
        }
        .btn-no:hover { background-color: #dbe0e5; }

    </style>
</head>
<body>

    <div class="overlay">
        <div class="logout-card">
            <div class="icon-box"></div>
            <h2>Sign Out?</h2>
            <p>Are you sure you want to end your session?</p>
            
            <div class="btn-group">
                <a href="logout.php?cancel=true" class="btn btn-no">Cancel</a>
                
                <a href="logout.php?confirm=true" class="btn btn-yes">Yes, Logout</a>
            </div>
        </div>
    </div>

</body>
</html>
