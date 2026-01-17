<?php
/**
 * Common Functions File
 * Contains reusable functions for the e-commerce site
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get all products from database
 * @param int|null $category_id Optional category filter
 * @return array Array of products
 */
function getAllProducts($category_id = null) {
    $pdo = getDBConnection();
    
    if ($category_id) {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.category_id = :category_id 
                               ORDER BY p.created_at DESC");
        $stmt->execute(['category_id' => $category_id]);
    } else {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.created_at DESC");
    }
    
    return $stmt->fetchAll();
}

/**
 * Get a single product by ID
 * @param int $product_id Product ID
 * @return array|false Product data or false if not found
 */
function getProductById($product_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = :id");
    $stmt->execute(['id' => $product_id]);
    return $stmt->fetch();
}

/**
 * Get all categories
 * @return array Array of categories
 */
function getAllCategories() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Format price with currency symbol
 * @param float $price Product price
 * @return string Formatted price
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Check if product is in stock
 * @param int $stock_quantity Stock quantity
 * @return bool True if in stock
 */
function isInStock($stock_quantity) {
    return $stock_quantity > 0;
}

/**
 * Get stock status message
 * @param int $stock_quantity Stock quantity
 * @return string Stock status message
 */
function getStockStatus($stock_quantity) {
    if ($stock_quantity > 10) {
        return 'In Stock';
    } elseif ($stock_quantity > 0) {
        return 'Low Stock';
    } else {
        return 'Out of Stock';
    }
}

/**
 * Sanitize input data
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate product data
 * @param array $data Product data
 * @return array Array of validation errors
 */
function validateProduct($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Product name is required';
    }
    
    if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
        $errors[] = 'Valid price is required';
    }
    
    if (empty($data['category_id']) || !is_numeric($data['category_id'])) {
        $errors[] = 'Category is required';
    }
    
    return $errors;
}
?>
