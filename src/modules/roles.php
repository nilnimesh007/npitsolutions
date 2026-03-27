<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $name = trim((string) post('name'));
    if ($name !== '') {
        $stmt = $pdo->prepare('INSERT INTO roles (name) VALUES (?)');
        $stmt->execute([$name]);
        flash('Role created.');
    }
    redirectTo('roles');
}

$rows = $pdo->query('SELECT * FROM roles ORDER BY id DESC')->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Add Role</h5>
      <form method="post" action="?module=roles&action=create">
        <input class="form-control mb-2" name="name" placeholder="Role name" required>
        <button class="btn btn-primary w-100">Save</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Roles</h5>
      <table class="table table-sm">
        <tr><th>ID</th><th>Name</th></tr>
        <?php foreach ($rows as $r): ?>
          <tr><td><?= (int) $r['id'] ?></td><td><?= h($r['name']) ?></td></tr>
        <?php endforeach; ?>
      </table>
    </div></div>
  </div>
</div>
