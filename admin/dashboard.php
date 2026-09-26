<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = getPDO(); requireAdminLogin();
$stats = [
 'cakes' => $pdo->query('SELECT COUNT(*) FROM cakes')->fetchColumn(),
 'customers' => $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
 'orders' => $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
 'pending' => $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status='Pending'")->fetchColumn(),
 'completed' => $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status='Delivered'")->fetchColumn(),
 'reviews' => $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn()
];
$recent = $pdo->query('SELECT o.id,o.total_amount,o.order_status,o.created_at,c.name FROM orders o JOIN customers c ON c.id=o.customer_id ORDER BY o.created_at DESC LIMIT 8')->fetchAll();
?><!doctype html><html><head><title>Admin Dashboard</title><link rel="stylesheet" href="../css/style.css"></head><body><?php include __DIR__.'/partials/sidebar.php'; ?><main class="section"><div class="container"><h1>Admin Dashboard</h1><div class="dashboard-cards"><?php foreach ($stats as $label=>$value): ?><div class="stat-card"><span><?php echo ucfirst($label); ?></span><h3><?php echo (int)$value; ?></h3></div><?php endforeach; ?></div><h2>Recent Orders</h2><table class="admin-table"><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr><?php foreach($recent as $row): ?><tr><td>#<?php echo $row['id']; ?></td><td><?php echo htmlspecialchars($row['name']); ?></td><td>₹<?php echo number_format($row['total_amount'],2); ?></td><td><?php echo htmlspecialchars($row['order_status']); ?></td><td><?php echo htmlspecialchars($row['created_at']); ?></td></tr><?php endforeach; ?></table></div></main></body></html>
