<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$query = 'SELECT c.*, cat.category_name FROM cakes c JOIN categories cat ON c.category_id = cat.id WHERE c.availability = 1';
$params = [];

if ($search !== '') {
    $query .= ' AND c.cake_name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($category !== '') {
    $query .= ' AND cat.category_name = ?';
    $params[] = $category;
}

$query .= ' ORDER BY c.created_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cakes = $stmt->fetchAll();

$catStmt = $pdo->query('SELECT category_name FROM categories ORDER BY id');
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cakes | Homemade Cake</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <h1>Our Cake Menu</h1>
        </div>
    </div>

    <main class="section">
        <div class="container">
            <form method="GET" class="form-wrap" style="margin-top:0;">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="search">Search Cake</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by cake name">
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">Filter Cakes</button>
                    <a href="cakes.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>

            <div class="cake-grid" style="margin-top:30px;">
                <?php if (empty($cakes)): ?>
                    <div class="alert alert-error">No cakes found for the selected filter.</div>
                <?php else: ?>
                    <?php foreach ($cakes as $cake): ?>
                        <div class="cake-card">
                            <img src="<?php echo htmlspecialchars($cake['image']); ?>" alt="<?php echo htmlspecialchars($cake['cake_name']); ?>">
                            <div class="cake-card-body">
                                <h3><?php echo htmlspecialchars($cake['cake_name']); ?></h3>
                                <div class="meta"><?php echo htmlspecialchars($cake['category_name']); ?></div>
                                <p><?php echo htmlspecialchars(substr($cake['description'], 0, 90)) . '...'; ?></p>
                                <div class="price-row">
                                    <span>₹<?php echo number_format($cake['price'], 2); ?></span>
                                </div>
                                <div class="cake-actions">
                                    <a href="cake-details.php?id=<?php echo $cake['id']; ?>" class="btn btn-small">View Details</a>
                                    <a href="cake-details.php?id=<?php echo $cake['id']; ?>" class="btn btn-small btn-secondary">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
