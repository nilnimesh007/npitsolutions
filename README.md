# Inventory Management Software (PHP + XAMPP)

This starter project is designed for your requirements and runs on **XAMPP localhost** with PHP + MySQL.

## ✅ Included in this starter

- User + Role schema (ready for authentication integration)
- Product module (`product_name`, `remarks`)
- Product group module with **fixed quantity per product**
- Vendor and Client master schema with full contact details
- Stock In with **multiple products in a single invoice**
- Stock Out with **multiple products OR product groups in a single invoice**
- Group-out logic: when selecting a product group in stock out, all grouped products are auto deducted by configured ratios
- Current stock module (product-wise)
- Base schema for product damage, replacement, in-house damage
- Stock ledger design for easy stock statement reports

## Folder structure

- `db/schema.sql` → Full database schema
- `config/database.php` → PDO connection
- `src/InventoryService.php` → Core inventory business logic
- `modules/stock_in.php` → Stock in form (multi-item invoice)
- `modules/stock_out.php` → Stock out form (multi-item and group logic)
- `modules/current_stock.php` → Current stock report
- `index.php` → Module launcher

## XAMPP setup

1. Copy this project to:
   - `C:/xampp/htdocs/npitsolutions`
2. Start **Apache** and **MySQL** in XAMPP control panel.
3. Open phpMyAdmin and execute:
   - `db/schema.sql`
4. Update DB credentials in:
   - `config/database.php` (default is `root` with empty password).
5. Open in browser:
   - `http://localhost/npitsolutions/`

## How product-group stock out works

1. Create products.
2. Create a product group in `product_groups`.
3. Add rows in `product_group_items`.
   - Example: Group `Bundle A`
     - Product P1: `unit_qty = 2`
     - Product P2: `unit_qty = 1`
4. In stock out, choose `Group: Bundle A` and quantity `5`.
5. System auto deducts:
   - P1 => `2 × 5 = 10`
   - P2 => `1 × 5 = 5`

## Recommended improvements (next steps)

1. Add login, password hashing, and role-based access middleware.
2. Add server-side validation for negative stock prevention.
3. Add dedicated modules/pages for:
   - Vendors, Clients, Products, Product Groups
   - Product Damage, Replace, In-house Damage
   - Stock Statement filters (vendor/client/date range/in-out)
4. Add printable invoice and export to Excel/PDF.
5. Add audit trail (who created/edited transactions).

## Example SQL report query (stock statement)

```sql
SELECT
  l.txn_date,
  l.txn_type,
  p.product_name,
  l.qty,
  l.ref_table,
  l.ref_id
FROM stock_ledger l
JOIN products p ON p.id = l.product_id
WHERE l.txn_date BETWEEN '2026-01-01' AND '2026-12-31'
ORDER BY l.txn_date, l.id;
```

---

If you want, next I can generate complete CRUD pages for all modules (vendor/client/product/group/user) and full stock statement UI.
