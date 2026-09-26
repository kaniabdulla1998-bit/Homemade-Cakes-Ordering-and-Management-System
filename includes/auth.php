<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isCustomerLoggedIn() {
    return !empty($_SESSION['customer_id']);
}

function requireCustomerLogin() {
    if (!isCustomerLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function getCartSession() {
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }
    return $_SESSION['guest_cart'];
}

function addToGuestCart($cakeId, $cakeName, $size, $quantity, $price, $cakeMessage = '') {
    $cart = getCartSession();
    $key = $cakeId . '|' . $size . '|' . $cakeMessage;

    if (isset($cart[$key])) {
        $cart[$key]['quantity'] += $quantity;
    } else {
        $cart[$key] = [
            'cake_id' => $cakeId,
            'cake_name' => $cakeName,
            'size' => $size,
            'quantity' => $quantity,
            'price' => $price,
            'cake_message' => $cakeMessage,
        ];
    }

    $_SESSION['guest_cart'] = $cart;
}

function mergeGuestCartToUser($pdo, $customerId) {
    if (!isset($_SESSION['guest_cart']) || empty($_SESSION['guest_cart'])) {
        return;
    }

    foreach ($_SESSION['guest_cart'] as $item) {
        $check = $pdo->prepare('SELECT id, quantity FROM cart WHERE customer_id = ? AND cake_id = ? AND size = ? AND cake_message = ?');
        $check->execute([$customerId, $item['cake_id'], $item['size'], $item['cake_message']]);
        $existing = $check->fetch();

        if ($existing) {
            $update = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE id = ?');
            $update->execute([$item['quantity'], $existing['id']]);
        } else {
            $insert = $pdo->prepare('INSERT INTO cart (customer_id, cake_id, size, quantity, cake_message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $insert->execute([$customerId, $item['cake_id'], $item['size'], $item['quantity'], $item['cake_message']]);
        }
    }

    unset($_SESSION['guest_cart']);
}
?>
