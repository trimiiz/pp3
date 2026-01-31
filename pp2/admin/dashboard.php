<?php

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Admin Dashboard - TechStore";

// Get statistics
$stats = [
    'total_products' => $conn->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'total_orders' => $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'total_revenue' => $conn->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'completed'")->fetchColumn(),
    'total_users' => $conn->query("SELECT COUNT(*) FROM users")->fetchColumn()
];

$recentOrders = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
$lowStockProducts = $conn->query("SELECT * FROM products WHERE stock < 5 ORDER BY stock ASC LIMIT 5")->fetchAll();
// Get users for admin table
$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 20")->fetchAll();

include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <a href="products.php" class="btn btn-primary">Manage Products</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-info">
                <h3><?php echo $stats['total_products']; ?></h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info">
                <h3><?php echo $stats['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-info">
                <h3><?php echo formatPrice($stats['total_revenue']); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h2>Recent Orders</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5">No orders yet</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></td>
                                <td><?php echo formatPrice($order['total']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-section">
            <h2>Low Stock Products</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lowStockProducts)): ?>
                        <tr>
                            <td colspan="4">All products are well stocked</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>
                                    <span class="stock-badge <?php echo $product['stock'] == 0 ? 'stock-zero' : 'stock-low'; ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="dashboard-grid">
        <div class="dashboard-section" style="grid-column: 1 / -1;">
            <h2>Users</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6">No users found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user <?php echo htmlspecialchars($user['username']); ?>? This cannot be undone.');">Delete</a>
                                    <?php else: ?>
                                        <span class="text-muted">Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
