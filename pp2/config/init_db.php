<?php
/**
 * Initialize Database Schema
 * Run this file once to create the database and tables
 */

require_once 'database.php';

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ecommerce_db");
    $pdo->exec("USE ecommerce_db");
    
    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category VARCHAR(100) NOT NULL,
        image VARCHAR(255),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'customer',
        date_of_birth DATE NULL,
        address VARCHAR(255) NULL,
        city VARCHAR(100) NULL,
        zip_code VARCHAR(20) NULL,
        gender VARCHAR(20) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total DECIMAL(10, 2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Create order_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");
    
    // Create wishlist table
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_product (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    
    // Insert sample products
    $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, description, price, category, image, stock) VALUES 
        (?, ?, ?, ?, ?, ?)");
    
    $products = [
        ['Samsung 55" 4K Smart TV', 'Ultra HD 4K resolution with HDR support', 799.99, 'TVs', 'tv1.jpg', 15],
        ['LG 65" OLED TV', 'Premium OLED display with perfect blacks', 1299.99, 'TVs', 'tv2.jpg', 8],
        ['Sony 43" LED TV', 'Full HD LED TV with Android TV', 399.99, 'TVs', 'tv3.jpg', 20],
        ['iPhone 15 Pro', 'Latest iPhone with A17 Pro chip', 999.99, 'Phones', 'phone1.jpg', 25],
        ['Samsung Galaxy S24', 'Flagship Android phone with AI features', 899.99, 'Phones', 'phone2.jpg', 18],
        ['Google Pixel 8', 'Pure Android experience with great camera', 699.99, 'Phones', 'phone3.jpg', 12],
        ['iPad Pro 12.9"', 'M2 chip with Liquid Retina display', 1099.99, 'Tablets', 'tablet1.jpg', 10],
        ['MacBook Pro 14"', 'M3 chip with 14-inch display', 1999.99, 'Laptops', 'laptop1.jpg', 5]
    ];
    
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    
    // Create admin user (password: admin123)
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@ecommerce.com', $adminPassword, 'admin']);
    
    echo "Database initialized successfully!<br>";
    echo "Admin credentials: username: admin, password: admin123";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
