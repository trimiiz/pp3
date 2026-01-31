<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Home - TechStore";
if (!$conn) {
    echo "<div style=\"padding:20px;max-width:800px;margin:40px auto;font-family:Arial,sans-serif;\">";
    echo "<h2 style=\"color:#c00;\">Database connection failed</h2>";
    echo "<p>Please initialize the database using phpMyAdmin (import <code>config/e_commerce.sql</code>) or by visiting <a href='config/init_db.php'>config/init_db.php</a> in your browser.</p>";
    echo "<p>After creating the database refresh this page.</p>";
    echo "</div>";
    include 'includes/footer.php';
    exit;
}

$featuredProducts = getProductsByCategory($conn);
$categories = ['TVs', 'Phones', 'Tablets', 'Laptops'];

$wishlistIds = [];
if (isLoggedIn()) {
    foreach ($featuredProducts as $p) {
        if (isInWishlist($conn, $_SESSION['user_id'], $p['id'])) {
            $wishlistIds[] = $p['id'];
        }
    }
}

include 'includes/header.php';
?>

<div class="hero-slider">
    <div class="slider-container">
        <div class="slide active">
            <div class="slide-content">
                <h1>Welcome to TechStore</h1>
                <p>Discover the latest in technology</p>
                <a href="products.php" class="btn btn-primary">Shop Now</a>
            </div>
            <div class="slide-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
        </div>
        <div class="slide">
            <div class="slide-content">
                <h1>New Arrivals</h1>
                <p>Check out our latest products</p>
                <a href="products.php" class="btn btn-primary">Explore</a>
            </div>
            <div class="slide-image" style="background: linear-gradient(135deg, #667eea 0%, #f5576c 100%);"></div>
        </div>
        <div class="slide">
            <div class="slide-content">
                <h1>Best Deals</h1>
                <p>Save big on premium electronics</p>
                <a href="products.php" class="btn btn-primary">View Deals</a>
            </div>
            <div class="slide-image" style="background: linear-gradient(135deg, #667eea 0%, #00f2fe 100%);"></div>
        </div>
    </div>
    <button class="slider-btn prev"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-btn next"><i class="fas fa-chevron-right"></i></button>
        <div class="slider-dots">
        <?php for ($i = 0; $i < 3; $i++): ?>
            <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>"></span>
        <?php endfor; ?>
    </div>
</div>

<div class="container">
    <section class="categories-section">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card" onclick="window.location.href='products.php?category=<?php echo urlencode($category); ?>'">
                    <div class="category-icon">
                        <?php if ($category === 'TVs'): ?>
                            <i class="fas fa-tv"></i>
                        <?php elseif ($category === 'Phones'): ?>
                            <i class="fas fa-mobile-alt"></i>
                        <?php elseif ($category === 'Tablets'): ?>
                            <i class="fas fa-tablet-alt"></i>
                        <?php else: ?>
                            <i class="fas fa-laptop"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="featured-products">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <?php
            $count = 0;
            foreach ($featuredProducts as $product):
                if ($count >= 8) break;
                $count++;
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <?php if (isLoggedIn()): ?>
                        <div class="product-badge">
                            <button type="button" class="btn-wishlist <?php echo in_array($product['id'], $wishlistIds) ? 'in-wishlist' : ''; ?>" 
                                    onclick="toggleWishlist(<?php echo $product['id']; ?>, this)" title="<?php echo in_array($product['id'], $wishlistIds) ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                <i class="<?php echo in_array($product['id'], $wishlistIds) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                        <div class="product-overlay">
                            <button class="btn-quick-view" onclick="quickView(<?php echo $product['id']; ?>)">
                                <i class="fas fa-eye"></i> Quick View
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                        <div class="product-actions">
                            <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
