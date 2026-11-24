-- SalesPro Database Schema and Seed Data
-- Database Name: salespro

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `salespro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `salespro`;

-- Drop existing tables
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `subscribers`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `team_members`;

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `image_url` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Products Table
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `description` TEXT,
  `image_url` VARCHAR(500),
  `is_featured` TINYINT(1) DEFAULT 0,
  `stock` INT DEFAULT 50,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Orders Table
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(30) NOT NULL UNIQUE,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Cash on Delivery',
  `subtotal` DECIMAL(10,2) NOT NULL,
  `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Order Items Table
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT,
  `product_name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `total_price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Contact Messages Table
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `topic` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Newsletter Subscribers Table
CREATE TABLE `subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Admins Table
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Seed Data
-- -------------------------------------------------------------

-- Insert Categories
INSERT INTO `categories` (`id`, `slug`, `name`, `description`, `image_url`) VALUES
(1, 'laptops', 'Laptops', 'Find the right laptop for your everyday.', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85'),
(2, 'desktops', 'Desktops', 'Powerful performance for work and home.', 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?auto=format&fit=crop&w=900&q=85'),
(3, 'printers', 'Printers', 'Print the things that matter to you.', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=900&q=85'),
(4, 'monitors', 'Monitors', 'Build a setup that works for you.', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=85'),
(5, 'accessories', 'Accessories', 'Keyboards, docks, mice and essential tech add-ons.', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=85');

-- Insert Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `price`, `description`, `image_url`, `is_featured`, `stock`) VALUES
(1, 1, 'SalesPro Book 14', 'salespro-book-14', 799.00, 'Light, fast and built for all-day battery life.', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85', 1, 45),
(2, 1, 'SalesPro Book 15 Pro', 'salespro-book-15-pro', 1199.00, 'Creator-ready display and a powerful processor.', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=85', 0, 30),
(3, 1, 'SalesPro Flip 13', 'salespro-flip-13', 999.00, '2-in-1 touchscreen that adapts to how you work.', 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=900&q=85', 0, 25),
(4, 2, 'SalesPro Tower Plus', 'salespro-tower-plus', 949.00, 'Expandable performance for the home office.', 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?auto=format&fit=crop&w=900&q=85', 1, 20),
(5, 2, 'SalesPro All-in-One 24', 'salespro-all-in-one-24', 879.00, 'Clean, space-saving design with a 24\" display.', 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=900&q=85', 0, 15),
(6, 2, 'SalesPro Mini Desktop', 'salespro-mini-desktop', 549.00, 'Compact power that fits almost anywhere.', 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=900&q=85', 0, 35),
(7, 3, 'SalesPro LaserJet 200', 'salespro-laserjet-200', 229.00, 'Fast, sharp black and white printing.', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=900&q=85', 0, 40),
(8, 3, 'SalesPro Ink Tank Wireless', 'salespro-ink-tank-wireless', 279.00, 'Print, scan and copy at very low cost per page.', 'https://images.unsplash.com/photo-1588619461234-9271a7eb5136?auto=format&fit=crop&w=900&q=85', 1, 28),
(9, 4, 'SalesPro 27\" QHD Monitor', 'salespro-27-qhd-monitor', 329.00, 'Crisp colour and thin bezels for focused work.', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=85', 1, 50),
(10, 4, 'SalesPro 32\" 4K Monitor', 'salespro-32-4k-monitor', 549.00, 'Sharp 4K detail with USB-C charging.', 'https://images.unsplash.com/photo-1547119957-637f8679db1e?auto=format&fit=crop&w=900&q=85', 0, 18),
(11, 5, 'SalesPro Wireless Keyboard & Mouse', 'salespro-wireless-keyboard-mouse', 59.00, 'Quiet keys and a comfortable, precise mouse.', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=85', 0, 80),
(12, 5, 'SalesPro USB-C Dock', 'salespro-usb-c-dock', 149.00, 'One cable for power, display and everything else.', 'https://images.unsplash.com/photo-1544652478-6653e09f18a2?auto=format&fit=crop&w=900&q=85', 0, 60);

-- Insert Default Admin (Password: admin123)
INSERT INTO `admins` (`id`, `username`, `password`, `name`, `email`) VALUES
(1, 'admin', '$2y$10$yMcv6.lQwfWqd9v4eE64buzwE7AjaOsguPiaAj/90yGEV5hNDaIpu', 'SalesPro Administrator', 'admin@salespro.example');

-- Insert Initial Sample Subscriber
INSERT INTO `subscribers` (`id`, `email`) VALUES
(1, 'info@salespro.example');

SET FOREIGN_KEY_CHECKS = 1;

