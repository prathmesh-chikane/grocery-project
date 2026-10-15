<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM `categories` WHERE category_id = $delete_id";
    $result_delete = mysqli_query($con, $delete_query);
    if ($result_delete) {
        $alert_msg = "Category successfully deleted!";
        $alert_type = "success";
    } else {
        $alert_msg = "Error deleting category: " . mysqli_error($con);
        $alert_type = "danger";
    }
}

// Handle update form submission
if (isset($_POST['update_cat'])) {
    $update_id = intval($_POST['cat_id']);
    $updated_name = mysqli_real_escape_string($con, trim($_POST['cat_name']));

    if (!empty($updated_name)) {
        $update_query = "UPDATE `categories` SET category_name = '$updated_name' WHERE category_id = $update_id";
        $result_update = mysqli_query($con, $update_query);

        if ($result_update) {
            $alert_msg = "Category name updated successfully!";
            $alert_type = "success";
        } else {
            $alert_msg = "Update failed: " . mysqli_error($con);
            $alert_type = "danger";
        }
    }
}

$page_title = "Manage Categories | Ganesh Store Admin";
$page_heading = "Category Management";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');

$categories_query = "SELECT c.*, COUNT(p.product_id) as total_products 
                     FROM categories c 
                     LEFT JOIN products p ON c.category_id = p.category_id 
                     GROUP BY c.category_id 
                     ORDER BY c.category_name ASC";
$result_categories = mysqli_query($con, $categories_query);
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

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-layer-group text-success me-2"></i> Store Categories</h3>
                    <p class="text-muted small m-0">Organize products into categories for easy customer discovery</p>
                </div>
                <a href="insert_categories.php" class="btn btn-success fw-bold rounded-pill px-3 py-2 small d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-folder-plus"></i>
                    <span>Add New Category</span>
                </a>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Category Name</th>
                            <th>Listed Products</th>
                            <th>Created Date</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result_categories && mysqli_num_rows($result_categories) > 0) {
                            while ($cat = mysqli_fetch_assoc($result_categories)) { 
                                $cat_id = $cat['category_id'];
                                $cat_name = htmlspecialchars($cat['category_name']);
                                $total_items = intval($cat['total_products']);
                                $date_text = !empty($cat['created_at']) ? date("d M Y", strtotime($cat['created_at'])) : 'Standard';
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $cat_id; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="stat-icon-wrapper icon-green" style="width:32px; height:32px; font-size:0.9rem;">
                                            <i class="fa-solid fa-basket-shopping"></i>
                                        </div>
                                        <span style="color: var(--admin-text-main); font-weight: 600;"><?php echo $cat_name; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border small">
                                        <i class="fa-solid fa-box me-1 text-success"></i> <?php echo $total_items; ?> products
                                    </span>
                                </td>
                                <td class="small text-muted"><?php echo $date_text; ?></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <button class="btn-action btn-action-edit" title="Edit Category" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cat_id; ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="admin_categories.php?delete=<?php echo $cat_id; ?>" class="btn-action btn-action-delete" title="Delete Category" onclick="return confirm('Delete category \'<?php echo addslashes($cat_name); ?>\'?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade text-start" id="editModal<?php echo $cat_id; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold" style="font-family: var(--font-heading);">Edit Category Name</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="admin_categories.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>">
                                                        <label class="admin-form-label">Category Name</label>
                                                        <input type="text" name="cat_name" class="admin-form-control" value="<?php echo $cat_name; ?>" required>
                                                    </div>
                                                    <div class="modal-footer border-top">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_cat" class="btn btn-success fw-bold rounded-pill px-4">Update Name</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No categories found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
