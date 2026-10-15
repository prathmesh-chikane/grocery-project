<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

if (isset($_POST['insert_cat'])) {
    $category_name = mysqli_real_escape_string($con, trim($_POST['cat_name'] ?? ''));

    if (empty($category_name)) {
        $alert_msg = "Please enter a valid category name.";
        $alert_type = "danger";
    } else {
        $select_query = "SELECT * FROM `categories` WHERE category_name = '$category_name'";
        $result_select = mysqli_query($con, $select_query);

        if (mysqli_num_rows($result_select) > 0) {
            $alert_msg = "Category '$category_name' already exists in the store.";
            $alert_type = "warning";
        } else {
            $insert_query = "INSERT INTO `categories` (`category_name`) VALUES ('$category_name')";
            $result = mysqli_query($con, $insert_query);

            if ($result) {
                $alert_msg = "Category '$category_name' created successfully!";
                $alert_type = "success";
            } else {
                $alert_msg = "Error creating category: " . mysqli_error($con);
                $alert_type = "danger";
            }
        }
    }
}

$page_title = "Add Category | Ganesh Store Admin";
$page_heading = "Create New Category";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <div class="admin-form-card" style="max-width: 600px;">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1" style="font-family: var(--font-heading);">New Category</h3>
                </div>
                <a href="admin_categories.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> View All
                </a>
            </div>

            <?php if ($alert_msg): ?>
                <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 small py-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                    <div><?php echo htmlspecialchars($alert_msg); ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="admin-form-group">
                    <label for="cat_name" class="admin-form-label">Category Title <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-layer-group"></i></span>
                        <input type="text" class="admin-form-control border-start-0" id="cat_name" name="cat_name" placeholder="e.g. ORGANIC PULSES, FRESH VEGETABLES" required autocomplete="off">
                    </div>

                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <button type="submit" name="insert_cat" class="btn-admin-submit">
                        <i class="fa-solid fa-plus"></i>
                        <span>Save Category</span>
                    </button>
                    <a href="admin_categories.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
