<?php
/**
 * Admin Products Management (CRUD Forms)
 * Demonstrates: CRUD Forms, Loops, Conditionals, Functions, Variables, Parameters
 */

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Manage Products - TechStore";
$message = '';
$messageType = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // CREATE
    if ($action === 'create') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = sanitizeInput($_POST['category'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);
        $image = sanitizeInput($_POST['image'] ?? 'placeholder.jpg');
        
        // Handle file upload if provided
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['image_file']['tmp_name']);
            finfo_close($finfo);
            if (in_array($mimeType, $allowedTypes) && $_FILES['image_file']['size'] <= 5 * 1024 * 1024) {
                $uploadDir = dirname(__DIR__) . '/assets/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
                $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
                $image = $baseName . '.' . $ext;
                move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $image);
            }
        }
        
        if ($name && $price > 0 && $category) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $price, $category, $stock, $image])) {
                $message = "Product created successfully!";
                $messageType = "success";
            } else {
                $message = "Error creating product";
                $messageType = "error";
            }
        } else {
            $message = "Please fill in all required fields";
            $messageType = "error";
        }
    }
    
    // UPDATE
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = sanitizeInput($_POST['category'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);
        $image = sanitizeInput($_POST['image'] ?? 'placeholder.jpg');
        
        // Handle file upload if provided
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['image_file']['tmp_name']);
            finfo_close($finfo);
            if (in_array($mimeType, $allowedTypes) && $_FILES['image_file']['size'] <= 5 * 1024 * 1024) {
                $uploadDir = dirname(__DIR__) . '/assets/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
                $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
                $image = $baseName . '.' . $ext;
                move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $image);
            }
        }
        
        if ($id && $name && $price > 0 && $category) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ?, image = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $price, $category, $stock, $image, $id])) {
                $message = "Product updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating product";
                $messageType = "error";
            }
        }
    }
    
    // DELETE
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = "Product deleted successfully!";
                $messageType = "success";
            } else {
                $message = "Error deleting product";
                $messageType = "error";
            }
        }
    }
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editProduct = getProductById($conn, $editId);
}

// Get all products
$products = getProductsByCategory($conn);
$categories = ['TVs', 'Phones', 'Tablets', 'Laptops'];

include '../includes/header.php';
?>

<div class="container">
    <div class="admin-header">
        <h1>Manage Products</h1>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="admin-crud-section">
        <div class="crud-form">
            <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $editProduct ? 'update' : 'create'; ?>">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" required 
                               value="<?php echo $editProduct['price'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" 
                               value="<?php echo $editProduct['stock'] ?? 0; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" 
                                        <?php echo (isset($editProduct['category']) && $editProduct['category'] === $cat) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="image-upload-zone" id="imageUploadZone" 
                             ondragover="event.preventDefault(); this.classList.add('dragover');"
                             ondragleave="this.classList.remove('dragover');"
                             ondrop="handleImageDrop(event);">
                            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp" 
                                   style="display:none" onchange="handleImageSelect(this)">
                            <div class="upload-zone-content" onclick="document.getElementById('image_file').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drop image here or click to browse</p>
                                <p class="upload-hint">JPG, PNG, GIF or WebP (max 5MB)</p>
                            </div>
                            <div class="image-preview" id="imagePreview" style="display:none">
                                <img id="previewImg" src="" alt="Preview">
                                <span class="preview-filename" id="previewFilename"></span>
                            </div>
                        </div>
                        <input type="hidden" id="image" name="image" 
                               value="<?php echo htmlspecialchars($editProduct['image'] ?? 'placeholder.jpg'); ?>">
                        <p class="form-hint"><?php echo $editProduct ? 'Current: ' : 'Will use: '; ?><span id="currentImage"><?php echo htmlspecialchars($editProduct['image'] ?? 'placeholder.jpg'); ?></span></p>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?php echo $editProduct ? 'Update Product' : 'Create Product'; ?>
                </button>
                <?php if ($editProduct): ?>
                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="products-list-section">
        <h2>All Products</h2>
        <table class="admin-table">
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
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6">No products found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo formatPrice($product['price']); ?></td>
                            <td>
                                <span class="stock-badge <?php echo $product['stock'] < 5 ? 'stock-low' : ''; ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-sm">Edit</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.image-upload-zone {
    border: 2px dashed var(--border-color);
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    min-height: 120px;
    background: var(--light-color);
}
.image-upload-zone:hover,
.image-upload-zone.dragover {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
}
.upload-zone-content {
    color: var(--text-light);
}
.upload-zone-content i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}
.upload-hint {
    font-size: 0.8rem;
    margin-top: 0.25rem;
}
.image-preview img {
    max-width: 150px;
    max-height: 100px;
    object-fit: contain;
    border-radius: 5px;
}
.preview-filename {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-light);
}
.form-hint {
    font-size: 0.875rem;
    color: var(--text-light);
    margin-top: 0.5rem;
}
</style>
<script>
function handleImageDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length && files[0].type.startsWith('image/')) {
        document.getElementById('image_file').files = files;
        handleImageSelect(document.getElementById('image_file'));
    }
}
function handleImageSelect(input) {
    const file = input.files[0];
    const zone = document.getElementById('imageUploadZone');
    const content = zone.querySelector('.upload-zone-content');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const previewFilename = document.getElementById('previewFilename');
    const imageInput = document.getElementById('image');
    const currentSpan = document.getElementById('currentImage');
    
    if (file) {
        content.style.display = 'none';
        preview.style.display = 'block';
        previewImg.src = URL.createObjectURL(file);
        previewFilename.textContent = file.name;
        const baseName = file.name.replace(/\.[^/.]+$/, '');
        const ext = file.name.split('.').pop().toLowerCase();
        const safeName = baseName.replace(/[^a-zA-Z0-9_-]/g, '_') + '.' + ext;
        imageInput.value = safeName;
        currentSpan.textContent = safeName;
    } else {
        content.style.display = 'block';
        preview.style.display = 'none';
    }
}
<?php if ($editProduct && !empty($editProduct['image'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const img = document.getElementById('previewImg');
    const preview = document.getElementById('imagePreview');
    const content = document.querySelector('.upload-zone-content');
    if (img && preview && content) {
        img.src = '../assets/images/<?php echo htmlspecialchars($editProduct['image']); ?>';
        img.onerror = function() { preview.style.display = 'none'; content.style.display = 'block'; };
        img.onload = function() {
            content.style.display = 'none';
            preview.style.display = 'block';
            document.getElementById('previewFilename').textContent = '<?php echo htmlspecialchars($editProduct['image']); ?>';
        };
    }
});
<?php endif; ?>
</script>
<?php include '../includes/footer.php'; ?>
