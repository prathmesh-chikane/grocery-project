<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_query = '';

if ($filter == 'day') {
    $filter_query = "AND DATE(created_at) = CURDATE()";
} elseif ($filter == 'month') {
    $filter_query = "AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
} elseif ($filter == 'year') {
    $filter_query = "AND YEAR(created_at) = YEAR(CURDATE())";
}

$query = "SELECT * FROM contact_us WHERE 1=1 $filter_query ORDER BY contact_id DESC";
$result = mysqli_query($con, $query);

$page_title = "Contact Inquiries | Ganesh Store Admin";
$page_heading = "Customer Support & Contact Inquiries";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <!-- Filter Tabs -->
        <div class="admin-card-table p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="view_contact.php" class="btn btn-sm <?php echo ($filter == 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?> rounded-pill px-3">
                        All Messages
                    </a>
                    <a href="view_contact.php?filter=day" class="btn btn-sm <?php echo ($filter == 'day') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        Today
                    </a>
                    <a href="view_contact.php?filter=month" class="btn btn-sm <?php echo ($filter == 'month') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        This Month
                    </a>
                    <a href="view_contact.php?filter=year" class="btn btn-sm <?php echo ($filter == 'year') ? 'btn-success fw-bold' : 'btn-outline-success'; ?> rounded-pill px-3">
                        This Year
                    </a>
                </div>
                <span class="text-muted small">Total: <strong><?php echo mysqli_num_rows($result); ?></strong> inquiries</span>
            </div>
        </div>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-envelope-open-text text-success me-2"></i> Messages Inbox</h3>
                    <p class="text-muted small m-0">Inquiries sent by customers through the Contact Us form</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Customer Name</th>
                            <th>Email Address</th>
                            <th>Message Content</th>
                            <th>Received At</th>
                            <th style="width: 100px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($c = mysqli_fetch_assoc($result)) {
                                $c_id = $c['contact_id'] ?? $c['id'];
                                $c_name = htmlspecialchars($c['name']);
                                $c_email = htmlspecialchars($c['email']);
                                $c_msg = htmlspecialchars($c['message']);
                                $c_date = !empty($c['created_at']) ? date("d M Y, h:i A", strtotime($c['created_at'])) : 'N/A';
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $c_id; ?></td>
                                <td class="fw-bold text-dark"><?php echo $c_name; ?></td>
                                <td>
                                    <a href="mailto:<?php echo $c_email; ?>" class="text-success text-decoration-none small">
                                        <i class="fa-solid fa-reply me-1"></i><?php echo $c_email; ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="small text-secondary" style="max-width: 380px;">
                                        <?php echo nl2br($c_msg); ?>
                                    </div>
                                </td>
                                <td class="small text-muted"><?php echo $c_date; ?></td>
                                <td class="text-center">
                                    <a href="mailto:<?php echo $c_email; ?>?subject=Regarding your inquiry at Ganesh Store" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        Reply
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No contact messages found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
