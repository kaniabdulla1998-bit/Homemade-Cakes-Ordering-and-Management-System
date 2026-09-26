<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$pdo = getPDO();
$customerLoggedIn = isCustomerLoggedIn();
$adminLoggedIn = isAdminLoggedIn();
?>
