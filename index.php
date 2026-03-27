<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; }
        a.card { text-decoration: none; border: 1px solid #ddd; padding: 14px; border-radius: 8px; color: #111; }
        a.card:hover { background: #f7f7f7; }
    </style>
</head>
<body>
<h1>Inventory Management (PHP + MySQL)</h1>
<p>Starter project with multi-product invoices and product-group auto deduction logic.</p>

<div class="grid">
    <a class="card" href="modules/stock_in.php">Stock In (multiple products)</a>
    <a class="card" href="modules/stock_out.php">Stock Out (multiple products + groups)</a>
    <a class="card" href="modules/current_stock.php">Current Stock</a>
    <a class="card" href="README.md">Setup & Module Guide</a>
</div>
</body>
</html>
