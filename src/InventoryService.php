<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class InventoryService
{
    public function stockIn(int $vendorId, string $invoiceNo, string $invoiceDate, array $items): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $invoiceStmt = $pdo->prepare('INSERT INTO stock_in_invoices (vendor_id, invoice_no, invoice_date) VALUES (?, ?, ?)');
            $invoiceStmt->execute([$vendorId, $invoiceNo, $invoiceDate]);
            $invoiceId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO stock_in_items (invoice_id, product_id, quantity) VALUES (?, ?, ?)');
            $ledgerStmt = $pdo->prepare('INSERT INTO stock_ledger (product_id, txn_type, qty, ref_table, ref_id, txn_date) VALUES (?, ?, ?, ?, ?, ?)');

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['quantity'];

                if ($qty <= 0) {
                    continue;
                }

                $itemStmt->execute([$invoiceId, $productId, $qty]);
                $ledgerStmt->execute([$productId, 'IN', $qty, 'stock_in_invoices', $invoiceId, $invoiceDate]);
            }

            $pdo->commit();
            return $invoiceId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function stockOut(int $clientId, string $invoiceNo, string $invoiceDate, array $lines): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $invoiceStmt = $pdo->prepare('INSERT INTO stock_out_invoices (client_id, invoice_no, invoice_date) VALUES (?, ?, ?)');
            $invoiceStmt->execute([$clientId, $invoiceNo, $invoiceDate]);
            $invoiceId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO stock_out_items (invoice_id, product_id, quantity) VALUES (?, ?, ?)');
            $groupItemStmt = $pdo->prepare('SELECT grouped_product_id, unit_qty FROM product_group_items WHERE product_group_id = ?');
            $ledgerStmt = $pdo->prepare('INSERT INTO stock_ledger (product_id, txn_type, qty, ref_table, ref_id, txn_date) VALUES (?, ?, ?, ?, ?, ?)');

            foreach ($lines as $line) {
                $type = $line['type'] ?? 'product';
                $id = (int) $line['id'];
                $qty = (float) $line['quantity'];

                if ($qty <= 0) {
                    continue;
                }

                if ($type === 'group') {
                    $groupItemStmt->execute([$id]);
                    $groupItems = $groupItemStmt->fetchAll();

                    foreach ($groupItems as $groupItem) {
                        $productId = (int) $groupItem['grouped_product_id'];
                        $deductQty = $qty * (float) $groupItem['unit_qty'];

                        $itemStmt->execute([$invoiceId, $productId, $deductQty]);
                        $ledgerStmt->execute([$productId, 'OUT', $deductQty, 'stock_out_invoices', $invoiceId, $invoiceDate]);
                    }
                    continue;
                }

                $itemStmt->execute([$invoiceId, $id, $qty]);
                $ledgerStmt->execute([$id, 'OUT', $qty, 'stock_out_invoices', $invoiceId, $invoiceDate]);
            }

            $pdo->commit();
            return $invoiceId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getCurrentStock(): array
    {
        $sql = "
            SELECT
                p.id,
                p.product_name,
                COALESCE(SUM(CASE WHEN l.txn_type IN ('IN', 'REPLACE_IN') THEN l.qty ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN l.txn_type IN ('OUT', 'DAMAGE_OUT', 'INHOUSE_DAMAGE') THEN l.qty ELSE 0 END), 0) AS current_stock
            FROM products p
            LEFT JOIN stock_ledger l ON l.product_id = p.id
            GROUP BY p.id, p.product_name
            ORDER BY p.product_name ASC
        ";

        return db()->query($sql)->fetchAll();
    }
}
