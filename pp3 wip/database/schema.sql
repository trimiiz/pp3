-- E-commerce Database Schema
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT NOT NULL,
    image_url VARCHAR(500),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Users table (for admin dashboard)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Phones', 'Smartphones and mobile devices'),
('TVs', 'Televisions and displays'),
('Laptops', 'Laptop computers'),
('Tablets', 'Tablet devices');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, image_url, stock_quantity) VALUES
('iPhone 15 Pro', 'Latest iPhone with advanced features', 999.99, 1, 'https://via.placeholder.com/300x300?text=iPhone+15+Pro', 50),
('Samsung Galaxy S24', 'Flagship Android smartphone', 899.99, 1, 'https://via.placeholder.com/300x300?text=Galaxy+S24', 45),
('Sony 65" 4K TV', 'Ultra HD Smart TV with HDR', 1299.99, 2, 'https://via.placeholder.com/300x300?text=Sony+65+TV', 30),
('LG 55" OLED TV', 'Premium OLED display technology', 1499.99, 2, 'https://via.placeholder.com/300x300?text=LG+55+OLED', 25),
('MacBook Pro 16"', 'Apple laptop with M3 chip', 2499.99, 3, 'https://via.placeholder.com/300x300?text=MacBook+Pro', 20),
('Dell XPS 15', 'High-performance Windows laptop', 1799.99, 3, 'https://via.placeholder.com/300x300?text=Dell+XPS', 35),
('iPad Pro 12.9"', 'Apple tablet with M2 chip', 1099.99, 4, 'https://via.placeholder.com/300x300?text=iPad+Pro', 40),
('Samsung Galaxy Tab S9', 'Premium Android tablet', 799.99, 4, 'https://via.placeholder.com/300x300?text=Galaxy+Tab', 30);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@ecommerce.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
