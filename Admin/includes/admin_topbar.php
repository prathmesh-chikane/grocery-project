<!-- Admin Top Header Bar -->
<header class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggleBtn" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="admin-topbar-title">
            <h2><?php echo $page_heading ?? 'Store Management Portal'; ?></h2>
        </div>
    </div>

    <div class="admin-topbar-actions">
        <a href="../index.php" target="_blank" class="btn-visit-store" title="Open customer website in new tab">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span>Visit Store</span>
        </a>
        <div class="dropdown">
            <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user text-success"></i>
                <span class="fw-semibold small"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="admin_profile.php"><i class="fa-solid fa-user-gear me-2"></i> Profile Settings</a></li>
                <li><a class="dropdown-item" href="stock_alert.php"><i class="fa-solid fa-bell me-2"></i> Stock Alerts</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="admin_logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>
