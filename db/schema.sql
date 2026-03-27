CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(120) NOT NULL,
  remarks VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_group_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_group_id INT NOT NULL,
  grouped_product_id INT NOT NULL,
  unit_qty DECIMAL(12,2) NOT NULL,
  UNIQUE KEY uq_group_product (product_group_id, grouped_product_id),
  FOREIGN KEY (product_group_id) REFERENCES product_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (grouped_product_id) REFERENCES products(id)
);

CREATE TABLE vendors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_name VARCHAR(160) NOT NULL,
  location VARCHAR(120),
  full_address TEXT,
  mobile_no VARCHAR(30),
  email_id VARCHAR(120),
  contact_person_name VARCHAR(120),
  contact_mobile_no VARCHAR(30),
  contact_email_id VARCHAR(120)
);

CREATE TABLE clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(160) NOT NULL,
  location VARCHAR(120),
  full_address TEXT,
  mobile_no VARCHAR(30),
  email_id VARCHAR(120),
  contact_person_name VARCHAR(120),
  contact_mobile_no VARCHAR(30),
  contact_email_id VARCHAR(120)
);

CREATE TABLE stock_in_invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_id INT NOT NULL,
  invoice_no VARCHAR(80) NOT NULL,
  invoice_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

CREATE TABLE stock_in_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES stock_in_invoices(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE stock_out_invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  invoice_no VARCHAR(80) NOT NULL,
  invoice_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE stock_out_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES stock_out_invoices(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE product_damages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  stock_out_invoice_id INT NOT NULL,
  product_id INT NOT NULL,
  damage_qty DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (stock_out_invoice_id) REFERENCES stock_out_invoices(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE product_replacements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  product_damage_id INT NOT NULL,
  product_id INT NOT NULL,
  replace_qty DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (product_damage_id) REFERENCES product_damages(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE inhouse_damages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  quantity DECIMAL(12,2) NOT NULL,
  reason VARCHAR(255),
  damage_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE stock_ledger (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  txn_type ENUM('IN', 'OUT', 'DAMAGE_OUT', 'REPLACE_IN', 'INHOUSE_DAMAGE') NOT NULL,
  qty DECIMAL(12,2) NOT NULL,
  ref_table VARCHAR(50) NOT NULL,
  ref_id INT NOT NULL,
  txn_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE OR REPLACE VIEW vw_current_stock AS
SELECT
  p.id AS product_id,
  p.product_name,
  COALESCE(SUM(CASE WHEN l.txn_type IN ('IN', 'REPLACE_IN') THEN l.qty ELSE 0 END), 0)
  - COALESCE(SUM(CASE WHEN l.txn_type IN ('OUT', 'DAMAGE_OUT', 'INHOUSE_DAMAGE') THEN l.qty ELSE 0 END), 0) AS current_qty
FROM products p
LEFT JOIN stock_ledger l ON l.product_id = p.id
GROUP BY p.id, p.product_name;
