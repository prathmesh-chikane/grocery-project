<?php
$page_title = "All Grocery Products | Ganesh General Store";
include('includes/header.php');

// Fetch current category name if selected
$current_category_name = "All Store Products";
if (isset($_GET['category'])) {
    $cat_id = intval($_GET['category']);
    $cat_q = mysqli_query($con, "SELECT category_name FROM categories WHERE category_id = $cat_id");
    if ($cat_q && $cat_row = mysqli_fetch_assoc($cat_q)) {
        $current_category_name = htmlspecialchars($cat_row['category_name']);
    }
}
?>

<main class="container my-4">
    <!-- Breadcrumb & Page Title -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">All Products</li>
                <?php if (isset($_GET['category'])): ?>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page"><?php echo $current_category_name; ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="fw-bold m-0" style="font-family: var(--font-heading);"><?php echo $current_category_name; ?></h2>
                <p class="text-muted small m-0">Explore our full selection of packaged staples, fresh dairy, spices & snacks</p>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Home
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="category-sidebar-card">
                <div class="category-sidebar-header">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Filter By Category</span>
                </div>
                <ul class="category-sidebar-list">
                    <?php getcategories(); ?>
                </ul>
            </div>
        </div>

        <!-- Main Product Grid -->
        <div class="col-lg-9 col-md-8">
            <div class="row g-3 align-items-stretch">
                <?php
                if (isset($_GET['category'])) {
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