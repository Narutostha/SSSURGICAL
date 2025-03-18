<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once 'includes/header.php';

// Fetch statistics
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Calculate percentage changes (you would typically compare with previous period)
$product_change = "+12%"; // Example value
$orders_change = "+8%";
$users_change = "+5%";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --success-gradient: linear-gradient(135deg, #43E97B 0%, #38F9D7 100%);
            --warning-gradient: linear-gradient(135deg, #FAD961 0%, #F76B1C 100%);
            --info-gradient: linear-gradient(135deg, #FA709A 0%, #FEE140 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fb;
            color: #2c3e50;
            line-height: 1.6;
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card:nth-child(1)::before {
            background: var(--primary-gradient);
        }

        .stat-card:nth-child(2)::before {
            background: var(--success-gradient);
        }

        .stat-card:nth-child(3)::before {
            background: var(--warning-gradient);
        }

        .stat-card:nth-child(4)::before {
            background: var(--info-gradient);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .stat-title {
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 500;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: rgba(107, 115, 255, 0.1);
            color: #6B73FF;
        }

        .stat-card:nth-child(2) .stat-icon {
            background: rgba(67, 233, 123, 0.1);
            color: #43E97B;
        }

        .stat-card:nth-child(3) .stat-icon {
            background: rgba(250, 217, 97, 0.1);
            color: #FAD961;
        }

        .stat-card:nth-child(4) .stat-icon {
            background: rgba(250, 112, 154, 0.1);
            color: #FA709A;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            background: rgba(67, 233, 123, 0.1);
            color: #43E97B;
        }

        .stat-change.positive {
            background: rgba(67, 233, 123, 0.1);
            color: #43E97B;
        }

        .stat-change.negative {
            background: rgba(255, 82, 82, 0.1);
            color: #FF5252;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header h1 {
                font-size: 2rem;
            }

            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back! Here's what's happening with your store today.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Total Products</h2>
                    <div class="stat-icon">📦</div>
                </div>
                <div class="stat-value"><?php echo number_format($total_products); ?></div>
                <div class="stat-change positive"><?php echo $product_change; ?> from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Total Orders</h2>
                    <div class="stat-icon">🛍️</div>
                </div>
                <div class="stat-value"><?php echo number_format($total_orders); ?></div>
                <div class="stat-change positive"><?php echo $orders_change; ?> from last month</div>
            </div>

         

            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Total Users</h2>
                    <div class="stat-icon">👥</div>
                </div>
                <div class="stat-value"><?php echo number_format($total_users); ?></div>
                <div class="stat-change positive"><?php echo $users_change; ?> from last month</div>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>