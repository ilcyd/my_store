<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="admin-dashboard.php"><img src="./dist/assets/compiled/svg/logo.svg" alt="Logo"></a>
                </div>
                <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    </div>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Admin Menu</li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>">
                    <a href="admin-dashboard.php" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-pos.php' ? 'active' : ''; ?>">
                    <a href="admin-pos.php" class='sidebar-link'>
                        <i class="bi bi-cart-check-fill"></i>
                        <span>POS System</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-orders.php' ? 'active' : ''; ?>">
                    <a href="admin-orders.php" class='sidebar-link'>
                        <i class="bi bi-receipt"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-returns.php' ? 'active' : ''; ?>">
                    <a href="admin-returns.php" class='sidebar-link'>
                        <i class="bi bi-arrow-return-left"></i>
                        <span>Returns</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-products.php' ? 'active' : ''; ?>">
                    <a href="admin-products.php" class='sidebar-link'>
                        <i class="bi bi-box"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-categories.php' ? 'active' : ''; ?>">
                    <a href="admin-categories.php" class='sidebar-link'>
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-batches.php' ? 'active' : ''; ?>">
                    <a href="admin-batches.php" class='sidebar-link'>
                        <i class="bi bi-calendar-event"></i>
                        <span>Batches & Expiry</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-stock-alerts.php' ? 'active' : ''; ?>">
                    <a href="admin-stock-alerts.php" class='sidebar-link'>
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Stock Alerts</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-expenses.php' ? 'active' : ''; ?>">
                    <a href="admin-expenses.php" class='sidebar-link'>
                        <i class="bi bi-cash-coin"></i>
                        <span>Expenses</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-customers.php' ? 'active' : ''; ?>">
                    <a href="admin-customers.php" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="logout.php" class='sidebar-link'>
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
