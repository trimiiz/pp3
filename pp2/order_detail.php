<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$orderId) redirect('index.php');

$stmt = $conn->prepare("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) redirect('index.php');

if (isLoggedIn() && $order['user_id'] != $_SESSION['user_id']) {
    redirect('index.php');
}
if (!isLoggedIn() && $order['user_id']) {
    redirect('login.php');
}

$stmt = $conn->prepare("
    SELECT oi.*, p.name as product_name, p.image 
    FROM order_items oi 
    JOIN products p ON p.id = oi.product_id 
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

$pageTitle = "Order #" . $orderId . " - TechStore";

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Order #<?php echo $order['id']; ?></h1>
        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
            <?php echo htmlspecialchars($order['status']); ?>
        </span>
    </div>

    <div class="order-detail-section">
        <p><strong>Order Date:</strong> <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
        <?php if ($order['username']): ?>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
        <?php endif; ?>
    </div>

    <div class="order-items-section">
        <h2>Order Items</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <img src="assets/images/<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     onerror="this.src='assets/images/placeholder.jpg'">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                </div>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="order-total-row">
            <strong>Total:</strong>
            <strong><?php echo formatPrice($order['total']); ?></strong>
        </div>
    </div>

    <div class="order-detail-actions">
        <?php if (isLoggedIn()): ?>
            <a href="user/dashboard.php?tab=orders" class="btn btn-primary">Back to My Orders</a>
        <?php endif; ?>
        <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>

<style>
.order-detail-section { margin-bottom: 2rem; }
.order-items-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}
.order-total-row {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1rem 0;
    font-size: 1.25rem;
}
.order-detail-actions { display: flex; gap: 1rem; }
</style>

<?php include 'includes/footer.php'; ?>
