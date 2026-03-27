CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  role_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  remarks TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_group_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NOT NULL,
  product_id INT NOT NULL,
  fixed_qty DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pgi_group FOREIGN KEY (group_id) REFERENCES product_groups(id),
  CONSTRAINT fk_pgi_product FOREIGN KEY (product_id) REFERENCES products(id),
  UNIQUE KEY uk_group_product (group_id, product_id)
);

CREATE TABLE IF NOT EXISTS vendors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  location VARCHAR(150) NULL,
  full_address TEXT NULL,
  mobile VARCHAR(30) NULL,
  email VARCHAR(150) NULL,
  contact_person_name VARCHAR(150) NULL,
  contact_person_mobile VARCHAR(30) NULL,
  contact_person_email VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  location VARCHAR(150) NULL,
  full_address TEXT NULL,
  mobile VARCHAR(30) NULL,
  email VARCHAR(150) NULL,
  contact_person_name VARCHAR(150) NULL,
  contact_person_mobile VARCHAR(30) NULL,
  contact_person_email VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  quantity_change DECIMAL(12,2) NOT NULL,
  movement_type ENUM(
    'stock_in', 'stock_out', 'stock_out_group',
    'product_damage', 'product_replace', 'inhouse_damage'
  ) NOT NULL,
  vendor_id INT NULL,
  client_id INT NULL,
  invoice_number VARCHAR(100) NULL,
  invoice_date DATE NULL,
  remarks TEXT NULL,
  ref_movement_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sm_product FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_sm_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id),
  CONSTRAINT fk_sm_client FOREIGN KEY (client_id) REFERENCES clients(id),
  CONSTRAINT fk_sm_ref FOREIGN KEY (ref_movement_id) REFERENCES stock_movements(id)
);

INSERT IGNORE INTO roles (id, name) VALUES (1, 'Admin'), (2, 'Manager'), (3, 'Operator');
