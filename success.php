<?php
require 'connect.php';
$ref = $_GET['ref'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM payments WHERE reference_no = :ref LIMIT 1");
$stmt->execute([':ref'=>$ref]);
$pay = $stmt->fetch();
if (!$pay) {
    exit('Payment not found.');
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payment Result</title></head><body>
  <h2>Payment Result</h2>
  <p>Reference: <?=htmlspecialchars($pay['reference_no'])?></p>
  <p>Amount: MYR <?=number_format($pay['amount'],2)?></p>
  <p>Method: <?=htmlspecialchars($pay['method'])?> <?= $pay['ewallet_provider'] ? '(' . htmlspecialchars($pay['ewallet_provider']) .')' : '' ?></p>
  <p>Status: <?=htmlspecialchars($pay['status'])?></p>
  <p>Created: <?=htmlspecialchars($pay['created_at'])?></p>
</body></html>
