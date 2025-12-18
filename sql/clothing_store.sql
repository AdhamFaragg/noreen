-- Create Database
CREATE DATABASE IF NOT EXISTS clothing_store;
USE clothing_store;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(20),
    role ENUM('admin', 'customer', 'staff') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    stock INT DEFAULT 0,
    sizes VARCHAR(100),
    colors VARCHAR(100),
    image VARCHAR(255),
    additional_images TEXT,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    featured TINYINT(1) DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    discount_code VARCHAR(50),
    payment_method ENUM('cod', 'online') DEFAULT 'cod',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(50),
    shipping_postal_code VARCHAR(20),
    shipping_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200),
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(20),
    color VARCHAR(50),
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    size VARCHAR(20),
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Discounts Table
CREATE TABLE IF NOT EXISTS discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(200),
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10, 2) NOT NULL,
    min_purchase DECIMAL(10, 2) DEFAULT 0,
    max_discount DECIMAL(10, 2),
    usage_limit INT DEFAULT 0,
    used_count INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Banners Table
CREATE TABLE IF NOT EXISTS banners (
    banner_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    subtitle VARCHAR(255),
    image VARCHAR(255),
    link VARCHAR(255),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin User (password: admin)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@clothing.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'System Administrator', 'admin');

-- Insert Sample Categories
INSERT INTO categories (category_name, description, status) VALUES
('Men', 'Clothing for men', 'active'),
('Women', 'Clothing for women', 'active'),
('Kids', 'Clothing for children', 'active'),
('Accessories', 'Fashion accessories', 'active'),
('Footwear', 'Shoes and sandals', 'active');

-- Insert Sample Products
INSERT INTO products (category_id, product_name, description, price, discount_price, stock, sizes, colors, status, featured) VALUES
(1, 'Men\'s Casual T-Shirt', 'Comfortable cotton t-shirt for everyday wear', 25.99, 19.99, 50, 'S,M,L,XL,XXL', 'Black,White,Blue,Gray', 'active', 1),
(1, 'Men\'s Denim Jeans', 'Classic fit denim jeans', 49.99, 39.99, 30, '28,30,32,34,36', 'Blue,Black', 'active', 1),
(2, 'Women\'s Summer Dress', 'Light and breezy summer dress', 39.99, 29.99, 40, 'S,M,L,XL', 'Red,Pink,Yellow,White', 'active', 1),
(2, 'Women\'s Blazer', 'Professional blazer for office wear', 79.99, 59.99, 25, 'S,M,L,XL', 'Black,Navy,Gray', 'active', 0),
(3, 'Kids T-Shirt', 'Colorful t-shirt for kids', 15.99, 12.99, 60, '2-3Y,4-5Y,6-7Y,8-9Y', 'Red,Blue,Green,Yellow', 'active', 1),
(4, 'Leather Belt', 'Genuine leather belt', 29.99, NULL, 45, 'S,M,L', 'Brown,Black', 'active', 0),
(5, 'Running Shoes', 'Comfortable running shoes', 89.99, 69.99, 35, '7,8,9,10,11', 'Black,White,Blue', 'active', 1);

-- Insert Sample Discount Codes
INSERT INTO discounts (code, description, discount_type, discount_value, min_purchase, start_date, end_date, status) VALUES
('WELCOME10', 'Welcome discount - 10% off', 'percentage', 10, 50, '2025-01-01', '2025-12-31', 'active'),
('SAVE20', 'Save $20 on orders over $100', 'fixed', 20, 100, '2025-01-01', '2025-12-31', 'active'),
('SUMMER25', 'Summer sale - 25% off', 'percentage', 25, 75, '2025-06-01', '2025-08-31', 'active');

-- Insert Sample Banners
INSERT INTO banners (title, subtitle, display_order, status) VALUES
('Summer Collection 2025', 'Up to 50% off on all summer wear', 1, 'active'),
('New Arrivals', 'Check out the latest fashion trends', 2, 'active'),
('Free Shipping', 'On orders above $50', 3, 'active');
