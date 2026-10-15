<?php
include('../includes/connection.php'); 
include('../functions/common_function.php'); 

$error = '';
$success = '';

if (isset($_POST['user_register'])) {
    $user_username = mysqli_real_escape_string($con, trim($_POST['user_username'] ?? ''));
    $user_email = mysqli_real_escape_string($con, trim($_POST['user_email'] ?? ''));
    $user_password = $_POST['user_password'] ?? '';
    $user_confirm_password = $_POST['user_confirm_password'] ?? '';
    $user_address = mysqli_real_escape_string($con, trim($_POST['user_address'] ?? ''));
    $user_contact = mysqli_real_escape_string($con, trim($_POST['user_contact'] ?? ''));
    
    $user_image = $_FILES['user_image']['name'] ?? '';
    $user_image_tmp = $_FILES['user_image']['tmp_name'] ?? '';
    $user_ip = getIPAddress();

    // Verification checks
    if ($user_password !== $user_confirm_password) {
        $error = 'Passwords do not match. Please verify and try again.';
    } else {
        // Check if username or email already exists
        $check_existing = mysqli_query($con, "SELECT user_id FROM `user` WHERE username = '$user_username' OR email = '$user_email'");
        if (mysqli_num_rows($check_existing) > 0) {
            $error = 'Username or email already registered. Please choose another or login.';
        } else {
            // Upload image
            $upload_folder = __DIR__ . "/user_images/";
            if (!is_dir($upload_folder)) {
                mkdir($upload_folder, 0777, true);
            }

            if (!empty($user_image)) {
                move_uploaded_file($user_image_tmp, $upload_folder . $user_image);
            }

            // Hash password or insert
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO `user` (username, email, password, image, user_ip, mobile_number, address) 
                             VALUES ('$user_username', '$user_email', '$hashed_password', '$user_image', '$user_ip', '$user_contact', '$user_address')";
            $sql_execute = mysqli_query($con, $insert_query);

            if ($sql_execute) {
                echo "<script>
                    alert('🎉 Registration successful! Welcome to Ganesh Store.');
                    window.location.href='user_login.php';
                </script>";
                exit();
            } else {
                $error = 'Database error: ' . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | Ganesh General Store</title>
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

<div class="auth-wrapper py-5">
    <div class="auth-card auth-card-wide">
        <div class="auth-header">
            <a href="../index.php" class="auth-logo-badge text-decoration-none">
                <img src="../images/ganesh_store_logo__1_-removebg-preview.png" alt="Logo" style="width:48px; height:48px; object-fit:contain;" onerror="this.src='../images/ganesh store logo.png'">
            </a>
            <h2 class="auth-title">Create an Account</h2>
            <p class="auth-subtitle">Join thousands enjoying fresh local groceries delivered in minutes</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 small py-2 rounded-3 mb-3" role="alert">
                <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                <div><?php echo htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" id="regForm">
            <div class="row g-3">
                <!-- Username -->
                <div class="col-md-6">
                    <label for="user_username" class="form-label-custom">Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="user_username" name="user_username" class="form-control form-control-custom border-start-0" placeholder="e.g. John Doe" required autocomplete="username">
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="user_email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" id="user_email" name="user_email" class="form-control form-control-custom border-start-0" placeholder="name@example.com" required autocomplete="email">
                    </div>
                </div>

                <!-- Contact Phone -->
                <div class="col-md-6">
                    <label for="user_contact" class="form-label-custom">Mobile Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                        <input type="tel" id="user_contact" name="user_contact" class="form-control form-control-custom border-start-0" placeholder="10-digit phone number" required autocomplete="tel">
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="col-md-6">
                    <label for="user_image" class="form-label-custom">Profile Photo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-camera"></i></span>
                        <input type="file" id="user_image" name="user_image" class="form-control form-control-custom border-start-0" accept="image/*">
                    </div>
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="user_password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="user_password" name="user_password" class="form-control form-control-custom border-start-0" placeholder="Create a password" required autocomplete="new-password">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label for="user_confirm_password" class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock-check"></i></span>
                        <input type="password" id="user_confirm_password" name="user_confirm_password" class="form-control form-control-custom border-start-0" placeholder="Confirm your password" required autocomplete="new-password">
                    </div>
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label for="user_address" class="form-label-custom">Delivery Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input type="text" id="user_address" name="user_address" class="form-control form-control-custom border-start-0" placeholder="Flat, society, landmark, Satara" required autocomplete="street-address">
                    </div>
                </div>
            </div>

            <button type="submit" name="user_register" class="btn-checkout border-0 mt-4 mb-3">
                <i class="fa-solid fa-user-plus me-1"></i> Register Account
            </button>

            <div class="text-center pt-2 border-top">
                <p class="small text-muted mb-2">Already have an account? <a href="user_login.php" class="fw-bold text-success text-decoration-none">Sign In Here</a></p>
                <a href="../index.php" class="small text-secondary text-decoration-none"><i class="fa-solid fa-house me-1"></i> Back to Homepage</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>