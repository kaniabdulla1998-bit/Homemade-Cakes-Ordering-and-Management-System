<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$pdo = getPDO();
requireAdminLogin();

$totalCakes = (int) $pdo->query('SELECT COUNT(*) FROM cakes')->fetchColumn();
totalCustomers = (int) $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalOrders = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
pendingOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
completedOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Delivered'")->fetchColumn();
totalReviews = (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
$recentOrders = $pdo->query('SELECT o.*, c.name FROM orders o JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Homemade Cake</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="section">
        <div class="container">
            <h2>Dashboard Summary</h2>
            <div class="dashboard-cards">
                <div class="stat-card"><span>Total Cakes</span><h3><?php echo $totalCakes; ?></h3></div>
                <div class="stat-card"><span>Total Customers</span><h3><?php echo $totalCustomers; ?></h3></div>
                <div class="stat-card"><span>Total Orders</span><h3><?php echo $totalOrders; ?></h3></div>
                <div class="stat-card"><span>Pending Orders</span><h3><?php echo $pendingOrders; ?></h3></div>
                <div class="stat-card"><span>Completed Orders</span><h3><?php echo $completedOrders; ?></h3></div>
                <div class="stat-card"><span>Total Reviews</span><h3><?php echo $totalReviews; ?></h3></div>
            </div>

            <h3>Recent Orders</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>HC-<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
