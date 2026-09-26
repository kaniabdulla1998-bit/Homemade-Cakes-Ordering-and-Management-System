CREATE DATABASE IF NOT EXISTS homemade_cake_db;
USE homemade_cake_db;

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS cakes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    cake_name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    available_sizes VARCHAR(255) NOT NULL,
    eggless_available TINYINT(1) DEFAULT 0,
    availability TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    cake_id INT NOT NULL,
    size VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    cake_message VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (cake_id) REFERENCES cakes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_charge DECIMAL(10,2) NOT NULL,
    delivery_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    delivery_date DATE NOT NULL,
    delivery_time VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'Demo Payment Successful',
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    special_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    cake_id INT NOT NULL,
    cake_name VARCHAR(150) NOT NULL,
    size VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    cake_message VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (cake_id) REFERENCES cakes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL,
    review_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO admin (name, email, password) VALUES
('Admin User', 'admin@homemadecake.in', '$2y$10$LlWf6OiL8hf0n6iu70b7QeKQ6f3cC0Oe3P0G9w0P3JcQcPbM9h5kC');

INSERT INTO categories (category_name) VALUES
('Birthday Cake'),
('Chocolate Cake'),
('Red Velvet'),
('Black Forest'),
('Photo Cake'),
('Eggless Cake');

INSERT INTO customers (name, email, mobile, password) VALUES
('Demo Customer', 'customer@homemadecake.in', '9876543210', '$2y$10$7cC0J2uYiTL9O0Aq4uKHn.v5d2P3b4l8vqjPlTQ0IUk5GhCCzYlG2');

INSERT INTO cakes (category_id, cake_name, description, price, image, available_sizes, eggless_available, availability) VALUES
(1, 'Royal Birthday Delight', 'A soft vanilla sponge topped with whipped cream, fruit layers and a classic celebration finish.', 1200.00, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 1, 1),
(2, 'Dark Chocolate Fantasy', 'Rich dark chocolate layers with silky ganache and a decadent cocoa finish.', 1400.00, 'https://images.unsplash.com/photo-1559620192-032c4bc4674e?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 0, 1),
(3, 'Velvet Rose', 'Soft red velvet sponge layered with cream cheese frosting and elegant berry notes.', 1300.00, 'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 1, 1),
(4, 'Black Forest Classic', 'Chocolate sponge with cherries, whipped cream and a rich dark chocolate touch.', 1350.00, 'https://images.unsplash.com/photo-1517433670267-08bbd4be890f?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 0, 1),
(5, 'Custom Photo Cake', 'A personalized celebration cake with edible photo printing and cream finishing.', 1800.00, 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 1, 1),
(6, 'Eggless Almond Delight', 'Light eggless sponge with almond flavour and soft vanilla cream.', 1250.00, 'https://images.unsplash.com/photo-1486427944299-d1955d23e34d?auto=format&fit=crop&w=900&q=80', '1 kg, 1.5 kg, 2 kg', 1, 1);

INSERT INTO orders (customer_id, total_amount, delivery_charge, delivery_address, city, pincode, delivery_date, delivery_time, payment_method, payment_status, order_status, special_instructions) VALUES
(1, 1200.00, 80.00, '12 Market Street', 'Coimbatore', '641001', '2026-09-28', '18:00', 'Cash on Delivery', 'Demo Payment Successful', 'Confirmed', 'Please add a birthday message on top.');

INSERT INTO order_items (order_id, cake_id, cake_name, size, quantity, price, cake_message) VALUES
(1, 1, 'Royal Birthday Delight', '1.5 kg', 1, 1200.00, 'Happy Birthday!');

INSERT INTO reviews (customer_id, order_id, rating, review_text) VALUES
(1, 1, 5, 'The cake was fresh and delicious. The delivery was on time and packaging was excellent.'),
(1, 1, 4, 'Very tasty and beautifully decorated. Will order again.');

SELECT 'Database initialized successfully.' AS status;
