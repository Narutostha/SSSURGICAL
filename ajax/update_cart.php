<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
        exit;
    }

    // Update the session cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = $quantity;

        // Fetch the updated product details
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            // Calculate the new subtotal for the product
            $newSubtotal = $product['price'] * $quantity;

            // Calculate the updated cart total
            $cartSubtotal = 0;
            foreach ($_SESSION['cart'] as $id => $qty) {
                $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();
                if ($item) {
                    $cartSubtotal += $item['price'] * $qty;
                }
            }

            $deliveryCharge = ($cartSubtotal > 0) ? 1000 : 0;
            $cartTotal = $cartSubtotal + $deliveryCharge;

            echo json_encode([
                'success' => true,
                'new_quantity' => $quantity,
                'new_subtotal' => $newSubtotal,
                'cart_subtotal' => $cartSubtotal,
                'cart_total' => $cartTotal,
            ]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit;
