<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Checkout - TechStore";

if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$cartTotal = getCartTotal();
$errors = [];
$userProfile = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, email, address, city, zip_code FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userProfile = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $city = sanitizeInput($_POST['city'] ?? '');
    $zip = sanitizeInput($_POST['zip'] ?? '');
    
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($zip)) $errors[] = "ZIP code is required";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt->execute([$userId, $cartTotal]);
        $orderId = $conn->lastInsertId();
        
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }
        
        $_SESSION['cart'] = [];
        
        redirect("order_success.php?order_id=$orderId");
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Checkout</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="checkout-container">
        <div class="checkout-form-section">
            <h2>Shipping Information</h2>
            <form method="POST" class="checkout-form">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ($userProfile['username'] ?? '')); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ($userProfile['email'] ?? '')); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" required 
                           value="<?php echo htmlspecialchars($_POST['address'] ?? ($userProfile['address'] ?? '')); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required 
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ($userProfile['city'] ?? '')); ?>">
                    </div>
                    <div class="form-group">
                        <label for="zip">ZIP Code *</label>
                        <input type="text" id="zip" name="zip" required 
                               value="<?php echo htmlspecialchars($_POST['zip'] ?? ($userProfile['zip_code'] ?? '')); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Place Order</button>
            </form>
        </div>
        
        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-item">
                        <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-total">
                <span>Total:</span>
                <span><?php echo formatPrice($cartTotal); ?></span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
