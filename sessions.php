<?php
// Start the session
session_start();

// Set session variables
$_SESSION['username'] = "ganeshstore";
$_SESSION['password'] = "ganeshstore123";
$_SESSION['email'] = "ganeshstore24@example.com";

echo "Session variables are set.";
?>