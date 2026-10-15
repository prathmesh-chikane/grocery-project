<?php
$page_title = "About Us | Ganesh General Store & Supermarket";
include('includes/header.php');
?>

<main class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About Us</li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <section class="checkout-card p-5 text-center mb-5" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #ffffff;">
        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">OUR STORE PROMISE</span>
        <h1 class="display-5 fw-bold mb-3" style="font-family: var(--font-heading); color: #ffffff;">Delivering Freshness & Quality Everyday</h1>
        <p class="lead mx-auto mb-4" style="max-width: 650px; color: #d1fae5;">
            Ganesh General Store is Satara’s premier neighbourhood e-grocery store, dedicated to providing handpicked spices, pure grains, fresh dairy, and daily essentials with lightning-fast delivery.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="display_all.php" class="btn btn-light fw-bold px-4 py-2 rounded-pill text-success">
                <i class="fa-solid fa-basket-shopping me-1"></i> Explore Store
            </a>
            <a href="contact_us.php" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">
                <i class="fa-solid fa-envelope me-1"></i> Contact Us
            </a>
        </div>
    </section>

    <!-- Story & Mission Grid -->
    <section class="mb-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="./images/kirana-new-bf88.jpg" alt="Our Store" class="img-fluid rounded-4 shadow-md w-100" style="object-fit: cover; max-height: 400px;" onerror="this.src='./images/ganesh store logo.png'">
                    <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded-3 shadow-sm border d-flex align-items-center gap-3">
                        <i class="fa-solid fa-award text-success fs-2"></i>
                        <div>
                            <strong class="d-block text-dark">Trusted Quality</strong>
                            <span class="small text-muted">100% Handpicked Essentials</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ps-lg-3">
                    <span class="text-success fw-bold text-uppercase small letter-spacing-1">Our Journey</span>
                    <h2 class="fw-bold mb-3" style="font-family: var(--font-heading);">From Traditional Kirana to Modern Quick-Commerce</h2>
                    <p class="text-secondary" style="line-height: 1.8;">
                        Started as a cherished local general store in Satara, Ganesh General Store has grown into a modern grocery shopping platform that blends trusted community values with the speed and convenience of online shopping.
                    </p>
                    <p class="text-secondary" style="line-height: 1.8;">
                        Whether it is your morning milk and paneer, premium Basmati rice, authentic aromatic spices, or midnight snack cravings, we ensure each item reaches your doorstep in pristine condition within minutes.
                    </p>

                    <div class="row g-3 mt-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="fw-semibold text-dark small">Direct Sourcing</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="fw-semibold text-dark small">Hygienic Storage & Packing</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="fw-semibold text-dark small">Express Local Delivery</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="fw-semibold text-dark small">Transparent Honest Pricing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Stats Counters -->
    <section class="checkout-card p-4 mb-5">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-bold text-success" style="font-family: var(--font-heading);">500+</div>
                <div class="small fw-semibold text-muted text-uppercase">Grocery Essentials</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-bold text-primary" style="font-family: var(--font-heading);">15m</div>
                <div class="small fw-semibold text-muted text-uppercase">Average Delivery</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-bold text-warning" style="font-family: var(--font-heading);">10k+</div>
                <div class="small fw-semibold text-muted text-uppercase">Orders Fulfilled</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-bold text-danger" style="font-family: var(--font-heading);">99.8%</div>
                <div class="small fw-semibold text-muted text-uppercase">Customer Satisfaction</div>
            </div>
        </div>
    </section>
</main>

<?php include('includes/footer.php'); ?>
