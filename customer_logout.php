<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to hotel main page
header("Location: hotel.php");
exit();
