-- Drop the database if it exists
DROP DATABASE IF EXISTS restaurant_management;

CREATE DATABASE restaurant_management;
USE restaurant_management;

-- 1. Menu Items Table
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50)
);

-- 2. Employees Table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50),
    salary DECIMAL(10, 2) NOT NULL,
    hire_date DATE
);

-- 3. Customers Table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(15),
    address TEXT
);

-- 4. Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    employee_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- 5. Order Items Table (Junction Table for Orders and Menu Items)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    menu_item_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);



////
INSERT INTO employees (name, role, salary, hire_date) VALUES 
('Alice Johnson', 'Manager', 65000.00, '2023-01-15'), 
('Bob Smith', 'Chef', 50000.00, '2023-02-10'),
 ('Charlie Brown', 'Waiter', 30000.00, '2023-03-05'), 
 ('Diana Prince', 'Host', 35000.00, '2023-04-12'),
  ('Evan Stone', 'Dishwasher', 25000.00, '2023-05-20'),
   ('Fay Carter', 'Cook', 40000.00, '2023-06-25')
    ('George Banks', 'Delivery', 28000.00, '2023-07-30');