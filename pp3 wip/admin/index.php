<?php

session_start();
require_once '../includes/functions.php';
require_once '../config/database.php';

$page_title = 'Admin Dashboard';


if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true; 
}

$pdo = getDBConnection();


$total_products = $pdo->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
$total_categories = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
$total_stock = $pdo->query("SELECT SUM(stock_quantity) as total FROM products")->fetch()['total'];
$low_stock = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 10 AND stock_quantity > 0")->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php" class="active">Dashboard</a></li>
                    <li><a href="products.php">Manage Products</a></li>
                    <li><a href="categories.php">Manage Categories</a></li>
                    <li><a href="../index.php">View Store</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Dashboard</h1>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📁</div>
                    <div class="stat-info">
                        <h3><?php echo $total_categories; ?></h3>
                        <p>Categories</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?php echo $total_stock ?? 0; ?></h3>
                        <p>Total Stock</p>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-info">
                        <h3><?php echo $low_stock; ?></h3>
                        <p>Low Stock Items</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2>Recent Products</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_products = $pdo->query("SELECT p.*, c.name as category_name 
                                                           FROM products p 
                                                           JOIN categories c ON p.category_id = c.id 
                                                           ORDER BY p.created_at DESC 
                                                           LIMIT 5")->fetchAll();
                            
                            foreach ($recent_products as $product) {
                                $stock_class = $product['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock';
                                
                                echo "<tr>";
                                echo "<td>{$product['id']}</td>";
                                echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                                echo "<td>{$product['category_name']}</td>";
                                echo "<td>" . formatPrice($product['price']) . "</td>";
                                echo "<td><span class='stock-badge {$stock_class}'>{$product['stock_quantity']}</span></td>";
                                echo "<td>";
                                echo "<a href='products.php?action=edit&id={$product['id']}' class='btn btn-small'>Edit</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
