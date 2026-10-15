<?php
$page_title = "Your Shopping Cart | Ganesh General Store";
include('includes/header.php');

// Handle update and remove logic
$get_ip_add = getIPAddress();

if (isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            $qty = max(1, intval($qty));
            $product_id = intval($product_id);
            $update_query = "UPDATE `cart` SET quantity = $qty WHERE ip_address = '$get_ip_add' AND product_id = $product_id";
            mysqli_query($con, $update_query);
        }
        echo "<script>window.location.href='cart.php';</script>";
        exit();
    }
}

if (isset($_POST['remove_cart'])) {
    if (!empty($_POST['remove']) && is_array($_POST['remove'])) {
        foreach ($_POST['remove'] as $remove_id) {
            $remove_id = intval($remove_id);
            $delete_query = "DELETE FROM `cart` WHERE product_id = $remove_id AND ip_address = '$get_ip_add'";
            mysqli_query($con, $delete_query);
        }
        echo "<script>window.location.href='cart.php';</script>";
        exit();
    }
}

// Single item remove via GET
if (isset($_GET['remove_item'])) {
    $del_id = intval($_GET['remove_item']);
    $del_q = "DELETE FROM `cart` WHERE product_id = $del_id AND ip_address = '$get_ip_add'";
    mysqli_query($con, $del_q);
    echo "<script>window.location.href='cart.php';</script>";
    exit();
}
?>

<main class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold m-0" style="font-family: var(--font-heading);">Your Shopping Cart</h2>
            <p class="text-muted small m-0">Review items, adjust quantities, and proceed to checkout</p>
        </div>
        <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>

    <?php
    $cart_query = "SELECT * FROM `cart` WHERE ip_address = '$get_ip_add'";
    $result = mysqli_query($con, $cart_query);
    $result_count = mysqli_num_rows($result);
    $total_price = 0;
    ?>

    <?php if ($result_count > 0): ?>
    <form action="cart.php" method="post">
        <div class="row g-4">
            <!-- Left Column: Items Table / List -->
            <div class="col-lg-8">
                <div class="cart-table-card table-responsive">
                    <table class="cart-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Select</th>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th style="width: 140px;">Quantity</th>
                                <th>Subtotal</th>
                                <th style="width: 80px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_array($result)) {
                                $product_id = intval($row['product_id']);
                                $quantity = max(1, intval($row['quantity']));

                                $select_product = "SELECT * FROM `products` WHERE product_id = '$product_id'";
                                $result_products = mysqli_query($con, $select_product);
                                $row_product = mysqli_fetch_array($result_products);

                                if ($row_product) {
                                    $product_name = htmlspecialchars($row_product['product_name']);
                                    $product_image = htmlspecialchars($row_product['product_image']);
                                    $product_unit = htmlspecialchars($row_product['Product_unit'] ?? 'Pack');
                                    
                                    $price_raw = $row_product['Product_price'];
                                    $num = preg_replace('/[^0-9.]/', '', strval($price_raw));
                                    $product_price = $num === '' ? 0.0 : floatval($num);
                                    
                                    $subtotal = $product_price * $quantity;
                                    $total_price += $subtotal;

                                    echo "
                                    <tr>
                                        <td>
                                            <input type='checkbox' name='remove[]' value='$product_id' class='form-check-input' title='Select to remove'>
                                        </td>
                                        <td>
                                            <div class='d-flex align-items-center gap-3'>
                                                <img src='./Admin/product_images/$product_image' class='cart-item-img' alt='$product_name' onerror=\"this.onerror=null;this.src='./images/kirana-new-bf88.jpg';\">
                                                <div>
                                                    <a href='product details.php?product_id=$product_id' class='fw-bold text-dark text-decoration-none d-block'>$product_name</a>
                                                    <span class='badge bg-light text-secondary border small'>$product_unit</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class='fw-semibold'>₹" . number_format($product_price, 2) . "</span>
                                        </td>
                                        <td>
                                            <div class='d-flex align-items-center gap-1'>
                                                <input type='number' name='qty[$product_id]' value='$quantity' min='1' max='99' class='cart-qty-input'>
                                            </div>
                                        </td>
                                        <td>
                                            <span class='fw-bold text-success'>₹" . number_format($subtotal, 2) . "</span>
                                        </td>
                                        <td class='text-center'>
                                            <a href='cart.php?remove_item=$product_id' class='text-danger fs-5' title='Remove item' onclick=\"return confirm('Remove this item from cart?')\">
                                                <i class='fa-regular fa-trash-can'></i>
                                            </a>
                                        </td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Action Buttons -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                    <button type="submit" name="remove_cart" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-trash me-1"></i> Remove Selected
                    </button>
                    <button type="submit" name="update_cart" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Update Quantities
                    </button>
                </div>
            </div>

            <!-- Right Column: Order Bill Summary -->
            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h3 class="order-summary-title">Bill Details</h3>

                    <div class="summary-row">
                        <span>Items Subtotal</span>
                        <span class="fw-bold">₹<?php echo number_format($total_price, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Delivery Charge</span>
                        <span class="text-success fw-bold">FREE <span class="badge bg-success bg-opacity-10 text-success ms-1">15 Mins</span></span>
                    </div>

                    <div class="summary-row">
                        <span>Handling & Packaging</span>
                        <span class="text-success fw-bold">₹0.00</span>
                    </div>

                    <div class="summary-row total-row">
                        <span>To Pay</span>
                        <span class="text-success">₹<?php echo number_format($total_price, 2); ?></span>
                    </div>

                    <!-- Checkout CTA Button -->
                    <a href="./user/checkout2.php" class="btn-checkout">
                        <span>Proceed to Checkout</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <!-- Trust Box -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="fa-solid fa-shield-check text-success fs-5"></i>
                            <span>Safe and secure payments with COD & Razorpay</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="fa-solid fa-truck-fast text-success fs-5"></i>
                            <span>Instant 15-min delivery from local Satara store</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
    <!-- Empty Cart State -->
    <div class="cart-table-card p-5 text-center my-4">
        <div class="empty-state-box">
            <div class="empty-state-icon">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Your Cart is Currently Empty</h3>
            <p class="text-muted mb-4" style="max-width: 450px; margin: 0 auto;">Looks like you haven't added anything to your cart yet. Explore our fresh vegetables, groceries, and staples to start shopping!</p>
            <a href="index.php" class="btn-primary-grocery btn-lg">
                <i class="fa-solid fa-bag-shopping me-1"></i> Start Shopping Now
            </a>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>
