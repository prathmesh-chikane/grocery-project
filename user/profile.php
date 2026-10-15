<?php
include('../includes/connection.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// Check if user is logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first to access your profile.');</script>";
    echo "<script>window.open('user_login.php', '_self');</script>";
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? '';

if ($user_id) {
    $query = "SELECT * FROM `user` WHERE user_id = '$user_id'";
} else {
    $query = "SELECT * FROM `user` WHERE username = '$username'";
}
$result = mysqli_query($con, $query);
$user_data = mysqli_fetch_assoc($result);

// User email & IP for orders query
$user_email = $user_data['email'] ?? '';
$user_ip = $user_data['user_ip'] ?? '';

// Fetch past orders — only this user's orders by email (secure, no IP leakage)
$past_orders_query = "SELECT * FROM orders WHERE email = '$user_email' ORDER BY order_date DESC LIMIT 10";
$past_orders_result = mysqli_query($con, $past_orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Ganesh General Store</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background-color: #f8fafc;">

<!-- Simple Header -->
<header class="bg-white border-bottom py-3 shadow-sm sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../index.php" class="grocery-brand">
            <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Ganesh Store" class="brand-logo-img" style="width:40px; height:40px;" onerror="this.src='../images/ganesh store logo.png'">
            <div>
                <h1 class="brand-title" style="font-size: 1.15rem;">Ganesh<span>Store</span></h1>
                <p class="brand-subtitle">Customer Account</p>
            </div>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="../cart.php" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-cart-shopping me-1"></i> Cart
            </a>
            <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>
    </div>
</header>

<main class="container my-5">
    <div class="row g-4">
        <!-- User Sidebar Card -->
        <div class="col-lg-4">
            <div class="checkout-card text-center p-4">
                <div class="mb-3">
                    <?php if (!empty($user_data['image'])): ?>
                        <img src="./user_images/<?php echo htmlspecialchars($user_data['image']); ?>" alt="Avatar" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #10b981;" onerror="this.src='../images/admin logo.png'">
                    <?php else: ?>
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success fw-bold fs-1" style="width: 100px; height: 100px;">
                            <?php echo strtoupper(substr($user_data['username'] ?? 'U', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h4 class="fw-bold mb-1" style="font-family: var(--font-heading);"><?php echo htmlspecialchars($user_data['username'] ?? 'Valued Customer'); ?></h4>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></p>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold mb-4">
                    <i class="fa-solid fa-star me-1 text-warning"></i> Registered Member
                </span>

                <div class="d-grid gap-2 text-start pt-3 border-top">
                    <a href="../index.php" class="btn btn-light border fw-semibold small text-start">
                        <i class="fa-solid fa-store me-2 text-success"></i> Browse Store
                    </a>
                    <a href="../cart.php" class="btn btn-light border fw-semibold small text-start">
                        <i class="fa-solid fa-cart-shopping me-2 text-success"></i> View Cart Items
                    </a>
                    <a href="logout.php" class="btn btn-light border text-danger fw-semibold small text-start">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Details & Orders -->
        <div class="col-lg-8">
            <!-- Profile Details Card -->
            <div class="checkout-card mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-address-card text-success"></i>
                    <span>Personal Information</span>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom text-muted">Full Username</label>
                        <div class="p-2 bg-light rounded-3 fw-semibold"><?php echo htmlspecialchars($user_data['username'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom text-muted">Email Address</label>
                        <div class="p-2 bg-light rounded-3 fw-semibold"><?php echo htmlspecialchars($user_data['email'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom text-muted">Mobile Number</label>
                        <div class="p-2 bg-light rounded-3 fw-semibold"><?php echo htmlspecialchars($user_data['mobile_number'] ?? 'Not set'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom text-muted">Default City</label>
                        <div class="p-2 bg-light rounded-3 fw-semibold">Satara, Maharashtra</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom text-muted">Default Delivery Address</label>
                        <div class="p-2 bg-light rounded-3 fw-semibold"><?php echo htmlspecialchars($user_data['address'] ?? 'Not set'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Card -->
            <div class="checkout-card">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-box-open text-success"></i>
                    <span>Recent Orders</span>
                </h5>

                <?php if ($past_orders_result && mysqli_num_rows($past_orders_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="border-bottom text-muted small text-uppercase">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($ord = mysqli_fetch_assoc($past_orders_result)): 
                                    $status = strtolower($ord['order_status'] ?? 'pending');
                                    $badge = match($status) {
                                        'delivered' => 'success',
                                        'shipped'   => 'primary',
                                        'cancelled' => 'danger',
                                        default     => 'warning'
                                    };
                                ?>
                                <tr class="border-bottom">
                                    <td class="fw-bold"><?php echo $ord['order_id']; ?></td>
                                    <td class="small text-muted"><?php echo date("d M Y", strtotime($ord['order_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badge; ?> <?php echo $badge==='warning'?'text-dark':''; ?>">
                                            <?php echo htmlspecialchars($ord['order_status']); ?>
                                        </span>
                                    </td>
                                    <td class="small"><?php echo htmlspecialchars($ord['payment_mode']); ?></td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="track_order.php?order_id=<?php echo $ord['order_id']; ?>" class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="fa-solid fa-location-dot me-1"></i> Track
                                            </a>
                                            <a href="order_summary.php?order_id=<?php echo $ord['order_id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                Receipt
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small m-0 py-3 text-center">No orders found under this account yet. Ready to fill your pantry?</p>
                    <div class="text-center pt-2">
                        <a href="../index.php" class="btn-primary-grocery btn-sm">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
