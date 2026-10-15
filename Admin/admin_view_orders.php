<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);
    $update_query = "UPDATE orders SET order_status = '$new_status' WHERE order_id = $order_id";
    if (mysqli_query($con, $update_query)) {
        $alert_msg = "Order #$order_id status updated to '$new_status'!";
        $alert_type = "success";
    } else {
        $alert_msg = "Error updating order: " . mysqli_error($con);
        $alert_type = "danger";
    }
}

// Handle status filter
$status_filter = "";
$active_filter = $_GET['status'] ?? 'all';
if (!empty($active_filter) && $active_filter !== 'all') {
    $status_filter = "WHERE order_status = '" . mysqli_real_escape_string($con, $active_filter) . "'";
}

$page_title = "Manage Customer Orders | Ganesh Store Admin";
$page_heading = "Customer Orders Management";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');

// Fetch orders
$orders_query = "SELECT * FROM orders $status_filter ORDER BY order_date DESC";
$orders_result = mysqli_query($con, $orders_query);
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

        <!-- Filter Pills Bar -->
        <div class="admin-card-table p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="admin_view_orders.php?status=all" class="btn btn-sm rounded-pill px-3 <?php echo ($active_filter == 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?>">
                        All Orders
                    </a>
                    <a href="admin_view_orders.php?status=Pending" class="btn btn-sm rounded-pill px-3 <?php echo ($active_filter == 'Pending') ? 'btn-warning fw-bold text-dark' : 'btn-outline-warning text-dark'; ?>">
                        <i class="fa-solid fa-clock me-1"></i> Pending
                    </a>
                    <a href="admin_view_orders.php?status=Shipped" class="btn btn-sm rounded-pill px-3 <?php echo ($active_filter == 'Shipped') ? 'btn-info fw-bold text-white' : 'btn-outline-info'; ?>">
                        <i class="fa-solid fa-truck-fast me-1"></i> Shipped
                    </a>
                    <a href="admin_view_orders.php?status=Delivered" class="btn btn-sm rounded-pill px-3 <?php echo ($active_filter == 'Delivered') ? 'btn-success fw-bold' : 'btn-outline-success'; ?>">
                        <i class="fa-solid fa-circle-check me-1"></i> Delivered
                    </a>
                    <a href="admin_view_orders.php?status=Cancelled" class="btn btn-sm rounded-pill px-3 <?php echo ($active_filter == 'Cancelled') ? 'btn-danger fw-bold' : 'btn-outline-danger'; ?>">
                        <i class="fa-solid fa-ban me-1"></i> Cancelled
                    </a>
                </div>
                <span class="text-muted small">Showing <?php echo mysqli_num_rows($orders_result); ?> matching orders</span>
            </div>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-cart-flatbed text-success me-2"></i> Orders Register</h3>
                    <p class="text-muted small m-0">Track order fulfillment, update delivery status & view customer items</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Order </th>
                            <th>Customer & Address</th>
                            <th>Ordered Items</th>
                            <th>Total Bill</th>
                            <th>Payment</th>
                            <th>Order Status</th>
                            <th style="width: 170px;">Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($orders_result && mysqli_num_rows($orders_result) > 0) {
                            while ($order = mysqli_fetch_assoc($orders_result)) { 
                                $order_id = $order['order_id'];
                                $customer_name = htmlspecialchars($order['first_name'] . ' ' . $order['last_name']);
                                $phone = htmlspecialchars($order['phone']);
                                $address = htmlspecialchars($order['address1'] . ', ' . $order['city'] . ' - ' . $order['postcode']);
                                $status = $order['order_status'];
                                $payment_mode = htmlspecialchars($order['payment_mode']);
                                $order_date = date("d M Y, h:i A", strtotime($order['order_date']));

                                // Status badge class
                                $pill_class = 'status-pending';
                                if ($status == 'Delivered' || $status == 'Completed') $pill_class = 'status-delivered';
                                elseif ($status == 'Shipped') $pill_class = 'status-shipped';
                                elseif ($status == 'Cancelled') $pill_class = 'status-cancelled';

                                // Fetch items for this order
                                $items_q = mysqli_query($con, "SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");
                                $items_list = [];
                                $total_calculated = 0;
                                while ($it = mysqli_fetch_assoc($items_q)) {
                                    $it_total = floatval($it['price']) * intval($it['quantity']);
                                    $total_calculated += $it_total;
                                    $items_list[] = htmlspecialchars($it['product_name']) . " (×" . $it['quantity'] . ")";
                                }
                        ?>
                            <tr>
                                <td class="fw-bold">
                                    <span class="text-success"><?php echo $order_id; ?></span>
                                    <div class="small text-muted" style="font-size: 0.72rem;"><?php echo $order_date; ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $customer_name; ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1 text-success"></i><?php echo $phone; ?></div>
                                    <div class="small text-muted" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo $address; ?>">
                                        <i class="fa-solid fa-location-dot me-1 text-danger"></i><?php echo $address; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark fw-semibold" style="max-width: 220px;">
                                        <?php echo !empty($items_list) ? implode(', ', $items_list) : 'Custom Order'; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6">₹<?php echo number_format($total_calculated, 2); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small fw-semibold">
                                        <?php echo $payment_mode; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill <?php echo $pill_class; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="" method="POST" class="d-flex align-items-center gap-1">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                        <select name="status" class="admin-form-control py-1 px-2 small" style="font-size: 0.8125rem;">
                                            <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Shipped" <?php if ($status == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="Delivered" <?php if ($status == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if ($status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-dark px-2 py-1" title="Save Status">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>No orders matching the selected filter.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
