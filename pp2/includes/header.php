<?php
$base = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'E-Commerce Store'; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css?v=<?php echo @filemtime(__DIR__ . '/../assets/css/style.css') ?: time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script>window.TECHSTORE_BASE = '<?php echo $base; ?>';</script>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo $base; ?>index.php"><i class="fas fa-shopping-bag"></i> TechStore</a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                <li class="nav-dropdown">
                    <a href="<?php echo $base; ?>products.php" class="nav-dropdown-toggle"><i class="fas fa-th-large"></i> Categories <i class="fas fa-chevron-down"></i></a>
                    <ul class="nav-dropdown-menu">
                        <li><a href="<?php echo $base; ?>products.php?category=TVs">TVs</a></li>
                        <li><a href="<?php echo $base; ?>products.php?category=Phones">Phones</a></li>
                        <li><a href="<?php echo $base; ?>products.php?category=Tablets">Tablets</a></li>
                        <li><a href="<?php echo $base; ?>products.php?category=Laptops">Laptops</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $base; ?>cart.php"><i class="fas fa-shopping-cart"></i> Cart (<span id="cart-count"><?php echo getCartCount(); ?></span>)</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : $base . 'admin/dashboard.php'; ?>">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>user/dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo $base; ?>user/dashboard.php?tab=wishlist"><i class="fas fa-heart"></i> Wishlist<?php 
                            $db = new Database();
                            $dbConn = $db->getConnection();
                            $wishCount = getWishlistCount($dbConn, $_SESSION['user_id']);
                            if ($wishCount > 0) echo ' (' . $wishCount . ')';
                        ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
