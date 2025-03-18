<?php
session_start();

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);

        $cartSubtotal = 0;
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $cartSubtotal += $_SESSION['cart'][$id] * $quantity;
        }

        $response = [
            'success' => true,
            'cart_subtotal' => $cartSubtotal,
            'cart_total' => $cartSubtotal + 1000 // Add delivery charge
        ];
    } else {
        $response['message'] = 'Product not found in cart.';
    }
}

echo json_encode($response);
