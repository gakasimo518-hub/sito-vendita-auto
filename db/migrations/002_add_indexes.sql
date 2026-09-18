-- db/migrations/002_add_indexes.sql
-- Migration: Add indexes for search columns and define foreign key constraints
-- Database: MySQL
-- ------------------------------------------------------------

-- 1. Indexes for fast searching and filtering
ALTER TABLE `cars`
    ADD INDEX `idx_cars_make` (`make`),
    ADD INDEX `idx_cars_model` (`model`),
    ADD INDEX `idx_cars_year` (`year`),
    ADD INDEX `idx_cars_price` (`price`),
    ADD INDEX `idx_cars_category_id` (`category_id`);

ALTER TABLE `categories`
    ADD INDEX `idx_categories_name` (`name`);

ALTER TABLE `users`
    ADD INDEX `idx_users_email` (`email`);

ALTER TABLE `orders`
    ADD INDEX `idx_orders_user_id` (`user_id`),
    ADD INDEX `idx_orders_status` (`status`),
    ADD INDEX `idx_orders_created_at` (`created_at`);

ALTER TABLE `order_items`
    ADD INDEX `idx_order_items_order_id` (`order_id`),
    ADD INDEX `idx_order_items_car_id` (`car_id`);

ALTER TABLE `contacts`
    ADD INDEX `idx_contacts_email` (`email`),
    ADD INDEX `idx_contacts_created_at` (`created_at`);

-- 2. Foreign key constraints to enforce referential integrity
-- Note: Ensure that the referenced columns are indexed (MySQL does this automatically for PKs)

ALTER TABLE `cars`
    ADD CONSTRAINT `fk_cars_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

ALTER TABLE `orders`
    ADD CONSTRAINT `fk_orders_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

ALTER TABLE `order_items`
    ADD CONSTRAINT `fk_order_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_order_items_car`
        FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

-- 3. Optional: Ensure uniqueness where appropriate
ALTER TABLE `users`
    ADD UNIQUE `uq_users_email` (`email`);

ALTER TABLE `categories`
    ADD UNIQUE `uq_categories_name` (`name`);

-- End of migration 002_add_indexes.sql