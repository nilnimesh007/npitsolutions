<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $damageMovementId = (int) post('damage_movement_id');
    $replaceQty = (float) post('replace_qty');
    if ($damageMovementId > 0 && $replaceQty > 0) {
        $stmt = $pdo->prepare("SELECT * FROM stock_movements WHERE id = ? AND movement_type = 'product_damage'");
        $stmt->execute([$damageMovementId]);
        $m = $stmt->fetch();
        if ($m) {
            recordStockMovement(
                $pdo,
                (int)$m['product_id'],
                -1 * $replaceQty,
                'product_replace',
                null,
                (int)$m['client_id'],
                (string)$m['invoice_number'],
                date('Y-m-d'),
                'Replacement against damage',
                (int)$m['id']
            );
            flash('Replacement entry saved.');
        }
    }
    redirectTo('product_replace');
}

$damageRows = $pdo->query("SELECT sm.id, sm.invoice_number, c.name client_name, p.name product_name, sm.quantity_change qty FROM stock_movements sm JOIN clients c ON c.id=sm.client_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type='product_damage' ORDER BY sm.id DESC LIMIT 150")->fetchAll();
$rows = $pdo->query("SELECT sm.*, c.name client_name, p.name product_name FROM stock_movements sm LEFT JOIN clients c ON c.id=sm.client_id JOIN products p ON p.id=sm.product_id WHERE sm.movement_type='product_replace' ORDER BY sm.id DESC LIMIT 100")->fetchAll();
?>
<div class="row g-3">
    <div class="col-md-5"><div class="card shadow-sm"><div class="card-body"><h5>Product Replace</h5>
        <form method="post" action="?module=product_replace&action=save">
            <select class="form-select mb-2" name="damage_movement_id" required>
                <option value="">Select damage transaction</option>
                <?php foreach ($damageRows as $r): ?>
                    <option value="<?= (int)$r['id'] ?>">#<?= (int)$r['id'] ?> | <?= h($r['client_name']) ?> | <?= h($r['product_name']) ?> | Qty <?= h((string)$r['qty']) ?></option>
                <?php endforeach; ?>
            </select>
            <input class="form-control mb-2" type="number" step="0.01" min="0.01" name="replace_qty" placeholder="Replace qty" required>
            <button class="btn btn-primary w-100">Save Replace</button>
        </form>
    </div></div></div>
    <div class="col-md-7"><div class="card shadow-sm"><div class="card-body"><h5>Replacement Transactions</h5>
        <table class="table table-sm"><tr><th>ID</th><th>Client</th><th>Product</th><th>Qty</th><th>Ref Damage ID</th></tr>
            <?php foreach ($rows as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= h($r['client_name']) ?></td><td><?= h($r['product_name']) ?></td><td><?= h((string)abs((float)$r['quantity_change'])) ?></td><td><?= h((string)$r['ref_movement_id']) ?></td></tr><?php endforeach; ?>
        </table>
    </div></div></div>
</div>
