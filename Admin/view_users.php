<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to apply date filters
function applyDateFilter($filter_type) {
    $current_date = date('Y-m-d');
    switch ($filter_type) {
        case 'day':
            return "DATE(created_at) = '$current_date'";
        case 'week':
            return "WEEK(created_at, 1) = WEEK('$current_date', 1)";
        case 'month':
            return "MONTH(created_at) = MONTH('$current_date') AND YEAR(created_at) = YEAR('$current_date')";
        case 'year':
            return "YEAR(created_at) = YEAR('$current_date')";
        default:
            return '';
    }
}

$filter_type = isset($_GET['filter']) ? $_GET['filter'] : '';
$filter_condition = applyDateFilter($filter_type);

$whereClause = '';
if (!empty($filter_condition)) {
    $whereClause = 'WHERE ' . $filter_condition;
}

$query = "SELECT * FROM `user` $whereClause ORDER BY user_id DESC";
$result = mysqli_query($con, $query);

$page_title = "User Directory | Ganesh Store Admin";
$page_heading = "Registered Customers Directory";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <!-- Date Filters Bar -->
        <div class="admin-card-table p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="view_users.php" class="btn btn-sm <?php echo empty($filter_type) ? 'btn-dark' : 'btn-outline-secondary'; ?> rounded-pill px-3">
                        All Users
                    </a>
                    <a href="view_users.php?filter=day" class="btn btn-sm <?php echo ($filter_type == 'day') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        Today
                    </a>
                    <a href="view_users.php?filter=week" class="btn btn-sm <?php echo ($filter_type == 'week') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        This Week
                    </a>
                    <a href="view_users.php?filter=month" class="btn btn-sm <?php echo ($filter_type == 'month') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        This Month
                    </a>
                </div>
                <span class="text-muted small">Total: <strong><?php echo mysqli_num_rows($result); ?></strong> customers</span>
            </div>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-users text-success me-2"></i> Customers</h3>
                    <p class="text-muted small m-0">Directory of all customers registered on the platform</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th style="width: 70px;">Avatar</th>
                            <th>Customer Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Default Delivery Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($usr = mysqli_fetch_assoc($result)) { 
                                $uid = $usr['user_id'];
                                $img = htmlspecialchars($usr['image'] ?? '');
                                $u_name = htmlspecialchars($usr['username']);
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $uid; ?></td>
                                <td>
                                    <?php if (!empty($img)): ?>
                                        <img src="../user/user_images/<?php echo $img; ?>" class="admin-table-img rounded-circle" alt="User" onerror="this.onerror=null;this.src='../images/admin logo.png';">
                                    <?php else: ?>
                                        <div class="stat-icon-wrapper icon-green rounded-circle mx-auto" style="width:36px; height:36px; font-size:0.85rem;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $u_name; ?></div>
                                </td>
                                <td>
                                    <span class="text-muted small"><i class="fa-solid fa-envelope me-1 text-success"></i><?php echo htmlspecialchars($usr['email']); ?></span>
                                </td>
                                <td class="small fw-semibold">
                                    <?php echo !empty($usr['mobile_number']) ? htmlspecialchars($usr['mobile_number']) : '<span class="text-muted">N/A</span>'; ?>
                                </td>
                                <td>
                                    <span class="small text-muted" style="max-width: 250px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($usr['address']); ?>">
                                        <i class="fa-solid fa-location-dot me-1 text-danger"></i><?php echo htmlspecialchars($usr['address']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No customers registered yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
