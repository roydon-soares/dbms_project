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

-- Dummy records
INSERT INTO employees (name, role, salary, hire_date) VALUES 
('Alice Johnson', 'Manager', 65000.00, '2023-01-15'), 
('Bob Smith', 'Chef', 50000.00, '2023-02-10'), 
('Charlie Brown', 'Waiter', 30000.00, '2023-03-05'), 
('Diana Prince', 'Host', 35000.00, '2023-04-12'), 
('Evan Stone', 'Dishwasher', 25000.00, '2023-05-20'), 
('Fay Carter', 'Cook', 40000.00, '2023-06-25'), 
('George Banks', 'Delivery', 28000.00, '2023-07-30');


INSERT INTO menu_items (name, description, price, category) VALUES
('Goan Fish Curry', 'A spicy and tangy curry made with fish, coconut, and raw mango', 12.99, 'Main Course'),
('Chicken Cafreal', 'Spicy green chicken marinated with herbs and spices', 10.99, 'Main Course'),
('Pork Vindaloo', 'A fiery curry with pork, vinegar, and spices', 13.99, 'Main Course'),
('Chicken Xacuti', 'A rich curry made with chicken, poppy seeds, and Kashmiri red chilies', 11.99, 'Main Course'),
('Shark Ambot Tik', 'A sour and spicy dish made with shark, red chilies, and kokum', 14.99, 'Main Course'),
('Sorpotel', 'A spicy dish made with pork or beef liver, vinegar, and spices', 12.49, 'Main Course'),
('Bebinca', 'A sweet layered dessert made with coconut milk and flour', 4.49, 'Dessert'),
('Tambdi Bhaji', 'A spicy vegetable dish made with drumstick leaves', 7.99, 'Vegetarian'),
('Sol Kadhi', 'A refreshing drink made with coconut milk and kokum', 3.99, 'Beverage'),
('Kokum Juice', 'A tangy and sweet drink made with kokum and jaggery', 2.99, 'Beverage');
