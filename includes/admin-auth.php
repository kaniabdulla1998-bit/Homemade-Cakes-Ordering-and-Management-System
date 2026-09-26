<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function isAdminLoggedIn(): bool { return !empty($_SESSION['admin_id']); }
function requireAdminLogin(): void { if (!isAdminLoggedIn()) { header('Location: login.php'); exit; } }
?>
