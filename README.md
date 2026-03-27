# Inventory Management (PHP + XAMPP)

This project is a starter web-based inventory management software built in PHP for local XAMPP usage.

## Modules Included
- User Module (with Role Management)
- Product Module (name + remarks)
- Product Group Module (fixed quantity per grouped product)
- Vendor Module
- Client Module
- Stock In Module (multi-product invoice)
- Stock Out Module (multi-product invoice + product-group auto deduction)
- Product Damage Module (against stock-out)
- Product Replace Module (against damage entry)
- Inhouse Damage Module
- Current Stock Module (product-wise)
- Stock Statement Module (vendor/client/date/type filters)

## Improvements added
- Unified stock ledger (`stock_movements`) for all stock operations.
- Transaction-safe stock in/out save flow.
- Product group deduction logic from one stock-out entry.

## Setup (XAMPP)
1. Copy project folder to: `C:\xampp\htdocs\npitsolutions`
2. Start **Apache** and **MySQL** from XAMPP.
3. Open phpMyAdmin and import `database.sql`.
4. Update DB settings in `config.php` if needed.
5. Open in browser: `http://localhost/npitsolutions/`

## Notes
- This is a functional starter. For production, add login/authentication, edit/delete screens, validation, and robust reporting/export.

## How to download all files
If you want all files from this project on your computer, use any one method below:

1. **Download ZIP from GitHub (easiest)**
   - Open the repository page in GitHub.
   - Click **Code** → **Download ZIP**.
   - Extract ZIP to `C:\xampp\htdocs\npitsolutions`.

2. **Clone with Git (recommended for updates)**
   ```bash
   git clone <your-repo-url> npitsolutions
   ```
   Then copy/move the folder to your XAMPP `htdocs` path.

3. **Pull latest updates (if already cloned)**
   ```bash
   git pull
   ```

After downloading, continue with the setup steps above (import `database.sql`, edit `config.php`, and open localhost URL).
