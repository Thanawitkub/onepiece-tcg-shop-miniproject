-- ========================================================
-- One Piece TCG E-Commerce Database Schema & Seed Data
-- Database Name: onepiece_tcg_db
-- Visual Style: Dark Rainy Sea & Pirates / Conqueror's Haki
-- ========================================================

CREATE DATABASE IF NOT EXISTS `onepiece_tcg_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `onepiece_tcg_db`;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `wallet_balance` DECIMAL(10,2) NOT NULL DEFAULT 5000.00,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `category` ENUM('Booster Pack', 'Booster Box', 'Carton') NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `image_url` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: orders
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'PromptPay / Bank Transfer',
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Paid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: order_items
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Seed Data: Users
-- Default Passwords:
-- admin / admin123
-- luffy / luffy123
-- ========================================================
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `wallet_balance`, `role`) VALUES
(1, 'admin', 'admin@pirates-tcg.com', '$2y$10$qAjPN/35D0xsDc5.WGpqquAONf79o.bOhNc1aT5FUKk5vjQJx0ZT2', 'Fleet Admiral Akainu', 50000.00, 'admin'),
(2, 'luffy', 'luffy@strawhat.com', '$2y$10$/gBU7THDpTFNLBhghdLn8.LFGQ5zOvr7Wf8dcHYCvakiAa5BF1PiK', 'Monkey D. Luffy', 9999999.00, 'user');

-- ========================================================
-- Seed Data: Products (Booster Pack, Booster Box, Carton)
-- ========================================================

-- 1. Booster Packs (OP-01 to OP-13, Price: 85 - 100 THB)
INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock`, `image_url`) VALUES
(1, 'One Piece Booster Pack OP-01 Romance Dawn', 'Booster Pack', 'The debut booster pack featuring Monkey D. Luffy, Roronoa Zoro, and the legendary Romance Dawn saga. Contains 6 cards per pack.', 95.00, 120, 'images/pack/pack_op01.png'),
(2, 'One Piece Booster Pack OP-02 Paramount War', 'Booster Pack', 'Experience the Marineford war with Whitebeard Pirates, Navy Admirals, and Portgas D. Ace.', 90.00, 95, 'images/pack/pack_op02.png'),
(3, 'One Piece Booster Pack OP-03 Pillars of Strength', 'Booster Pack', 'Showcasing CP9, Big Mom Pirates, and powerful Yellow card deck archetypes.', 85.00, 80, 'images/pack/pack_op03.png'),
(4, 'One Piece Booster Pack OP-04 Kingdoms of Intrigue', 'Booster Pack', 'Focusing on the Alabasta Kingdom and Dressrosa Saga, featuring Nefertari Vivi and Donquixote Doflamingo.', 90.00, 110, 'images/pack/pack_op04.png'),
(5, 'One Piece Booster Pack OP-05 Awakening of the New Era', 'Booster Pack', 'The sensational 1st Anniversary booster pack starring Gear 5 Sun God Nika Luffy and Oda signed cards!', 100.00, 65, 'images/pack/pack_op05.png'),
(6, 'One Piece Booster Pack OP-06 Wings of the Captain', 'Booster Pack', 'Highlighting the dual wings of the Pirate King: Zoro and Sanji, alongside Germa 66 and Thriller Bark.', 95.00, 75, 'images/pack/pack_op06.png'),
(7, 'One Piece Booster Pack OP-07 500 Years in the Future', 'Booster Pack', 'Futuristic cards featuring Egghead Island, Vegapunk, Jewelry Bonney, and the Revolutionary Army.', 90.00, 85, 'images/pack/pack_op07.png'),
(8, 'One Piece Booster Pack OP-08 Two Legends', 'Booster Pack', 'Featuring the Dark King Silvers Rayleigh and Edward Newgate in their prime eras.', 95.00, 100, 'images/pack/pack_op08.png'),
(9, 'One Piece Booster Pack OP-09 The New Four Emperors', 'Booster Pack', 'Celebrates the 2nd Anniversary highlighting Luffy, Buggy, Shanks, and Blackbeard Teach!', 100.00, 50, 'images/pack/pack_op09.png'),
(10, 'One Piece Booster Pack OP-10 Royal Blood', 'Booster Pack', 'Unveils cards themed around the celestial royal lineage and ancient bloodlines across the Grand Line.', 95.00, 90, 'images/pack/pack_op10.png'),
(11, 'One Piece Booster Pack OP-11 A New Era Begins', 'Booster Pack', 'Intense competitive meta cards shaping the next competitive season of the One Piece Card Game.', 95.00, 115, 'images/pack/pack_op11.png'),
(12, 'One Piece Booster Pack OP-12 Legacy of the Pirate King', 'Booster Pack', 'Legendary treasures, Gold Roger crew chronicles, and God Valley secrets in high rarity.', 90.00, 105, 'images/pack/pack_op12.png'),
(13, 'One Piece Booster Pack OP-13 Dawn of the New Age', 'Booster Pack', 'The latest pinnacle expansion with breathtaking secret rares and manga alternate art cards.', 98.00, 140, 'images/pack/pack_op13.png');

