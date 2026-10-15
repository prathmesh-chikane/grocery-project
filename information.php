<?php
// Start the session
session_start();

// Check if session variables are set
if (isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['email'])) {
    echo "Welcome " . $_SESSION['username'];
    echo "<br>";
    echo "your password is " . $_SESSION['password'];
    echo "<br>";
    echo "your email is " . $_SESSION['email'];
} else {
    echo "please login  again to continue";
}
?>