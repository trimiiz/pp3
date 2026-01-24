<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$page_title = 'Home - TechStore';
$categories = getAllCategories();
$featured_products = getAllProducts();
?>

<div class="hero-section">
    <div class="hero-slider" id="heroSlider">
        <div class="slide active">
            <div class="slide-content">
                <h1>Welcome to TechStore</h1>
                <p>Your one-stop shop for the latest technology</p>
                <a href="products.php" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
        <div class="slide">
            <div class="slide-content">
                <img src="images/phones.jpg" alt="" border="5">
                <h1>Latest Phones</h1>
                <p>Discover the newest smartphones</p>
                <a href="products.php?category=1" class="btn btn-primary">View Phones</a>
            </div>
        </div>
        <div class="slide">
            <div class="slide-content">
                <h1>Premium TVs</h1>
                <p>Experience the best in home entertainment</p>
                <a href="products.php?category=2" class="btn btn-primary">View TVs</a>
            </div>
        </div>
    </div>
    <div class="slider-controls">
        <button class="slider-btn prev" onclick="changeSlide(-1)">❮</button>
        <button class="slider-btn next" onclick="changeSlide(1)">❯</button>
    </div>
    <div class="slider-dots">
        <?php
        
        for ($i = 0; $i < 3; $i++) {
            $active_class = ($i === 0) ? 'active' : '';
            echo "<span class='dot {$active_class}' onclick='goToSlide({$i})'></span>";
        }
        ?>
    </div>
</div>

<div class="container">
    <section class="categories-section">
        <h2>Shop by Category</h2>
        <div class="categories-grid">
            <?php
            
            foreach ($categories as $category) {
                $category_id = $category['id'];
                $category_name = $category['name'];
                $category_desc = $category['description'] ?? 'Browse our ' . $category_name;
                
                echo "<div class='category-card'>";
                echo "<h3>{$category_name}</h3>";
                echo "<p>{$category_desc}</p>";
                echo "<a href='products.php?category={$category_id}' class='btn btn-secondary'>View Products</a>";
                echo "</div>";
            }
            ?>
        </div>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="products-grid">
            <?php
       
            $product_count = 0;
            foreach ($featured_products as $product) {
               
                if ($product_count >= 6) {
                    break;
                }
                
                $product_id = $product['id'];
                $product_name = $product['name'];
                $product_price = formatPrice($product['price']);
                $product_image = $product['image_url'] ?? 'http://dummyimage.com/300x300/cccccc/000.png&text=No+Image';
                $in_stock = isInStock($product['stock_quantity']);
                $stock_class = $in_stock ? 'in-stock' : 'out-of-stock';
                
                echo "<div class='product-card'>";
                echo "<div class='product-image'>";
                echo "<img src='{$product_image}' alt='{$product_name}'>";
                echo "<span class='stock-badge {$stock_class}'>" . getStockStatus($product['stock_quantity']) . "</span>";
                echo "</div>";
                echo "<div class='product-info'>";
                echo "<h3>{$product_name}</h3>";
                echo "<p class='product-price'>{$product_price}</p>";
                echo "<a href='product-detail.php?id={$product_id}' class='btn btn-primary'>View Details</a>";
                echo "</div>";
                echo "</div>";
                
                $product_count++;
            }
            ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
