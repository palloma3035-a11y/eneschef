-- Database: restaurant
CREATE DATABASE IF NOT EXISTS restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant;

-- Admins
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(100) DEFAULT 'Admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables (physical restaurant tables)
CREATE TABLE IF NOT EXISTS tables_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  capacity INT NOT NULL,
  description VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(255) NOT NULL,
  customer_phone VARCHAR(50) DEFAULT '',
  date DATE NOT NULL,
  time_slot VARCHAR(20) NOT NULL,
  seats INT NOT NULL,
  table_id INT DEFAULT NULL,
  status ENUM('pending','confirmed','seated','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (table_id) REFERENCES tables_info(id) ON DELETE SET NULL
);

-- Prepopulate sample data
INSERT IGNORE INTO admins (email, password, name) VALUES (
  'admin@example.com',
  -- password: password123 (bcrypt)
  '$2y$10$CwTycUXWue0Thq9StjUM0uJ8G8Y6K1xHqH5N6ZPa1J1R9YvBq0k8e',
  'Main Admin'
);

INSERT IGNORE INTO tables_info (name, capacity, description) VALUES
('Table 1', 2, 'Near window'),
('Table 2', 2, 'Near window'),
('Table 3', 4, 'Center'),
('Table 4', 4, 'Center'),
('Table 5', 6, 'Private booth');

-- Optional sample reservation:
-- INSERT INTO reservations (customer_name, customer_email, date, time_slot, seats, table_id, status)
-- VALUES ('Jane Doe','jane@example.com','2025-12-20','19:00',2,1,'confirmed');