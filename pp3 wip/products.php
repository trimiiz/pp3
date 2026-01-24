<?php


require_once 'includes/functions.php';
require_once 'includes/header.php';

$page_title = 'Products - TechStore';


$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : null;


if ($selected_category) {
    $products = getAllProducts($selected_category);
    $category_name = '';
    $categories = getAllCategories();
    foreach ($categories as $cat) {
        if ($cat['id'] == $selected_category) {
            $category_name = $cat['name'];
            break;
        }
    }
} else {
    $products = getAllProducts();
    $category_name = 'All Products';
}

$categories = getAllCategories();
?>

<div class="container">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($category_name); ?></h1>
    </div>

    <div class="products-layout">
        <aside class="sidebar">
            <h3>Filter by Category</h3>
            <ul class="category-filter">
                <li>
                    <a href="products.php" class="<?php echo !$selected_category ? 'active' : ''; ?>">
                        All Products
                    </a>
                </li>
                <?php
                
                foreach ($categories as $category) {
                    $cat_id = $category['id'];
                    $cat_name = $category['name'];
                    $active_class = ($selected_category == $cat_id) ? 'active' : '';
                    
                    echo "<li>";
                    echo "<a href='products.php?category={$cat_id}' class='{$active_class}'>";
                    echo htmlspecialchars($cat_name);
                    echo "</a>";
                    echo "</li>";
                }
                ?>
            </ul>
        </aside>

        <div class="products-content">
            <?php
        
            if (empty($products)) {
                echo "<div class='no-products'>";
                echo "<p>No products found in this category.</p>";
                echo "<a href='products.php' class='btn btn-primary'>View All Products</a>";
                echo "</div>";
            } else {
                echo "<div class='products-grid'>";
                
              
                foreach ($products as $product) {
                    $product_id = $product['id'];
                    $product_name = $product['name'];
                    $product_price = formatPrice($product['price']);
                    $product_image = $product['image_url'] ?? 'http://dummyimage.com/300x300/cccccc/000.png&text=No+Image';
                    $product_category = $product['category_name'];
                    $in_stock = isInStock($product['stock_quantity']);
                    $stock_status = getStockStatus($product['stock_quantity']);
                    $stock_class = $in_stock ? 'in-stock' : 'out-of-stock';
                    
                    echo "<div class='product-card'>";
                    echo "<div class='product-image'>";
                    echo "<img src='{$product_image}' alt='{$product_name}'>";
                    echo "<span class='stock-badge {$stock_class}'>{$stock_status}</span>";
                    echo "</div>";
                    echo "<div class='product-info'>";
                    echo "<span class='product-category'>{$product_category}</span>";
                    echo "<h3>{$product_name}</h3>";
                    echo "<p class='product-price'>{$product_price}</p>";
                    echo "<a href='product-detail.php?id={$product_id}' class='btn btn-primary'>View Details</a>";
                    echo "</div>";
                    echo "</div>";
                }
                
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
