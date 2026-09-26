<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if (!isCustomerLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Build customer data for checkout form.
$customer = $pdo->prepare('SELECT name, email, mobile FROM customers WHERE id = ?');
$customer->execute([$_SESSION['customer_id']]);
$customerData = $customer->fetch();

$cartStmt = $pdo->prepare('SELECT c.id AS cart_id, c.size, c.quantity, c.cake_message, k.id, k.cake_name, k.price, k.image FROM cart c JOIN cakes k ON c.cake_id = k.id WHERE c.customer_id = ?');
$cartStmt->execute([$_SESSION['customer_id']]);
$cartItems = $cartStmt->fetchAll();

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryCharge = $subtotal > 0 ? 80 : 0;
$grandTotal = $subtotal + $deliveryCharge;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $deliveryDate = trim($_POST['delivery_date'] ?? '');
    $deliveryTime = trim($_POST['delivery_time'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $specialInstructions = trim($_POST['special_instructions'] ?? '');

    if (empty($cartItems)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Your cart is empty.'];
        header('Location: cart.php');
        exit();
    }

    if (empty($name) || empty($mobile) || empty($email) || empty($address) || empty($city) || empty($pincode) || empty($deliveryDate) || empty($deliveryTime) || empty($paymentMethod)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Please complete all required checkout fields.'];
        header('Location: checkout.php');
        exit();
    }

    $pdo->beginTransaction();

    $insertOrder = $pdo->prepare('INSERT INTO orders (customer_id, total_amount, delivery_charge, delivery_address, city, pincode, delivery_date, delivery_time, payment_method, payment_status, order_status, special_instructions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $insertOrder->execute([
        $_SESSION['customer_id'],
        $grandTotal,
        $deliveryCharge,
        $address,
        $city,
        $pincode,
        $deliveryDate,
        $deliveryTime,
        $paymentMethod,
        'Demo Payment Successful',
        'Pending',
        $specialInstructions,
    ]);

    $orderId = $pdo->lastInsertId();

    foreach ($cartItems as $item) {
        $orderItem = $pdo->prepare('INSERT INTO order_items (order_id, cake_id, cake_name, size, quantity, price, cake_message) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $orderItem->execute([
            $orderId,
            $item['id'],
            $item['cake_name'],
            $item['size'],
            $item['quantity'],
            $item['price'],
            $item['cake_message'],
        ]);
    }

    $clearCart = $pdo->prepare('DELETE FROM cart WHERE customer_id = ?');
    $clearCart->execute([$_SESSION['customer_id']]);

    $pdo->commit();

    $_SESSION['last_order_id'] = $orderId;
    header('Location: order-confirmation.php?order_id=' . $orderId);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Checkout</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <div class="alert alert-error">Your cart is empty. Add cakes before checkout.</div>
                <a href="cakes.php" class="btn">Explore Cakes</a>
            <?php else: ?>
                <form method="POST" data-validate="true">
                    <div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:24px;">
                        <div class="form-wrap" style="margin:0;">
                            <h3>Customer Details</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($customerData['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input type="text" name="mobile" value="<?php echo htmlspecialchars($customerData['mobile']); ?>" required>
                                </div>
                                <div class="form-group form-full">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($customerData['email']); ?>" required>
                                </div>
                            </div>

                            <h3 style="margin-top:24px;">Delivery Details</h3>
                            <div class="form-grid">
                                <div class="form-group form-full">
                                    <label>Address</label>
                                    <textarea name="address" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="text" name="pincode" required>
                                </div>
                                <div class="form-group">
                                    <label>Delivery Date</label>
                                    <input type="date" name="delivery_date" required>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Delivery Time</label>
                                    <input type="time" name="delivery_time" required>
                                </div>
                                <div class="form-group form-full">
                                    <label>Special Instructions</label>
                                    <textarea name="special_instructions"></textarea>
                                </div>
                            </div>

                            <h3 style="margin-top:24px;">Payment</h3>
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" required>
                                    <option value="">Select Payment</option>
                                    <option value="Cash on Delivery">Cash on Delivery</option>
                                    <option value="UPI Demo">UPI Demo</option>
                                    <option value="Card Demo">Card Demo</option>
                                </select>
                            </div>
                        </div>

                        <aside class="summary-box">
                            <h3>Order Summary</h3>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="summary-row">
                                    <span><?php echo htmlspecialchars($item['cake_name']); ?> x <?php echo $item['quantity']; ?></span>
                                    <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <div class="summary-row"><span>Delivery Charge</span><span>₹<?php echo number_format($deliveryCharge, 2); ?></span></div>
                            <div class="summary-row summary-total"><span>Grand Total</span><span>₹<?php echo number_format($grandTotal, 2); ?></span></div>

                            <div class="form-actions" style="margin-top:20px; flex-direction:column;">
                                <button type="submit" class="btn">Place Order</button>
                                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                            </div>
                        </aside>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