-- 2. Booster Boxes (OP-01 to OP-13, Price: 2050 - 2450 THB)
INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock`, `image_url`) VALUES
(14, 'One Piece Booster Box OP-01 Romance Dawn', 'Booster Box', 'Factory sealed Booster Box containing 24 booster packs with guaranteed SR, SEC, and alternate art chase cards.', 2350.00, 24, 'images/box/box_op01.png'),
(15, 'One Piece Booster Box OP-02 Paramount War', 'Booster Box', 'Complete sealed box with 24 packs. Hunt for Manga Ace and Leader parallel arts from Marineford!', 2180.00, 18, 'images/box/box_op02.png'),
(16, 'One Piece Booster Box OP-03 Pillars of Strength', 'Booster Box', 'Sealed display box of 24 packs. Features Manga Sogeking and elite Big Mom Pirates cards.', 2050.00, 15, 'images/box/box_op03.png'),
(17, 'One Piece Booster Box OP-04 Kingdoms of Intrigue', 'Booster Box', 'Sealed box with 24 packs. Featuring Manga Sabo and powerful Alabasta/Dressrosa deck staples.', 2150.00, 20, 'images/box/box_op04.png'),
(18, 'One Piece Booster Box OP-05 Awakening of the New Era', 'Booster Box', 'The Holy Grail 1st Anniversary sealed box! Chance to pull Manga Gear 5 Luffy, Signed Oda Luffy, and Kid/Law.', 2450.00, 7, 'images/box/box_op05.png'),
(19, 'One Piece Booster Box OP-06 Wings of the Captain', 'Booster Box', 'Sealed 24-pack box. Featuring Manga Roronoa Zoro, Sanji, and Moria black control meta.', 2290.00, 16, 'images/box/box_op06.png'),
(20, 'One Piece Booster Box OP-07 500 Years in the Future', 'Booster Box', 'Sealed 24-pack box. High-tech Egghead Island expansion starring Manga Boa Hancock!', 2200.00, 22, 'images/box/box_op07.png'),
(21, 'One Piece Booster Box OP-08 Two Legends', 'Booster Box', 'Sealed 24-pack box. Features Manga Silvers Rayleigh and Whitebeard prime commander cards.', 2320.00, 19, 'images/box/box_op08.png'),
(22, 'One Piece Booster Box OP-09 The New Four Emperors', 'Booster Box', 'Sealed 24-pack 2nd Anniversary box featuring Manga Shanks and Four Emperors gold specials.', 2450.00, 5, 'images/box/box_op09.png'),
(23, 'One Piece Booster Box OP-10 Royal Blood', 'Booster Box', 'Factory sealed box containing 24 booster packs of Royal Blood with premium foil cards.', 2280.00, 25, 'images/box/box_op10.png'),
(24, 'One Piece Booster Box OP-11 A New Era Begins', 'Booster Box', 'Factory sealed box with 24 booster packs. Essential for top-tier competitive tournament players.', 2350.00, 30, 'images/box/box_op11.png'),
(25, 'One Piece Booster Box OP-12 Legacy of the Pirate King', 'Booster Box', 'Factory sealed box with 24 booster packs capturing Gol D. Roger legacy and epic naval clashes.', 2250.00, 14, 'images/box/box_op12.png'),
(26, 'One Piece Booster Box OP-13 Dawn of the New Age', 'Booster Box', 'The newest release sealed box with 24 booster packs of high-demand OP-13 cards.', 2420.00, 35, 'images/box/box_op13.png');

-- 3. Carton Category (Cases containing 12 sealed booster boxes)
INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock`, `image_url`) VALUES
(27, 'One Piece Master Carton OP-01 Romance Dawn [12 Boxes]', 'Carton', 'Official sealed wholesale case containing 12 factory-sealed Booster Boxes of Romance Dawn. Maximum chase rate.', 27500.00, 3, 'images/box/box_op01.png'),
(28, 'One Piece Master Carton OP-05 Awakening of the New Era [12 Boxes]', 'Carton', 'Official sealed wholesale case of 12 Booster Boxes. Highest value sealed case in One Piece TCG history.', 28900.00, 2, 'images/box/box_op05.png'),
(29, 'One Piece Master Carton OP-09 The New Four Emperors [12 Boxes]', 'Carton', 'Official sealed case of 12 Booster Boxes celebrating the 2nd Anniversary with all 4 Emperors.', 28500.00, 4, 'images/box/box_op09.png');

