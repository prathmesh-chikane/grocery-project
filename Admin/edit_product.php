<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

if (!isset($_GET['id'])) {
    header('Location: view_products.php');
    exit();
}

$product_id = intval($_GET['id']);

// Fetch existing product
$select = mysqli_query($con, "SELECT * FROM products WHERE product_id = $product_id");
if (!$select || mysqli_num_rows($select) == 0) {
    die("<div class='container my-5 text-center'><h3>Product not found.</h3><a href='view_products.php' class='btn btn-success mt-2'>Back to Catalog</a></div>");
}
$product = mysqli_fetch_assoc($select);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, trim($_POST['product_name'] ?? ''));
    $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $price = mysqli_real_escape_string($con, trim($_POST['Product_price'] ?? ''));
    $unit = mysqli_real_escape_string($con, trim($_POST['Product_unit'] ?? ''));
    $category = intval($_POST['product_category'] ?? 0);
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);

    $new_image = $product['product_image'];
    if (!empty($_FILES['product_image']['name'])) {
        $img_name = $_FILES['product_image']['name'];
        $img_tmp = $_FILES['product_image']['tmp_name'];
        $upload_dir = __DIR__ . '/product_images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        move_uploaded_file($img_tmp, $upload_dir . $img_name);
        $new_image = $img_name;
    }

    $update_sql = "UPDATE products 
                   SET product_name = '$name', description = '$description', 
                       Product_price = '$price', Product_unit = '$unit', 
                       category_id = $category, product_image = '$new_image', 
                       stock_quantity = $stock_quantity 
                   WHERE product_id = $product_id";
    $res = mysqli_query($con, $update_sql);

    if ($res) {
        $alert_msg = "Product details successfully updated!";
        $alert_type = "success";
        // Refresh product data
        $select = mysqli_query($con, "SELECT * FROM products WHERE product_id = $product_id");
        $product = mysqli_fetch_assoc($select);
    } else {
        $alert_msg = "Error updating product: " . mysqli_error($con);
        $alert_type = "danger";
    }
}

$page_title = "Edit Product: " . htmlspecialchars($product['product_name']) . " | Ganesh Store";
$page_heading = "Edit Product Details";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <div class="admin-form-card">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1" style="font-family: var(--font-heading);">Edit Product #<?php echo $product_id; ?></h3>
                    <p class="text-muted small m-0">Modify pricing, category, stock inventory, and product imagery</p>
                </div>
                <a href="view_products.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <?php if ($alert_msg): ?>
                <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 small py-2 rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <div><?php echo htmlspecialchars($alert_msg); ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Product Title -->
                    <div class="col-md-8">
                        <label for="product_name" class="admin-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" id="product_name" class="admin-form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>

                    <!-- Category -->
                    <div class="col-md-4">
                        <label for="product_category" class="admin-form-label">Category <span class="text-danger">*</span></label>
                        <select name="product_category" id="product_category" class="admin-form-control" required>
                            <?php
                            $cat_q = mysqli_query($con, "SELECT * FROM `categories` ORDER BY category_name ASC");
                            while ($cat = mysqli_fetch_assoc($cat_q)) {
                                $selected = ($cat['category_id'] == $product['category_id']) ? 'selected' : '';
                                echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Price & Unit & Stock -->
                    <div class="col-md-4">
                        <label for="Product_price" class="admin-form-label">Price (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">₹</span>
                            <input type="text" name="Product_price" id="Product_price" class="admin-form-control border-start-0" value="<?php echo htmlspecialchars($product['Product_price']); ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="Product_unit" class="admin-form-label">Unit / Packaging <span class="text-danger">*</span></label>
                        <input type="text" name="Product_unit" id="Product_unit" class="admin-form-control" value="<?php echo htmlspecialchars($product['Product_unit'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="stock_quantity" class="admin-form-label">Stock Quantity Available <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="admin-form-control" value="<?php echo intval($product['stock_quantity'] ?? 0); ?>" min="0" required>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="admin-form-label">Product Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="4" class="admin-form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <!-- Image Upload with Current Preview -->
                    <div class="col-12">
                        <label class="admin-form-label">Product Image</label>
                        <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-3 border">
                            <div>
                                <span class="d-block small text-muted mb-1">Current Image:</span>
                                <img id="currentImg" src="product_images/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Current" style="height: 80px; width: 80px; object-fit: contain; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; padding: 4px;" onerror="this.onerror=null;this.src='../images/kirana-new-bf88.jpg';">
                            </div>
                            <div class="flex-grow-1">
                                <span class="d-block small text-muted mb-1">Change Image (Optional):</span>
                                <input type="file" name="product_image" class="admin-form-control" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                    <button type="submit" class="btn-admin-submit">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Updates</span>
                    </button>
                    <a href="view_products.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('currentImg').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include('includes/admin_footer.php'); ?>
