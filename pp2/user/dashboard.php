<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$database = new Database();
$conn = $database->getConnection();

$pageTitle = "My Dashboard - TechStore";
$tab = isset($_GET['tab']) ? sanitizeInput($_GET['tab']) : 'profile';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$profileError = '';
$profileSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profile_update'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $dateOfBirth = sanitizeInput($_POST['date_of_birth'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $city = sanitizeInput($_POST['city'] ?? '');
    $zipCode = sanitizeInput($_POST['zip_code'] ?? '');
    $gender = sanitizeInput($_POST['gender'] ?? '');
    
    if (empty($username) || empty($email)) {
        $profileError = "Username and email are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileError = "Please enter a valid email";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $profileError = "Username or email already in use";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, date_of_birth = ?, address = ?, city = ?, zip_code = ?, gender = ? WHERE id = ?");
            $stmt->execute([
                $username,
                $email,
                $dateOfBirth ?: null,
                $address ?: null,
                $city ?: null,
                $zipCode ?: null,
                $gender ?: null,
                $_SESSION['user_id']
            ]);
            $_SESSION['username'] = $username;
            $user = array_merge($user, [
                'username' => $username,
                'email' => $email,
                'date_of_birth' => $dateOfBirth,
                'address' => $address,
                'city' => $city,
                'zip_code' => $zipCode,
                'gender' => $gender
            ]);
            $profileSuccess = "Profile updated successfully";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_update'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (!password_verify($currentPassword, $user['password'])) {
        $profileError = "Current password is incorrect";
    } elseif (strlen($newPassword) < 6) {
        $profileError = "New password must be at least 6 characters";
    } elseif ($newPassword !== $confirmPassword) {
        $profileError = "New passwords do not match";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
        $profileSuccess = "Password updated successfully";
    }
}

$stmt = $conn->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$stmt = $conn->prepare("
    SELECT p.*, w.id as wishlist_id 
    FROM wishlist w 
    JOIN products p ON p.id = w.product_id 
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$wishlistProducts = $stmt->fetchAll();

$base = '../';
include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>My Dashboard</h1>
    </div>

    <div class="user-dashboard-tabs">
        <a href="?tab=profile" class="tab-btn <?php echo $tab === 'profile' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a>
        <a href="?tab=orders" class="tab-btn <?php echo $tab === 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i> My Orders
        </a>
        <a href="?tab=wishlist" class="tab-btn <?php echo $tab === 'wishlist' ? 'active' : ''; ?>">
            <i class="fas fa-heart"></i> Wishlist
        </a>
    </div>

    <?php if ($tab === 'profile'): ?>
        <div class="dashboard-section user-profile-section">
            <?php if ($profileError): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($profileError); ?></div>
            <?php endif; ?>
            <?php if ($profileSuccess): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($profileSuccess); ?></div>
            <?php endif; ?>

            <h2>Profile Information</h2>
            <form method="POST" class="profile-form">
                <input type="hidden" name="profile_update" value="1">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" 
                               value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select</option>
                            <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" 
                           value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" 
                               value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="zip_code">ZIP Code</label>
                        <input type="text" id="zip_code" name="zip_code" 
                               value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>

            <h2 class="mt-2">Change Password</h2>
            <form method="POST" class="profile-form">
                <input type="hidden" name="password_update" value="1">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>

    <?php elseif ($tab === 'orders'): ?>
        <div class="dashboard-section">
            <h2>My Orders</h2>
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p>You haven't placed any orders yet.</p>
                    <a href="../products.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Order #<?php echo $order['id']; ?></span>
                                <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <span><?php echo $order['item_count']; ?> item(s)</span>
                                <span class="order-total"><?php echo formatPrice($order['total']); ?></span>
                            </div>
                            <a href="../order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($tab === 'wishlist'): ?>
        <div class="dashboard-section">
            <h2>My Wishlist</h2>
            <?php if (empty($wishlistProducts)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <p>Your wishlist is empty.</p>
                    <a href="../products.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="products-grid wishlist-grid">
                    <?php foreach ($wishlistProducts as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='../assets/images/placeholder.jpg'">
                                <div class="product-overlay">
                                    <a href="../product_detail.php?id=<?php echo $product['id']; ?>" class="btn-quick-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                                <div class="product-actions">
                                    <button class="btn btn-primary" 
                                            onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)"
                                            <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <button class="btn btn-secondary btn-wishlist-remove" 
                                            onclick="removeFromWishlist(<?php echo $product['id']; ?>, this)">
                                        <i class="fas fa-heart-broken"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.user-dashboard-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.tab-btn {
    padding: 0.75rem 1.5rem;
    background: white;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: all 0.3s;
}
.tab-btn:hover, .tab-btn.active {
    background: var(--primary-color);
    color: white;
}
.user-profile-section .mt-2 { margin-top: 2rem; }
.orders-list { display: flex; flex-direction: column; gap: 1rem; }
.order-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.order-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}
.order-id { font-weight: bold; }
.order-date { color: var(--text-light); }
.order-details {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}
.order-total { font-weight: bold; color: var(--primary-color); }
.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.empty-state i {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1rem;
}
.wishlist-grid .product-actions { flex-direction: column; }
.btn-wishlist-remove { margin-top: 0.5rem; }
</style>

<script>
function removeFromWishlist(productId, btn) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);
    fetch('../add_to_wishlist.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.closest('.product-card').remove();
                if (typeof showNotification === 'function') showNotification('Removed from wishlist', 'success');
            }
        });
}
</script>

<?php include '../includes/footer.php'; ?>
