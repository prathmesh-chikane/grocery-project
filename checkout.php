<?php
// Forward seamlessly to the main modernized checkout
$query_string = $_SERVER['QUERY_STRING'] ?? '';
$redirect_url = './user/checkout2.php' . ($query_string ? '?' . $query_string : '');
header("Location: $redirect_url");
exit();
?>
