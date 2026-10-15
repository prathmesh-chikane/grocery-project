<?php
session_start();
session_regenerate_id(true);
session_unset();
session_destroy();
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
echo "<script>
    alert('Your account has been logged out.');
    window.location.href = '../index.php';
</script>";
header("Refresh:0; url=./index.php");
exit;
?>
