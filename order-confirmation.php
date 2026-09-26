<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if (!isCustomerLoggedIn()) {
    header('Location: login.php');
    exit();
}

$orderId = $_GET['order_id'] ?? $_SESSION['last_order_id'] ?? 0;
$stmt = $pdo->prepare('SELECT o.*, c.name AS customer_name FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.id = ? AND o.customer_id = ?');
$stmt->execute([$orderId, $_SESSION['customer_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: my-orders.php');
    exit();
}

$orderItemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$orderItemsStmt->execute([$orderId]);
$orderItems = $orderItemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Order Confirmation</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <div class="form-wrap" style="max-width:900px;">
                <div class="alert alert-success">Your order has been placed successfully!</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" value="HC-<?php echo $order['id']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['customer_name']); ?>" readonly>
                    </div>
                    <div class="form-group form-full">
                        <label>Ordered Cakes</label>
                        <textarea readonly><?php foreach ($orderItems as $item) { echo $item['cake_name'] . ' - ' . $item['quantity'] . ' qty (' . $item['size'] . ')\n'; } ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Total Amount</label>
                        <input type="text" value="₹<?php echo number_format($order['total_amount'], 2); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Delivery Date</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['delivery_date']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Delivery Time</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['delivery_time']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['payment_method']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Order Status</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['order_status']); ?>" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="my-orders.php" class="btn">View My Orders</a>
                    <a href="reviews.php" class="btn btn-secondary">Write a Review</a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
