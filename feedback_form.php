<?php
include('includes/connection.php');
include('functions/common_function.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$feedback_status = '';
$feedback_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['username'] ?? '')));
    $message = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['message'] ?? '')));

    if (!empty($username) && !empty($message)) {
        $stmt = $con->prepare("INSERT INTO feedback (username, message) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $message);
            if ($stmt->execute()) {
                $feedback_status = "Thank you! Your feedback has been submitted successfully.";
                $feedback_type = "success";
            } else {
                $feedback_status = "Error saving feedback: " . $stmt->error;
                $feedback_type = "danger";
            }
            $stmt->close();
        } else {
            $feedback_status = "Database error: " . $con->error;
            $feedback_type = "danger";
        }
    } else {
        $feedback_status = "Please provide both your name and feedback.";
        $feedback_type = "warning";
    }
}

$page_title = "Customer Feedback | Ganesh General Store";
include('includes/header.php');
?>

<main class="container my-5" style="max-width: 650px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Feedback</li>
        </ol>
    </nav>

    <div class="checkout-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="empty-state-icon mb-3" style="width: 60px; height: 60px; font-size: 1.6rem; color: #f59e0b; background: #fef3c7;">
                <i class="fa-solid fa-star"></i>
            </div>
            <h2 class="fw-bold mb-1" style="font-family: var(--font-heading);">How Was Your Experience?</h2>
            <p class="text-muted small">We continuously improve our service based on your reviews and suggestions.</p>
        </div>

        <?php if ($feedback_status): ?>
            <div class="alert alert-<?php echo $feedback_type; ?> d-flex align-items-center gap-2 small py-2 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <div><?php echo $feedback_status; ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label-custom">Your Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control-custom" id="username" name="username" placeholder="e.g. Priyanshu / Regular Customer" required value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="message" class="form-label-custom">Your Feedback & Comments <span class="text-danger">*</span></label>
                <textarea class="form-control-custom" id="message" name="message" rows="5" placeholder="Share your experience regarding delivery speed, product freshness, or suggestions..." required></textarea>
            </div>

            <button type="submit" class="btn-checkout border-0">
                <i class="fa-solid fa-paper-plane me-1"></i> Submit Feedback
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="index.php" class="text-muted small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Homepage
            </a>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