-- ========================================================
-- Seed Data: Sample Orders & Items for Reports Demonstration
-- ========================================================
INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `shipping_address`, `payment_method`, `total_amount`, `status`, `created_at`) VALUES
(1001, 2, 'Monkey D. Luffy', 'luffy@strawhat.com', 'Thousand Sunny, Grand Line Dock 1', 'PromptPay QR', 4900.00, 'Completed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1002, 2, 'Roronoa Zoro', 'zoro@strawhat.com', 'Shimotsuki Dojo, East Blue', 'Bank Transfer', 2450.00, 'Completed', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1003, 2, 'Nami Navigator', 'nami@strawhat.com', 'Cocoyasi Village, Tangerine Groves', 'PromptPay QR', 28900.00, 'Completed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1004, 2, 'Trafalgar Law', 'law@heartpirates.com', 'Submarine Polar Tang, North Blue', 'PromptPay QR', 4700.00, 'Completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1005, 2, 'Eustass Kid', 'kid@kidpirates.com', 'Victoria Punk, South Blue', 'Credit Card', 2350.00, 'Completed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1006, 2, 'Monkey D. Luffy', 'luffy@strawhat.com', 'Wano Country, Flower Capital', 'PromptPay QR', 975.00, 'Completed', NOW());

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `subtotal`) VALUES
(1001, 18, 'One Piece Booster Box OP-05 Awakening of the New Era', 2, 2450.00, 4900.00),
(1002, 22, 'One Piece Booster Box OP-09 The New Four Emperors', 1, 2450.00, 2450.00),
(1003, 28, 'One Piece Master Carton OP-05 Awakening of the New Era [12 Boxes]', 1, 28900.00, 28900.00),
(1004, 14, 'One Piece Booster Box OP-01 Romance Dawn', 2, 2350.00, 4700.00),
(1005, 24, 'One Piece Booster Box OP-11 A New Era Begins', 1, 2350.00, 2350.00),
(1006, 1, 'One Piece Booster Pack OP-01 Romance Dawn', 5, 95.00, 475.00),
(1006, 5, 'One Piece Booster Pack OP-05 Awakening of the New Era', 5, 100.00, 500.00);

