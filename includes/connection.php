<?php

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
$onLocal = (strpos($serverName, 'localhost') !== false)
    || in_array($serverAddr, ['127.0.0.1', '::1'])
    || (php_sapi_name() === 'cli');

if ($onLocal) {
    // xampp
    $host = '127.0.0.1';  // Use IP instead of 'localhost' to force TCP (avoids socket issues)
    $username = 'root';
    $password = '';
    $database = 'mystore';
} else {
    // InfinityFree 
    $host = 'sql307.infinityfree.com';
    $username = 'if0_42430295';
    $password = 'EGHd98FhOX';
    $database = 'if0_42430295_mystore';
}

$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    $err = mysqli_connect_error();
    error_log('Database connection failed: ' . $err);
    die('<b>Database connection failed:</b> ' . htmlspecialchars($err) . '<br>Make sure MySQL is running in XAMPP Control Panel.');
}
?>