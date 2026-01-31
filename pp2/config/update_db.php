<?php
/**
 * Database Update Script
 * Run this file once to add new columns and tables for user profiles and wishlist
 */

require_once 'database.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ecommerce_db;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Add new columns to users table (ignore if already exist)
    $newColumns = [
        'date_of_birth' => "ALTER TABLE users ADD COLUMN date_of_birth DATE NULL",
        'address' => "ALTER TABLE users ADD COLUMN address VARCHAR(255) NULL",
        'city' => "ALTER TABLE users ADD COLUMN city VARCHAR(100) NULL",
        'zip_code' => "ALTER TABLE users ADD COLUMN zip_code VARCHAR(20) NULL",
        'gender' => "ALTER TABLE users ADD COLUMN gender VARCHAR(20) NULL"
    ];
    
    foreach ($newColumns as $col => $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                throw $e;
            }
        }
    }
    
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
    
    echo "Database updated successfully!<br>";
    echo "New user profile fields and wishlist table have been added.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
