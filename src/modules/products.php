<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $name = trim((string) post('name'));
    $remarks = trim((string) post('remarks'));
    if ($name !== '') {
        $stmt = $pdo->prepare('INSERT INTO products (name, remarks) VALUES (?, ?)');
        $stmt->execute([$name, $remarks]);
        flash('Product created.');
    }
    redirectTo('products');
}

$rows = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Add Product</h5>
      <form method="post" action="?module=products&action=create">
        <input class="form-control mb-2" name="name" placeholder="Product name" required>
        <textarea class="form-control mb-2" name="remarks" placeholder="Remarks"></textarea>
        <button class="btn btn-primary w-100">Save</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Products</h5>
      <table class="table table-sm">
        <tr><th>ID</th><th>Name</th><th>Remarks</th></tr>
        <?php foreach ($rows as $r): ?>
          <tr><td><?= (int) $r['id'] ?></td><td><?= h($r['name']) ?></td><td><?= h($r['remarks']) ?></td></tr>
        <?php endforeach; ?>
      </table>
    </div></div>
  </div>
</div>
