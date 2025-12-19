# Admin Panel - Multiple Images Upload Setup

## Changes Made

### 1. Updated Files

#### `/admin/add_product.php`
- Added import of `product_images_functions.php`
- Added multiple file input for `additional_images[]`
- Shows current additional images with delete buttons (when editing)
- Handles upload of additional images on save
- Added preview functionality for selected images

#### `/admin/delete_product_image.php` (NEW)
- AJAX endpoint for deleting product images
- Verifies admin access before deletion
- Returns JSON response

### 2. New Features

**Adding Products:**
- Upload main image (as before)
- Upload multiple additional images at once
- Images preview before upload

**Editing Products:**
- See all current additional images
- Delete individual images with one-click button
- Add new images to existing products

### 3. How to Use

#### Adding a New Product with Multiple Images:
1. Go to Admin → Manage Products → Add New Product
2. Fill in product details
3. Upload main product image
4. In "Additional Images" section, select multiple images (Ctrl+Click to select multiple)
5. Click Save
6. Images will be added to the product_images table

#### Editing a Product:
1. Go to Admin → Manage Products → Edit Product
2. Scroll to "Additional Images" section
3. View current images with delete buttons
4. Click red trash icon to delete an image
5. Or upload new images using the file input
6. Click Update

#### Current Additional Images Display:
- Shows thumbnail previews of all additional images
- Each has a delete button
- Click delete to remove from gallery (page reloads)

### 4. Database Setup Required

**IMPORTANT:** You must run this SQL first:

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
2. Select `clothing_store` database
3. Go to SQL tab
4. Paste the SQL
5. Click Go

### 5. Testing

**Test Adding Multiple Images:**
1. Create a new product
2. Upload 2-3 additional images
3. Save product
4. Go to product page (customer side)
5. You should see image gallery with thumbnails and navigation

**Test Deleting Images:**
1. Edit the product you just created
2. Scroll to "Current Additional Images"
3. Click the trash button on any image
4. Page reloads and image is deleted

### 6. Complete Workflow

```
Admin Panel: admin/add_product.php
↓ (Upload images)
Server: Stores files in assets/images/products/
↓ (Insert into product_images table)
Database: product_images table
↓
Customer Site: customer/product_details.php
↓ (Fetches from product_images table)
Gallery: Shows thumbnails + navigation
```

## Troubleshooting

**Images not uploading?**
- Check file permissions on `assets/images/products/` folder
- Verify file sizes are under 5MB
- Check PHP upload limits in php.ini

**Delete button not working?**
- Check browser console (F12) for errors
- Verify admin permissions
- Verify AJAX path is correct

**Images not showing in gallery?**
- Run the SQL migration
- Verify images are in database: `SELECT * FROM product_images;`
- Clear browser cache
- Check UPLOAD_URL in `db/config.php`

