<?php
$rows = $pdo->query("SELECT p.id, p.name, p.remarks, COALESCE(SUM(sm.quantity_change),0) current_stock FROM products p LEFT JOIN stock_movements sm ON sm.product_id = p.id GROUP BY p.id, p.name, p.remarks ORDER BY p.name")->fetchAll();
?>
<div class="card shadow-sm"><div class="card-body">
    <h5>Current Stock (Product Wise)</h5>
    <table class="table table-sm"><tr><th>Product</th><th>Remarks</th><th>Current Stock</th></tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= h($r['name']) ?></td>
                <td><?= h($r['remarks']) ?></td>
                <td><strong><?= h((string)$r['current_stock']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div></div>
