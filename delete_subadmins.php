<?php
include "db.php";

$id = $_GET['id'];

// Only delete subadmins
$sql = "DELETE FROM admins WHERE id = '$id' AND role = 'subadmin'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Subadmin deleted successfully!'); window.location='manage_subadmins.php';</script>";
} else {
    echo "Error deleting subadmin: " . mysqli_error($conn);
}
?>
