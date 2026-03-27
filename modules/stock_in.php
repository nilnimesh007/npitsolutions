<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/InventoryService.php';

$pdo = db();
$vendors = $pdo->query('SELECT id, vendor_name FROM vendors ORDER BY vendor_name')->fetchAll();
$products = $pdo->query('SELECT id, product_name FROM products ORDER BY product_name')->fetchAll();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = [];
    foreach ($_POST['product_id'] ?? [] as $i => $productId) {
        $items[] = [
            'product_id' => (int) $productId,
            'quantity' => (float) ($_POST['quantity'][$i] ?? 0),
        ];
    }

    $service = new InventoryService();
    $invoiceId = $service->stockIn((int) $_POST['vendor_id'], trim($_POST['invoice_no']), $_POST['invoice_date'], $items);
    $message = "Stock in saved successfully. Invoice ID: {$invoiceId}";
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Stock In</title>
</head>
<body>
<h2>Stock In (Multiple Product in Single Invoice)</h2>
<?php if ($message): ?><p><strong><?= htmlspecialchars($message) ?></strong></p><?php endif; ?>
<form method="post">
    <label>Vendor</label>
    <select name="vendor_id" required>
        <?php foreach ($vendors as $v): ?>
            <option value="<?= (int) $v['id'] ?>"><?= htmlspecialchars($v['vendor_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Invoice Number</label>
    <input name="invoice_no" required>

    <label>Invoice Date</label>
    <input type="date" name="invoice_date" required>

    <h4>Items</h4>
    <table id="items" border="1" cellpadding="6">
        <tr><th>Product</th><th>Quantity</th><th></th></tr>
        <tr>
            <td>
                <select name="product_id[]" required>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" step="0.01" min="0.01" name="quantity[]" required></td>
            <td><button type="button" onclick="removeRow(this)">x</button></td>
        </tr>
    </table>
    <button type="button" onclick="addRow()">+ Add Product</button>
    <button type="submit">Save</button>
</form>

<p><a href="../index.php">Back</a></p>
<script>
const productSelectHtml = `<?= str_replace("\n", '', addslashes('<select name="product_id[]" required>' . implode('', array_map(fn($p) => '<option value="' . (int)$p['id'] . '">' . htmlspecialchars($p['product_name']) . '</option>', $products)) . '</select>')) ?>`;
function addRow() {
  const tr = document.createElement('tr');
  tr.innerHTML = `<td>${productSelectHtml}</td><td><input type="number" step="0.01" min="0.01" name="quantity[]" required></td><td><button type="button" onclick="removeRow(this)">x</button></td>`;
  document.getElementById('items').appendChild(tr);
}
function removeRow(button) {
  const rows = document.querySelectorAll('#items tr');
  if (rows.length > 2) button.closest('tr').remove();
}
</script>
</body>
</html>
