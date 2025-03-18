<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
if ($current_page === 'index') {
    $current_page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Laptop Station'; ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }

        header {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px 5%;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-wrap {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .logo span {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        .search {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: #333;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 5px;
            border-radius: 4px;
            transition: 0.3s;
        }

        nav a img {
            width: 24px;
            height: 24px;
        }

        nav a:hover, 
        nav a.active {
            background: #e6efff;
            color: #2563eb;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart {
            position: relative;
            padding: 5px;
        }

        .cart img {
            width: 24px;
            height: 24px;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc2626;
            color: #fff;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 15px;
            text-align: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn img {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-outline {
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .btn-outline:hover {
            background: #e6efff;
        }

        @media (max-width: 768px) {
            .header-wrap {
                flex-wrap: wrap;
            }
            
            .search {
                order: 3;
                max-width: 100%;
            }

            nav {
                order: 4;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
    <div class="header-wrap">
    <a href="index" class="logo">
        
        <span>SS Surgical</span>
    </a>


    <!-- Navigation -->
    <nav>
        <a href="index" <?php echo ($current_page === 'home') ? 'class="active"' : ''; ?>>
        <img src="assets/icons/home-button.svg" alt="Home" width="100">
            Home
        </a>
        <a href="products" <?php echo ($current_page === 'product') ? 'class="active"' : ''; ?>>
        <img src="assets/icons/sell-product.svg" alt="Product" width="100">
            Products
        </a>
        <a href="product_category" <?php echo ($current_page === 'category') ? 'class="active"' : ''; ?>>
        <img src="assets/icons/category.svg" alt="Home" width="100">
            Categories
        </a>
        <a href="contact_us" <?php echo ($current_page === 'support') ? 'class="active"' : ''; ?>>
        <img src="assets/icons/images.png" alt="Home" width="100">
            Support
        </a>
    </nav>

    <!-- Cart -->
    <div class="user-actions">
        <a href="cart" class="cart">
        <img src="assets/icons/add-to-cart.svg" alt="Add to cart" width="100">
            <?php if (isset($_SESSION['cart_count'])): ?>
                <span class="cart-count"><?php echo $_SESSION['cart_count']; ?></span>
            <?php endif; ?>
        </a>

        <!-- Login/Logout -->
        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="dashboard" class="btn btn-outline">
            <img src="assets/icons/profile.svg" alt="Dashboard" width="100">
                Dashboard
            </a>
            <a href="logout" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <path d="M15 12H3m0 0l3-3m-3 3l3 3m12-9v12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </a>
        <?php else: ?>
            <a href="login" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <path d="M15 12H3m0 0l3-3m-3 3l3 3m12-9v12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Login
            </a>
        <?php endif; ?>
    </div>
</div>


        </div>
    </header>
</body>
</html>