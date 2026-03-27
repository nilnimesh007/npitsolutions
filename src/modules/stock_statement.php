<?php
$vendorId = (int)($_GET['vendor_id'] ?? 0);
$clientId = (int)($_GET['client_id'] ?? 0);
$dateFrom = trim((string)($_GET['date_from'] ?? ''));
$dateTo = trim((string)($_GET['date_to'] ?? ''));
$type = trim((string)($_GET['type'] ?? 'all'));

$sql = "SELECT sm.*, p.name product_name, v.name vendor_name, c.name client_name
        FROM stock_movements sm
        JOIN products p ON p.id = sm.product_id
        LEFT JOIN vendors v ON v.id = sm.vendor_id
        LEFT JOIN clients c ON c.id = sm.client_id
        WHERE 1=1";
$params = [];

if ($vendorId > 0) {
    $sql .= ' AND sm.vendor_id = ?';
    $params[] = $vendorId;
}
if ($clientId > 0) {
    $sql .= ' AND sm.client_id = ?';
    $params[] = $clientId;
}
if ($dateFrom !== '') {
    $sql .= ' AND sm.invoice_date >= ?';
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $sql .= ' AND sm.invoice_date <= ?';
    $params[] = $dateTo;
}
if ($type === 'in') {
    $sql .= ' AND sm.quantity_change > 0';
} elseif ($type === 'out') {
    $sql .= ' AND sm.quantity_change < 0';
}

$sql .= ' ORDER BY sm.id DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$vendors = getVendors($pdo);
$clients = getClients($pdo);
?>
<div class="card shadow-sm mb-3"><div class="card-body">
    <h5>Stock Statement Filters</h5>
    <form method="get" class="row g-2">
        <input type="hidden" name="module" value="stock_statement">
        <div class="col-md-2"><select class="form-select" name="vendor_id"><option value="0">All Vendors</option><?php foreach ($vendors as $v): ?><option value="<?= (int)$v['id'] ?>" <?= $vendorId===(int)$v['id']?'selected':'' ?>><?= h($v['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select class="form-select" name="client_id"><option value="0">All Clients</option><?php foreach ($clients as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $clientId===(int)$c['id']?'selected':'' ?>><?= h($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input class="form-control" type="date" name="date_from" value="<?= h($dateFrom) ?>"></div>
        <div class="col-md-2"><input class="form-control" type="date" name="date_to" value="<?= h($dateTo) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="type"><option value="all" <?= $type==='all'?'selected':'' ?>>All</option><option value="in" <?= $type==='in'?'selected':'' ?>>In Stock</option><option value="out" <?= $type==='out'?'selected':'' ?>>Out Stock</option></select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="card-body">
    <h5>Stock Statement</h5>
    <table class="table table-sm">
        <tr><th>Date</th><th>Type</th><th>Invoice</th><th>Product</th><th>Vendor</th><th>Client</th><th>Qty + / -</th><th>Remarks</th></tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= h($r['invoice_date']) ?></td>
                <td><?= h($r['movement_type']) ?></td>
                <td><?= h($r['invoice_number']) ?></td>
                <td><?= h($r['product_name']) ?></td>
                <td><?= h($r['vendor_name']) ?></td>
                <td><?= h($r['client_name']) ?></td>
                <td><?= h((string)$r['quantity_change']) ?></td>
                <td><?= h($r['remarks']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div></div>
