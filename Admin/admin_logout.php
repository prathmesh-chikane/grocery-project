<?php
session_start();
session_unset();
session_destroy();
// After admin logout, redirect to site home page
header('Location: ../index.php');
exit();
