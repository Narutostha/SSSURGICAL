<?php
session_start();

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $newQuantity = (int)$_POST['quantity'];

    if ($newQuantity > 0) {
        // Initialize database connection
        require_once '../config/database.php';

        try {
            // Fetch the product details
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Update session cart
                $_SESSION['cart'][$productId] = $newQuantity;

                $cartSubtotal = 0;
                foreach ($_SESSION['cart'] as $id => $quantity) {
                    // Recalculate subtotal for each product in the cart
                    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $productData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($productData) {
                        $cartSubtotal += $productData['price'] * $quantity;
                    }
                }

                $response = [
                    'success' => true,
                    'new_quantity' => $newQuantity,
                    'new_subtotal' => $product['price'] * $newQuantity,
                    'cart_subtotal' => $cartSubtotal,
                    'cart_total' => $cartSubtotal + 1000 // Add delivery charge
                ];
            } else {
                $response['message'] = 'Product not found.';
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $response['message'] = 'An error occurred while updating the cart.';
        }
    } else {
        $response['message'] = 'Invalid quantity.';
    }
}

echo json_encode($response);
