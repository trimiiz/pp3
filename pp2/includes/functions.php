<?php
/**
 * Utility Functions
 * Functions for reusable code
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format price with currency
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Get products by category
 * @param PDO $conn
 * @param string $category
 * @return array
 */
function getProductsByCategory($conn, $category = null) {
    if (!$conn) {
        return [];
    }
    if ($category) {
        // Select one row per product name within the category to avoid duplicate entries
        $sql = "SELECT p.* FROM products p
                INNER JOIN (
                    SELECT name, MIN(id) AS id
                    FROM products
                    WHERE category = ?
                    GROUP BY name
                ) t ON p.id = t.id
                ORDER BY p.id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$category]);
    } else {
        // Select one row per product name across all categories
        $sql = "SELECT p.* FROM products p
                INNER JOIN (
                    SELECT name, MIN(id) AS id
                    FROM products
                    GROUP BY name
                ) t ON p.id = t.id
                ORDER BY p.id ASC";
        $stmt = $conn->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * Get single product by ID
 * @param PDO $conn
 * @param int $id
 * @return array|false
 */
function getProductById($conn, $id) {
    if (!$conn) {
        return false;
    }
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to a page
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Get cart total
 * @return float
 */
function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

/**
 * Get cart item count
 * @return int
 */
function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

/**
 * Check if product is in user's wishlist
 * @param PDO $conn
 * @param int $userId
 * @param int $productId
 * @return bool
 */
function isInWishlist($conn, $userId, $productId) {
    if (!$conn || !$userId) return false;
    try {
        $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return (bool)$stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get wishlist count for user
 * @param PDO $conn
 * @param int $userId
 * @return int
 */
function getWishlistCount($conn, $userId) {
    if (!$conn || !$userId) return 0;
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}
