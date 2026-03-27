<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/InventoryService.php';

$rows = (new InventoryService())->getCurrentStock();
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Current Stock</title></head>
<body>
<h2>Current Stock (Product Wise)</h2>
<table border="1" cellpadding="6">
    <tr><th>Product</th><th>Current Qty</th></tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['product_name']) ?></td>
            <td><?= number_format((float) $r['current_stock'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<p><a href="../index.php">Back</a></p>
</body>
</html>
