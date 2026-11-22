-- ==========================================================
-- Into Hinabi Bakes - Local MySQL Database Setup (XAMPP)
-- Synced with Supabase structure for local MySQL backup
-- ==========================================================

CREATE DATABASE IF NOT EXISTS sweet_cookie_db;
USE sweet_cookie_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Address Details Table
CREATE TABLE IF NOT EXISTS address_details (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    recipient_name VARCHAR(255),
    phone_number VARCHAR(50),
    street_address VARCHAR(255),
    barangay VARCHAR(255),
    city VARCHAR(255),
    postal_code VARCHAR(20),
    delivery_notes TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Contact Inquiries Table
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Product Categories Table
CREATE TABLE IF NOT EXISTS product_categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO product_categories (id, category_name) VALUES 
(1, 'Cookie'),
(2, 'Cake')
ON DUPLICATE KEY UPDATE category_name = VALUES(category_name);

-- 5. Products Table
CREATE TABLE IF NOT EXISTS products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id BIGINT,
    price DECIMAL(10, 2) DEFAULT 0.00,
    image_url TEXT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Payment Types Table
CREATE TABLE IF NOT EXISTS payment_types (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO payment_types (id, type) VALUES 
(1, 'Cash on Delivery'),
(2, 'Gcash'),
(3, 'Bank')
ON DUPLICATE KEY UPDATE type = VALUES(type);

-- 7. Payment Statuses Table
CREATE TABLE IF NOT EXISTS payment_statuses (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    status_type VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO payment_statuses (id, status_type) VALUES 
(1, 'Cash on Delivery'),
(2, 'Paid'),
(3, 'Pending')
ON DUPLICATE KEY UPDATE status_type = VALUES(status_type);

-- 8. Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(100) NOT NULL,
    user_id BIGINT NOT NULL,
    address_id BIGINT NOT NULL,
    status VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method BIGINT NOT NULL,
    payment_status BIGINT NOT NULL,
    customer_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (address_id) REFERENCES address_details(id),
    FOREIGN KEY (payment_method) REFERENCES payment_types(id),
    FOREIGN KEY (payment_status) REFERENCES payment_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity BIGINT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
