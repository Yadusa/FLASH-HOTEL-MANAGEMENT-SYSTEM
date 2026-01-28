<?php
// Database Configuration
$host = "localhost";
$username = "root"; // Default for XAMPP/WAMP
$password = "";     // Default for XAMPP
$dbname = "obsidian_spa";

// 1. Create Connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$show_popup = false;

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and capture inputs
    $full_name = $_POST['full_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $room_number = $_POST['room_number'];
    $appointment_date = $_POST['appointment_date'];
    $time_slot = $_POST['time_slot'];
    $service = $_POST['service'];
    $guests = $_POST['guests'];
    $concerns = $_POST['concerns'];

    // 3. Prepare and Bind
    $stmt = $conn->prepare("INSERT INTO reservations (full_name, contact_number, email, room_number, appointment_date, time_slot, service, guests, concerns) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $full_name, $contact_number, $email, $room_number, $appointment_date, $time_slot, $service, $guests, $concerns);

    if ($stmt->execute()) {
        $show_popup = true;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">