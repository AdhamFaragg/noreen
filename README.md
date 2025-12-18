# 🛍️ Online Clothing Store - MVP Architecture

A complete, fully functional web application for an online clothing store refactored into a proper Model-View-Controller architecture built with PHP 8.2, MySQL, HTML, CSS, JavaScript, and Bootstrap 5.

## 🏗️ Architecture

This project has been professionally refactored from a procedural PHP application into a **Model-View-Controller (MVC)** architecture pattern, making it highly maintainable, testable, and scalable.

### Key Improvements
✅ **Separation of Concerns** - Clear separation between data (Models), presentation (Views), and logic (Controllers)  
✅ **Reusable Components** - Base classes for Controllers and Models reduce code duplication  
✅ **Centralized Routing** - Single entry point (`public/index.php`) routes all requests  
✅ **Automatic Autoloading** - No more manual includes/requires  
✅ **Better Security** - Centralized DB wrapper with prepared statement support  
✅ **Cleaner URLs** - Query-string based routing (extensible to pretty URLs)

## 📂 Project Structure

```
noreen/
├── app/
│   ├── bootstrap.php                 # Auto-loader and app initialization
│   ├── Controllers/                  # Request handlers
│   │   ├── Controller.php            # Base class
│   │   ├── AuthController.php        # Login/Register/Logout
│   │   ├── HomeController.php        # Home & static pages
│   │   ├── ProductController.php     # Product browsing
│   │   ├── CartController.php        # Shopping & checkout
│   │   └── AdminController.php       # Admin CRUD operations
│   ├── Models/                       # Business logic & DB access
│   │   ├── Model.php                 # Base class
│   │   ├── DB.php                    # Database wrapper
│   │   ├── UserModel.php
│   │   ├── ProductModel.php
│   │   ├── OrderModel.php
│   │   └── CategoryModel.php
│   └── Views/                        # PHP templates
│       ├── layout.php                # Main wrapper
│       ├── partials/                 # Reusable components
│       ├── home.php
│       ├── products/
│       ├── cart/
│       ├── auth/
│       └── admin/
├── public/
│   └── index.php                     # Front controller & router
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── images/products/
├── sql/
│   └── clothing_store.sql            # Database schema
└── README.md
```

## 📋 Features

### 👨‍💼 Admin Features
- **Dashboard** with real-time statistics
- **Product Management** - Add, edit, delete products with images
- **Order Management** - View and update order status
- **User Management** - View all registered customers
- **Category Management** - Organize products
- Secure admin authentication & role-based access

### 👤 Customer Features
- Secure registration and login
- Browse & search products
- Filter by category
- Product detail pages
- Add to cart
- Checkout & order placement
- Responsive design (Bootstrap 5)

### 🔒 Security
- Password hashing with `password_hash()`
- Input validation & output escaping
- Session-based authentication
- Admin-only route protection
- CSRF-ready structure

## 🚀 Quick Start

### Requirements
- PHP 7.4+ (tested with 8.2)
- MySQL 5.7+
- XAMPP/Apache

### Setup (5 minutes)

**1. Place Files**
```bash
# Copy to XAMPP
cp -r noreen /Applications/XAMPP/xamppfiles/htdocs/
```

**2. Import Database**
```bash
# Option A: phpMyAdmin
# Create database 'clothing_store' and import sql/clothing_store.sql

# Option B: MySQL CLI
mysql -u root < sql/clothing_store.sql
```

**3. Start Dev Server**
```bash
# Using PHP Dev Server (no Apache needed)
cd /Applications/XAMPP/xamppfiles/htdocs/noreen
/Applications/XAMPP/bin/php -S localhost:8080 -t public
```

**4. Visit Application**
```
http://localhost:8080/?page=home
```

### Access Credentials
**Admin Account** (create manually in phpMyAdmin or via register):
- Email: admin@example.com
- Password: password123
- Role: admin (set in database)

## 🎮 Usage Examples

### Customer Flow
```
Home → Browse Products → View Details → Add to Cart → Checkout → Order Confirmed
```

### Admin Flow
```
Login (admin) → Dashboard → Manage Products → Edit Product → Save
```

### URL Patterns
```
http://localhost:8080/?page=home                              # Home
http://localhost:8080/?page=products                          # All products
http://localhost:8080/?page=products&category=1               # Filter by category
http://localhost:8080/?page=products&search=shirt             # Search
http://localhost:8080/?page=products&action=show&id=5         # Product detail
http://localhost:8080/?page=auth&action=login                 # Login
http://localhost:8080/?page=auth&action=register              # Register
http://localhost:8080/?page=cart                              # Shopping cart
http://localhost:8080/?page=cart&action=checkout              # Checkout
http://localhost:8080/?page=admin&action=dashboard            # Admin dashboard
http://localhost:8080/?page=admin&action=manageProducts       # Product management
```

## 🛠️ Controllers Reference

### HomeController
```php
index()      // Homepage with featured products
about()      // About page
contact()    // Contact page
```

### ProductController
```php
index()      // List all products with filters
show()       // Show single product details
addToCart()  // Add product to cart (AJAX)
```

### CartController
```php
index()           // View cart
update()          // Update quantities
remove()          // Remove item
checkout()        // Show checkout form
processCheckout() // Process order
```

