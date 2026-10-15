<?php
session_start();
if (!isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}
$order_id = intval($_GET['order_id']);
header("Location: order_summary.php?order_id=$order_id");
exit();
?>
