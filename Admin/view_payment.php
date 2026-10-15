<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Build filter conditions
$filterType = $_GET['filter_type'] ?? '';
$conditions = [];

switch ($filterType) {
    case 'day':
        if (!empty($_GET['day'])) {
            $day = mysqli_real_escape_string($con, $_GET['day']);
            $conditions[] = "DATE(p.payment_date) = '$day'";
        }
        break;

    case 'week':
        if (!empty($_GET['day'])) {
            $day = mysqli_real_escape_string($con, $_GET['day']);
            $conditions[] = "YEARWEEK(p.payment_date, 1) = YEARWEEK('$day', 1)";
        }
        break;

    case 'month':
        if (!empty($_GET['month']) && !empty($_GET['year'])) {
            $month = (int) $_GET['month'];
            $year = (int) $_GET['year'];
            $conditions[] = "MONTH(p.payment_date) = $month AND YEAR(p.payment_date) = $year";
        }
        break;
}

$whereClause = '';
if (count($conditions) > 0) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

// Query payments
$query = "SELECT 
            p.payment_id, 
            p.order_id, 
            p.payment_status, 
            p.amount, 
            p.payment_date AS created_at, 
            o.first_name, 
            o.last_name,
            GROUP_CONCAT(DISTINCT pr.product_name SEPARATOR ', ') AS product_names
          FROM payment p
          LEFT JOIN orders o ON p.order_id = o.order_id
          LEFT JOIN order_items oi ON p.order_id = oi.order_id
          LEFT JOIN products pr ON oi.product_id = pr.product_id
          $whereClause
          GROUP BY p.payment_id, p.order_id, p.payment_status, p.amount, p.payment_date, o.first_name, o.last_name
          ORDER BY p.payment_date DESC";

$result = mysqli_query($con, $query);

$page_title = "Payments History | Ganesh Store Admin";
$page_heading = "Payments Ledger & Revenue";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <!-- Date Filters Card -->
        <div class="admin-card-table p-3 mb-4">
            <form method="GET" action="view_payment.php" class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold small text-muted text-uppercase me-2"><i class="fa-solid fa-filter text-success me-1"></i> Filter By:</span>
                </div>
                <div class="col-auto">
                    <a href="view_payment.php" class="btn btn-sm <?php echo empty($filterType) ? 'btn-dark' : 'btn-outline-secondary'; ?> rounded-pill px-3">All Time</a>
                </div>
                <div class="col-auto">
                    <input type="date" name="day" class="admin-form-control py-1 px-2 small" value="<?php echo htmlspecialchars($_GET['day'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="col-auto">
                    <input type="hidden" name="filter_type" value="day">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                        Filter By Date
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-credit-card text-success me-2"></i> Payment Records</h3>
                    <p class="text-muted small m-0">Detailed transaction ledger for customer checkouts</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Order id</th>
                            <th>Customer Name</th>
                            <th>Products Included</th>
                            <th>Amount (₹)</th>
                            <th>Status</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            $total_sum = 0;
                            while ($pay = mysqli_fetch_assoc($result)) {
                                $amt = floatval($pay['amount']);
                                $total_sum += $amt;
                                $status = $pay['payment_status'];
                                $badge = ($status == 'Successful' || $status == 'Success') ? 'status-delivered' : 'status-pending';
                        ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <span class="text-muted font-monospace small"><?php echo htmlspecialchars($pay['payment_id']); ?></span>
                                </td>
                                <td class="fw-bold text-success"><?php echo htmlspecialchars($pay['order_id']); ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($pay['first_name'] . ' ' . $pay['last_name']); ?></td>
                                <td>
                                    <span class="small text-muted" style="max-width: 250px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($pay['product_names'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-success fs-6">₹<?php echo number_format($amt, 2); ?></td>
                                <td>
                                    <span class="status-pill <?php echo $badge; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td class="small text-muted">
                                    <?php echo !empty($pay['created_at']) ? date("d M Y, h:i A", strtotime($pay['created_at'])) : 'N/A'; ?>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>No payment transactions found matching the filter.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
