<?php $flash = flash(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?module=dashboard">Inventory App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExample07">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="?module=users">Users</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=roles">Roles</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=product_groups">Product Groups</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=vendors">Vendors</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=clients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=stock_in">Stock In</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=stock_out">Stock Out</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=product_damage">Product Damage</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=product_replace">Product Replace</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=inhouse_damage">Inhouse Damage</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=current_stock">Current Stock</a></li>
        <li class="nav-item"><a class="nav-link" href="?module=stock_statement">Stock Statement</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <?php if ($flash): ?>
    <div class="alert alert-success"><?= h($flash) ?></div>
  <?php endif; ?>
