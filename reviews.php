<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

$reviewsStmt = $pdo->query('SELECT r.*, c.name AS customer_name FROM reviews r JOIN customers c ON r.customer_id = c.id ORDER BY r.created_at DESC');
$reviews = $reviewsStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    requireCustomerLogin();

    $rating = (int) $_POST['rating'];
    $reviewText = trim($_POST['review_text']);
    $orderId = (int) $_POST['order_id'];

    if ($rating < 1 || $rating > 5 || $reviewText === '') {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Please provide a valid rating and review text.'];
        header('Location: reviews.php');
        exit();
    }

    $check = $pdo->prepare('SELECT id FROM orders WHERE id = ? AND customer_id = ?');
    $check->execute([$orderId, $_SESSION['customer_id']]);
    if (!$check->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Only customers with placed orders can write reviews.'];
        header('Location: reviews.php');
        exit();
    }

    $insert = $pdo->prepare('INSERT INTO reviews (customer_id, order_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())');
    $insert->execute([$_SESSION['customer_id'], $orderId, $rating, $reviewText]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Thank you for your review.'];
    header('Location: reviews.php');
    exit();
}

$customerOrders = [];
if (isCustomerLoggedIn()) {
    $orderList = $pdo->prepare('SELECT id FROM orders WHERE customer_id = ? ORDER BY created_at DESC');
    $orderList->execute([$_SESSION['customer_id']]);
    $customerOrders = $orderList->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Customer Reviews</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($_SESSION['message']['text']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (isCustomerLoggedIn() && !empty($customerOrders)): ?>
                <div class="form-wrap">
                    <h3>Submit a Review</h3>
                    <form method="POST" data-validate="true">
                        <div class="form-group">
                            <label for="order_id">Order</label>
                            <select name="order_id" required>
                                <option value="">Select Order</option>
                                <?php foreach ($customerOrders as $orderId): ?>
                                    <option value="<?php echo $orderId; ?>">Order #<?php echo $orderId; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select name="rating" required>
                                <option value="">Select Stars</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?php echo $i; ?>"><?php echo str_repeat('★', $i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="review_text">Your Review</label>
                            <textarea name="review_text" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="submit_review" class="btn">Submit Review</button>
                        </div>
                    </form>
                </div>
            <?php elseif (isCustomerLoggedIn()): ?>
                <div class="alert alert-error">You need to place at least one order before writing a review.</div>
            <?php else: ?>
                <div class="alert alert-error">Please login to submit a review.</div>
            <?php endif; ?>

            <div style="margin-top:40px;">
                <?php foreach ($reviews as $review): ?>
                    <div class="form-wrap" style="margin:0 0 20px; padding:20px;">
                        <div class="review-stars"><?php echo str_repeat('★', $review['rating']); ?><?php echo str_repeat('☆', 5 - $review['rating']); ?></div>
                        <p>“<?php echo htmlspecialchars($review['review_text']); ?>”</p>
                        <small><strong><?php echo htmlspecialchars($review['customer_name']); ?></strong> • <?php echo date('d M Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
