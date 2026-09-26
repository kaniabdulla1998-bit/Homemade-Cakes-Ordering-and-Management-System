<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$pdo = getPDO();

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: dashboard.php');
        exit();
    }

    $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid admin email or password.'];
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Homemade Cake</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="page-banner">
        <div class="container">
            <h1>Admin Login</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <div class="form-wrap" style="max-width:500px;">
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <form method="POST" data-validate="true">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>
</html>
