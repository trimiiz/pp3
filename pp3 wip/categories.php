<?php

session_start();
require_once '../includes/functions.php';
require_once '../config/database.php';

$page_title = 'Manage Categories';

if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
}

$pdo = getDBConnection();
$message = '';
$message_type = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        
        if (empty($name)) {
            $message = 'Category name is required!';
            $message_type = 'error';
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
                $stmt->execute(['name' => $name, 'description' => $description]);
                $message = 'Category created successfully!';
                $message_type = 'success';
            } else {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
                $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description]);
                $message = 'Category updated successfully!';
                $message_type = 'success';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message = 'Category deleted successfully!';
        $message_type = 'success';
    }
}

$action = $_GET['action'] ?? 'list';
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$category = null;
if ($action === 'edit' && $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute(['id' => $category_id]);
    $category = $stmt->fetch();
    if (!$category) {
        $action = 'list';
    }
}

$categories = getAllCategories();
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
                    <li><a href="products.php">Manage Products</a></li>
                    <li><a href="categories.php" class="active">Manage Categories</a></li>
                    <li><a href="../index.php">View Store</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Categories</h1>
                <a href="categories.php?action=create" class="btn btn-primary">Add New Category</a>
            </header>

            <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <?php if ($action === 'create' || $action === 'edit'): ?>
            <div class="form-container">
                <h2><?php echo $action === 'create' ? 'Create New Category' : 'Edit Category'; ?></h2>
                <form method="POST" class="crud-form">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $action === 'create' ? 'Create Category' : 'Update Category'; ?>
                        </button>
                        <a href="categories.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (empty($categories)) {
                            echo "<tr><td colspan='4' class='text-center'>No categories found.</td></tr>";
                        } else {
                            foreach ($categories as $cat) {
                                echo "<tr>";
                                echo "<td>{$cat['id']}</td>";
                                echo "<td>" . htmlspecialchars($cat['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($cat['description'] ?? '') . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='categories.php?action=edit&id={$cat['id']}' class='btn btn-small'>Edit</a> ";
                                echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure? This will delete all products in this category.\");'>";
                                echo "<input type='hidden' name='action' value='delete'>";
                                echo "<input type='hidden' name='id' value='{$cat['id']}'>";
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
