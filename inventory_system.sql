-- Drop Tables if They Exist
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;


-- Create Tables
CREATE TABLE `categories` (
  `id` INT unsigned NOT NULL,
  `name` varchar(60) NOT NULL
);

CREATE TABLE `media` (
  `id` INT unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL
);

CREATE TABLE `products` (
  `id` INT unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `buy_price` decimal(25,2) DEFAULT NULL,
  `sale_price` decimal(25,2) NOT NULL,
  `category_id` INT unsigned NOT NULL,
  `media_id` INT unsigned DEFAULT 0,
  `date` datetime NOT NULL
);

CREATE TABLE `sales` (
  `id` INT unsigned NOT NULL,
  `product_id` INT unsigned NOT NULL,
  `qty` INT NOT NULL,
  `total_sales` decimal(25,2) NOT NULL,
  `date` date NOT NULL
);

CREATE TABLE `users` (
  `id` INT unsigned NOT NULL,
  `name` VARCHAR(60) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `last_login` DATETIME DEFAULT NULL
);

-- Add Primary Key, AUTO_INCREMENT, and Indexes
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `media_id` (`media_id`),
  MODIFY `id` INT unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  MODIFY `id` INT unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT unsigned NOT NULL AUTO_INCREMENT;

-- Add Foreign Key Constraints
ALTER TABLE `products`
  ADD CONSTRAINT `FK_products` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sales`
  ADD CONSTRAINT `FK_sales` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Insert Categories
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Demo Category'),
(2, 'Raw Materials'),
(3, 'Finished Goods'),
(4, 'Packing Materials'),
(5, 'Machinery'),
(6, 'Work in Progress'),
(7, 'Stationery Items'),
(8, 'Jansen Venal');

-- Insert Products
INSERT INTO `products` (`name`, `quantity`, `buy_price`, `sale_price`, `category_id`, `media_id`, `date`) VALUES
('Demo Product', '48', '100.00', '500.00', 1, 0, '2021-04-04 16:45:51'),
('Box Varieties', '12000', '55.00', '130.00', 4, 0, '2021-04-04 18:44:52'),
('Wheat', '69', '2.00', '5.00', 2, 0, '2021-04-04 18:48:53'),
('Timber', '1200', '780.00', '1069.00', 2, 0, '2021-04-04 19:03:23'),
('W1848 Oscillating Floor Drill Press', '26', '299.00', '494.00', 5, 0, '2021-04-04 19:11:30'),
('Portable Band Saw XBP02Z', '42', '280.00', '415.00', 5, 0, '2021-04-04 19:13:35'),
('Life Breakfast Cereal-3 Pk', '107', '3.00', '7.00', 3, 0, '2021-04-04 19:15:38'),
('Chicken of the Sea Sardines W', '110', '13.00', '20.00', 3, 0, '2021-04-04 19:17:11'),
('Disney Woody - Action Figure', '67', '29.00', '55.00', 3, 0, '2021-04-04 19:19:20'),
('Hasbro Marvel Legends Series Toys', '106', '219.00', '322.00', 3, 0, '2021-04-04 19:20:28'),
('Packing Chips', '78', '21.00', '31.00', 4, 0, '2021-04-04 19:25:22'),
('Classic Desktop Tape Dispenser 38', '160', '5.00', '10.00', 8, 0, '2021-04-04 19:48:01'),
('Small Bubble Cushioning Wrap', '199', '8.00', '19.00', 4, 0, '2021-04-04 19:49:00');

-- Insert Sales
INSERT INTO `sales` (`product_id`, `qty`, `total_sales`, `date`) VALUES
(1, 2, '1000.00', '2021-04-04'),
(3, 3, '15.00', '2021-04-04'),
(10, 6, '1932.00', '2021-04-04'),
(6, 2, '830.00', '2021-04-04'),
(12, 5, '50.00', '2021-04-04'),
(13, 21, '399.00', '2021-04-04'),
(7, 5, '35.00', '2021-04-04'),
(2, 5, '650.00', '2021-04-04');

-- Users
INSERT INTO `users` (`name`, `username`, `password_hash`) VALUES
('Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3');
