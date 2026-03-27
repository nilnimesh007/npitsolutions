<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $name = trim((string) post('name'));
    $email = trim((string) post('email'));
    $roleId = (int) post('role_id', 0);
    if ($name !== '' && $email !== '' && $roleId > 0) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, role_id) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $roleId]);
        flash('User created.');
    }
    redirectTo('users');
}

$roles = getRoles($pdo);
$rows = $pdo->query('SELECT u.*, r.name role_name FROM users u JOIN roles r ON r.id = u.role_id ORDER BY u.id DESC')->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Add User</h5>
      <form method="post" action="?module=users&action=create">
        <input class="form-control mb-2" name="name" placeholder="User name" required>
        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <select class="form-select mb-2" name="role_id" required>
          <option value="">Select role</option>
          <?php foreach ($roles as $role): ?>
            <option value="<?= (int) $role['id'] ?>"><?= h($role['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-primary w-100">Save</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Users</h5>
      <table class="table table-sm">
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
        <?php foreach ($rows as $r): ?>
          <tr><td><?= (int) $r['id'] ?></td><td><?= h($r['name']) ?></td><td><?= h($r['email']) ?></td><td><?= h($r['role_name']) ?></td></tr>
        <?php endforeach; ?>
      </table>
    </div></div>
  </div>
</div>
