<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $stockOutMovementId = (int) post('stock_out_movement_id');
    $damageQty = (float) post('damage_qty');
    if ($stockOutMovementId > 0 && $damageQty > 0) {
        $stmt = $pdo->prepare("SELECT * FROM stock_movements WHERE id = ? AND movement_type IN ('stock_out', 'stock_out_group')");
        $stmt->execute([$stockOutMovementId]);
        $m = $stmt->fetch();
        if ($m) {
            recordStockMovement(
                $pdo,
                (int)$m['product_id'],
                $damageQty,
                'product_damage',
                null,
                (int)$m['client_id'],
                (string)$m['invoice_number'],
                (string)$m['invoice_date'],
                'Damage return from client',
                (int)$m['id']
            );
            flash('Damage entry added (stock added back as damaged holding).');
        }
    }
    redirectTo('product_damage');
}

$outRows = $pdo->query("SELECT sm.id, sm.invoice_number, sm.invoice_date, c.name client_name, p.name product_name, ABS(sm.quantity_change) qty FROM stock_movements sm JOIN clients c ON c.id=sm.client_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type IN ('stock_out','stock_out_group') ORDER BY sm.id DESC LIMIT 150")->fetchAll();
$damageRows = $pdo->query("SELECT sm.*, c.name client_name, p.name product_name FROM stock_movements sm LEFT JOIN clients c ON c.id=sm.client_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type='product_damage' ORDER BY sm.id DESC LIMIT 100")->fetchAll();
?>
<div class="row g-3">
    <div class="col-md-5"><div class="card shadow-sm"><div class="card-body"><h5>Product Damage (Client Return)</h5>
        <form method="post" action="?module=product_damage&action=save">
            <select class="form-select mb-2" name="stock_out_movement_id" required>
                <option value="">Select stock out transaction</option>
                <?php foreach ($outRows as $r): ?>
                    <option value="<?= (int)$r['id'] ?>">#<?= (int)$r['id'] ?> | <?= h($r['client_name']) ?> | <?= h($r['product_name']) ?> | Qty <?= h((string)$r['qty']) ?> | <?= h($r['invoice_number']) ?></option>
                <?php endforeach; ?>
            </select>
            <input class="form-control mb-2" type="number" step="0.01" min="0.01" name="damage_qty" placeholder="Damage qty" required>
            <button class="btn btn-primary w-100">Save Damage</button>
        </form>
    </div></div></div>
    <div class="col-md-7"><div class="card shadow-sm"><div class="card-body"><h5>Damage Transactions</h5>
        <table class="table table-sm"><tr><th>ID</th><th>Client</th><th>Product</th><th>Qty</th><th>Ref Out ID</th></tr>
            <?php foreach ($damageRows as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= h($r['client_name']) ?></td><td><?= h($r['product_name']) ?></td><td><?= h((string)$r['quantity_change']) ?></td><td><?= h((string)$r['ref_movement_id']) ?></td></tr><?php endforeach; ?>
        </table>
    </div></div></div>
</div>
