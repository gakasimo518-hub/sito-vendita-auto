SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------
-- Utenti (admin)
-- -------------------------------------------------
INSERT INTO users (id, name, email, password_hash, role, created_at) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$V1a2b3c4d5e6f7g8h9i0jKLMNOPQRSTUVWXyzABCDEFGHIJKL', 'admin', NOW());

-- -------------------------------------------------
-- Auto in vendita
-- -------------------------------------------------
INSERT INTO cars (id, make, model, year, price, mileage, color, description, status, created_at, updated_at) VALUES
(1, 'Toyota', 'Corolla', 2020, 15000.00, 25000, 'White', 'Reliable compact sedan with great fuel efficiency.', 'available', NOW(), NOW()),
(2, 'Ford', 'Mustang', 2019, 30000.00, 15000, 'Red', 'Sporty coupe with powerful V8 engine.', 'available', NOW(), NOW()),
(3, 'BMW', 'X5', 2021, 55000.00, 10000, 'Black', 'Luxury SUV with advanced technology and comfort.', 'available', NOW(), NOW());

-- -------------------------------------------------
-- Immagini delle auto
-- -------------------------------------------------
INSERT INTO car_images (id, car_id, url, alt_text, sort_order) VALUES
(1, 1, 'images/cars/toyota_corolla_1.jpg', 'Toyota Corolla front view', 1),
(2, 1, 'images/cars/toyota_corolla_2.jpg', 'Toyota Corolla interior', 2),
(3, 2, 'images/cars/ford_mustang_1.jpg', 'Ford Mustang front view', 1),
(4, 2, 'images/cars/ford_mustang_2.jpg', 'Ford Mustang side view', 2),
(5, 3, 'images/cars/bmw_x5_1.jpg', 'BMW X5 front view', 1),
(6, 3, 'images/cars/bmw_x5_2.jpg', 'BMW X5 interior', 2);

-- -------------------------------------------------
-- Ordini
-- -------------------------------------------------
INSERT INTO orders (id, user_id, total_amount, status, created_at, updated_at) VALUES
(1, 1, 15000.00, 'completed', NOW(), NOW()),
(2, 1, 30000.00, 'pending', NOW(), NOW());

-- -------------------------------------------------
-- Dettagli degli ordini (articoli)
-- -------------------------------------------------
INSERT INTO order_items (id, order_id, car_id, quantity, unit_price) VALUES
(1, 1, 1, 1, 15000.00),
(2, 2, 2, 1, 30000.00);

-- -------------------------------------------------
-- Messaggi di contatto
-- -------------------------------------------------
INSERT INTO contacts (id, name, email, phone, message, created_at) VALUES
(1, 'Mario Rossi', 'mario.rossi@example.com', '+391234567890', 'Vorrei maggiori informazioni sulla Toyota Corolla.', NOW()),
(2, 'Luisa Bianchi', 'luisa.bianchi@example.com', '+391098765432', 'È possibile fare un test drive della Ford Mustang?', NOW());

SET FOREIGN_KEY_CHECKS = 1;