<?php
/**
 * Admin Products Management
 * CRUD operations for products
 */

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
require_once '../includes/functions.php';
require_once '../config/database.php';

$page_title = 'Manage Products';

if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
}

$pdo = getDBConnection();
$message = $_GET['message'] ?? '';
$message_type = $_GET['type'] ?? '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'edit') {
        // Sanitize input data using function
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);
        $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
        $image_url = sanitizeInput($_POST['image_url'] ?? '');
        
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = '../images/';
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'images/' . $file_name;
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
        
        // Validate using function
        $errors = validateProduct([
            'name' => $name,
            'price' => $price,
            'category_id' => $category_id
        ]);
        
        if (empty($errors)) {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity, image_url) 
                                       VALUES (:name, :description, :price, :category_id, :stock_quantity, :image_url)");
                $stmt->execute([
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category_id' => $category_id,
                    'stock_quantity' => $stock_quantity,
                    'image_url' => $image_url
                ]);
                $message = 'Product created successfully!';
                $message_type = 'success';
                // Redirect to list after create
                header("Location: products.php?message=" . urlencode($message) . "&type=" . $message_type);
                exit;
            } else {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE products 
                                       SET name = :name, description = :description, price = :price, 
                                           category_id = :category_id, stock_quantity = :stock_quantity, 
                                           image_url = :image_url 
                                       WHERE id = :id");
                $stmt->execute([
                    'id' => $id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category_id' => $category_id,
                    'stock_quantity' => $stock_quantity,
                    'image_url' => $image_url
                ]);
                $message = 'Product updated successfully!';
                $message_type = 'success';
                // Redirect to list after update
                header("Location: products.php?message=" . urlencode($message) . "&type=" . $message_type);
                exit;
            }
        } else {
            $message = implode('<br>', $errors);
            $message_type = 'error';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message = 'Product deleted successfully!';
        $message_type = 'success';
        // Redirect to list after delete
        header("Location: products.php?message=" . urlencode($message) . "&type=" . $message_type);
        exit;
    }
}

// Get action and product ID from URL
$action = $_GET['action'] ?? 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product data for edit
$product = null;
if ($action === 'edit' && $product_id) {
    $product = getProductById($product_id);
    if (!$product) {
        $action = 'list';
    }
}

$categories = getAllCategories();
$products = getAllProducts();
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
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="products.php" class="active">Manage Products</a></li>
                    <li><a href="categories.php">Manage Categories</a></li>
                    <li><a href="../index.php">View Store</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Products</h1>
                <a href="products.php?action=create" class="btn btn-primary">Add New Product</a>
            </header>

            <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <?php if ($action === 'create' || $action === 'edit'): ?>
            <!-- CREATE/UPDATE Form -->
            <div class="form-container">
                <h2><?php echo $action === 'create' ? 'Create New Product' : 'Edit Product'; ?></h2>
                <form method="POST" enctype="multipart/form-data" class="crud-form">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required 
                                   value="<?php echo $product['price'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                foreach ($categories as $category) {
                                    $selected = ($product['category_id'] ?? 0) == $category['id'] ? 'selected' : '';
                                    echo "<option value='{$category['id']}' {$selected}>{$category['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" 
                                   value="<?php echo $product['stock_quantity'] ?? 0; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input type="url" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>">
                            <small>Or upload an image file below</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image">Upload Image File</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $action === 'create' ? 'Create Product' : 'Update Product'; ?>
                        </button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- READ/List Products -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Using foreach loop to display all products
                        if (empty($products)) {
                            echo "<tr><td colspan='7' class='text-center'>No products found.</td></tr>";
                        } else {
                            foreach ($products as $prod) {
                                $stock_class = $prod['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock';
                                $prod_image = $prod['image_url'] ?: 'http://dummyimage.com/50x50/cccccc/000.png&text=Img';
                                
                                echo "<tr>";
                                echo "<td>{$prod['id']}</td>";
                                echo "<td><img src='{$prod_image}' alt='{$prod['name']}' class='table-image'></td>";
                                echo "<td>" . htmlspecialchars($prod['name']) . "</td>";
                                echo "<td>{$prod['category_name']}</td>";
                                echo "<td>" . formatPrice($prod['price']) . "</td>";
                                echo "<td><span class='stock-badge {$stock_class}'>{$prod['stock_quantity']}</span></td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='products.php?action=edit&id={$prod['id']}' class='btn btn-small'>Edit</a> ";
                                echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>";
                                echo "<input type='hidden' name='action' value='delete'>";
                                echo "<input type='hidden' name='id' value='{$prod['id']}'>";
                                echo "<button type='submit' class='btn btn-small btn-danger'>Delete</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
