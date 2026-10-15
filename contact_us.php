<?php
include('./includes/connection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$msg_feedback = '';
$msg_type = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username'])) {
        echo "<script>
            alert('Please sign in first to submit your inquiry.');
            window.location.href = './user/user_login.php';
        </script>";
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? 0;
    $name = mysqli_real_escape_string($con, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($con, trim($_POST['email'] ?? ''));
    $message = mysqli_real_escape_string($con, trim($_POST['message'] ?? ''));

    if (!empty($name) && !empty($email) && !empty($message)) {
        $query = "INSERT INTO contact_us (user_id, name, email, message) VALUES ('$user_id', '$name', '$email', '$message')";
        if (mysqli_query($con, $query)) {
            $msg_feedback = "Thank you for contacting us! Our support team will get in touch shortly.";
            $msg_type = "success";
        } else {
            $msg_feedback = "Error submitting message: " . mysqli_error($con);
            $msg_type = "danger";
        }
    } else {
        $msg_feedback = "Please fill out all required fields.";
        $msg_type = "warning";
    }
}

$page_title = "Contact Us | Ganesh General Store";
include('includes/header.php');
?>

<main class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Contact Information Side -->
        <div class="col-lg-5">
            <div class="checkout-card h-100">
                <span class="text-success fw-bold text-uppercase small">Get in Touch</span>
                <h2 class="fw-bold mb-3" style="font-family: var(--font-heading);">We Are Here to Assist You</h2>
                <p class="text-secondary small mb-4">Have questions about an order, product availability, or local deliveries? Drop us a note or call our store directly.</p>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <strong class="d-block text-dark">Store Location</strong>
                        <span class="text-muted small">Satara City, Maharashtra, India - 415012</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-circle"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <strong class="d-block text-dark">Phone Helpline</strong>
                        <span class="text-muted small">+91 97654 73412</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-circle"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <strong class="d-block text-dark">Customer Support Email</strong>
                        <span class="text-muted small">ganeshgrocerystore123@gmail.com</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-circle"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <strong class="d-block text-dark">Daily Operating Hours</strong>
                        <span class="text-muted small">Monday - Sunday: 9:00 AM to 9:00 PM</span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-2 text-success fw-bold small">
                        <i class="fa-solid fa-bolt"></i> Need Instant Answers?
                    </div>
                    <p class="text-muted small m-0 mt-1">For urgent order issues, WhatsApp us at <strong>+91 97654 73412</strong> for immediate assistance.</p>
                </div>
            </div>
        </div>

        <!-- Contact Form Side -->
        <div class="col-lg-7">
            <div class="checkout-card">
                <h4 class="fw-bold mb-3" style="font-family: var(--font-heading);">Send a Message</h4>

                <?php if ($msg_feedback): ?>
                    <div class="alert alert-<?php echo $msg_type; ?> d-flex align-items-center gap-2 small py-2 rounded-3 mb-3">
                        <i class="fa-solid fa-circle-info"></i>
                        <div><?php echo htmlspecialchars($msg_feedback); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control-custom" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control-custom" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Your Message / Inquiry <span class="text-danger">*</span></label>
                        <textarea name="message" rows="5" class="form-control-custom" placeholder="How can we assist you today? Please share details..." required></textarea>
                    </div>

                    <button type="submit" class="btn-checkout border-0">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
