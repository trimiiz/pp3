<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'includes/functions.php';
require_once 'includes/header.php';

$page_title = 'Product Details - TechStore';


$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$product = getProductById($product_id);


if (!$product) {
    echo "<div class='container'>";
    echo "<div class='error-message'>";
    echo "<h2>Product Not Found</h2>";
    echo "<p>The product you're looking for doesn't exist.</p>";
    echo "<a href='products.php' class='btn btn-primary'>Back to Products</a>";
    echo "</div>";
    echo "</div>";
    require_once 'includes/footer.php';
    exit;
}

$page_title = $product['name'] . ' - TechStore';
$product_name = $product['name'];
$product_description = $product['description'];
$product_price = formatPrice($product['price']);
$product_image = $product['image_url'] ?? 'http://dummyimage.com/500x500/cccccc/000.png&text=No+Image';
$product_category = $product['category_name'];
$stock_quantity = $product['stock_quantity'];
$in_stock = isInStock($stock_quantity);
$stock_status = getStockStatus($stock_quantity);
$stock_class = $in_stock ? 'in-stock' : 'out-of-stock';


$related_products = getAllProducts($product['category_id']);
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> / 
        <a href="products.php">Products</a> / 
        <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo $product_category; ?></a> / 
        <span><?php echo htmlspecialchars($product_name); ?></span>
    </div>

    <div class="product-detail">
        <div class="product-images">
            <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" id="mainProductImage">
        </div>
        
        <div class="product-info">
            <span class="product-category"><?php echo htmlspecialchars($product_category); ?></span>
            <h1><?php echo htmlspecialchars($product_name); ?></h1>
            <p class="product-price-large"><?php echo $product_price; ?></p>
            
            <div class="stock-info">
                <span class="stock-badge <?php echo $stock_class; ?>"><?php echo $stock_status; ?></span>
                <?php if ($in_stock): ?>
                    <span class="stock-quantity"><?php echo $stock_quantity; ?> units available</span>
                <?php endif; ?>
            </div>
            
            <div class="product-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product_description)); ?></p>
            </div>
            
            <div class="product-actions">
                <?php if ($in_stock): ?>
                    <button class="btn btn-primary btn-large" onclick="addToCart(<?php echo $product_id; ?>)">Add to Cart</button>
                <?php else: ?>
                    <button class="btn btn-disabled btn-large" disabled>Out of Stock</button>
                <?php endif; ?>
                <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
            </div>
        </div>
    </div>

    <?php if (count($related_products) > 1): ?>
    <section class="related-products">
        <h2>Related Products</h2>
        <div class="products-grid">
            <?php
            $related_count = 0;
            foreach ($related_products as $related) {
              
                if ($related['id'] == $product_id || $related_count >= 4) {
                    continue;
                }
                
                $related_id = $related['id'];
                $related_name = $related['name'];
                $related_price = formatPrice($related['price']);
                $related_image = $related['image_url'] ?? 'https://via.placeholder.com/300x300';
                
                echo "<div class='product-card'>";
                echo "<div class='product-image'>";
                echo "<img src='{$related_image}' alt='{$related_name}'>";
                echo "</div>";
                echo "<div class='product-info'>";
                echo "<h3>{$related_name}</h3>";
                echo "<p class='product-price'>{$related_price}</p>";
                echo "<a href='product-detail.php?id={$related_id}' class='btn btn-primary'>View Details</a>";
                echo "</div>";
                echo "</div>";
                
                $related_count++;
            }
            ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