### AuthController
```php
login()              // Show login form
handleLogin()        // Process login
register()           // Show register form
handleRegister()     // Process registration
logout()             // Clear session & logout
```

### AdminController
```php
dashboard()           // Admin overview
manageProducts()      // List products
addProduct()          // Add product form & handler
editProduct()         // Edit product form & handler
deleteProduct()       // Delete product
manageOrders()        // View all orders
manageUsers()         // View all users
manageCategories()    // Manage categories
```

## 📊 Models Reference

### ProductModel
```php
getActiveProducts()      // Get all active products with pagination
getFeaturedProducts()    // Get featured products
getProductById()         // Get single product
getProductsByCategory()  // Filter by category
searchProducts()         // Search functionality
createProduct()          // Add new product
updateProduct()          // Edit product
deleteProduct()          // Delete product
```

### UserModel
```php
getUserById()      // Get user by ID
getUserByEmail()   // Get user by email
getAllUsers()      // List all users
createUser()       // Create new user
updateUser()       // Edit user
deleteUser()       // Delete user
authenticateUser() // Login validation
```

### OrderModel
```php
getOrderById()     // Get single order
getOrdersByUser()  // Get customer's orders
getAllOrders()     // List all orders (admin)
createOrder()      // Place new order
updateOrder()      // Update order status
getTotalRevenue()  // Calculate revenue
```

### CategoryModel
```php
getCategoryById()     // Get category
getAllCategories()    // List all categories
createCategory()      // Add category
updateCategory()      // Edit category
deleteCategory()      // Delete category
```

## 🧪 Testing

Run the test suite:
```bash
chmod +x test.sh
./test.sh
```

Expected Output:
```
✓ Home page loaded successfully
✓ Products page loaded successfully
✓ Login page loaded successfully
✓ Register page loaded successfully
✓ Cart page loaded successfully
```

## 🔧 Extending

### Add a New Feature

**1. Create Model** (`app/Models/FeatureModel.php`)
```php
class FeatureModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'features';
    }
}
```

**2. Create Controller** (`app/Controllers/FeatureController.php`)
```php
class FeatureController extends Controller {
    public function index() {
        $data = // fetch data
        return $this->render('feature/index', $data);
    }
}
```

**3. Create Views** (`app/Views/feature/`)
```php
// app/Views/feature/index.php
<h1>Features</h1>
...
```

**4. Update Router** (optional)
Add to `$pageMap` in `public/index.php`:
```php
'feature' => 'Feature'
```

**5. Access**
```
http://localhost:8080/?page=feature
```

## 🚨 Troubleshooting

### Database Connection Error
```
Check DB.php credentials match your MySQL setup
```

### "View not found" Error
```
Verify file path in app/Views/
View name must match filename (without .php)
```

### Admin pages redirecting
```
Ensure user role is 'admin' in users table
Check session is started properly
```

### Images not showing
```
Verify assets/images/products/ exists (chmod 755)
Check database for correct image filenames
```

## 📚 Key Files Explained

| File | Purpose |
|------|---------|
| `public/index.php` | Front controller - routes all requests |
| `app/bootstrap.php` | Initializes app, sets up autoloader |
| `app/Controllers/Controller.php` | Base class with common methods |
| `app/Models/Model.php` | Base class for all models |
| `app/Models/DB.php` | Database wrapper with static methods |
| `app/Views/layout.php` | Main HTML wrapper for all views |

## 🔐 Security Best Practices Applied

✅ Password hashing with bcrypt (`password_hash`)  
✅ Input validation and sanitization  
✅ Output escaping with `htmlspecialchars()`  
✅ Session-based authentication  
✅ SQL injection prevention via escaping  
✅ Admin-only route protection  
✅ CSRF-ready structure (can be added)

## 📈 Performance Tips

1. **Use database indexing** on frequently queried columns
2. **Implement caching** for product lists
3. **Optimize images** before upload
4. **Use lazy loading** for product images
5. **Add database query logging** for debugging

## 🎓 Learning Resources

- **MVP Pattern**: https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter
- **PHP PSR Standards**: https://www.php.fig.org/psr/
- **Bootstrap 5**: https://getbootstrap.com/docs/5.0/
- **MySQL**: https://dev.mysql.com/doc/

## 📝 Changelog

### v2.0 (Current - MVP Refactor)
- ✅ Implemented Model-View-Controller architecture
- ✅ Created base Controller and Model classes
- ✅ Built centralized Router
- ✅ Established auto-loader via bootstrap.php
- ✅ Separated concerns between Models, Views, Controllers
- ✅ Created comprehensive admin interface
- ✅ Implemented order management
- ✅ Added comprehensive documentation

### v1.0 (Original)
- Basic procedural PHP application
- Product browsing and cart functionality
- User registration and login
- Admin dashboard

## 👨‍💼 Author

Built as a reference implementation of MVP architecture for PHP web applications.

---

**Last Updated**: December 18, 2025  
**PHP Version**: 8.2.4  
**Database**: MySQL 5.7+  
**Framework**: Vanilla PHP (MVP Architecture)  
**Frontend**: Bootstrap 5, Vanilla JS

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
