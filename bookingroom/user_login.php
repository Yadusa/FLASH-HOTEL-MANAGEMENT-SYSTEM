<?php
session_start();
require_once "db.php";

$username = $_POST['username'];
$password = $_POST['password'];
$room = $_POST['room'];

$stmt = $conn->prepare("SELECT * FROM customer WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password_hash'])) {

        $_SESSION['customer'] = $user['username'];

        if (!empty($room)) {
            header("Location: booking-form.html?room=" . $room);
        } else {
            header("Location: ../hotel.html");
        }
        exit;
    }
}

echo "Invalid username or password";
$conn->close();
?>
