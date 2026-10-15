<?php
session_start();
include('../includes/connection.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../user/user_login.php');
    exit();
}

// Helper: parse price string like '40$' or '40' to float
function parse_price($price_str) {
    $num = preg_replace('/[^0-9.]/', '', $price_str);
    return $num === '' ? 0.0 : floatval($num);
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Fetch customer details
    $customer_name = mysqli_real_escape_string($con, $_POST['customer_name']);
    $customer_phone = mysqli_real_escape_string($con, $_POST['customer_phone']);
    $customer_email = mysqli_real_escape_string($con, $_POST['customer_email']);
    $customer_address = mysqli_real_escape_string($con, $_POST['customer_address']);
    $grand_total = floatval($_POST['grand_total']);
    $username = $_SESSION['username'];

    // Split customer name into first and last (best-effort)
    $name_parts = explode(' ', trim($customer_name), 2);
    $first_name = mysqli_real_escape_string($con, $name_parts[0] ?? '');
    $last_name = mysqli_real_escape_string($con, $name_parts[1] ?? '');

    // Insert a parent order record into orders table using available columns in schema
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $order_status = 'Pending';

    $insert_order = "INSERT INTO orders (user_ip, first_name, last_name, address1, phone, email, order_status, order_date) 
                     VALUES ('" . mysqli_real_escape_string($con, $user_ip) . "', '$first_name', '$last_name', '" . mysqli_real_escape_string($con, $customer_address) . "', '" . mysqli_real_escape_string($con, $customer_phone) . "', '" . mysqli_real_escape_string($con, $customer_email) . "', '$order_status', NOW())";
    $order_result = mysqli_query($con, $insert_order);

    if ($order_result) {
        // Get the last inserted order_id
        $order_id = mysqli_insert_id($con);

        // Insert each cart item into order_items table
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cart_item) {
                $product_id = intval($cart_item['product_id']);
                $quantity = intval($cart_item['product_quantity']);

                // Fetch product price (unit price)
                $product_query = "SELECT Product_price, stock_quantity FROM products WHERE product_id = $product_id";
                $product_result = mysqli_query($con, $product_query);
                $product_data = mysqli_fetch_assoc($product_result);
                $product_price_raw = $product_data['Product_price'] ?? '0';
                $unit_price = parse_price($product_price_raw);

                // Insert into order_items table (price = unit price)
                $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                VALUES ('$order_id', '$product_id', '$quantity', '$unit_price')";
                mysqli_query($con, $insert_item);

                // Optionally decrease stock_quantity if column exists
                if (isset($product_data['stock_quantity'])) {
                    $new_stock = intval($product_data['stock_quantity']) - $quantity;
                    $update_stock = "UPDATE products SET stock_quantity = $new_stock WHERE product_id = $product_id";
                    @mysqli_query($con, $update_stock);
                }
            }
        }

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect or success message
        echo "<script>alert('Order placed successfully! Thank you for shopping.'); window.location.href='../index.php';</script>";
        exit();

    } else {
        echo "<script>alert('Failed to place order. Please try again!'); window.history.back();</script>";
        exit();
    }

} else {
    header('Location: ../display_all.php');
    exit();
}
?>
