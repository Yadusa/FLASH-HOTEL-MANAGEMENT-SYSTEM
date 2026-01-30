<?php
require_once('db.php');

echo "Testing database connection...\n";

if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error . "\n";
    exit(1);
}

echo "Connection successful!\n";

// Test if rooms table exists
$result = $conn->query("SHOW TABLES LIKE 'rooms'");
if ($result->num_rows > 0) {
    echo "Rooms table exists.\n";

    // Check if rooms table has room_price column
    $columns = $conn->query("DESCRIBE rooms");
    $has_room_price = false;
    while ($col = $columns->fetch_assoc()) {
        if ($col['Field'] == 'room_price') {
            $has_room_price = true;
            break;
        }
    }

    if ($has_room_price) {
        echo "Rooms table has room_price column.\n";
    } else {
        echo "ERROR: Rooms table missing room_price column!\n";
    }

} else {
    echo "ERROR: Rooms table does not exist!\n";
}

// Test if bookings table exists
$result = $conn->query("SHOW TABLES LIKE 'bookings'");
if ($result->num_rows > 0) {
    echo "Bookings table exists.\n";
} else {
    echo "ERROR: Bookings table does not exist!\n";
}

$conn->close();
?>
