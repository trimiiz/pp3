<?php
/**
 * Add/Remove from Wishlist AJAX Handler
 */

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to use wishlist']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    
    if (!$productId) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        exit;
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    $userId = $_SESSION['user_id'];
    
    if ($action === 'add') {
        try {
            $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            echo json_encode([
                'success' => true,
                'inWishlist' => true,
                'message' => 'Added to wishlist'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist']);
        }
    } elseif ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        echo json_encode([
            'success' => true,
            'inWishlist' => false,
            'message' => 'Removed from wishlist'
        ]);
    } elseif ($action === 'toggle') {
        $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $count = getWishlistCount($conn, $userId);
            echo json_encode(['success' => true, 'inWishlist' => false, 'message' => 'Removed from wishlist', 'wishlistCount' => $count]);
        } else {
            $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            $count = getWishlistCount($conn, $userId);
            echo json_encode(['success' => true, 'inWishlist' => true, 'message' => 'Added to wishlist', 'wishlistCount' => $count]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
