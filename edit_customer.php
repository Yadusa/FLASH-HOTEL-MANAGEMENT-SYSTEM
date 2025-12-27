<?php
session_start();
require_once "db.php";

$customer = null;

// 1. Fetching Customer Data
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
}

// 2. Handling the Update (Backend Logic)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_customer'])) {
    $id      = (int)$_POST['id'];
    $name    = trim($_POST['cust_name']);
    $email   = trim($_POST['cust_email']);
    $contact = trim($_POST['contact_number']);

    if ($id > 0) {
        $update_sql = "UPDATE customer SET cust_name = ?, cust_email = ?, contact_number = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $name, $email, $contact, $id);
        
        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: customers.php?updated=1");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }

        if ($stmt->execute()) {
           if ($stmt->affected_rows > 0) {
        // Success: Something actually changed
           header("Location: customers.php?updated=1");
         exit;
        } else {
        // No rows changed (maybe the data was the same as before?)
          echo "No changes were made to the record.";
        }
       } else {
          echo "SQL Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="customer.css">
</head>
<body>
<div class="main-content">
    <h3>Edit Customer Profile</h3>

    <?php if (!$customer): ?>
        <div style="text-align:center;">
            <p style="color:#b00;">Customer not found or invalid ID.</p>
            <a href="customers.php">Back to List</a>
        </div>
    <?php else: ?>
        <form method="post" action="" onsubmit="return confirm('Save these changes?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($customer['id']); ?>">

            <label for="cust_name">Full Name</label>
            <input type="text" id="cust_name" name="cust_name" 
                   value="<?= htmlspecialchars($customer['cust_name'] ?? ''); ?>" required>

            <label for="cust_email">Email Address</label>
            <input type="email" id="cust_email" name="cust_email" 
                   value="<?= htmlspecialchars($customer['cust_email'] ?? ''); ?>" required>

            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" 
                   value="<?= htmlspecialchars($customer['contact_number'] ?? ''); ?>" required>

            <button type="submit" name="update_customer">Update Customer</button>
            <a href="customers.php" class="cancel-link">Cancel and Go Back</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>