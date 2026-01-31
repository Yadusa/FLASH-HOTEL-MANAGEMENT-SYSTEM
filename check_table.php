<?php
require_once "db.php";

$result = $conn->query("DESCRIBE customer");
if ($result) {
    echo "Customer table columns:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
