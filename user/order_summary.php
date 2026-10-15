
<?php
session_start();
require_once __DIR__ . '/../includes/connection.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = (int) $_GET['order_id'];

// Fetch order securely
$order_stmt = mysqli_prepare(
    $con,
    "SELECT * FROM orders WHERE order_id = ? LIMIT 1"
);

mysqli_stmt_bind_param($order_stmt, 'i', $order_id);
mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die(
        "<div style='text-align:center;margin-top:50px;'>
            <h3>Order not found.</h3>
            <a href='../index.php'>Back to Home</a>
        </div>"
    );
}

// Fetch ordered items
$items_stmt = mysqli_prepare(
    $con,
    "SELECT oi.*, p.product_name, p.product_image, p.Product_unit
     FROM order_items oi
     LEFT JOIN products p ON oi.product_id = p.product_id
     WHERE oi.order_id = ?"
);

mysqli_stmt_bind_param($items_stmt, 'i', $order_id);
mysqli_stmt_execute($items_stmt);

$items_result = mysqli_stmt_get_result($items_stmt);

$items = [];
$grand_total = 0;

while ($item = mysqli_fetch_assoc($items_result)) {
    $price = (float) ($item['price'] ?? 0);
    $quantity = (int) ($item['quantity'] ?? 0);

    $item['price'] = $price;
    $item['quantity'] = $quantity;
    $item['item_total'] = $price * $quantity;

    $grand_total += $item['item_total'];
    $items[] = $item;
}

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order Confirmation #<?= $order_id ?> | Ganesh General Store</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="../style.css">

    <style>
        body {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            flex: 1;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            top: 20px;
            left: -50%;
            width: 100%;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .timeline-step:first-child::before {
            display: none;
        }

        .timeline-step.completed::before {
            background: #10b981;
        }

        .timeline-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #64748b;
            position: relative;
            z-index: 2;
        }

        .timeline-step.completed .timeline-circle {
            background: #10b981;
            border-color: #10b981;
            color: #fff;
        }

        .timeline-step.active .timeline-circle {
            border-color: #10b981;
            color: #10b981;
        }

        .checkout-card {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.05);
        }

        @media print {
            header, .no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .checkout-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>

<header class="bg-white border-bottom py-3 shadow-sm">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../index.php" class="grocery-brand text-decoration-none">
            <img
                src="../images/ganesh_store_logo__1_-removebg-preview.png"
                alt="Ganesh Store"
                class="brand-logo-img"
                style="width:40px;height:40px;"
                onerror="this.onerror=null;this.src='../images/ganesh store logo.png';"
            >
            <div>
                <h1 class="brand-title" style="font-size:1.15rem;">
                    Ganesh<span>Store</span>
                </h1>
                <p class="brand-subtitle mb-0">Order Confirmation</p>
            </div>
        </a>

        <a href="../index.php"
           class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold no-print">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Store
        </a>
    </div>
</header>

<main class="container my-5" style="max-width:860px;">

    <div class="checkout-card text-center mb-4">
        <div class="empty-state-icon mb-3"
             style="width:70px;height:70px;font-size:2rem;">
            <i class="fa-solid fa-check"></i>
        </div>

        <h2 class="fw-bold mb-1">
            Thank You! Your Order is Confirmed
        </h2>

        <p class="text-muted mb-3">
            Order ID:
            <strong class="text-dark">#<?= $order_id ?></strong>
            • Placed on
            <?= h(date('d M Y, h:i A', strtotime($order['order_date'] ?? 'now'))) ?>
        </p>

        <div class="d-flex justify-content-between my-4 px-2">
            <div class="timeline-step completed">
                <div class="timeline-circle">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="small fw-bold mt-2">Placed</div>
            </div>

            <div class="timeline-step active">
                <div class="timeline-circle">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="small fw-bold mt-2">Packing</div>
            </div>

            <div class="timeline-step">
                <div class="timeline-circle">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="small fw-semibold mt-2 text-muted">
                    Out for Delivery
                </div>
            </div>

            <div class="timeline-step">
                <div class="timeline-circle">
                    <i class="fa-solid fa-house-chimney-check"></i>
                </div>
                <div class="small fw-semibold mt-2 text-muted">
                    Delivered
                </div>
            </div>
        </div>

        <div class="alert alert-success d-inline-flex align-items-center gap-2 small m-0 px-3 py-2 rounded-pill">
            <i class="fa-solid fa-bolt"></i>
            <span>
                Estimated delivery in <strong>15 Minutes</strong>
                by Ganesh Store Express Partner
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="checkout-card h-100">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-location-dot text-success me-2"></i>
                    Delivery Details
                </h5>

                <p class="mb-1 fw-bold">
                    <?= h(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?>
                </p>

                <p class="text-muted small mb-2">
                    <?= h($order['address1'] ?? '') ?>
                    <?php if (!empty($order['address2'])): ?>
                        , <?= h($order['address2']) ?>
                    <?php endif; ?>
                    <br>
                    <?= h($order['city'] ?? '') ?>,
                    <?= h($order['state'] ?? '') ?> -
                    <?= h($order['postcode'] ?? '') ?>
                </p>

                <div class="small text-muted mb-1">
                    <i class="fa-solid fa-phone me-2 text-success"></i>
                    <?= h($order['phone'] ?? '') ?>
                </div>

                <div class="small text-muted">
                    <i class="fa-solid fa-envelope me-2 text-success"></i>
                    <?= h($order['email'] ?? '') ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="checkout-card h-100">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-wallet text-success me-2"></i>
                    Payment Information
                </h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Payment Mode</span>
                    <span class="fw-bold">
                        <?= h($order['payment_mode'] ?? '') ?>
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Order Status</span>
                    <span class="badge bg-warning text-dark fw-bold">
                        <?= h($order['order_status'] ?? 'Pending') ?>
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Delivery Type</span>
                    <span class="badge bg-success">Instant 15-Min</span>
                </div>
            </div>
        </div>

    </div>

    <div class="checkout-card mb-4">
        <h5 class="fw-bold mb-3">
            <i class="fa-solid fa-basket-shopping text-success me-2"></i>
            Ordered Items
        </h5>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="border-bottom">
                    <tr class="text-muted small text-uppercase">
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="border-bottom">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            src="../Admin/product_images/<?= h($item['product_image'] ?? '') ?>"
                                            alt="<?= h($item['product_name'] ?? 'Product') ?>"
                                            style="width:38px;height:38px;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0;padding:2px;"
                                            onerror="this.onerror=null;this.src='../images/kirana-new-bf88.jpg';"
                                        >

                                        <div>
                                            <div class="fw-bold small">
                                                <?= h($item['product_name'] ?? 'Product unavailable') ?>
                                            </div>
                                            <span class="text-muted" style="font-size:.75rem;">
                                                <?= h($item['Product_unit'] ?? '') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="small">
                                    ₹<?= number_format($item['price'], 2) ?>
                                </td>

                                <td class="small fw-semibold">
                                    <?= (int)$item['quantity'] ?>
                                </td>

                                <td class="text-end fw-bold text-dark">
                                    ₹<?= number_format($item['item_total'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No items found for this order.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold pt-3">
                            Delivery Fee:
                        </td>
                        <td class="text-end text-success fw-bold pt-3">
                            FREE
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="text-end fw-bold fs-5 text-dark">
                            Total Amount:
                        </td>
                        <td class="text-end fw-bold fs-5 text-success">
                            ₹<?= number_format($grand_total, 2) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 no-print">
        <button onclick="window.print()"
                class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-print me-1"></i> Print Receipt
        </button>

        <a href="../index.php" class="btn-primary-grocery btn-lg">
            <i class="fa-solid fa-bag-shopping me-1"></i>
            Continue Shopping
        </a>
    </div>

</main>
</body>
</html>
