<?php
include('includes/connection.php'); 
include('functions/common_function.php');

if (!isset($_GET['product_id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$select_query = "SELECT p.*, c.category_name FROM `products` p LEFT JOIN `categories` c ON p.category_id = c.category_id WHERE p.product_id = $product_id";
$result_query = mysqli_query($con, $select_query);

if (!$result_query || mysqli_num_rows($result_query) == 0) {
    die("<div class='container my-5 text-center'><h3>Product not found.</h3><a href='index.php' class='btn btn-success mt-3'>Return to Store</a></div>");
}

$row = mysqli_fetch_assoc($result_query);
$product_name = htmlspecialchars($row['product_name']);
$description = htmlspecialchars($row['description']);
$product_image = htmlspecialchars($row['product_image']);
$product_unit = htmlspecialchars($row['Product_unit'] ?? 'Pack');
$category_name = htmlspecialchars($row['category_name'] ?? 'General Grocery');
$category_id = intval($row['category_id']);
$raw_price = $row['Product_price'];
$price_num = preg_replace('/[^0-9.]/', '', strval($raw_price));
$price_float = $price_num === '' ? 0.0 : floatval($price_num);
$price_formatted = number_format($price_float, 2);
$stock_qty = isset($row['stock_quantity']) ? intval($row['stock_quantity']) : 0;

$page_title = "$product_name | Ganesh General Store";
include('includes/header.php');
?>

<main class="container my-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item"><a href="display_all.php">Products</a></li>
            <li class="breadcrumb-item"><a href="index.php?category=<?php echo $category_id; ?>"><?php echo $category_name; ?></a></li>
            <li class="breadcrumb-item active text-success fw-semibold" aria-current="page"><?php echo $product_name; ?></li>
        </ol>
    </nav>

    <!-- Product Showcase Card -->
    <div class="product-detail-card">
        <div class="row g-4 align-items-center">
            <!-- Left: Product Image -->
            <div class="col-lg-5">
                <div class="product-detail-img-box position-relative">
                    <?php if ($stock_qty <= 0): ?>
                        <span class="card-badge-top badge-out-of-stock" style="top:14px; left:14px;">Out of Stock</span>
                    <?php elseif ($stock_qty <= 5): ?>
                        <span class="card-badge-top badge-low-stock" style="top:14px; left:14px;">Only <?php echo $stock_qty; ?> Left</span>
                    <?php else: ?>
                        <span class="card-badge-top badge-fresh" style="top:14px; left:14px;"><i class="fa-solid fa-leaf me-1"></i> 100% Fresh</span>
                    <?php endif; ?>

                    <img src="./Admin/product_images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>" class="img-fluid" onerror="this.onerror=null;this.src='./images/kirana-new-bf88.jpg';">
                </div>
            </div>

            <!-- Right: Product Information & Actions -->
            <div class="col-lg-7">
                <div class="ps-lg-3">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-layer-group me-1"></i> <?php echo $category_name; ?>
                        </span>
                        <span class="badge bg-light text-secondary border fw-semibold px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-scale-balanced me-1"></i> Unit: <?php echo $product_unit; ?>
                        </span>
                    </div>

                    <h1 class="product-detail-title"><?php echo $product_name; ?></h1>

                    <div class="d-flex align-items-baseline gap-3 mb-3">
                        <div class="product-detail-price mb-0">₹<?php echo $price_formatted; ?></div>
                        <span class="text-muted small">Inclusive of all taxes</span>
                    </div>

                    <!-- Stock Status Badge -->
                    <div class="mb-4">
                        <?php if ($stock_qty > 0): ?>
                            <span class="text-success fw-bold small d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> In Stock & Ready to Dispatch (<?php echo $stock_qty; ?> units available)
                            </span>
                        <?php else: ?>
                            <span class="text-danger fw-bold small d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Currently Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Product Description</h6>
                        <p class="text-secondary" style="line-height:1.7;"><?php echo nl2br($description); ?></p>
                    </div>

                    <!-- Actions Form -->
                    <div class="d-flex flex-wrap align-items-center gap-3 pt-2 border-top">
                        <?php if ($stock_qty > 0): ?>
                            <!-- Add to Cart -->
                            <a href="index.php?add_to_cart=<?php echo $product_id; ?>" class="btn-primary-grocery btn-lg px-4 py-2" title="Add to Cart">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                            </a>
                            <!-- Buy Now Direct -->
                            <a href="./user/checkout2.php?buy_now=1&product_id=<?php echo $product_id; ?>&quantity=1" class="btn btn-warning btn-lg px-4 py-2 fw-bold text-dark rounded-pill">
                                <i class="fa-solid fa-bolt me-1"></i> Buy Now
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg px-4 py-2 rounded-pill disabled" disabled>
                                <i class="fa-solid fa-ban me-1"></i> Out of Stock
                            </button>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Store
                        </a>
                    </div>

                    <!-- Service Highlights -->
                    <div class="product-detail-features">
                        <div class="detail-feature-card">
                            <i class="fa-solid fa-truck-fast"></i>
                            <div>
                                <strong class="d-block small text-dark">15-Min Delivery</strong>
                                <span class="small text-muted">To your doorstep</span>
                            </div>
                        </div>
                        <div class="detail-feature-card">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                            <div>
                                <strong class="d-block small text-dark">Fresh Guarantee</strong>
                                <span class="small text-muted">100% Quality inspected</span>
                            </div>
                        </div>
                        <div class="detail-feature-card">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <div>
                                <strong class="d-block small text-dark">COD & UPI</strong>
                                <span class="small text-muted">Pay on arrival</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products in same Category -->
    <?php
    $related_query = "SELECT * FROM `products` WHERE category_id = $category_id AND product_id != $product_id LIMIT 4";
    $related_result = mysqli_query($con, $related_query);
    if ($related_result && mysqli_num_rows($related_result) > 0):
    ?>
    <section class="mt-5 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold m-0" style="font-family: var(--font-heading);">Similar Items in <?php echo $category_name; ?></h3>
                <p class="text-muted small m-0">Customers also bought these items</p>
            </div>
            <a href="index.php?category=<?php echo $category_id; ?>" class="text-success fw-bold small text-decoration-none">
                View all in category <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            <?php
            while ($rel_row = mysqli_fetch_assoc($related_result)) {
                render_grocery_product_card($rel_row);
            }
            ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>