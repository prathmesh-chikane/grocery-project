<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Admin Dashboard | Ganesh Store Management";
$page_heading = "Dashboard Overview";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <?php if (isset($_GET['profile_updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>Admin profile settings updated successfully!</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php
        // Fetch dashboard statistics
        $total_products = 0;
        $total_orders = 0;
        $pending_orders = 0;
        $total_users = 0;
        $low_stock_count = 0;
        $total_revenue = 0;

        // Products count
        $q_prod = mysqli_query($con, "SELECT COUNT(*) as count FROM products");
        if ($q_prod) $total_products = intval(mysqli_fetch_assoc($q_prod)['count']);

        // Orders count
        $q_ord = mysqli_query($con, "SELECT COUNT(*) as count FROM orders");
        if ($q_ord) $total_orders = intval(mysqli_fetch_assoc($q_ord)['count']);

        // Pending orders
        $q_pend = mysqli_query($con, "SELECT COUNT(*) as count FROM orders WHERE order_status = 'Pending'");
        if ($q_pend) $pending_orders = intval(mysqli_fetch_assoc($q_pend)['count']);

        // Users count
        $q_usr = mysqli_query($con, "SELECT COUNT(*) as count FROM `user`");
        if ($q_usr) $total_users = intval(mysqli_fetch_assoc($q_usr)['count']);

        // Low stock count
        $q_low = mysqli_query($con, "SELECT COUNT(*) as count FROM products WHERE stock_quantity <= 5");
        if ($q_low) $low_stock_count = intval(mysqli_fetch_assoc($q_low)['count']);

        // Revenue sum
        $q_rev = mysqli_query($con, "SELECT SUM(amount) as total FROM payment WHERE payment_status = 'Successful' OR payment_status = 'Success'");
        if ($q_rev) {
            $r_val = mysqli_fetch_assoc($q_rev)['total'];
            $total_revenue = $r_val ? floatval($r_val) : 0;
        }
        ?>

        <!-- Key Metrics Row -->
        <div class="row g-3 mb-4">
            <!-- Total Revenue -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-green">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">₹<?php echo number_format($total_revenue, 0); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-blue">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders (<?php echo $pending_orders; ?> Pending)</div>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-purple">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $total_products; ?></div>
                        <div class="stat-label">Products in Store</div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $low_stock_count; ?></div>
                        <div class="stat-label">Low Stock Alerts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Banner -->
        <div class="admin-card-table p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="fw-bold small text-muted text-uppercase">
                    <i class="fa-solid fa-bolt text-warning me-1"></i> Quick Shortcuts:
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="insert_product.php" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i> Add Product
                    </a>
                    <a href="insert_categories.php" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-folder-plus me-1"></i> Add Category
                    </a>
                    <a href="admin_view_orders.php" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-list-check me-1"></i> Process Orders
                    </a>
                    <a href="stock_alert.php" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-bell me-1"></i> Stock Monitor
                    </a>
                </div>
            </div>
        </div>

        <!-- Low Stock Notice Banner (If low stock exists) -->
        <?php if ($low_stock_count > 0): ?>
        <div class="alert alert-warning border-warning d-flex align-items-center justify-content-between flex-wrap gap-2 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation fs-4 text-warning"></i>
                <div>
                    <strong>Attention: <?php echo $low_stock_count; ?> items are critically low in stock!</strong>
                    <div class="small">Restock them promptly to avoid missing customer orders.</div>
                </div>
            </div>
            <a href="stock_alert.php" class="btn btn-sm btn-warning fw-bold rounded-pill px-3">View Low Stock Items</a>
        </div>
        <?php endif; ?>

        <!-- Two Columns: Recent Orders & Recent Products -->
        <div class="row g-4">
            <!-- Recent Orders Table -->
            <div class="col-lg-7">
                <div class="admin-card-table h-100">
                    <div class="admin-card-header">
                        <h3><i class="fa-solid fa-cart-flatbed text-success me-2"></i> Recent Orders</h3>
                        <a href="admin_view_orders.php" class="small text-success fw-bold text-decoration-none">View All &rarr;</a>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_orders = mysqli_query($con, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
                                if ($recent_orders && mysqli_num_rows($recent_orders) > 0) {
                                    while ($ord = mysqli_fetch_assoc($recent_orders)) {
                                        $status = $ord['order_status'];
                                        $pill_class = 'status-pending';
                                        if ($status == 'Completed' || $status == 'Delivered') $pill_class = 'status-delivered';
                                        elseif ($status == 'Shipped') $pill_class = 'status-shipped';
                                        elseif ($status == 'Cancelled') $pill_class = 'status-cancelled';

                                        echo "<tr>
                                            <td class='fw-bold'>{$ord['order_id']}</td>
                                            <td class='fw-semibold'>" . htmlspecialchars($ord['first_name'] . ' ' . $ord['last_name']) . "</td>
                                            <td class='small text-muted'>" . date('d M, h:i A', strtotime($ord['order_date'])) . "</td>
                                            <td><span class='status-pill $pill_class'>$status</span></td>
                                            <td class='small'>" . htmlspecialchars($ord['payment_mode']) . "</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No orders placed yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Products Preview -->
            <div class="col-lg-5">
                <div class="admin-card-table h-100">
                    <div class="admin-card-header">
                        <h3><i class="fa-solid fa-box text-primary me-2"></i> Products In Store</h3>
                        <a href="view_products.php" class="small text-primary fw-bold text-decoration-none">View All &rarr;</a>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_prods = mysqli_query($con, "SELECT * FROM products ORDER BY product_id DESC LIMIT 5");
                                if ($recent_prods && mysqli_num_rows($recent_prods) > 0) {
                                    while ($p = mysqli_fetch_assoc($recent_prods)) {
                                        $stock = intval($p['stock_quantity'] ?? 0);
                                        $stock_badge = ($stock > 5) ? "<span class='badge bg-success small'>$stock in stock</span>" : "<span class='badge bg-danger small'>$stock left</span>";

                                        echo "<tr>
                                            <td><img src='product_images/" . htmlspecialchars($p['product_image']) . "' class='admin-table-img' onerror=\"this.onerror=null;this.src='../images/kirana-new-bf88.jpg';\"></td>
                                            <td class='fw-semibold small'>" . htmlspecialchars($p['product_name']) . "</td>
                                            <td class='fw-bold text-success small'>" . htmlspecialchars($p['Product_price']) . "</td>
                                            <td>$stock_badge</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No products added yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
