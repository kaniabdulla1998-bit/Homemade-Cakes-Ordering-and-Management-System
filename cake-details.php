<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    requireCustomerLogin();

    $cakeId = (int) $_POST['cake_id'];
    $size = trim($_POST['size']);
    $quantity = max(1, (int) $_POST['quantity']);
    $cakeMessage = trim($_POST['cake_message'] ?? '');

    $stmt = $pdo->prepare('SELECT cake_name, price FROM cakes WHERE id = ? AND availability = 1');
    $stmt->execute([$cakeId]);
    $cake = $stmt->fetch();

    if (!$cake) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Selected cake is not available.'];
        header('Location: cakes.php');
        exit();
    }

    $existing = $pdo->prepare('SELECT id, quantity FROM cart WHERE customer_id = ? AND cake_id = ? AND size = ? AND cake_message = ?');
    $existing->execute([$_SESSION['customer_id'], $cakeId, $size, $cakeMessage]);
    $item = $existing->fetch();

    if ($item) {
        $update = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE id = ?');
        $update->execute([$quantity, $item['id']]);
    } else {
        $insert = $pdo->prepare('INSERT INTO cart (customer_id, cake_id, size, quantity, cake_message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $insert->execute([$_SESSION['customer_id'], $cakeId, $size, $quantity, $cakeMessage]);
    }

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Cake added to cart successfully!'];
    header('Location: cart.php');
    exit();
}

$cakeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT c.*, cat.category_name FROM cakes c JOIN categories cat ON c.category_id = cat.id WHERE c.id = ? AND c.availability = 1');
$stmt->execute([$cakeId]);
$cake = $stmt->fetch();

if (!$cake) {
    header('Location: cakes.php');
    exit();
}

$sizeOptions = explode(',', $cake['available_sizes']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($cake['cake_name']); ?> | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1><?php echo htmlspecialchars($cake['cake_name']); ?></h1>
        </div>
    </div>

    <main class="detail-page">
        <div class="container">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="detail-grid">
                <div class="detail-image">
                    <img src="<?php echo htmlspecialchars($cake['image']); ?>" alt="<?php echo htmlspecialchars($cake['cake_name']); ?>">
                </div>
                <div>
                    <div class="meta"><?php echo htmlspecialchars($cake['category_name']); ?></div>
                    <h2><?php echo htmlspecialchars($cake['cake_name']); ?></h2>
                    <div class="price-display">₹<?php echo number_format($cake['price'], 2); ?></div>
                    <p><?php echo htmlspecialchars($cake['description']); ?></p>

                    <form method="POST" data-validate="true">
                        <input type="hidden" name="cake_id" value="<?php echo $cake['id']; ?>">

                        <div class="form-group">
                            <label>Available Sizes</label>
                            <select name="size" required>
                                <?php foreach ($sizeOptions as $size): ?>
                                    <option value="<?php echo trim($size); ?>"><?php echo trim($size); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" min="1" value="1" required>
                        </div>

                        <?php if ($cake['eggless_available']): ?>
                            <div class="form-group">
                                <label>Egg / Eggless</label>
                                <select name="egg_type">
                                    <option value="Egg">Egg</option>
                                    <option value="Eggless">Eggless</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Custom Message on Cake</label>
                            <input type="text" name="cake_message" placeholder="Enter custom cake message">
                        </div>

                        <div class="form-group">
                            <label>Delivery Date</label>
                            <input type="date" name="delivery_date" required>
                        </div>

                        <div class="form-group">
                            <label>Preferred Delivery Time</label>
                            <input type="time" name="delivery_time" required>
                        </div>

                        <div class="form-group">
                            <label>Special Instructions</label>
                            <textarea name="special_instructions" placeholder="Any special requirement or notes"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                            <a href="cakes.php" class="btn btn-secondary">Back to Cakes</a>
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
