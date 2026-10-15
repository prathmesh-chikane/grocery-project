<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Product Catalog | Ganesh Store Admin";
$page_heading = "Product Catalog Management";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');

$query = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC";
$result = mysqli_query($con, $query);
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>Product has been successfully deleted from the store database.</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>Product details updated successfully!</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="admin-card-table">
            <div class="admin-card-header">
                <div>
                    <h3><i class="fa-solid fa-boxes-stacked text-success me-2"></i> All Products</h3>
                    <p class="text-muted small m-0">View, search, edit, and manage stock for all store items</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="productSearchInput" class="admin-form-control ps-5" placeholder="Search products..." style="width: 250px;">
                    </div>
                    <a href="insert_product.php" class="btn btn-success fw-bold rounded-pill px-3 py-2 small d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add New Product</span>
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table" id="productAdminTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Stock Status</th>
                            <th style="width: 120px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                                $stock = intval($row['stock_quantity'] ?? 0);
                                $stock_badge = "";
                                if ($stock <= 0) {
                                    $stock_badge = "<span class='status-pill status-outofstock'><i class='fa-solid fa-ban'></i> Out of Stock</span>";
                                } elseif ($stock <= 5) {
                                    $stock_badge = "<span class='status-pill status-lowstock'><i class='fa-solid fa-triangle-exclamation'></i> Low ($stock)</span>";
                                } else {
                                    $stock_badge = "<span class='status-pill status-instock'><i class='fa-solid fa-check'></i> In Stock ($stock)</span>";
                                }
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $row['product_id']; ?></td>
                                <td>
                                    <img src="product_images/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product" class="admin-table-img" onerror="this.onerror=null;this.src='../images/kirana-new-bf88.jpg';">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark product-name-cell"><?php echo htmlspecialchars($row['product_name']); ?></div>
                                    <div class="text-muted small" style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border small">
                                        <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                </td>
                                <td class="small fw-semibold"><?php echo htmlspecialchars($row['Product_unit'] ?? 'Pack'); ?></td>
                                <td class="fw-bold text-success">₹<?php echo preg_replace('/[^0-9.]/', '', strval($row['Product_price'])); ?></td>
                                <td><?php echo $stock_badge; ?></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn-action btn-action-edit" title="Edit Product">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn-action btn-action-delete" title="Delete Product" onclick="return confirm('Are you sure you want to delete \'<?php echo addslashes($row['product_name']); ?>\' from the database?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center py-5 text-muted'>No products found. <a href='insert_product.php'>Add your first product!</a></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
// Instant live search filter
document.getElementById('productSearchInput').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#productAdminTable tbody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include('includes/admin_footer.php'); ?>
