<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $clientId = (int) post('client_id');
    $invoiceNumber = trim((string) post('invoice_number'));
    $invoiceDate = trim((string) post('invoice_date'));
    $productIds = post('product_id', []);
    $quantities = post('qty', []);
    $groupId = (int) post('group_id', 0);
    $groupQty = (float) post('group_qty', 0);

    $pdo->beginTransaction();
    try {
        foreach ($productIds as $index => $pid) {
            $productId = (int) $pid;
            $qty = (float) ($quantities[$index] ?? 0);
            if ($productId > 0 && $qty > 0) {
                recordStockMovement($pdo, $productId, -1 * $qty, 'stock_out', null, $clientId, $invoiceNumber, $invoiceDate, 'Stock out direct');
            }
        }

        if ($groupId > 0 && $groupQty > 0) {
            $stmt = $pdo->prepare('SELECT * FROM product_group_items WHERE group_id = ?');
            $stmt->execute([$groupId]);
            $items = $stmt->fetchAll();
            foreach ($items as $it) {
                $qty = (float) $it['fixed_qty'] * $groupQty;
                recordStockMovement(
                    $pdo,
                    (int) $it['product_id'],
                    -1 * $qty,
                    'stock_out_group',
                    null,
                    $clientId,
                    $invoiceNumber,
                    $invoiceDate,
                    'Auto from group #' . $groupId
                );
            }
        }

        $pdo->commit();
        flash('Stock out saved. Group logic applied when selected.');
    } catch (Throwable $t) {
        $pdo->rollBack();
        flash('Failed: ' . $t->getMessage());
    }

    redirectTo('stock_out');
}

$clients = getClients($pdo);
$products = getProducts($pdo);
$groups = $pdo->query('SELECT * FROM product_groups ORDER BY name')->fetchAll();
$rows = $pdo->query("SELECT sm.*, c.name client_name, p.name product_name FROM stock_movements sm LEFT JOIN clients c ON c.id=sm.client_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type IN ('stock_out', 'stock_out_group') ORDER BY sm.id DESC LIMIT 120")->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-5"><div class="card shadow-sm"><div class="card-body"><h5>Stock Out</h5>
    <form method="post" action="?module=stock_out&action=save">
      <select class="form-select mb-2" name="client_id" required><option value="">Select client</option><?php foreach ($clients as $c): ?><option value="<?= (int)$c['id'] ?>"><?= h($c['name']) ?></option><?php endforeach; ?></select>
      <input class="form-control mb-2" name="invoice_number" placeholder="Invoice number" required>
      <input class="form-control mb-2" type="date" name="invoice_date" required>
      <div class="alert alert-secondary py-2">Direct Product Out (optional)</div>
      <?php for ($i=0; $i<4; $i++): ?>
      <div class="row g-1 mb-1">
        <div class="col-8"><select class="form-select" name="product_id[]"><option value="">Product</option><?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-4"><input class="form-control" type="number" step="0.01" min="0" name="qty[]" placeholder="Qty"></div>
      </div>
      <?php endfor; ?>
      <div class="alert alert-secondary py-2 mt-2">Product Group Out (optional)</div>
      <select class="form-select mb-2" name="group_id"><option value="">Select product group</option><?php foreach ($groups as $g): ?><option value="<?= (int)$g['id'] ?>"><?= h($g['name']) ?></option><?php endforeach; ?></select>
      <input class="form-control mb-2" type="number" step="0.01" min="0" name="group_qty" placeholder="Group qty">
      <button class="btn btn-primary w-100">Save Stock Out</button>
    </form>
  </div></div></div>
  <div class="col-md-7"><div class="card shadow-sm"><div class="card-body"><h5>Recent Stock Out</h5>
    <table class="table table-sm"><tr><th>Date</th><th>Invoice</th><th>Client</th><th>Product</th><th>Qty</th><th>Type</th></tr>
      <?php foreach ($rows as $r): ?><tr><td><?= h($r['invoice_date']) ?></td><td><?= h($r['invoice_number']) ?></td><td><?= h($r['client_name']) ?></td><td><?= h($r['product_name']) ?></td><td><?= h((string)abs((float)$r['quantity_change'])) ?></td><td><?= h($r['movement_type']) ?></td></tr><?php endforeach; ?>
    </table></div></div></div>
</div>
