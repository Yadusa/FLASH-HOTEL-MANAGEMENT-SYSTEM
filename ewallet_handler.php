<?php
require 'connect.php';
$ref = $_GET['ref'] ?? '';
$provider = $_GET['provider'] ?? 'ewallet';

if (!$ref) {
    exit('Missing reference.');
}

// Simulate a short "provider" page with confirm button
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>eWallet - <?=htmlspecialchars($provider)?></title></head>
<body>
  <h2>Pay with <?=htmlspecialchars($provider)?></h2>
  <p>Reference: <?=htmlspecialchars($ref)?></p>
  <form method="post" action="ewallet_handler.php">
    <input type="hidden" name="ref" value="<?=htmlspecialchars($ref)?>">
    <input type="hidden" name="provider" value="<?=htmlspecialchars($provider)?>">
    <button type="submit" name="action" value="confirm">Confirm Payment (simulate)</button>
    <button type="submit" name="action" value="cancel">Cancel</button>
  </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ref = $_POST['ref'] ?? '';
    $provider = $_POST['provider'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($action === 'confirm') {
        // mark payment success
        $stmt = $pdo->prepare("UPDATE payments SET status = 'success' WHERE reference_no = :ref");
        $stmt->execute([':ref'=>$ref]);
        header("Location: success.php?ref=" . urlencode($ref));
        exit;
    } else {
        $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE reference_no = :ref");
        $stmt->execute([':ref'=>$ref]);
        echo "<p>Payment cancelled.</p>";
    }
}
?>
</body>
</html>
