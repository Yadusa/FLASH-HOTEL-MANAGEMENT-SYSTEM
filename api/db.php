<?php
$conn = new mysqli("localhost", "root", "", "flashhotel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
