<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $stmt = $pdo->prepare('INSERT INTO vendors (name, location, full_address, mobile, email, contact_person_name, contact_person_mobile, contact_person_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        trim((string) post('name')),
        trim((string) post('location')),
        trim((string) post('full_address')),
        trim((string) post('mobile')),
        trim((string) post('email')),
        trim((string) post('contact_person_name')),
        trim((string) post('contact_person_mobile')),
        trim((string) post('contact_person_email')),
    ]);
    flash('Vendor saved.');
    redirectTo('vendors');
}
$rows = $pdo->query('SELECT * FROM vendors ORDER BY id DESC')->fetchAll();
?>
<div class="row g-3">
  <div class="col-md-5"><div class="card shadow-sm"><div class="card-body"><h5>Add Vendor</h5>
    <form method="post" action="?module=vendors&action=create">
      <input class="form-control mb-2" name="name" placeholder="Vendor name" required>
      <input class="form-control mb-2" name="location" placeholder="Location">
      <textarea class="form-control mb-2" name="full_address" placeholder="Full address"></textarea>
      <input class="form-control mb-2" name="mobile" placeholder="Mobile number">
      <input class="form-control mb-2" name="email" placeholder="Email">
      <input class="form-control mb-2" name="contact_person_name" placeholder="Contact person name">
      <input class="form-control mb-2" name="contact_person_mobile" placeholder="Contact person mobile">
      <input class="form-control mb-2" name="contact_person_email" placeholder="Contact person email">
      <button class="btn btn-primary w-100">Save</button>
    </form>
  </div></div></div>
  <div class="col-md-7"><div class="card shadow-sm"><div class="card-body"><h5>Vendor List</h5>
    <table class="table table-sm"><tr><th>Name</th><th>Location</th><th>Mobile</th><th>Email</th></tr>
      <?php foreach ($rows as $r): ?><tr><td><?= h($r['name']) ?></td><td><?= h($r['location']) ?></td><td><?= h($r['mobile']) ?></td><td><?= h($r['email']) ?></td></tr><?php endforeach; ?>
    </table>
  </div></div></div>
</div>
