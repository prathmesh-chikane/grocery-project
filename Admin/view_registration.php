<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$filter_type = $_GET['filter_type'] ?? 'all';
$selected_date = $_GET['filter_date'] ?? '';
$condition = "";
$date_info = "All Registered Users";

if ($filter_type === 'day' && !empty($selected_date)) {
    $condition = "WHERE DATE(created_at) = '$selected_date'";
    $date_info = "Showing users registered on <strong>" . date("d M Y", strtotime($selected_date)) . "</strong>";
} elseif ($filter_type === 'week' && !empty($selected_date)) {
    $dt = new DateTime();
    $dt->setISODate(intval(substr($selected_date, 0, 4)), intval(substr($selected_date, 6, 2)));
    $start_date = $dt->format('Y-m-d');
    $dt->modify('+6 days');
    $end_date = $dt->format('Y-m-d');
    $condition = "WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    $date_info = "Showing users registered from <strong>" . date("d M Y", strtotime($start_date)) . "</strong> to <strong>" . date("d M Y", strtotime($end_date)) . "</strong>";
} elseif ($filter_type === 'month' && !empty($selected_date)) {
    $month = date('m', strtotime($selected_date));
    $year = date('Y', strtotime($selected_date));
    $condition = "WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year";
    $date_info = "Showing users registered in <strong>" . date("F Y", strtotime($selected_date)) . "</strong>";
}

$query = "SELECT * FROM `user` $condition ORDER BY user_id DESC";
$result = mysqli_query($con, $query);

$page_title = "User Registration Analytics | Ganesh Store Admin";
$page_heading = "Customer Registrations";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <!-- Date Filter Card -->
        <div class="admin-card-table p-3 mb-4">
            <form method="GET" action="view_registration.php" class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold small text-muted text-uppercase me-2"><i class="fa-solid fa-calendar text-success me-1"></i> Date Filter:</span>
                </div>
                <div class="col-auto">
                    <select name="filter_type" class="admin-form-control py-1 px-2 small">
                        <option value="all" <?php if ($filter_type == 'all') echo 'selected'; ?>>All Time</option>
                        <option value="day" <?php if ($filter_type == 'day') echo 'selected'; ?>>By Specific Day</option>
                        <option value="month" <?php if ($filter_type == 'month') echo 'selected'; ?>>By Month</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="filter_date" class="admin-form-control py-1 px-2 small" value="<?php echo htmlspecialchars($selected_date); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                        Apply Filter
                    </button>
                    <a href="view_registration.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Reset</a>
                </div>
            </form>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-user-plus text-success me-2"></i> User Registrations</h3>
                    <p class="text-muted small m-0"><?php echo $date_info; ?></p>
                </div>
                <span class="badge bg-light text-secondary border">Total: <?php echo mysqli_num_rows($result); ?> users</span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Avatar</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($usr = mysqli_fetch_assoc($result)) {
                                $img = htmlspecialchars($usr['image'] ?? '');
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $usr['user_id']; ?></td>
                                <td>
                                    <?php if (!empty($img)): ?>
                                        <img src="../user/user_images/<?php echo $img; ?>" class="admin-table-img rounded-circle" alt="User" onerror="this.onerror=null;this.src='../images/admin logo.png';">
                                    <?php else: ?>
                                        <div class="stat-icon-wrapper icon-green rounded-circle mx-auto" style="width:34px; height:34px; font-size:0.8rem;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($usr['username']); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars($usr['email']); ?></td>
                                <td class="small"><?php echo htmlspecialchars($usr['mobile_number'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="small text-muted" style="max-width: 200px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($usr['address']); ?>">
                                        <?php echo htmlspecialchars($usr['address']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border font-monospace small"><?php echo htmlspecialchars($usr['user_ip'] ?? 'N/A'); ?></span>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>No user registrations found matching the filter.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
