<?php
/**
 * Add to Cart AJAX Handler
 * Demonstrates: Variables, Parameters, Functions
 */

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $productName = sanitizeInput($_POST['product_name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($productId && $productName && $price > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Check if product already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $productId) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            // Add new item if not found
            if (!$found) {
                $database = new Database();
                $conn = $database->getConnection();
                $product = getProductById($conn, $productId);
                
                $_SESSION['cart'][] = [
                    'id' => $productId,
                    'name' => $productName,
                    'price' => $price,
                    'quantity' => $quantity,
                    'category' => $product['category'] ?? '',
                    'image' => $product['image'] ?? 'placeholder.jpg'
                ];
            }
            
            echo json_encode([
                'success' => true,
                'cartCount' => getCartCount()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product data'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
