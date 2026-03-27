<?php
$counts = [
    'Products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'Vendors' => (int) $pdo->query('SELECT COUNT(*) FROM vendors')->fetchColumn(),
    'Clients' => (int) $pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
    'Users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'Total Stock In' => (float) $pdo->query("SELECT COALESCE(SUM(quantity_change),0) FROM stock_movements WHERE quantity_change > 0")->fetchColumn(),
    'Total Stock Out' => (float) abs((float) $pdo->query("SELECT COALESCE(SUM(quantity_change),0) FROM stock_movements WHERE quantity_change < 0")->fetchColumn()),
];
?>
<div class="p-4 mb-4 bg-white rounded-3 shadow-sm">
    <h1 class="h3">Inventory Management Dashboard</h1>
    <p class="text-muted">Ready-to-use starter for your required modules on XAMPP (PHP + MySQL).</p>
</div>
<div class="row g-3">
    <?php foreach ($counts as $label => $value): ?>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="small text-muted"><?= h($label) ?></div>
                <div class="h4 mb-0"><?= h((string) $value) ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
