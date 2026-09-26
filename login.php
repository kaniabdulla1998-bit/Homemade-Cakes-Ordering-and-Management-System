<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if (isCustomerLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE email = ?');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['name'];

        if (!empty($_SESSION['guest_cart'])) {
            $merge = $pdo->prepare('SELECT customer_id, cake_id, size, quantity, cake_message FROM cart WHERE customer_id = ?');
            $merge->execute([$customer['id']]);
            foreach ($_SESSION['guest_cart'] as $item) {
                $exists = $pdo->prepare('SELECT id, quantity FROM cart WHERE customer_id = ? AND cake_id = ? AND size = ? AND cake_message = ?');
                $exists->execute([$customer['id'], $item['cake_id'], $item['size'], $item['cake_message']]);
                $dbItem = $exists->fetch();
                if ($dbItem) {
                    $upd = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE id = ?');
                    $upd->execute([$item['quantity'], $dbItem['id']]);
                } else {
                    $ins = $pdo->prepare('INSERT INTO cart (customer_id, cake_id, size, quantity, cake_message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
                    $ins->execute([$customer['id'], $item['cake_id'], $item['size'], $item['quantity'], $item['cake_message']]);
                }
            }
            unset($_SESSION['guest_cart']);
        }

        header('Location: index.php');
        exit();
    }

    $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid email or password. Please try again.'];
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Customer Login</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="form-wrap">
                <form method="POST" data-validate="true">
                    <div class="form-grid">
                        <div class="form-group form-full">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group form-full">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Login</button>
                        <a href="register.php" class="btn btn-secondary">Create New Account</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
