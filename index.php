<?php
$page_title = "Ganesh General Store | Fresh Groceries Delivered in 15 Mins";
include('includes/header.php');

// Fetch current category name if selected
$current_category_name = "All Fresh Products";
if (isset($_GET['category'])) {
    $cat_id = intval($_GET['category']);
    $cat_q = mysqli_query($con, "SELECT category_name FROM categories WHERE category_id = $cat_id");
    if ($cat_q && $cat_row = mysqli_fetch_assoc($cat_q)) {
        $current_category_name = htmlspecialchars($cat_row['category_name']);
    }
}
?>

<main class="container my-4">

    <!-- Hero Promotional Section (Only displayed on home when not searching) -->
    <?php if (!isset($_GET['search_data_product']) && !isset($_GET['category'])): ?>
    <section class="hero-promo-section mb-4">
        <div class="row g-3">
            <!-- Main Hero Card -->
            <div class="col-lg-8">
                <div class="promo-hero-card">
                    <span class="promo-badge-tag"><i class="fa-solid fa-sparkles me-1"></i> Special Weekly Offer</span>
                    <h2 class="promo-hero-title">Fresh Groceries Delivered<br>To Your Door in 15 Mins</h2>
                    <p class="promo-hero-desc">Discover the freshest pulses, dairy, cooking oils, snacks, and daily household essentials with unbeatable prices and instant delivery.</p>
                    <a href="display_all.php" class="promo-cta-btn">
                        <span>Shop All Essentials</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Side Promo Banners -->
            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="side-promo-card card-orange">
                    <div>
                        <span class="badge bg-danger text-white mb-2 fw-bold">SAVE UP TO 30%</span>
                        <h4 class="fw-bold mb-1" style="font-family: var(--font-heading); color: #7c2d12;">Snacks & Drinks</h4>
                        <p class="small text-muted mb-3">Biscuits, chips, beverages & cold drinks ready for your movie night.</p>
                    </div>
                    <a href="index.php?category=1" class="text-dark fw-bold small text-decoration-none">
                        Browse Snacks <i class="fa-solid fa-arrow-right ms-1 text-danger"></i>
                    </a>
                </div>

                <div class="side-promo-card card-blue">
                    <div>
                        <span class="badge bg-primary text-white mb-2 fw-bold">FARM FRESH</span>
                        <h4 class="fw-bold mb-1" style="font-family: var(--font-heading); color: #0c4a6e;">Dairy & Staples</h4>
                        <p class="small text-muted mb-3">Pure milk, fresh paneer, desi ghee & premium quality grains.</p>
                    </div>
                    <a href="index.php?category=2" class="text-dark fw-bold small text-decoration-none">
                        Shop Dairy Products <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Features Bar -->
    <section class="trust-features-bar mb-4">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="feature-pill">
                    <div class="feature-icon-circle"><i class="fa-solid fa-bolt"></i></div>
                    <div>
                        <h6 class="feature-pill-title">15-Min Delivery</h6>
                        <p class="feature-pill-desc">Superfast doorstep delivery</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-pill">
                    <div class="feature-icon-circle"><i class="fa-solid fa-leaf"></i></div>
                    <div>
                        <h6 class="feature-pill-title">100% Quality</h6>
                        <p class="feature-pill-desc">Handpicked fresh groceries</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-pill">
                    <div class="feature-icon-circle"><i class="fa-solid fa-tag"></i></div>
                    <div>
                        <h6 class="feature-pill-title">Best Prices</h6>
                        <p class="feature-pill-desc">Wholesale market rates</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-pill">
                    <div class="feature-icon-circle"><i class="fa-solid fa-shield-halved"></i></div>
                    <div>
                        <h6 class="feature-pill-title">Easy Payments</h6>
                        <p class="feature-pill-desc">COD & Secure UPI</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Products Browsing Area -->
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="category-sidebar-card">
                <div class="category-sidebar-header">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Categories</span>
                </div>
                <ul class="category-sidebar-list">
                    <?php getcategories(); ?>
                </ul>
            </div>
        </div>

        <!-- Main Product Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Grid Title Bar -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold m-0" style="font-family: var(--font-heading);">
                        <?php 
                        if (isset($_GET['search_data_product'])) {
                            echo "Search Results for '" . htmlspecialchars($_GET['search_data'] ?? '') . "'";
                        } else {
                            echo $current_category_name;
                        }
                        ?>
                    </h3>
                    <p class="text-muted small m-0">Handpicked items delivered instantly with top hygiene standards</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="display_all.php" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-eye me-1"></i> View All
                    </a>
                </div>
            </div>

            <!-- Products Row -->
            <div class="row g-3 align-items-stretch">
                <?php
                if (isset($_GET['search_data_product'])) {
                    search_product();
                } elseif (isset($_GET['category'])) {
                    get_unique_categories();
                } else {
                    getproducts();
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>