<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $vendorId = (int) post('vendor_id');
    $invoiceNumber = trim((string) post('invoice_number'));
    $invoiceDate = trim((string) post('invoice_date'));
    $productIds = post('product_id', []);
    $quantities = post('qty', []);

    $pdo->beginTransaction();
    try {
        foreach ($productIds as $index => $pid) {
            $productId = (int) $pid;
            $qty = (float) ($quantities[$index] ?? 0);
            if ($productId > 0 && $qty > 0) {
                recordStockMovement($pdo, $productId, $qty, 'stock_in', $vendorId, null, $invoiceNumber, $invoiceDate, 'Stock in');
            }
        }
        $pdo->commit();
        flash('Stock in entry saved.');
    } catch (Throwable $t) {
        $pdo->rollBack();
        flash('Failed: ' . $t->getMessage());
    }
    redirectTo('stock_in');
}

$vendors = getVendors($pdo);
$products = getProducts($pdo);
$rows = $pdo->query("SELECT sm.*, v.name vendor_name, p.name product_name FROM stock_movements sm LEFT JOIN vendors v ON v.id=sm.vendor_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type='stock_in' ORDER BY sm.id DESC LIMIT 100")->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
    <h5>Stock In</h5>
    <form method="post" action="?module=stock_in&action=save">
      <select class="form-select mb-2" name="vendor_id" required><option value="">Select vendor</option><?php foreach ($vendors as $v): ?><option value="<?= (int)$v['id'] ?>"><?= h($v['name']) ?></option><?php endforeach; ?></select>
      <input class="form-control mb-2" name="invoice_number" placeholder="Invoice number" required>
      <input class="form-control mb-2" type="date" name="invoice_date" required>
      <?php for ($i=0; $i<5; $i++): ?>
      <div class="row g-1 mb-1">
        <div class="col-8"><select class="form-select" name="product_id[]"><option value="">Product</option><?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-4"><input class="form-control" type="number" step="0.01" min="0" name="qty[]" placeholder="Qty"></div>
      </div>
      <?php endfor; ?>
      <button class="btn btn-primary w-100 mt-2">Save Stock In</button>
    </form>
  </div></div></div>
  <div class="col-md-7"><div class="card shadow-sm"><div class="card-body"><h5>Recent Stock In</h5>
    <table class="table table-sm"><tr><th>Date</th><th>Invoice</th><th>Vendor</th><th>Product</th><th>Qty</th></tr>
      <?php foreach ($rows as $r): ?><tr><td><?= h($r['invoice_date']) ?></td><td><?= h($r['invoice_number']) ?></td><td><?= h($r['vendor_name']) ?></td><td><?= h($r['product_name']) ?></td><td><?= h((string)$r['quantity_change']) ?></td></tr><?php endforeach; ?>
    </table></div></div></div>
</div>
