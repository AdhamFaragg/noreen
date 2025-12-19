-- Create Product Images Table for storing multiple images per product
USE clothing_store;

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

-- Add index for faster queries
CREATE INDEX idx_product_images_order ON product_images(product_id, image_order);
