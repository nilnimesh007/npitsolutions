<?php

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function post(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function flash(?string $message = null): ?string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($message !== null) {
        $_SESSION['flash_message'] = $message;
        return null;
    }

    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }

    return null;
}

function redirectTo(string $module, string $action = 'list'): void
{
    header('Location: ?module=' . urlencode($module) . '&action=' . urlencode($action));
    exit;
}

function getCurrentStock(PDO $pdo, int $productId): float
{
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity_change), 0) FROM stock_movements WHERE product_id = ?');
    $stmt->execute([$productId]);
    return (float) $stmt->fetchColumn();
}

function recordStockMovement(
    PDO $pdo,
    int $productId,
    float $quantityChange,
    string $movementType,
    ?int $vendorId = null,
    ?int $clientId = null,
    ?string $invoiceNumber = null,
    ?string $invoiceDate = null,
    ?string $remarks = null,
    ?int $refMovementId = null
): void {
    $stmt = $pdo->prepare(
        'INSERT INTO stock_movements
        (product_id, quantity_change, movement_type, vendor_id, client_id, invoice_number, invoice_date, remarks, ref_movement_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)' 
    );

    $stmt->execute([
        $productId,
        $quantityChange,
        $movementType,
        $vendorId,
        $clientId,
        $invoiceNumber,
        $invoiceDate,
        $remarks,
        $refMovementId,
    ]);
}

function getRoles(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM roles ORDER BY name')->fetchAll();
}

function getProducts(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
}

function getVendors(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM vendors ORDER BY name')->fetchAll();
}

function getClients(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM clients ORDER BY name')->fetchAll();
}
