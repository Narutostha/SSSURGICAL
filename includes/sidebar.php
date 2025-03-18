<nav class="sidebar">
    <div class="sidebar-header">
        <div class="mb-3">
            <i class="fas fa-user-circle fa-3x"></i>
        </div>
        <h5 class="mb-0"><?php echo htmlspecialchars($customer['name']); ?></h5>
        <small>Customer Dashboard</small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-home"></i> Overview
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'order.php' ? 'active' : ''; ?>" href="order.php">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'track-order.php' ? 'active' : ''; ?>" href="track-order.php">
                <i class="fas fa-truck"></i> Track Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                <i class="fas fa-store"></i> Shop Now
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                <i class="fas fa-user"></i> Profile
            </a>
        </li>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>