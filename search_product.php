<?php
$search_query_text = htmlspecialchars($_GET['search_data'] ?? '');
$page_title = "Search: " . ($search_query_text ? $search_query_text : 'Products') . " | Ganesh General Store";
include('includes/header.php');
?>

<main class="container my-4">
    <!-- Breadcrumb & Page Title -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search Results</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="fw-bold m-0" style="font-family: var(--font-heading);">
                    Search Results for <span class="text-success">"<?php echo $search_query_text; ?>"</span>
                </h2>
                <p class="text-muted small m-0">Showing matching grocery items available for immediate delivery</p>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> All Products
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="category-sidebar-card">
                <div class="category-sidebar-header">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Browse Categories</span>
                </div>
                <ul class="category-sidebar-list">
                    <?php getcategories(); ?>
                </ul>
            </div>
        </div>

        <!-- Main Product Grid -->
        <div class="col-lg-9 col-md-8">
            <div class="row g-3">
                <?php
                if (isset($_GET['search_data_product'])) {
                    search_product();
                } else {
                    getproducts();
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>