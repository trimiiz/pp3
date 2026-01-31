<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Shopping Cart - TechStore";

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($action) {
        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity > 0) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $productId) {
                        $item['quantity'] = $quantity;
                        break;
                    }
                }
            }
            break;
        case 'remove':
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
                return $item['id'] != $productId;
            });
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }
    
    redirect('cart.php');
}

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Shopping Cart</h1>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Add some products to get started!</p>
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cartTotal = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $itemTotal = $item['price'] * $item['quantity'];
                            $cartTotal += $itemTotal;
                        ?>
                            <tr>
                                <td>
                                    <div class="cart-product">
                                        <img src="assets/images/<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             onerror="this.src='assets/images/placeholder.jpg'">
                                        <div>
                                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="product-category"><?php echo htmlspecialchars($item['category'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td>
                                    <form method="POST" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="99" onchange="this.form.submit()" class="quantity-input">
                                    </form>
                                </td>
                                <td><?php echo formatPrice($itemTotal); ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Remove this item?');">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn-remove"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <form method="POST" onsubmit="return confirm('Clear entire cart?');" class="clear-cart-form">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-secondary">Clear Cart</button>
                </form>
            </div>
            
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($cartTotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($cartTotal); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
                <a href="products.php" class="btn btn-secondary btn-block">Continue Shopping</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
