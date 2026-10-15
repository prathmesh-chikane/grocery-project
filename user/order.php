<!-- <?php
include('../includes/connection.php'); 
include('../functions/common_function.php'); 

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    die('User ID not provided.');
}

// Getting total price of all items
$get_ip_address = getIPAddress();
$total_price = 0;

$cart_query_price = "SELECT * FROM `cart` WHERE ip_address='$get_ip_address'";
$result_cart_price = mysqli_query($con, $cart_query_price);
$invoice_number = mt_rand();
$status = 'pending';
$count_products = mysqli_num_rows($result_cart_price);

while ($row_price = mysqli_fetch_array($result_cart_price)) {
    $product_id = $row_price['product_id'];
    $select_product = "SELECT * FROM `products` WHERE product_id=$product_id";
    $run_price = mysqli_query($con, $select_product);

    while ($row_product_price = mysqli_fetch_array($run_price)) {
        $product_price = $row_product_price['Product_price'];
        $total_price += $product_price;
    }
}

// Getting quantity from cart
$get_cart = "SELECT SUM(quantity) as total_quantity FROM `cart` WHERE ip_address='$get_ip_address'";
$run_cart = mysqli_query($con, $get_cart);
$get_item_quantity = mysqli_fetch_array($run_cart);
$quantity = $get_item_quantity['total_quantity'];

if ($quantity == 0) {
    $quantity = 1;
}
$subtotal = $total_price * $quantity;

// Insert order
$insert_orders = "INSERT INTO `orders` (user_id, amount_due, invoice_number, total_products, ordered_date, order_status)
VALUES ('$user_id', '$subtotal', '$invoice_number', '$count_products', NOW(), '$status')";
$result_query = mysqli_query($con, $insert_orders);

if ($result_query) {
    echo "<script>alert('Order placed successfully');</script>";
    echo "<script>window.open('profile.php','_self');</script>";
}
?> -->
