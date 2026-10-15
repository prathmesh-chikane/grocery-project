<?php
require_once __DIR__ . '/includes/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['admin_username'])) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = mysqli_prepare($con, "SELECT * FROM `admin` WHERE username = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = $result ? mysqli_fetch_assoc($result) : null;

            if ($row) {
                $hash = isset($row['password']) ? $row['password'] : '';
                $password_ok = false;

                if (password_verify($password, $hash)) {
                    $password_ok = true;
                } elseif ($password === $hash) {
                    $password_ok = true;
                }

                if ($password_ok) {
                    $_SESSION['admin_id'] = $row['admin_id'] ?? $row['id'] ?? 1;
                    $_SESSION['admin_username'] = $row['username'];
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Invalid credentials. Please verify your password.';
                }
            } else {
                $error = 'No administrator found with this username.';
            }
        } else {
            $error = 'Database connection error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Sign In | Ganesh Store</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="admin_style.css">
</head>
<body style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">

<div class="admin-card-table p-4 p-md-5" style="max-width: 440px; width: 100%; border: 1px solid #334155; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
    <div class="text-center mb-4">
        <div class="stat-icon-wrapper icon-green mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h2 class="fw-bold text-dark m-0" style="font-family: var(--font-heading); font-size: 1.6rem;">Admin Portal</h2>
        <p class="text-muted small mt-1">Sign in with your management credentials</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 small py-2 rounded-3 mb-3">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="admin-form-label">Admin Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                <input type="text" class="admin-form-control border-start-0" id="username" name="username" placeholder="Enter admin username" required autocomplete="username">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="admin-form-label">Admin Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="admin-form-control border-start-0" id="password" name="password" placeholder="Enter password" required autocomplete="current-password">
            </div>
        </div>

        <button type="submit" class="btn-admin-submit w-100 justify-content-center py-3 mb-3">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Log In to Dashboard</span>
        </button>

        <div class="text-center pt-3 border-top">
            <a href="../index.php" class="text-secondary small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Return to Customer Website
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
