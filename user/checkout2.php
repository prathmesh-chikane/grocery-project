<?php
session_start();
include('../includes/connection.php');
include('../functions/common_function.php');
$get_ip_add = getIPAddress();

// Authentication requirement
if (!isset($_SESSION['username'])) {
    header('Location: ../user/user_login.php');
    exit();
}

$is_buy_now = isset($_GET['buy_now']) && $_GET['buy_now'] == '1';
$subtotal = 0;
$buy_now_data = [];

// Helper: parse price string like '40$' or '40' to float
function parse_price($price_str) {
    $num = preg_replace('/[^0-9.]/', '', strval($price_str));
    return $num === '' ? 0.0 : floatval($num);
}

if ($is_buy_now) {
    $product_id = intval($_GET['product_id'] ?? 0);
    $quantity = max(1, intval($_GET['quantity'] ?? 1));
    $product_query = "SELECT * FROM `products` WHERE product_id = '$product_id'";
    $product_result = mysqli_query($con, $product_query);
    $product = mysqli_fetch_assoc($product_result);

    if ($product) {
        $price = parse_price($product['Product_price']);
        $total = $price * $quantity;
        $subtotal += $total;

        $buy_now_data[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price,
            'name' => $product['product_name'],
            'image' => $product['product_image'] ?? '',
            'total' => $total
        ];
    }
} else {
    $cart_query = "SELECT * FROM `cart` WHERE ip_address = '$get_ip_add'";
    $cart_result = mysqli_query($con, $cart_query);

    while ($row = mysqli_fetch_assoc($cart_result)) {
        $product_id = intval($row['product_id']);
        $qty = intval($row['quantity']);
        $product_query = "SELECT * FROM `products` WHERE product_id = '$product_id'";
        $product_result = mysqli_query($con, $product_query);
        $product = mysqli_fetch_assoc($product_result);

        if ($product) {
            $price = parse_price($product['Product_price']);
            $total = $price * $qty;
            $subtotal += $total;

            $buy_now_data[] = [
                'product_id' => $product_id,
                'quantity' => $qty,
                'price' => $price,
                'name' => $product['product_name'],
                'image' => $product['product_image'] ?? '',
                'total' => $total
            ];
        }
    }
}

// Discount & Taxes
$discount = ($subtotal > 200) ? 10 : 0;
$subtotal_after_discount = max(0, $subtotal - $discount);
$gst = round($subtotal_after_discount * 0.01, 2);
$grand_total = $subtotal_after_discount + $gst;

