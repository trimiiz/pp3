<?php

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Products - TechStore";
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;


if ($search) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id ASC LIMIT ? OFFSET ?");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $perPage, $offset]);
    $products = $stmt->fetchAll();
    
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name LIKE ? OR description LIKE ?");
    $countStmt->execute([$searchTerm, $searchTerm]);
    $totalProducts = $countStmt->fetchColumn();
} elseif ($category) {
    $products = getProductsByCategory($conn, $category);
    $totalProducts = count($products);
    $products = array_slice($products, $offset, $perPage);
} else {
    $products = getProductsByCategory($conn);
    $totalProducts = count($products);
    $products = array_slice($products, $offset, $perPage);
}

$totalPages = ceil($totalProducts / $perPage);
$categories = ['TVs', 'Phones', 'Tablets', 'Laptops'];

$wishlistIds = [];
if (isLoggedIn()) {
    foreach ($products as $p) {
        if (isInWishlist($conn, $_SESSION['user_id'], $p['id'])) {
            $wishlistIds[] = $p['id'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>
            <?php 
            if ($category) {
                echo htmlspecialchars($category);
            } elseif ($search) {
                echo "Search Results for: " . htmlspecialchars($search);
            } else {
                echo "All Products";
            }
            ?>
        </h1>
    </div>

    <div class="products-filters">
        <div class="filter-section">
            <form method="GET" action="products.php" class="search-form">
                <input type="text" name="search" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($search ?? ''); ?>" class="search-input">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="filter-section">
            <div class="category-filters">
                <a href="products.php" class="filter-btn <?php echo !$category ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?php echo urlencode($cat); ?>" 
                       class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="products-grid">
        <?php if (empty($products)): ?>
            <div class="no-products">
                <p>No products found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <div class="product-badge">
                            <?php if ($product['stock'] < 5): ?>
                                <span class="badge badge-warning">Low Stock</span>
                            <?php endif; ?>
                            <?php if (isLoggedIn()): ?>
                                <button type="button" class="btn-wishlist <?php echo in_array($product['id'], $wishlistIds) ? 'in-wishlist' : ''; ?>" 
                                        onclick="toggleWishlist(<?php echo $product['id']; ?>, this)" title="<?php echo in_array($product['id'], $wishlistIds) ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                    <i class="<?php echo in_array($product['id'], $wishlistIds) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="product-overlay">
                            <button class="btn-quick-view" onclick="quickView(<?php echo $product['id']; ?>)">
                                <i class="fas fa-eye"></i> Quick View
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 60)) . '...'; ?></p>
                        <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                        <div class="product-stock">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="stock-available">In Stock (<?php echo $product['stock']; ?>)</span>
                            <?php else: ?>
                                <span class="stock-unavailable">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-primary" 
                                    onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)"
                                    <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                   class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
