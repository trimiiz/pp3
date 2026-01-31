<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function formatPrice($price) {
    return '$' . number_format($price, 2);
}
function getProductsByCategory($conn, $category = null) {
    if (!$conn) {
        return [];
    }
    if ($category) {
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
function getProductById($conn, $id) {
    if (!$conn) {
        return false;
    }
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

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
