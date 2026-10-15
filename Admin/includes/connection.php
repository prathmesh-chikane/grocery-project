<?php
// Database connection with environment detection (local XAMPP vs InfinityFree)
// Uses local DB when running on localhost/127.0.0.1; otherwise uses the provided InfinityFree credentials.

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
$onLocal = (strpos($serverName, 'localhost') !== false)
    || in_array($serverAddr, ['127.0.0.1', '::1'])
    || (php_sapi_name() === 'cli');

if ($onLocal) {
    // Local XAMPP settings
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'mystore';
} else {
    // InfinityFree settings (replace these if your vPanel gives different values)
    $host = 'sql307.infinityfree.com';
    $username = 'if0_42430295';
    $password = 'EGHd98FhOX';
    $database = 'if0_42430295_mystore';
}

$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    // Log detailed error on server logs but show a generic message to users
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Database connection failed. Check server logs.');
}
?>