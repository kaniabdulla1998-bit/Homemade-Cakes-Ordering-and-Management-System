# Homemade Cake Ordering and Management System

A simple PHP 8 + MySQL academic mini project with customer ordering and a protected admin panel.

## Setup
1. Install PHP 8+, MySQL/MariaDB, and enable PDO MySQL.
2. Import `database/homemade_cake_db.sql` using phpMyAdmin or `mysql -u root -p < database/homemade_cake_db.sql`.
3. Edit `config/database.php`, or set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` environment variables.
4. Run from the project root: `php -S localhost:8000`.
5. Open `http://localhost:8000/`.

## Important test accounts
The SQL file includes sample records. Because password hashes can vary by PHP version, create known credentials with these one-time PHP commands or register a customer through the UI. For admin, generate a hash with `php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"`, then update the admin row in MySQL.

## Main routes
Customer: `index.php`, `cakes.php`, `cake-details.php`, `cart.php`, `login.php`, `register.php`, `checkout.php`, `my-orders.php`, `reviews.php`.
Admin: `admin/login.php`, `admin/dashboard.php`, `admin/cakes.php`, `admin/orders.php`, `admin/customers.php`, `admin/reviews.php`.

All database writes use PDO prepared statements. Payments are deliberately demo-only.
