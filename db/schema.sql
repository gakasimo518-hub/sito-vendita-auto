SET NAMES utf8mb4;
SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- =========================================================
--  DATABASE SCHEMA FOR progetto: sito-vendita-auto
--  ENGINE: MySQL 8.x
--  This file contains the complete definition of all tables,
--  columns, primary keys, foreign keys and indexes.
-- =========================================================

-- =========================================================
--  DROP TABLES IF THEY EXIST (order matters due to FK)
-- =========================================================
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS car_images;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS car_categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS contacts;

SET foreign_key_checks = 1;

-- =========================================================
--  USERS
--  Table used for admin / staff authentication.
-- =========================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  CONTACTS
--  Messages sent through the "Contatti" page.
-- =========================================================
CREATE TABLE contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contacts_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  CAR CATEGORIES
--  E.g. Sedan, SUV, Coupe, etc.
-- =========================================================
CREATE TABLE car_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  CARS
--  Main catalog of vehicles.
-- =========================================================
CREATE TABLE cars (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(80) NOT NULL,
    model VARCHAR(80) NOT NULL,
    year YEAR NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    mileage INT UNSIGNED NOT NULL,
    color VARCHAR(50) NOT NULL,
    transmission ENUM('Manual','Automatic','Semi-Automatic') NOT NULL,
    fuel_type ENUM('Petrol','Diesel','Hybrid','Electric','CNG','LPG') NOT NULL,
    engine VARCHAR(50) NOT NULL,
    description TEXT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cars_category
        FOREIGN KEY (category_id) REFERENCES car_categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    INDEX idx_cars_make_model_year (make, model, year),
    INDEX idx_cars_price (price),
    INDEX idx_cars_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  CAR IMAGES
--  One‑to‑many relationship with cars.
-- =========================================================
CREATE TABLE car_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(150) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_car_images_car
        FOREIGN KEY (car_id) REFERENCES cars(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    INDEX idx_car_images_car_sort (car_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  ORDERS
--  Customer orders (even if the site is B2C, we keep a user reference for future expansion).
-- =========================================================
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','confirmed','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    INDEX idx_orders_status (status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  ORDER_ITEMS
--  Each car purchased in an order (normally quantity = 1).
-- =========================================================
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    car_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_order_items_car
        FOREIGN KEY (car_id) REFERENCES cars(id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_car (car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
--  ADDITIONAL INDEXES FOR SEARCH PERFORMANCE
-- =========================================================
CREATE FULLTEXT INDEX ft_cars_description ON cars(description);
CREATE INDEX idx_cars_mileage ON cars(mileage);
CREATE INDEX idx_cars_engine ON cars(engine);
CREATE INDEX idx_cars_fuel_type ON cars(fuel_type);
CREATE INDEX idx_cars_transmission ON cars(transmission);

-- =========================================================
--  END OF SCHEMA
-- =========================================================
SELECT 'Schema creation completed' AS message;