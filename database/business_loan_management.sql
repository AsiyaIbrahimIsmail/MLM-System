-- Business Loan Management System Database
-- For Shop/Supermarket Credit and Debt Management

-- Create database
CREATE DATABASE IF NOT EXISTS business_loan_management;
USE business_loan_management;

-- Users table for admin and staff
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    unit_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Credit sales table (main credit transaction)
CREATE TABLE IF NOT EXISTS credit_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    sale_date DATE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    due_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Credit sale items table (items in each credit sale)
CREATE TABLE IF NOT EXISTS credit_sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    credit_sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (credit_sale_id) REFERENCES credit_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    credit_sale_id INT,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Cash',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (credit_sale_id) REFERENCES credit_sales(id) ON DELETE SET NULL
);

-- Sample data for users
INSERT INTO users (full_name, email, password, role) VALUES
('Admin User', 'admin@businessloan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Staff User', 'staff@businessloan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');

-- Sample data for customers
INSERT INTO customers (full_name, email, phone, address) VALUES
('John Smith', 'john.smith@email.com', '555-0101', '123 Main Street, City'),
('Maria Garcia', 'maria.garcia@email.com', '555-0102', '456 Oak Avenue, Town'),
('Robert Johnson', 'robert.j@email.com', '555-0103', '789 Pine Road, Village'),
('Sarah Williams', 'sarah.w@email.com', '555-0104', '321 Elm Street, City'),
('Michael Brown', 'michael.b@email.com', '555-0105', '654 Maple Drive, Town');

-- Sample data for products
INSERT INTO products (product_name, description, unit_price) VALUES
('Rice (5kg)', 'Premium quality rice 5kg bag', 25.00),
('Sugar (1kg)', 'Refined sugar 1kg pack', 5.50),
('Cooking Oil (1L)', 'Pure cooking oil 1 liter', 8.00),
('Bread (Loaf)', 'Freshly baked bread', 3.50),
('Milk (1L)', 'Fresh milk 1 liter', 4.00),
('Eggs (Dozen)', 'Fresh eggs dozen', 6.00),
('Flour (2kg)', 'All-purpose flour 2kg', 7.50),
('Coffee (250g)', 'Instant coffee 250g', 12.00),
('Tea (100 bags)', 'Black tea 100 bags', 8.50),
('Salt (1kg)', 'Table salt 1kg', 2.50);

-- Sample credit sales
INSERT INTO credit_sales (customer_id, sale_date, total_amount, due_date, notes) VALUES
(1, '2026-04-15', 85.50, '2026-05-15', 'First credit purchase'),
(2, '2026-04-18', 42.00, '2026-05-18', 'Regular supplies'),
(3, '2026-04-20', 120.00, '2026-05-20', 'Monthly stock'),
(1, '2026-04-20', 30.00, '2026-05-20', 'Additional items'),
(4, '2026-04-21', 55.00, '2026-05-21', 'Opening account');

-- Sample credit sale items
INSERT INTO credit_sale_items (credit_sale_id, product_id, quantity, unit_price, subtotal) VALUES
(1, 1, 2, 25.00, 50.00),
(1, 2, 3, 5.50, 16.50),
(1, 3, 2, 8.00, 16.00),
(1, 4, 1, 3.50, 3.50),
(2, 5, 4, 4.00, 16.00),
(2, 6, 2, 6.00, 12.00),
(2, 7, 2, 7.50, 15.00),
(3, 1, 3, 25.00, 75.00),
(3, 3, 3, 8.00, 24.00),
(3, 8, 2, 12.00, 24.00),
(4, 9, 2, 8.50, 17.00),
(4, 10, 5, 2.50, 12.50),
(5, 5, 5, 4.00, 20.00),
(5, 6, 3, 6.00, 18.00),
(5, 4, 5, 3.50, 17.50);

-- Sample payments
INSERT INTO payments (customer_id, credit_sale_id, payment_amount, payment_date, payment_method, notes) VALUES
(1, 1, 50.00, '2026-04-18', 'Cash', 'Partial payment'),
(2, 2, 42.00, '2026-04-20', 'Bank Transfer', 'Full payment'),
(3, 3, 60.00, '2026-04-22', 'Cash', 'First installment');

-- Create views for easy reporting
-- View: Customer balance summary
CREATE OR REPLACE VIEW customer_balance_summary AS
SELECT
    c.id AS customer_id,
    c.full_name,
    c.phone,
    COALESCE(SUM(cs.total_amount), 0) AS total_credit,
    COALESCE(SUM(p.payment_amount), 0) AS total_paid,
    COALESCE(SUM(cs.total_amount), 0) - COALESCE(SUM(p.payment_amount), 0) AS remaining_balance
FROM customers c
LEFT JOIN credit_sales cs ON c.id = cs.customer_id
LEFT JOIN payments p ON c.id = p.customer_id
GROUP BY c.id, c.full_name, c.phone;

-- View: Monthly credit summary
CREATE OR REPLACE VIEW monthly_credit_summary AS
SELECT
    DATE_FORMAT(sale_date, '%Y-%m') AS month,
    COUNT(*) AS total_transactions,
    SUM(total_amount) AS total_credit_issued,
    (SELECT SUM(payment_amount) FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = DATE_FORMAT(sale_date, '%Y-%m')) AS totalPayments
FROM credit_sales
GROUP BY DATE_FORMAT(sale_date, '%Y-%m');