<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/InventoryService.php';

$pdo = db();
$clients = $pdo->query('SELECT id, client_name FROM clients ORDER BY client_name')->fetchAll();
$products = $pdo->query('SELECT id, product_name FROM products ORDER BY product_name')->fetchAll();
$groups = $pdo->query('SELECT id, group_name FROM product_groups ORDER BY group_name')->fetchAll();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lines = [];
    foreach ($_POST['item_ref'] ?? [] as $i => $itemRef) {
        [$type, $id] = explode(':', $itemRef);
        $lines[] = [
            'type' => $type,
            'id' => (int) $id,
            'quantity' => (float) ($_POST['quantity'][$i] ?? 0),
        ];
    }

    $service = new InventoryService();
    $invoiceId = $service->stockOut((int) $_POST['client_id'], trim($_POST['invoice_no']), $_POST['invoice_date'], $lines);
    $message = "Stock out saved successfully. Invoice ID: {$invoiceId}";
}

$options = [];
foreach ($products as $p) {
    $options[] = sprintf('<option value="product:%d">Product: %s</option>', (int) $p['id'], htmlspecialchars($p['product_name']));
}
foreach ($groups as $g) {
    $options[] = sprintf('<option value="group:%d">Group: %s</option>', (int) $g['id'], htmlspecialchars($g['group_name']));
}
$optionHtml = implode('', $options);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Stock Out</title>
</head>
<body>
<h2>Stock Out (Multiple Product/Group in Single Invoice)</h2>
<?php if ($message): ?><p><strong><?= htmlspecialchars($message) ?></strong></p><?php endif; ?>
<form method="post">
    <label>Client</label>
    <select name="client_id" required>
        <?php foreach ($clients as $c): ?>
            <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['client_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Invoice Number</label>
    <input name="invoice_no" required>

    <label>Invoice Date</label>
    <input type="date" name="invoice_date" required>

    <h4>Items (product or product group)</h4>
    <table id="items" border="1" cellpadding="6">
        <tr><th>Product/Group</th><th>Quantity</th><th></th></tr>
        <tr>
            <td>
                <select name="item_ref[]" required><?= $optionHtml ?></select>
            </td>
            <td><input type="number" step="0.01" min="0.01" name="quantity[]" required></td>
            <td><button type="button" onclick="removeRow(this)">x</button></td>
        </tr>
    </table>
    <button type="button" onclick="addRow()">+ Add Line</button>
    <button type="submit">Save</button>
</form>

<p><a href="../index.php">Back</a></p>
<script>
const optionHtml = `<?= str_replace("\n", '', addslashes($optionHtml)) ?>`;
function addRow() {
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><select name="item_ref[]" required>${optionHtml}</select></td><td><input type="number" step="0.01" min="0.01" name="quantity[]" required></td><td><button type="button" onclick="removeRow(this)">x</button></td>`;
  document.getElementById('items').appendChild(tr);
}
function removeRow(button) {
  const rows = document.querySelectorAll('#items tr');
  if (rows.length > 2) button.closest('tr').remove();
}
</script>
</body>
</html>
