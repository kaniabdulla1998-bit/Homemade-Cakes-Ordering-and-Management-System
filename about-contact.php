<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name !== '' && $email !== '' && $message !== '') {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Your message has been sent successfully. We will contact you soon.'];
        header('Location: about-contact.php');
        exit();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Please fill in all contact form fields.'];
        header('Location: about-contact.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About & Contact | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>About Us & Contact</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
                <div class="form-wrap" style="margin:0;">
                    <h3>About Homemade Cake</h3>
                    <p>Homemade Cake is a small bakery business passionate about creating delicious, handcrafted cakes that make celebrations memorable. We use fresh ingredients, homemade recipes, and custom designs to make every cake special.</p>
                    <p><strong>Mission:</strong> To deliver baked joy with premium ingredients and heartwarming service.</p>
                    <p><strong>Why Homemade Cakes?</strong> Because they taste pure, fresh, and full of love—unlike mass-produced desserts.</p>
                    <ul>
                        <li>Fresh ingredients</li>
                        <li>Custom cake designs</li>
                        <li>Affordable celebration cakes</li>
                        <li>Reliable delivery</li>
                    </ul>
                </div>

                <div class="form-wrap" style="margin:0;">
                    <h3>Contact Information</h3>
                    <p><strong>Phone:</strong> +91 98765 43210</p>
                    <p><strong>Email:</strong> hello@homemadecake.in</p>
                    <p><strong>Address:</strong> 22 Bakery Lane, Coimbatore, Tamil Nadu</p>
                    <p><strong>Business Hours:</strong> Mon - Sat: 9:00 AM - 9:00 PM</p>
                    <p><strong>Sunday:</strong> 9:00 AM - 2:00 PM</p>

                    <form method="POST" data-validate="true" style="margin-top:20px;">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
