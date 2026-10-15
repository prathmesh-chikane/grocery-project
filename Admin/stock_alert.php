<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

// Handle quick restock form submission
if (isset($_POST['restock_product'])) {
    $p_id = intval($_POST['product_id']);
    $add_qty = intval($_POST['additional_stock'] ?? 0);

    if ($add_qty > 0) {
        $update_stock = "UPDATE `products` SET stock_quantity = stock_quantity + $add_qty WHERE product_id = $p_id";
        if (mysqli_query($con, $update_stock)) {
            $alert_msg = "Successfully added $add_qty units to inventory!";
            $alert_type = "success";
        } else {
            $alert_msg = "Error updating stock: " . mysqli_error($con);
            $alert_type = "danger";
        }
    }
}

$page_title = "Stock Inventory Alerts | Ganesh Store Admin";
$page_heading = "Low Stock & Restock Alerts";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');

// Fetch low stock items (<= 5)
$low_stock_query = "SELECT p.*, c.category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    WHERE p.stock_quantity <= 5 
                    ORDER BY p.stock_quantity ASC";
$low_stock_res = mysqli_query($con, $low_stock_query);
$count_alerts = mysqli_num_rows($low_stock_res);
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <?php if ($alert_msg): ?>
            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show d-flex align-items-center gap-2 small rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div><?php echo htmlspecialchars($alert_msg); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Warning Top Banner -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="stat-card" style="border-left: 4px solid #ef4444;">
                    <div class="stat-icon-wrapper icon-red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $count_alerts; ?></div>
                        <div class="stat-label">Products Requiring Attention (&le; 5 units)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card" style="border-left: 4px solid #10b981;">
                    <div class="stat-icon-wrapper icon-green">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">Instant Restock</div>
                        <div class="stat-label">Add incoming inventory below to replenish items</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-bell text-danger me-2"></i> Low Inventory Monitor</h3>
                    <p class="text-muted small m-0">Products running critically low or out of stock</p>
                </div>
                <a href="view_products.php" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                    <i class="fa-solid fa-boxes-stacked me-1"></i> Full Inventory
                </a>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Urgency Level</th>
                            <th style="width: 250px;">Quick Restock (+ Units)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($low_stock_res && $count_alerts > 0) {
                            while ($prod = mysqli_fetch_assoc($low_stock_res)) {
                                $pid = $prod['product_id'];
                                $stock = intval($prod['stock_quantity'] ?? 0);
                                $status_badge = "";

                                if ($stock <= 0) {
                                    $status_badge = "<span class='status-pill status-outofstock'><i class='fa-solid fa-triangle-exclamation'></i> Out of Stock</span>";
                                } else {
                                    $status_badge = "<span class='status-pill status-lowstock'><i class='fa-solid fa-clock-rotate-left'></i> Critical ($stock left)</span>";
                                }
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $pid; ?></td>
                                <td>
                                    <img src="product_images/<?php echo htmlspecialchars($prod['product_image']); ?>" class="admin-table-img" alt="Product" onerror="this.onerror=null;this.src='../images/kirana-new-bf88.jpg';">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($prod['product_name']); ?></div>
                                    <span class="text-muted small"><?php echo htmlspecialchars($prod['Product_unit'] ?? 'Pack'); ?> • ₹<?php echo preg_replace('/[^0-9.]/', '', strval($prod['Product_price'])); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border small">
                                        <?php echo htmlspecialchars($prod['category_name'] ?? 'General'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fs-5 fw-bold text-danger"><?php echo $stock; ?></span>
                                    <span class="small text-muted">units</span>
                                </td>
                                <td><?php echo $status_badge; ?></td>
                                <td>
                                    <form action="" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                        <input type="number" name="additional_stock" class="admin-form-control text-center" style="width: 90px; padding: 6px 10px;" value="20" min="1" required>
                                        <button type="submit" name="restock_product" class="btn btn-sm btn-success fw-bold rounded-pill px-3 text-nowrap">
                                            <i class="fa-solid fa-plus me-1"></i> Restock
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo "<tr>
                                    <td colspan='7' class='text-center py-5'>
                                        <div class='stat-icon-wrapper icon-green mx-auto mb-3' style='width:64px; height:64px;'>
                                            <i class='fa-solid fa-circle-check fs-2'></i>
                                        </div>
                                        <h5 class='fw-bold text-dark'>All Items Well-Stocked</h5>
                                        <p class='text-muted small'>There are currently no products with low or critical inventory levels.</p>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>