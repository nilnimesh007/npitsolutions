<?php
/**
 * Inventory Management Starter (XAMPP + PHP + MySQL)
 *
 * Quick start:
 * 1) Create database `inventory_db`
 * 2) Import `database.sql`
 * 3) Update DB credentials in `config.php`
 * 4) Place this folder in htdocs and open http://localhost/npitsolutions/
 */

require_once __DIR__ . '/src/bootstrap.php';

$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';

$allowedModules = [
    'dashboard', 'users', 'roles', 'products', 'product_groups', 'vendors', 'clients',
    'stock_in', 'stock_out', 'product_damage', 'product_replace', 'inhouse_damage',
    'current_stock', 'stock_statement'
];

if (!in_array($module, $allowedModules, true)) {
    $module = 'dashboard';
}

$moduleFile = __DIR__ . '/src/modules/' . $module . '.php';

include __DIR__ . '/src/views/header.php';

if (file_exists($moduleFile)) {
    include $moduleFile;
} else {
    echo '<div class="alert alert-danger">Module not found.</div>';
}

include __DIR__ . '/src/views/footer.php';
