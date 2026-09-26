<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();
$stmt = $pdo->query("SELECT c.*, cat.category_name FROM cakes c JOIN categories cat ON c.category_id = cat.id WHERE c.availability = 1 ORDER BY c.created_at DESC LIMIT 6");
$featuredCakes = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM categories ORDER BY id');
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homemade Cake | Freshly Baked Homemade Cakes Made With Love</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <header class="hero">
        <div class="container hero-inner">
            <div>
                <h1>Freshly Baked Homemade Cakes Made With Love</h1>
                <p>Beautiful homemade cakes for birthdays, anniversaries, celebrations, and everyday sweet moments.</p>
                <div class="hero-actions">
                    <a href="cakes.php" class="btn">Explore Cakes</a>
                    <a href="cakes.php" class="btn btn-secondary">Order Now</a>
                </div>
            </div>
            <div class="hero-card">
                <h3>Popular Picks</h3>
                <p>Freshly baked daily with premium ingredients and custom designs for every occasion.</p>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="category-icon">🎂</div>
                        <strong>Birthday</strong>
                    </div>
                    <div class="feature-card">
                        <div class="category-icon">🍫</div>
                        <strong>Chocolate</strong>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <div class="section-heading">
                    <h2>Popular Cake Categories</h2>
                </div>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <div class="category-icon">
                                <?php
                                $icons = ['🎂', '🍫', '🍓', '🌲', '📷', '🥚'];
                                echo $icons[$category['id'] - 1] ?? '🎂';
                                ?>
                            </div>
                            <h4><?php echo htmlspecialchars($category['category_name']); ?></h4>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" style="background:#fffaf6;">
            <div class="container">
                <div class="section-heading">
                    <h2>Featured Cakes</h2>
                </div>
                <div class="cake-grid">
                    <?php foreach ($featuredCakes as $cake): ?>
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
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-heading">
                    <h2>Why Choose Us</h2>
                </div>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="category-icon">🌿</div>
                        <h4>Fresh ingredients</h4>
                        <p>We use fresh cream, fruit, and premium quality ingredients for every cake.</p>
                    </div>
                    <div class="feature-card">
                        <div class="category-icon">🏡</div>
                        <h4>Homemade quality</h4>
                        <p>Each cake is prepared carefully with homemade taste and bakery-level finishing.</p>
                    </div>
                    <div class="feature-card">
                        <div class="category-icon">🎨</div>
                        <h4>Custom cake designs</h4>
                        <p>Personalize your cake with theme, photo, message, and special requirements.</p>
                    </div>
                    <div class="feature-card">
                        <div class="category-icon">🚚</div>
                        <h4>On-time delivery</h4>
                        <p>We ensure your cake reaches on time and in perfect condition.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
