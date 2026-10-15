<?php
require_once __DIR__ . '/includes/connection.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: view_products.php');
    exit();
}

$product_id = intval($_GET['id']);

// Delete product
$delete_query = "DELETE FROM `products` WHERE product_id = $product_id";
$result = mysqli_query($con, $delete_query);

header('Location: view_products.php?deleted=1');
exit();
?>
