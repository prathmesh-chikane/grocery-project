<?php
require_once __DIR__ . '/includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Fetch admin details
$stmt = mysqli_prepare($con, "SELECT * FROM `admin` WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $admin_username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username'] ?? '');
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if ($new_username === '') {
        $error = "Username cannot be empty.";
    } else {
        if (!empty($new_password)) {
            if (password_verify($current_password, $admin['password']) || $current_password === $admin['password']) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $hashed = $admin['password'];
        }

        if ($error === '') {
            $update = "UPDATE `admin` SET username=?, password=? WHERE admin_id=?";
            $stmt = mysqli_prepare($con, $update);
            mysqli_stmt_bind_param($stmt, 'ssi', $new_username, $hashed, $admin['admin_id']);
            $ok = mysqli_stmt_execute($stmt);

            if ($ok) {
                $_SESSION['admin_username'] = $new_username;
                $success = "Admin profile updated successfully!";
                // Refresh local admin record
                $admin['username'] = $new_username;
                $admin['password'] = $hashed;
            } else {
                $error = "Error updating profile. Please try again.";
            }
        }
    }
}

$page_title = "Admin Profile Settings | Ganesh Store";
$page_heading = "Admin Profile & Security";
include('includes/admin_header.php');
include('includes/admin_sidebar.php');
?>

<div class="admin-main">
    <?php include('includes/admin_topbar.php'); ?>

    <div class="admin-content-body">
        <div class="admin-form-card" style="max-width: 650px;">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="admin-avatar" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1" style="font-family: var(--font-heading);">Profile Settings</h3>
                    <p class="text-muted small m-0">Update administrative credentials and store access password</p>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 small py-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <div><?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 small py-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation fs-5"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="username">Admin Username <span class="text-danger">*</span></label>
                    <input type="text" class="admin-form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" required>
                    <small class="text-muted">This username is used to log in to the management dashboard.</small>
                </div>

                <div class="p-3 bg-light rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark mb-2 small"><i class="fa-solid fa-key me-1 text-warning"></i> Change Password (Optional)</h6>
                    <p class="text-muted small mb-3">Leave blank if you do not wish to change your current password.</p>

                    <div class="mb-3">
                        <label class="admin-form-label" for="current_password">Current Password</label>
                        <input type="password" class="admin-form-control" id="current_password" name="current_password" placeholder="Enter current password to verify">
                    </div>

                    <div>
                        <label class="admin-form-label" for="new_password">New Password</label>
                        <input type="password" class="admin-form-control" id="new_password" name="new_password" placeholder="Enter new strong password">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <button type="submit" name="update_profile" class="btn-admin-submit">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Profile Changes</span>
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 small">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php include('includes/admin_footer.php'); ?>
