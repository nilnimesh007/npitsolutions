<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $productId = (int) post('product_id');
    $qty = (float) post('qty');
    $reason = trim((string) post('reason'));
    if ($productId > 0 && $qty > 0) {
        recordStockMovement($pdo, $productId, -1 * $qty, 'inhouse_damage', null, null, null, date('Y-m-d'), $reason);
        flash('Inhouse damage saved.');
    }
    redirectTo('inhouse_damage');
}
$products = getProducts($pdo);
$rows = $pdo->query("SELECT sm.*, p.name product_name FROM stock_movements sm JOIN products p ON p.id=sm.product_id WHERE sm.movement_type='inhouse_damage' ORDER BY sm.id DESC LIMIT 100")->fetchAll();
?>
<div class="row g-3">
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h5>Inhouse Damage</h5>
        <form method="post" action="?module=inhouse_damage&action=save">
            <select class="form-select mb-2" name="product_id" required><option value="">Select product</option><?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?></select>
            <input class="form-control mb-2" type="number" step="0.01" min="0.01" name="qty" placeholder="Damage qty" required>
            <textarea class="form-control mb-2" name="reason" placeholder="Reason of damage" required></textarea>
            <button class="btn btn-primary w-100">Save</button>
        </form>
    </div></div></div>
    <div class="col-md-8"><div class="card shadow-sm"><div class="card-body"><h5>Inhouse Damage List</h5>
        <table class="table table-sm"><tr><th>Date</th><th>Product</th><th>Qty</th><th>Reason</th></tr>
            <?php foreach ($rows as $r): ?><tr><td><?= h($r['invoice_date']) ?></td><td><?= h($r['product_name']) ?></td><td><?= h((string)abs((float)$r['quantity_change'])) ?></td><td><?= h($r['remarks']) ?></td></tr><?php endforeach; ?>
        </table>
    </div></div></div>
</div>
