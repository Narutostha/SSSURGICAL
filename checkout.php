<?php
session_start();
require_once 'config/database.php';

// Redirect to login if customer is not logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login?redirect=checkout');
    exit;
}

// Initialize variables
$error = '';
$cart_items = [];
$subtotal = 0;
$total = 0;

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $shipping_address = trim($_POST['shipping_address']);
    $phone = trim($_POST['phone']);
    $payment_method = trim($_POST['payment_method']);

    // Validate form inputs
    if (empty($shipping_address) || empty($phone) || empty($payment_method)) {
        $error = 'All fields are required.';
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $error = 'Please enter a valid 10-digit phone number.';
    } else {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Calculate subtotal and prepare cart items
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();

                if (!$product || $product['stock'] < $quantity) {
                    throw new Exception("Insufficient stock for product ID: $product_id");
                }

                $item_subtotal = $product['price'] * $quantity;
                $subtotal += $item_subtotal;
                $cart_items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $product['price'],
                ];
            }

            // Calculate total amount
            $total = $subtotal ;

            // Insert order
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, total_amount, payment_method, 
                                    payment_status, delivery_status, 
                                    shipping_address, phone, created_at)
                VALUES (?, ?, ?, 'pending', 'processing', ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['customer_id'],
                $total,
                $payment_method,
                $shipping_address,
                $phone,
            ]);

            $order_id = $pdo->lastInsertId();

            // Insert order items and update stock
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($cart_items as $item) {
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                $update_stock->execute([$item['quantity'], $item['product_id']]);
            }

            // Add initial tracking status
            $track_stmt = $pdo->prepare("
                INSERT INTO order_tracking (order_id, status, description)
                VALUES (?, 'Order Placed', 'Your order has been received and is being processed.')
            ");
            $track_stmt->execute([$order_id]);

            // Commit transaction
            $pdo->commit();

            // Clear cart and redirect
            unset($_SESSION['cart']);
            header("Location: confirmation?order_id=" . $order_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to process order: " . $e->getMessage();
            error_log($e->getMessage());
        }
    }
}

// Fetch cart items for display
try {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $item_subtotal = $product['price'] * $quantity;
            $subtotal += $item_subtotal;
            $cart_items[] = [
                'name' => $product['name'],
                'quantity' => $quantity,
                'subtotal' => $item_subtotal,
            ];
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Failed to fetch cart items.";
}

$total = $subtotal ;

$page_title = "Checkout";
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        :root {
            --primary: #2563eb;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f59e0b;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .checkout-title {
            font-size: 2rem;
            color: var(--gray-800);
            margin-bottom: 10px;
        }

        .checkout-subtitle {
            color: var(--gray-600);
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
        }

        .checkout-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background: #f8fafc;
            padding: 20px 25px;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-header h3 {
            color: var(--gray-800);
            font-size: 1.25rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: var(--gray-800);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 10px;
        }

        .payment-method {
            display: none;
        }

        .payment-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-label img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .payment-method:checked + .payment-label {
            border-color: var(--primary);
            background: #f0f7ff;
        }

        .order-summary-items {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background: var(--gray-100);
            padding: 5px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            color: var(--gray-800);
            margin-bottom: 5px;
        }

        .item-quantity {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 600;
            color: var(--primary);
        }

        .summary-totals {
            padding-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--gray-600);
        }

        .summary-total {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-800);
            padding-top: 15px;
            margin-top: 15px;
            border-top: 2px solid var(--gray-200);
        }

        .place-order-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .place-order-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .benefits-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0fdf4;
            border-radius: 8px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #166534;
        }

        .benefit-icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1 class="checkout-title">Secure Checkout</h1>
            <p class="checkout-subtitle">Complete your order with confidence</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="checkout-grid">
            <!-- Billing Details -->
            <div class="checkout-card">
                <div class="card-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Billing Details
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" id="checkout-form">
                        <div class="form-group">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <div class="payment-methods">
                                <input type="radio" name="payment_method" value="cod" id="cod" class="payment-method" required>
                                <label for="cod" class="payment-label">
                                    <img src="assets/icons/cod.png" alt="COD">
                                    <span>Cash on Delivery</span>
                                </label>

                                <input type="radio" name="payment_method" value="esewa" id="esewa" class="payment-method">
                                <label for="esewa" class="payment-label">
                                    <img src="assets/icons/esewa.png" alt="eSewa">
                                    <span>eSewa</span>
                                </label>

                                <input type="radio" name="payment_method" value="khalti" id="khalti" class="payment-method">
                                <label for="khalti" class="payment-label">
                                    <img src="assets/icons/khalti.png" alt="Khalti">
                                    <span>Khalti</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="place-order-btn">Place Order</button>

                        <div class="benefits-section">
                            <div class="benefit-item">
                                <svg class="benefit-icon" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span>Secure Payment</span>
                            </div>
                            <div class="benefit-item">
                                <svg class="benefit-icon" viewBox="0 0 24 24">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                <span>100% Purchase Protection</span>
                            </div>
                            <div class="benefit-item">
                                <svg class="benefit-icon" viewBox="0 0 24 24">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                </svg>
                                <span>Quality Assured Products</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="checkout-card">
                <div class="card-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Order Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="order-summary-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                
                    

                                <div class="item-details">
                                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="item-price">
                                    Rs. <?php echo number_format($item['subtotal'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span>Rs. <?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        // Add form validation
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone"]');
            if (!phone.value.match(/^\d{10}$/)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number');
                phone.focus();
            }
        });
    </script>
</body>
</html>



