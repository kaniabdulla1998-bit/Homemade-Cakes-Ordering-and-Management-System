<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['customer_id'])) {
    header('Location: index.php');
    exit();
}

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    if ($fullName === '' || $email === '' || $mobile === '' || $password === '' || $confirmPassword === '') {
        $errors[] = 'All fields are required.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password and confirm password do not match.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM customers WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'This email is already registered.';
        }
    }

    if (empty($errors)) {
        $insert = $pdo->prepare('INSERT INTO customers (name, email, mobile, password, created_at) VALUES (?, ?, ?, ?, NOW())');
        $insert->execute([$fullName, $email, $mobile, password_hash($password, PASSWORD_DEFAULT)]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Registration successful! Please login.'];
        header('Location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Create Your Account</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (!empty($errors ?? [])): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-wrap">
                <form method="POST" data-validate="true">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="form-group form-full">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Register</button>
                        <a href="login.php" class="btn btn-secondary">Already have an account?</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
