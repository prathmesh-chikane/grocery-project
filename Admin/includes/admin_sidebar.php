<?php
// Calculate live stock alerts count
$low_stock_badge = 0;
if (isset($con)) {
    $alert_q = mysqli_query($con, "SELECT COUNT(*) as count FROM products WHERE stock_quantity <= 5");
    if ($alert_q) {
        $alert_r = mysqli_fetch_assoc($alert_q);
        $low_stock_badge = intval($alert_r['count']);
    }
}

// Current page detection for active class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand-header">
        <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Store Logo" class="admin-brand-logo" onerror="this.src='../images/admin logo.png'">
        <div>
            <div class="admin-brand-name">Ganesh Store</div>
            <span class="admin-brand-badge"><i class="fa-solid fa-shield-halved me-1"></i> Admin Portal</span>
        </div>
    </div>

    <ul class="admin-sidebar-nav">
        <li class="admin-nav-item">
            <a href="index.php" class="admin-nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="admin-nav-section-title">Catalog Management</div>
        <li class="admin-nav-item">
            <a href="view_products.php" class="admin-nav-link <?php echo ($current_page == 'view_products.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>All Products</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="insert_product.php" class="admin-nav-link <?php echo ($current_page == 'insert_product.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Add Product</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="admin_categories.php" class="admin-nav-link <?php echo ($current_page == 'admin_categories.php' || $current_page == 'view_categories.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="insert_categories.php" class="admin-nav-link <?php echo ($current_page == 'insert_categories.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-folder-plus"></i>
                <span>Add Category</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="stock_alert.php" class="admin-nav-link <?php echo ($current_page == 'stock_alert.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Stock Alerts</span>
                <?php if ($low_stock_badge > 0): ?>
                    <span class="admin-nav-badge badge-alert"><?php echo $low_stock_badge; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <div class="admin-nav-section-title">Orders & Sales</div>
        <li class="admin-nav-item">
            <a href="admin_view_orders.php" class="admin-nav-link <?php echo ($current_page == 'admin_view_orders.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cart-flatbed"></i>
                <span>Orders</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="view_payment.php" class="admin-nav-link <?php echo ($current_page == 'view_payment.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-credit-card"></i>
                <span>Payments</span>
            </a>
        </li>

        <div class="admin-nav-section-title">Users & Messages</div>
        <li class="admin-nav-item">
            <a href="view_registration.php" class="admin-nav-link <?php echo ($current_page == 'view_registration.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i>
                <span>Registered Users</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="view_users.php" class="admin-nav-link <?php echo ($current_page == 'view_users.php' || $current_page == 'view_user.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-check"></i>
                <span>User Directory</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="view_contact.php" class="admin-nav-link <?php echo ($current_page == 'view_contact.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>Contact Messages</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="view_feedback.php" class="admin-nav-link <?php echo ($current_page == 'view_feedback.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-comments"></i>
                <span>Feedback Reviews</span>
            </a>
        </li>

        <div class="admin-nav-section-title">Settings</div>
        <li class="admin-nav-item">
            <a href="admin_profile.php" class="admin-nav-link <?php echo ($current_page == 'admin_profile.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-gear"></i>
                <span>Admin Profile</span>
            </a>
        </li>
    </ul>

    <div class="admin-sidebar-footer">
        <div class="admin-user-info">
            <div class="admin-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div style="min-width: 0;">
                <div class="admin-name-text"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></div>
                <div class="admin-role-text">Store Manager</div>
            </div>
        </div>
        <a href="admin_logout.php" title="Sign Out" class="text-danger ps-2">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
    </div>
</aside>
