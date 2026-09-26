<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
$pdo = getPDO();
$customerLoggedIn = isCustomerLoggedIn();
$cartCount = 0;
if ($customerLoggedIn) {
    $count = $pdo->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart WHERE customer_id = ?');
    $count->execute([$_SESSION['customer_id']]);
    $cartCount = (int)$count->fetchColumn();
} else { foreach ($_SESSION['guest_cart'] ?? [] as $item) $cartCount += (int)$item['quantity']; }
?><!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../css/style.css' : 'css/style.css'; ?>"></head><body>
<nav class="navbar"><div class="container nav-inner"><a class="brand" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">🍰 Homemade Cake</a><div class="nav-links">
<a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">Home</a><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../cakes.php' : 'cakes.php'; ?>">Cakes</a><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../about-contact.php' : 'about-contact.php'; ?>">About & Contact</a><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../cart.php' : 'cart.php'; ?>">Cart (<?php echo $cartCount; ?>)</a><?php if ($customerLoggedIn): ?><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../my-orders.php' : 'my-orders.php'; ?>">My Orders</a><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../logout.php' : 'logout.php'; ?>">Logout</a><?php else: ?><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../login.php' : 'login.php'; ?>">Login</a><a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../register.php' : 'register.php'; ?>">Register</a><?php endif; ?></div></div></nav>
