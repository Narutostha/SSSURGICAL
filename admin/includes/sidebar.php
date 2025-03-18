<style>
    /* Enhanced Sidebar Styling */
    .sidebar {
        background-color: #1e1e2f;
        color: white;
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    }
    .sidebar .logo {
        font-size: 1.8rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 2rem;
        color: #f8f9fa;
        letter-spacing: 1px;
    }
    .sidebar a {
        color: #c0c0d1;
        text-decoration: none;
        font-size: 1rem;
        margin: 0.5rem 0;
        padding: 0.8rem 1rem;
        border-radius: 5px;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }
    .sidebar a:hover {
        background-color: #343a40;
        color: #f8f9fa;
    }
    .sidebar a.active {
        background-color: #0197f6;
        color: white;
    }
    .sidebar a i {
        font-size: 1.2rem;
    }
    .sidebar .logout {
        margin-top: auto;
        color: #ff6b6b;
    }
    .sidebar .logout:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="sidebar">
    <div class="logo">Admin Panel</div>
    <a href="index.php" class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="products.php" class="<?php echo ($current_page == 'products') ? 'active' : ''; ?>">
        <i class="fas fa-box"></i> Products
    </a>
    <a href="orders.php" class="<?php echo ($current_page == 'orders') ? 'active' : ''; ?>">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    <a href="categories.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
        <i class="nav-icon fas fa-tags"></i>
        <p>Categories</p>
    </a>s
    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    <a href="categories.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
        <i class="nav-icon fas fa-tags"></i>
        <p>Categories</p>
    </a>
</div>
