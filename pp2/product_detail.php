<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($conn, $productId);

if (!$product) {
    redirect('products.php');
}

$inWishlist = isLoggedIn() && isInWishlist($conn, $_SESSION['user_id'], $productId);
$pageTitle = htmlspecialchars($product['name']) . " - TechStore";

include 'includes/header.php';
?>

<div class="container">
    <div class="product-detail">
        <div class="product-detail-image">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 onerror="this.src='assets/images/placeholder.jpg'">
        </div>
        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
            <p class="product-price-large"><?php echo formatPrice($product['price']); ?></p>
            <div class="product-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            <div class="product-stock">
                <?php if ($product['stock'] > 0): ?>
                    <span class="stock-available">In Stock (<?php echo $product['stock']; ?> available)</span>
                <?php else: ?>
                    <span class="stock-unavailable">Out of Stock</span>
                <?php endif; ?>
            </div>
            <div class="product-actions">
                <button class="btn btn-primary btn-large" 
                        onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)"
                        <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
                <?php if (isLoggedIn()): ?>
                    <button type="button" class="btn btn-secondary btn-wishlist <?php echo $inWishlist ? 'in-wishlist' : ''; ?>" 
                            onclick="toggleWishlist(<?php echo $product['id']; ?>, this)">
                        <i class="<?php echo $inWishlist ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i> <?php echo $inWishlist ? 'In Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                <?php endif; ?>
                <a href="products.php" class="btn btn-secondary">Back to Products</a>
            </div>
        </div>
    </div>
</div>

<style>
.product-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin: 2rem 0;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.product-detail-image img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.product-detail-info h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.product-price-large {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
    margin: 1.5rem 0;
}

.product-description {
    margin: 2rem 0;
}

.product-description h3 {
    margin-bottom: 1rem;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
