<?php
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/../functions/common_function.php';

// Start session safely if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Ganesh General Store | Fresh Groceries & Daily Essentials'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Custom Grocery Design System -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Announcement Bar -->
<div class="top-bar-announcement">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-bolt me-1"></i>15 MINS</span>
            <span>Superfast Grocery Delivery • 100% Quality Guaranteed</span>
        </div>
        <div class="d-none d-md-flex align-items-center gap-3">
            <span><i class="fa-solid fa-phone me-1"></i> Helpline: +91 97654 73412</span>
            <span><i class="fa-solid fa-clock me-1"></i> Open: 9 AM - 9 PM</span>
        </div>
    </div>
</div>

<!-- Main Sticky Header -->
<header class="grocery-navbar">
    <div class="container">
        <div class="row align-items-center g-2 g-md-3">
            <!-- Brand Logo -->
            <div class="col-auto">
                <a href="index.php" class="grocery-brand">
                    <img src="./images/ganesh_store_logo__1_-removebg-preview.png" alt="Ganesh Store" class="brand-logo-img" onerror="this.src='./images/ganesh store logo.png'">
                    <div>
                        <h1 class="brand-title">Ganesh<span>Store</span></h1>
                        <p class="brand-subtitle">Fresh Daily Supermarket</p>
                    </div>
                </a>
            </div>

            <!-- Location Badge (Desktop) -->
            <div class="col-auto d-none d-xl-block">
                <div class="delivery-location-pill">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <div style="font-size:0.7rem; color:var(--text-muted); line-height:1;">Deliver to</div>
                        <div style="line-height:1.2;">Satara, 415012</div>
                    </div>
                    <span class="delivery-time-badge">15m</span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="col col-md">
                <div class="search-form-wrapper">
                    <form action="search_product.php" method="get" class="search-input-group">
                        <i class="fa-solid fa-magnifying-glass search-icon-left"></i>
                        <input type="search" class="search-input-field" name="search_data" placeholder="Search for milk, rice, oil, snacks..." value="<?php echo htmlspecialchars($_GET['search_data'] ?? ''); ?>" required autocomplete="off">
                        <button type="submit" name="search_data_product" class="search-submit-btn" title="Search">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Actions (User & Cart) -->
            <div class="col-auto d-flex align-items-center gap-2">
                <!-- User Account Dropdown -->
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <button class="nav-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user text-success"></i>
                            <span class="d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="./user/profile.php"><i class="fa-solid fa-user me-2 text-primary"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="cart.php"><i class="fa-solid fa-bag-shopping me-2 text-success"></i> My Cart</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="./user/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="./user/user_login.php" class="nav-action-btn">
                        <i class="fa-regular fa-user"></i>
                        <span class="d-none d-sm-inline">Login</span>
                    </a>
                <?php endif; ?>

                <!-- Cart Button -->
                <a href="cart.php" class="nav-action-btn nav-cart-btn" title="View Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="d-none d-sm-inline">My Cart</span>
                    <span class="cart-count-badge"><?php cart_item(); ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Horizontal Category Quick Bar -->
<div class="category-nav-bar">
    <div class="container">
        <div class="d-flex align-items-center gap-2">
            <a href="display_all.php" class="category-pill-link" style="background:#0f172a; color:#ffffff;"><i class="fa-solid fa-grid-2 me-1"></i> All Products</a>
            <?php if (function_exists('get_category_pills')) get_category_pills(); ?>
            <a href="about.php" class="category-pill-link"><i class="fa-solid fa-circle-info me-1"></i> About</a>
            <a href="contact_us.php" class="category-pill-link"><i class="fa-solid fa-headset me-1"></i> Contact</a>
            <a href="feedback_form.php" class="category-pill-link"><i class="fa-solid fa-star me-1"></i> Feedback</a>
        </div>
    </div>
</div>

<!-- Process Add To Cart Trigger -->
<?php cart(); ?>
