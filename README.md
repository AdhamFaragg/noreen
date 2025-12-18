# 🛍️ Online Clothing Store System

A complete, fully functional web application for an online clothing store built with PHP (procedural style), MySQL, HTML, CSS, JavaScript, and Bootstrap.

## 📋 Features

### 👩‍💼 Admin Features
- **Dashboard** with sales statistics and quick insights
- **Product Management** - Add, edit, delete products with image upload
- **Category Management** - Manage product categories
- **Order Management** - View and update order status
- **User Management** - View all registered users
- **Discount Codes** - Create and manage discount codes
- **Sales Reports** - Daily, weekly, and monthly sales reports
- Secure admin authentication

### 👤 Customer Features
- User registration and login with secure password hashing
- Browse products with search and filtering
- Filter by category, sort by price/name
- Product details with size and color selection
- Add to cart functionality
- Wishlist system
- Apply discount codes at checkout
- Secure checkout process
- Order history and tracking
- Product reviews and ratings
- Responsive design for all devices

### 👨‍🔧 Staff Features
- View and manage pending orders
- Update order status (Processing → Shipped → Delivered)
- Quick order management interface

## 🚀 Installation Instructions

### Prerequisites
- XAMPP (or any Apache + MySQL + PHP environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Setup Files
1. Copy the entire `noreen` folder to your `htdocs` directory:
   ```
   C:\xampp\htdocs\noreen\
   ```

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `clothing_store`
3. Import the SQL file:
   - Click on the `clothing_store` database
   - Go to the "Import" tab
   - Choose the file: `noreen/sql/clothing_store.sql`
   - Click "Go"

### Step 3: Configure Database Connection
1. Open `noreen/db/config.php`
2. Update database credentials if needed (default):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'clothing_store');
   ```

### Step 4: Create Upload Directory
1. Ensure the following directory exists and is writable:
   ```
   noreen/assets/images/products/
   ```
2. If it doesn't exist, create it manually

### Step 5: Access the Application
1. Start XAMPP (Apache and MySQL)
2. Open your browser and navigate to:
   ```
   http://localhost/noreen/
   ```

## 🔐 Default Login Credentials

### Admin Account
- **Username:** admin
- **Password:** admin123
- **Access:** http://localhost/noreen/login.php

### Customer Account
- Register a new account at: http://localhost/noreen/register.php

## 📂 Project Structure

```
noreen/
├── admin/                      # Admin panel files
│   ├── dashboard.php          # Admin dashboard
│   ├── manage_products.php    # Product management
│   ├── add_product.php        # Add/edit product form
│   ├── manage_categories.php  # Category management
│   ├── manage_orders.php      # Order management
│   ├── manage_users.php       # User management
│   ├── manage_discounts.php   # Discount codes
│   └── reports.php            # Sales reports
├── customer/                   # Customer-facing pages
│   ├── index.php              # Product listing
│   ├── product_details.php    # Product details page
│   ├── cart.php               # Shopping cart
│   ├── checkout.php           # Checkout process
│   ├── orders.php             # Order history
│   └── wishlist.php           # Customer wishlist
├── staff/                      # Staff panel
│   └── orders.php             # Order management for staff
├── assets/                     # Static assets
│   ├── css/
│   │   └── style.css          # Custom CSS styles
│   ├── js/
│   │   └── script.js          # Custom JavaScript
│   └── images/
│       └── products/          # Product images directory
├── db/
│   └── config.php             # Database configuration
├── includes/
│   ├── header.php             # Common header
│   ├── footer.php             # Common footer
│   └── functions.php          # Reusable PHP functions
├── sql/
│   └── clothing_store.sql     # Database schema
├── index.php                   # Homepage/landing page
├── login.php                   # Login page
├── register.php                # Registration page
├── logout.php                  # Logout handler
└── README.md                   # This file
```

## 💻 Database Tables

- **users** - User accounts (admin, staff, customer)
- **categories** - Product categories
- **products** - Product information
- **orders** - Customer orders
- **order_items** - Items in each order
- **cart** - Shopping cart items
- **wishlist** - Customer wishlists
- **reviews** - Product reviews and ratings
- **discounts** - Discount codes
- **banners** - Homepage promotional banners

## 🛠️ Technologies Used

- **Backend:** PHP 7.4+ (Procedural Style)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Framework:** Bootstrap 5.3
- **Icons:** Font Awesome 6.4
- **Server:** Apache (XAMPP)

## 🔧 Configuration

### Base URL
Update the base URL in `db/config.php` if your setup differs:
```php
define('BASE_URL', 'http://localhost/noreen/');
```

### Upload Directory
Ensure the upload directory path is correct in `db/config.php`:
```php
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/noreen/assets/images/products/');
```

## 📱 Responsive Design

The application is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with `mysqli_real_escape_string()`
- Session-based authentication
- Input sanitization
- XSS protection with `htmlspecialchars()`
- File upload validation

## 🎨 Features Walkthrough

### For Customers:
1. **Browse Products:** Visit the homepage and browse by category
2. **Search:** Use the search bar to find specific products
3. **Add to Cart:** Select size/color and add items to cart
4. **Checkout:** Complete purchase with shipping information
5. **Track Orders:** View order history and status
6. **Wishlist:** Save favorite items for later

### For Admin:
1. **Login:** Use admin credentials
2. **Dashboard:** View sales statistics and metrics
3. **Manage Products:** Add, edit, or delete products
4. **Manage Orders:** Update order status
5. **Reports:** Generate sales reports

## 🐛 Troubleshooting

### Images not uploading:
- Ensure `assets/images/products/` directory exists
- Check directory permissions (must be writable)

### Database connection error:
- Verify MySQL is running in XAMPP
- Check database credentials in `config.php`
- Ensure database `clothing_store` exists

### Page not found:
- Check that files are in `htdocs/noreen/` directory
- Verify Apache is running
- Check BASE_URL in `config.php`

## 📧 Support

For issues or questions:
- Check the database connection settings
- Ensure all files are properly uploaded
- Verify Apache and MySQL are running

## 📄 License

This project is created for educational purposes.

## 🙏 Credits

- Bootstrap 5.3
- Font Awesome 6.4
- PHP & MySQL

---

**Enjoy your Online Clothing Store System! 🎉**
