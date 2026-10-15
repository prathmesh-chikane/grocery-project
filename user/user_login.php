<?php
require_once __DIR__ . '/../includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_login'])) {
    $input_username = trim($_POST['user_username'] ?? '');
    $input_password = $_POST['user_password'] ?? '';

    if ($input_username === '' || $input_password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Admin Login Check
        $stmt = mysqli_prepare($con, "SELECT admin_id, username, password FROM admin WHERE username = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $input_username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $admin_id, $admin_username, $admin_password);
                mysqli_stmt_fetch($stmt);
                if (password_verify($input_password, $admin_password) || $input_password === $admin_password) {
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_username'] = $admin_username;
                    header('Location: ../Admin/index.php');
                    exit();
                }
            }
        }

        // Customer Login Check
        $stmt2 = mysqli_prepare($con, "SELECT user_id, username, password FROM user WHERE username = ? LIMIT 1");
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, 's', $input_username);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_store_result($stmt2);

            if (mysqli_stmt_num_rows($stmt2) > 0) {
                mysqli_stmt_bind_result($stmt2, $user_id, $user_username, $user_password);
                mysqli_stmt_fetch($stmt2);
                if (password_verify($input_password, $user_password) || $input_password === $user_password) {
                    $_SESSION['username'] = $user_username;
                    $_SESSION['user_id'] = $user_id;

                    $ip = getIPAddress();
                    @mysqli_query($con, "INSERT INTO user_login (user_id, username) VALUES ('$user_id', '$user_username')");

                    header('Location: ../index.php');
                    exit();
                }
            }
        }

        $error = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login | Ganesh General Store</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <a href="../index.php" class="auth-logo-badge text-decoration-none">
                <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Logo" style="width:48px; height:48px; object-fit:contain;" onerror="this.src='../images/ganesh store logo.png'">
            </a>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Login to order groceries & track your deliveries</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 small py-2 rounded-3 mb-3" role="alert">
                <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                <div><?php echo htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="user_username" class="form-label-custom">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="user_username" id="user_username" class="form-control form-control-custom border-start-0" placeholder="Enter your registered username" required autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="user_password" class="form-label-custom mb-0">Password</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="user_password" id="user_password" class="form-control form-control-custom border-start-0" placeholder="Enter your password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" name="user_login" class="btn-checkout border-0 mt-2 mb-3">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In
            </button>

            <div class="text-center pt-2 border-top">
                <p class="small text-muted mb-2">New to Ganesh Store? <a href="user_registration.php" class="fw-bold text-success text-decoration-none">Create an Account</a></p>
                <a href="../index.php" class="small text-secondary text-decoration-none"><i class="fa-solid fa-house me-1"></i> Back to Homepage</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
