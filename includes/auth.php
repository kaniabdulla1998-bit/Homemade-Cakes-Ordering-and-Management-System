<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function isCustomerLoggedIn(): bool { return !empty($_SESSION['customer_id']); }
function requireCustomerLogin(): void { if (!isCustomerLoggedIn()) { $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php'; header('Location: login.php'); exit; } }
function addToGuestCart(array $item): void { $_SESSION['guest_cart'] ??= []; $key = $item['cake_id'].'|'.$item['size'].'|'.$item['cake_message']; if (isset($_SESSION['guest_cart'][$key])) $_SESSION['guest_cart'][$key]['quantity'] += $item['quantity']; else $_SESSION['guest_cart'][$key] = $item; }
?>
