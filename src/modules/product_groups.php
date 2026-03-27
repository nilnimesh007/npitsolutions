<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_group') {
    $name = trim((string) post('name'));
    if ($name !== '') {
        $stmt = $pdo->prepare('INSERT INTO product_groups (name) VALUES (?)');
        $stmt->execute([$name]);
        flash('Product group created.');
    }
    redirectTo('product_groups');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_item') {
    $groupId = (int) post('group_id');
    $productId = (int) post('product_id');
    $fixedQty = (float) post('fixed_qty');

    if ($groupId > 0 && $productId > 0 && $fixedQty > 0) {
        $stmt = $pdo->prepare('INSERT INTO product_group_items (group_id, product_id, fixed_qty) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE fixed_qty = VALUES(fixed_qty)');
        $stmt->execute([$groupId, $productId, $fixedQty]);
        flash('Group item saved.');
    }
    redirectTo('product_groups');
}

$groups = $pdo->query('SELECT * FROM product_groups ORDER BY id DESC')->fetchAll();
$products = getProducts($pdo);
$items = $pdo->query('SELECT gi.*, g.name group_name, p.name product_name FROM product_group_items gi JOIN product_groups g ON g.id=gi.group_id JOIN products p ON p.id=gi.product_id ORDER BY gi.id DESC')->fetchAll();
?>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h5>Create Group</h5>
            <form method="post" action="?module=product_groups&action=create_group">
                <input class="form-control mb-2" name="name" placeholder="Group name" required>
                <button class="btn btn-primary w-100">Save Group</button>
            </form>
        </div></div>
        <div class="card shadow-sm"><div class="card-body">
            <h5>Add Product to Group</h5>
            <form method="post" action="?module=product_groups&action=add_item">
                <select class="form-select mb-2" name="group_id" required>
                    <option value="">Select group</option>
                    <?php foreach ($groups as $g): ?><option value="<?= (int) $g['id'] ?>"><?= h($g['name']) ?></option><?php endforeach; ?>
                </select>
                <select class="form-select mb-2" name="product_id" required>
                    <option value="">Select product</option>
                    <?php foreach ($products as $p): ?><option value="<?= (int) $p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?>
                </select>
                <input class="form-control mb-2" type="number" step="0.01" min="0.01" name="fixed_qty" placeholder="Fixed qty per 1 group" required>
                <button class="btn btn-success w-100">Save Mapping</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Group Product Mapping</h5>
            <table class="table table-sm"><tr><th>Group</th><th>Product</th><th>Fixed Qty</th></tr>
                <?php foreach ($items as $it): ?><tr><td><?= h($it['group_name']) ?></td><td><?= h($it['product_name']) ?></td><td><?= h((string)$it['fixed_qty']) ?></td></tr><?php endforeach; ?>
            </table>
        </div></div>
    </div>
</div>
