<?php
session_start();

// Remove only customer-related session variables
unset($_SESSION['customer_id']);
unset($_SESSION['customer_username']);

// Optionally destroy session if you only use it for customers
// session_destroy(); 

// Redirect back to hotel page or login page
header("Location: hotel.php");
exit();
