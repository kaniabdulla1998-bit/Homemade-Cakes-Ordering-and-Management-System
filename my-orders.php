<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();
requireCustomerLogin();

$orderStmt = $pdo->prepare('SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC');
$orderStmt->execute([$_SESSION['customer_id']]);
$orders = $orderStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>My Orders</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (empty($orders)): ?>
                <div class="alert alert-error">You have not placed any orders yet.</div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Cake(s)</th>
                            <th>Amount</th>
                            <th>Delivery Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>HC-<?php echo $order['id']; ?></td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php
                                    $items = $pdo->prepare('SELECT cake_name, quantity FROM order_items WHERE order_id = ?');
                                    $items->execute([$order['id']]);
                                    $itemRows = $items->fetchAll();
                                    $names = [];
                                    foreach ($itemRows as $row) {
                                        $names[] = $row['cake_name'] . ' x ' . $row['quantity'];
                                    }
                                    echo htmlspecialchars(implode(', ', $names));
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['delivery_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><a href="order-confirmation.php?order_id=<?php echo $order['id']; ?>" class="btn btn-small">View Order</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
