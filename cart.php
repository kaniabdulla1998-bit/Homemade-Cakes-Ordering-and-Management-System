<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if (!isset($_SESSION['customer_id'])) {
    $cart = $_SESSION['guest_cart'] ?? [];
} else {
    $stmt = $pdo->prepare('SELECT c.id, c.cake_name, c.price, c.image, ca.category_name, cart.size, cart.quantity, cart.cake_message FROM cart JOIN cakes c ON cart.cake_id = c.id JOIN categories ca ON c.category_id = ca.id WHERE cart.customer_id = ?');
    $stmt->execute([$_SESSION['customer_id']]);
    $cart = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $itemId = (int) $_POST['item_id'];
        $newQty = max(1, (int) $_POST['quantity']);

        if (isset($_SESSION['customer_id'])) {
            $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ? AND customer_id = ?');
            $stmt->execute([$newQty, $itemId, $_SESSION['customer_id']]);
        } else {
            $index = $_POST['index'] ?? null;
            if ($index !== null && isset($_SESSION['guest_cart'][$index])) {
                $_SESSION['guest_cart'][$index]['quantity'] = $newQty;
            }
        }
    }

    if (isset($_POST['remove_item'])) {
        $itemId = (int) $_POST['item_id'];
        if (isset($_SESSION['customer_id'])) {
            $stmt = $pdo->prepare('DELETE FROM cart WHERE id = ? AND customer_id = ?');
            $stmt->execute([$itemId, $_SESSION['customer_id']]);
        } else {
            $index = $_POST['index'] ?? null;
            if ($index !== null && isset($_SESSION['guest_cart'][$index])) {
                unset($_SESSION['guest_cart'][$index]);
            }
        }
    }

    header('Location: cart.php');
    exit();
}

$subtotal = 0;
if (isset($_SESSION['customer_id'])) {
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
} else {
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
$deliveryCharge = $subtotal > 0 ? 80 : 0;
$grandTotal = $subtotal + $deliveryCharge;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Your Cart</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (empty($cart)): ?>
                <div class="alert alert-error">Your cart is empty. Add some delicious cakes to continue.</div>
                <div class="form-actions">
                    <a href="cakes.php" class="btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div style="display:grid; grid-template-columns:1.7fr 0.8fr; gap:24px;">
                    <div>
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($_SESSION['customer_id'])): ?>
                                    <?php foreach ($cart as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <div class="cart-product">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['cake_name']); ?>">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['cake_name']); ?></strong><br>
                                                        <?php if (!empty($item['cake_message'])): ?>
                                                            <small>Message: <?php echo htmlspecialchars($item['cake_message']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                    <div class="qty-controls">
                                                        <input type="number" name="quantity" min="1" value="<?php echo $item['quantity']; ?>" style="width:70px;">
                                                        <button type="submit" name="update_quantity" class="qty-btn">✓</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-small btn-secondary">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($cart as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <div class="cart-product">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['cake_name']); ?>">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['cake_name']); ?></strong><br>
                                                        <small>Message: <?php echo htmlspecialchars($item['cake_message']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                                    <div class="qty-controls">
                                                        <input type="number" name="quantity" min="1" value="<?php echo $item['quantity']; ?>" style="width:70px;">
                                                        <button type="submit" name="update_quantity" class="qty-btn">✓</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-small btn-secondary">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="summary-box">
                        <h3>Order Summary</h3>
                        <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="summary-row"><span>Delivery Charge</span><span>₹<?php echo number_format($deliveryCharge, 2); ?></span></div>
                        <div class="summary-row summary-total"><span>Grand Total</span><span>₹<?php echo number_format($grandTotal, 2); ?></span></div>
                        <div class="form-actions" style="margin-top:20px; flex-direction:column;">
                            <a href="cakes.php" class="btn btn-secondary">Continue Shopping</a>
                            <a href="checkout.php" class="btn">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
