<?php
session_start();
require_once "db.php";

// 1. Only run this logic if the form was actually submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Use ?? '' to safely handle empty values and prevent warnings
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $room     = $_POST['room'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['customer'] = $user['username'];

            if (!empty($room)) {
                header("Location: booking-form.html?room=" . urlencode($room));
            } else {
                header("Location: hotel.html");
            }
            exit;
        }
    }

    // 2. Use a JavaScript alert for failed login so the user stays on the same page
    echo "<script>
            alert('Invalid username or password');
            window.history.back();
          </script>";
    exit;
}

// 3. Optional: If someone tries to access this file directly without POST, 
// redirect them back to the login form
header("Location: user_login.html"); 
exit;
?>
