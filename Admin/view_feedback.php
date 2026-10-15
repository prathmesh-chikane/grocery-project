<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sql = "SELECT * FROM feedback ORDER BY feedback_id DESC";
$result = mysqli_query($con, $sql);

$page_title = "Customer Feedback | Ganesh Store Admin";
$page_heading = "Customer Feedback & Reviews";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-comments text-success me-2"></i> Feedback Reviews</h3>
                    <p class="text-muted small m-0">Customer opinions and service experience submissions</p>
                </div>
                <span class="badge bg-light text-secondary border">Total Reviews: <?php echo mysqli_num_rows($result); ?></span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Customer Name</th>
                            <th>Feedback Comments</th>
                            <th>Submitted On</th>
                            <th style="width: 120px;" class="text-center">Sentiment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($fb = mysqli_fetch_assoc($result)) {
                                $fb_id = $fb['feedback_id'];
                                $fb_user = htmlspecialchars($fb['username']);
                                $fb_msg = htmlspecialchars($fb['message']);
                                $fb_date = !empty($fb['created_at']) ? date("d M Y, h:i A", strtotime($fb['created_at'])) : 'Recent';
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $fb_id; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="stat-icon-wrapper icon-green rounded-circle" style="width:34px; height:34px; font-size:0.85rem;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <strong class="text-dark"><?php echo $fb_user; ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-secondary" style="max-width: 450px; line-height: 1.6;">
                                        “<?php echo nl2br($fb_msg); ?>”
                                    </div>
                                </td>
                                <td class="small text-muted"><?php echo $fb_date; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill">
                                        <i class="fa-solid fa-thumbs-up me-1"></i> Verified
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No feedback reviews received yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
