# Multiple Product Images Setup Guide

## Changes Made

### 1. Database Setup
A new SQL migration file has been created at: `sql/add_product_images_table.sql`

**Run this SQL in your database to create the product_images table:**
```sql
CREATE TABLE IF NOT EXISTS product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_order INT DEFAULT 0,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    KEY idx_product_id (product_id)
);
```

**How to apply in phpMyAdmin:**
1. Open phpMyAdmin
2. Select your `clothing_store` database
3. Go to SQL tab
4. Copy and paste the SQL from `sql/add_product_images_table.sql`
5. Click "Go"

### 2. Product Details Page Updated
File: `customer/product_details.php`

**New Features:**
- Image gallery with navigation arrows (← →)
- Click thumbnails to view different images
- Use keyboard arrow keys to navigate (← →)
- Responsive thumbnail gallery
- Smooth image transitions

**How it works:**
- Main product image is displayed
- If product has multiple images in `product_images` table, thumbnails appear below
- Click any thumbnail to view full image
- Navigation arrows appear when multiple images exist

### 3. Product Images Functions
File: `includes/product_images_functions.php`

**Available Functions:**
- `get_product_images($product_id)` - Get all images for a product
- `add_product_image($product_id, $image_path, $is_primary, $image_order)` - Add new image
- `delete_product_image($image_id)` - Delete an image
- `update_image_order($image_id, $new_order)` - Reorder images
- `handle_product_image_uploads($product_id, $files)` - Handle multiple file uploads

## How to Add Multiple Images to Products

### Option 1: Direct SQL Insert
```sql
INSERT INTO product_images (product_id, image_path, image_order, is_primary) 
VALUES (1, 'image1.jpg', 0, 1);

INSERT INTO product_images (product_id, image_path, image_order, is_primary) 
VALUES (1, 'image2.jpg', 1, 0);
```

### Option 2: Using PHP Functions
```php
require_once 'includes/product_images_functions.php';

// Add images
add_product_image(1, 'image1.jpg', 1, 0);  // Primary image, order 0
add_product_image(1, 'image2.jpg', 0, 1);  // Not primary, order 1
add_product_image(1, 'image3.jpg', 0, 2);  // Not primary, order 2
```

## File Structure

```
assets/images/
└── products/
    ├── image1.jpg
    ├── image2.jpg
    └── image3.jpg
```

## Next Steps for Admin Panel

To complete the implementation, you'll need to:

1. **Update Add Product Page** (`admin/add_product.php`)
   - Add multiple file input for product images
   - Store images in product_images table

2. **Update Edit Product Page** (`admin/edit_product.php`)
   - Show current product images
   - Allow reordering images
   - Allow deleting specific images
   - Allow adding new images

3. **Test the Gallery**
   - Add test images to a product via SQL
   - View product details and test navigation
   - Use arrow keys to navigate between images

## Current Image Usage

- **Main product image**: Still stored in `products.image` column
- **Additional images**: New images go in `product_images` table
- The gallery will show the main image first, then any additional images

## Troubleshooting

**Images not showing?**
1. Verify UPLOAD_URL is correct in `db/config.php`
2. Verify images are in `assets/images/products/`
3. Check database connection in product_details.php

**Gallery not working?**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Check browser console for JavaScript errors (F12)
3. Verify product_images table exists

