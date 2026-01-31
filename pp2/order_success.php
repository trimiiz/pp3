<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Order Success - TechStore";
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

include 'includes/header.php';
?>

<div class="container">
    <div class="success-message">
        <i class="fas fa-check-circle"></i>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your purchase. Your order ID is: <strong>#<?php echo $orderId; ?></strong></p>
        <p>You will receive a confirmation email shortly.</p>
        <div class="success-actions">
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
