<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alert_msg = '';
$alert_type = '';

if (isset($_POST['insert_product'])) {
    $product_name = mysqli_real_escape_string($con, trim($_POST['product_name'] ?? ''));
    $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $product_keywords = mysqli_real_escape_string($con, trim($_POST['product_keywords'] ?? ''));
    $product_category = intval($_POST['product_category'] ?? 0);
    $Product_price = mysqli_real_escape_string($con, trim($_POST['Product_price'] ?? ''));
    $Product_unit = mysqli_real_escape_string($con, trim($_POST['Product_unit'] ?? ''));
    $stock_quantity = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
    $product_status = 'true';

    $product_image = $_FILES['product_image']['name'] ?? '';
    $temp_image = $_FILES['product_image']['tmp_name'] ?? '';

    if (empty($product_image)) {
        $alert_msg = "Please upload a product image.";
        $alert_type = "danger";
    } elseif (empty($product_name) || empty($description) || empty($Product_price) || empty($product_category)) {
        $alert_msg = "Please fill in all mandatory fields.";
        $alert_type = "danger";
    } else {
        $upload_dir = __DIR__ . "/product_images/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        move_uploaded_file($temp_image, $upload_dir . $product_image);

        $insert_products = "INSERT INTO `products`
            (product_name, description, product_keywords, category_id, product_image, Product_price, Product_unit, stock_quantity, date, status)
            VALUES ('$product_name', '$description', '$product_keywords', $product_category, '$product_image', '$Product_price', '$Product_unit', $stock_quantity, NOW(), '$product_status')";
        $result = mysqli_query($con, $insert_products);

        if ($result) {
            $alert_msg = "Product '$product_name' successfully added to the store!";
            $alert_type = "success";
        } else {
            $alert_msg = "Error adding product: " . mysqli_error($con);
            $alert_type = "danger";
        }
    }
}

$page_title = "Add Product | Ganesh Store Admin";
$page_heading = "Add New Grocery Product";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <div class="admin-form-card">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1" style="font-family: var(--font-heading);">Product Information</h3>
                    <p class="text-muted small m-0">Enter details to list a new item in your online store catalog</p>
                </div>
                <a href="view_products.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <?php if ($alert_msg): ?>
                <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 small py-2 rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                    <div><?php echo htmlspecialchars($alert_msg); ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Product Title -->
                    <div class="col-md-8">
                        <label for="product_name" class="admin-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" id="product_name" class="admin-form-control" placeholder="e.g. Fortune Sunlite Sunflower Oil 1L" required>
                    </div>

                    <!-- Category -->
                    <div class="col-md-4">
                        <label for="product_category" class="admin-form-label">Category <span class="text-danger">*</span></label>
                        <select name="product_category" id="product_category" class="admin-form-control" required>
                            <option value="">Select Category</option>
                            <?php
                            $cat_q = mysqli_query($con, "SELECT * FROM `categories` ORDER BY category_name ASC");
                            while ($cat = mysqli_fetch_assoc($cat_q)) {
                                echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Unit and Price -->
                    <div class="col-md-4">
                        <label for="Product_price" class="admin-form-label">Price (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">₹</span>
                            <input type="text" name="Product_price" id="Product_price" class="admin-form-control border-start-0" placeholder="e.g. 150" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="Product_unit" class="admin-form-label">Unit / Quantity <span class="text-danger">*</span></label>
                        <input type="text" name="Product_unit" id="Product_unit" class="admin-form-control" placeholder="e.g. 1 kg, 500g, 1L, Pack of 4" required>
                    </div>

                    <div class="col-md-4">
                        <label for="stock_quantity" class="admin-form-label">Initial Stock Available <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="admin-form-control" placeholder="e.g. 50" min="0" required value="20">
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="admin-form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="4" class="admin-form-control" placeholder="Describe the product quality, benefits, packaging, etc..." required></textarea>
                    </div>

                    <!-- Keywords -->
                    <div class="col-12">
                        <label for="product_keywords" class="admin-form-label">Search Keywords <span class="text-danger">*</span></label>
                        <input type="text" name="product_keywords" id="product_keywords" class="admin-form-control" placeholder="Comma separated keywords: oil, cooking oil, sunflower, refined oil" required>
                        <small class="text-muted">Keywords help shoppers find this product easily via the search bar.</small>
                    </div>

                    <!-- Product Image -->
                    <div class="col-12">
                        <label for="product_image" class="admin-form-label">Product Image <span class="text-danger">*</span></label>
                        <input type="file" name="product_image" id="product_image" class="admin-form-control" accept="image/*" required onchange="previewImage(this)">
                        <div class="mt-3" id="imgPreviewWrapper" style="display: none;">
                            <span class="small text-muted d-block mb-1">Image Preview:</span>
                            <img id="imgPreview" src="" alt="Preview" style="max-height: 120px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px;">
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                    <button type="submit" name="insert_product" class="btn-admin-submit">
                        <i class="fa-solid fa-plus"></i>
                        <span>Publish Product</span>
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
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewWrapper').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include('includes/admin_footer.php'); ?>