if (isset($_POST['place_order'])) {
    $first_name = mysqli_real_escape_string($con, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($con, $_POST['last_name'] ?? '');
    $address1 = mysqli_real_escape_string($con, $_POST['address1'] ?? '');
    $address2 = mysqli_real_escape_string($con, $_POST['address2'] ?? '');
    $city = mysqli_real_escape_string($con, $_POST['city'] ?? '');
    $state = mysqli_real_escape_string($con, $_POST['state'] ?? '');
    $postcode = mysqli_real_escape_string($con, $_POST['postcode'] ?? '');
    $phone = mysqli_real_escape_string($con, $_POST['phone'] ?? '');
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $payment_mode = mysqli_real_escape_string($con, $_POST['payment_mode'] ?? 'COD');
    $order_status = "Pending";
    $order_date = date('Y-m-d H:i:s');

    $insert_order = "INSERT INTO orders (user_ip, first_name, last_name, address1, address2, city, state, postcode, phone, email, payment_mode, order_status, order_date)
                     VALUES ('$get_ip_add', '$first_name', '$last_name', '$address1', '$address2', '$city', '$state', '$postcode', '$phone', '$email', '$payment_mode', '$order_status', '$order_date')";
    mysqli_query($con, $insert_order);
    $order_id = mysqli_insert_id($con);

    foreach ($buy_now_data as $item) {
        $product_id_i = intval($item['product_id']);
        $quantity_i = intval($item['quantity']);
        $pq = mysqli_query($con, "SELECT stock_quantity FROM products WHERE product_id = $product_id_i");
        $prod = $pq ? mysqli_fetch_assoc($pq) : null;
        $stock_available = $prod ? intval($prod['stock_quantity']) : 0;

        if ($stock_available < $quantity_i) {
            echo "<script>alert('Insufficient stock for {$item['name']}. Available: $stock_available'); window.history.back();</script>";
            mysqli_query($con, "DELETE FROM order_items WHERE order_id = $order_id");
            mysqli_query($con, "DELETE FROM orders WHERE order_id = $order_id");
            exit();
        }

        $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price)
                        VALUES ('$order_id', '$product_id_i', '$quantity_i', '{$item['price']}')";
        mysqli_query($con, $insert_item);

        // Decrease stock
        $new_stock = max(0, $stock_available - $quantity_i);
        mysqli_query($con, "UPDATE products SET stock_quantity = $new_stock WHERE product_id = $product_id_i");
    }

    if (!empty($_POST['payment_id'])) {
        $payment_id = $_POST['payment_id'];
        $payment_status = $_POST['payment_status'];
        $payment_amount = $_POST['payment_amount'];

        $insert_payment = "INSERT INTO payment (order_id, payment_id, payment_status, amount) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($insert_payment);
        $stmt->bind_param("issi", $order_id, $payment_id, $payment_status, $payment_amount);
        $stmt->execute();
    }

    if (!$is_buy_now) {
        $clear_cart = "DELETE FROM cart WHERE ip_address = '$get_ip_add'";
        mysqli_query($con, $clear_cart);
    }

    // Redirect to Order Summary Confirmation page
    echo "<script>
        alert('🎉 Order placed successfully! Order ID: #$order_id');
        window.location.href='order_summary.php?order_id=$order_id';
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | Ganesh General Store</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Store Styles -->
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background-color: #f8fafc;">

<!-- Simple Checkout Header -->
<header class="bg-white border-bottom py-3 shadow-sm sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../index.php" class="grocery-brand">
            <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Ganesh Store" class="brand-logo-img" style="width:40px; height:40px;" onerror="this.src='../images/ganesh store logo.png'">
            <div>
                <h1 class="brand-title" style="font-size: 1.15rem;">Ganesh<span>Store</span></h1>
                <p class="brand-subtitle">Secure Checkout</p>
            </div>
        </a>
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fa-solid fa-lock text-success fs-6"></i>
            <span class="fw-semibold">256-bit Secure Encryption</span>
        </div>
    </div>
</header>

<main class="container my-5">
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="font-family: var(--font-heading);">Checkout & Delivery Details</h2>
        <p class="text-muted small">Please verify your shipping address and choose a payment option.</p>
    </div>

    <?php if (count($buy_now_data) > 0): ?>
    <form action="" method="POST" id="checkoutForm">
        <div class="row g-4">
            <!-- Left: Delivery Information -->
            <div class="col-lg-7">
                <div class="checkout-card mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-location-dot text-success"></i>
                        <span>Delivery Address</span>
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control-custom" placeholder="e.g. Rahul" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control-custom" placeholder="e.g. Sharma" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address1" class="form-control-custom" placeholder="House/Flat No., Building name, Street" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Apartment, Suite, Landmark (Optional)</label>
                            <input type="text" name="address2" class="form-control-custom" placeholder="Near Temple / Opposite Bank">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control-custom" value="Satara" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" class="form-control-custom" value="Maharashtra" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">PIN / Postal Code <span class="text-danger">*</span></label>
                            <input type="text" name="postcode" class="form-control-custom" placeholder="415012" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control-custom" placeholder="10-digit mobile number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control-custom" placeholder="name@example.com" required>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="checkout-card">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-credit-card text-success"></i>
                        <span>Select Payment Method</span>
                    </h5>

                    <!-- Cash on Delivery -->
                    <label class="payment-radio-box active w-100" for="codOption">
                        <input class="form-check-input mt-0" type="radio" name="payment_mode" id="codOption" value="COD" checked>
                        <div>
                            <div class="fw-bold text-dark"><i class="fa-solid fa-money-bill-1-wave text-success me-1"></i> Cash on Delivery (COD)</div>
                            <div class="small text-muted">Pay easily in cash or UPI QR code when our delivery partner arrives at your door.</div>
                        </div>
                    </label>

                    <!-- Online Razorpay -->
                    <label class="payment-radio-box w-100" for="onlineOption">
                        <input class="form-check-input mt-0" type="radio" name="payment_mode" id="onlineOption" value="Online">
                        <div>
                            <div class="fw-bold text-dark"><i class="fa-solid fa-bolt text-primary me-1"></i> Online Payment (UPI, Cards, NetBanking)</div>
                            <div class="small text-muted">Instant and secure payment processed via Razorpay.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Right: Order Summary Sidebar -->
            <div class="col-lg-5">
                <div class="order-summary-card">
                    <h4 class="order-summary-title">Order Items (<?php echo count($buy_now_data); ?>)</h4>

                    <div class="mb-3" style="max-height: 240px; overflow-y: auto;">
                        <?php foreach ($buy_now_data as $item): ?>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <img src="../Admin/product_images/<?= htmlspecialchars($item['image']) ?>" alt="" style="width:40px; height:40px; object-fit:contain; border-radius:8px; border:1px solid #e2e8f0; padding:2px;" onerror="this.onerror=null;this.src='../images/kirana-new-bf88.jpg';">
                                <div>
                                    <div class="fw-semibold small text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                    <span class="text-muted small">Qty: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></span>
                                </div>
                            </div>
                            <span class="fw-bold text-dark small">₹<?= number_format($item['total'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row">
                        <span>Items Subtotal</span>
                        <span>₹<?= number_format($subtotal, 2); ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="summary-row">
                        <span class="text-success"><i class="fa-solid fa-tag me-1"></i> Special Discount</span>
                        <span class="text-success fw-semibold">-₹<?= number_format($discount, 2); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>GST / Estimated Taxes (1%)</span>
                        <span>₹<?= number_format($gst, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Delivery Charges</span>
                        <span class="text-success fw-bold">FREE</span>
                    </div>

                    <div class="summary-row total-row">
                        <span>Grand Total</span>
                        <span class="text-success">₹<?= number_format($grand_total, 2); ?></span>
                    </div>

                    <button type="button" id="bookAndPayButton" class="btn-checkout">
                        <i class="fa-solid fa-lock"></i>
                        <span>Confirm & Place Order</span>
                    </button>

                    <div class="text-center mt-3">
                        <a href="../cart.php" class="text-muted small text-decoration-none">
                            <i class="fa-solid fa-arrow-left me-1"></i> Return to Shopping Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="payment_id" name="payment_id">
        <input type="hidden" id="payment_status" name="payment_status">
        <input type="hidden" id="payment_amount" name="payment_amount">
    </form>
    <?php else: ?>
    <div class="checkout-card text-center p-5">
        <i class="fa-solid fa-cart-shopping text-muted fs-1 mb-3"></i>
        <h4 class="fw-bold">No Items to Checkout</h4>
        <p class="text-muted">Your cart is empty. Please add items before checking out.</p>
        <a href="../index.php" class="btn-primary-grocery mt-2">Go to Store</a>
    </div>
    <?php endif; ?>
</main>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.querySelectorAll('.payment-radio-box').forEach(box => {
    box.addEventListener('click', () => {
        document.querySelectorAll('.payment-radio-box').forEach(b => b.classList.remove('active'));
        box.classList.add('active');
        box.querySelector('input').checked = true;
    });
});

document.getElementById('bookAndPayButton').onclick = function (e) {
    e.preventDefault();
    const form = document.getElementById('checkoutForm');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;

    if (paymentMode === 'COD') {
        let placeOrderField = document.createElement("input");
        placeOrderField.type = "hidden";
        placeOrderField.name = "place_order";
        placeOrderField.value = "1";
        form.appendChild(placeOrderField);
        form.submit();
    } else {
        var options = {
            "key": "rzp_test_0UMsjYgsVFPp5e",
            "amount": <?= round($grand_total * 100) ?>,
            "currency": "INR",
            "name": "Ganesh General Store",
            "description": "Grocery Order Payment",
            "handler": function (response) {
                document.getElementById('payment_id').value = response.razorpay_payment_id;
                document.getElementById('payment_status').value = "Successful";
                document.getElementById('payment_amount').value = <?= $grand_total ?>;

                let placeOrderField = document.createElement("input");
                placeOrderField.type = "hidden";
                placeOrderField.name = "place_order";
                placeOrderField.value = "1";
                form.appendChild(placeOrderField);
                form.submit();
            },
            "theme": {
                "color": "#059669"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    }
};
</script>
</body>
</html>
