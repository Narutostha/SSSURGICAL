-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2024 at 04:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pendrive_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Gaming Laptop', 'Gaming Laptop', '2024-12-19 12:37:46'),
(2, 'Business Laptop', 'Business Laptop', '2024-12-19 12:37:46'),
(3, 'Student Laptop', 'Student Laptop', '2024-12-19 12:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Reyaham Shrestha', 'bhuwanshrestha475@gmail.com', '$2y$10$lGOcL0k49TKafH9GDDkOYO4yq8T7uxFMOAXzp6vzBfRuFQxYO200q', '2024-12-19 15:36:10'),
(2, 'Simosh Shrestha', 'demo@gmail.com', '$2y$10$w3AIavNYTkeEgzE0iUbsj.jLdrCsGHkPr/xZt9z6iRh21HUC7oXdq', '2024-12-20 13:28:08'),
(3, 'Luniya Shrestha', 'luniva@gmail.com', '$2y$10$VxHisdf9kZdRZuSx6iz3feRwchjBdC62olA.XD0JK9rmqUk7/rHx6', '2024-12-20 14:04:46'),
(4, 'Simosh Shrestha', 'simoshshrestha10@gmail.com', '$2y$10$A/JzaGTl452G97DhcDbgO.HJui6PMFGcMTsQDJbMdjcQ1p.4pvwpC', '2024-12-25 12:42:00'),
(5, 'Reyahaaa', '1@gmail.com', '$2y$10$l6MxH4U6F1Oqb4tGKTpGbeV6OQaADXwulc.dzw7WHuq8PH3KC5gKK', '2024-12-26 17:45:12'),
(6, 'Dinesh Bista', 'dineshjungbista@gmail.com', '$2y$10$o64Fs1BJ/2zUdkOZBGZZnuM6ll6l5Md3Xs9Pa3Fk3pTrnoVIo8KDS', '2024-12-27 04:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','esewa','khalti','fonepay') NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `delivery_status` enum('pending','processing','shipped','delivered') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `payment_method`, `payment_status`, `delivery_status`, `shipping_address`, `phone`, `created_at`, `user_id`) VALUES
(56, NULL, 155000.00, 'khalti', 'pending', 'processing', 'Kapan', '9804936669', '2024-12-27 06:18:14', 6),
(57, NULL, 110000.00, 'cod', 'completed', 'shipped', 'REYAHAM SHRESTHA', '9804936669', '2024-12-27 12:59:17', 5);

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    INSERT INTO order_tracking (order_id, status, description)
    VALUES (NEW.id, 'Order Placed', 'Your order has been received and is being processed.');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(34, 56, 29, 1, 155000.00),
(35, 57, 28, 1, 110000.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking`
--

CREATE TABLE `order_tracking` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Order Placed',
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_tracking`
--

INSERT INTO `order_tracking` (`id`, `order_id`, `status`, `location`, `description`, `timestamp`) VALUES
(26, 56, 'Order Placed', NULL, 'Your order has been received and is being processed.', '2024-12-27 06:18:14'),
(27, 56, 'Order Placed', NULL, 'Your order has been received and is being processed.', '2024-12-27 06:18:14'),
(28, 57, 'Order Placed', NULL, 'Your order has been received and is being processed.', '2024-12-27 12:59:17'),
(29, 57, 'Order Placed', NULL, 'Your order has been received and is being processed.', '2024-12-27 12:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `section` enum('information','customer_service','legal') NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `section`, `active`, `created_at`) VALUES
(1, 'About Us', 'about-us', NULL, 'information', 1, '2024-12-19 12:42:00'),
(2, 'Contact Us', 'contact-us', NULL, 'information', 1, '2024-12-19 12:42:00'),
(3, 'Privacy Policy', 'privacy-policy', NULL, 'legal', 1, '2024-12-19 12:42:00'),
(4, 'Terms & Conditions', 'terms-conditions', NULL, 'legal', 1, '2024-12-19 12:42:00'),
(5, 'Shipping Information', 'shipping', NULL, 'customer_service', 1, '2024-12-19 12:42:00'),
(6, 'Payment Methods', 'payment-methods', NULL, 'customer_service', 1, '2024-12-19 12:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `category_id`, `created_at`) VALUES
(26, 'Lenovo LOQ 15IRH8 (12th Gen Intel Core i5)', 'Key Specification:\r\n12th Generation Intel Core i5 12450H Processor\r\n8GB DDR5 RAM | 512GB NVMe SSD Storage\r\nNVIDIA GeForce RTX 2050 4GB Graphics Card\r\n15.6\" FHD (1920 x 1080p) 144Hz Display\r\nFree Laptop Bag, Gaming Mouse and Mousepad', 90000.00, 15, '1735237856_lenovo-loq-i5-12th-gen-price-in-nepal.webp', 1, '2024-12-26 18:30:56'),
(27, 'Acer Nitro V 15 2024 AMD Ryzen 5 7535HS', 'Key Features\r\nModel: Acer Nitro V 15 2024 \r\nProcessor: AMD Ryzen 5 7535HS Processor\r\nRAM: 8 GB DDR5 4800MHz\r\nStorage: 512GB SSD\r\nDisplay: 15.6\" display with IPS (In-Plane Switching) technology, Full HD 1920 x 1080, Acer ComfyView LED-backlit TFT LCD,16:9 aspect ratio, supporting 144 Hz refresh rate\r\nGraphic: NVIDIA® GeForce® RTX 2050 with 4GB of dedicated GDDR6 VRAM\r\nWarranty: 1 Year Warranty', 94000.00, 20, '1735238005_6ae59888-9a83-4351-83b5-a76353e0446c.webp', 1, '2024-12-26 18:33:25'),
(28, 'Apple M1 MacBook Air 2020', 'Key Features\r\nModel: Apple M1 MacBook Air 2020\r\nProcessor: Apple M1 Chip 8-core CPU\r\nRAM: 8GB unified memory\r\nStorage: 256GB SSD\r\nDisplay: 13.3-inch LED-backlit display, IPS, 400 nits\r\nGraphic: 7-core GPU\r\nWarranty: 1 Year Warranty', 110000.00, 3, '1735238106_289d5b32-14ea-4990-b234-7ce8f48a2b1f.webp', 2, '2024-12-26 18:35:06'),
(29, 'Apple M3 MacBook Air 2024 - Apple M3 Chip', 'Key Features\r\nModel: Apple M3 Air MacBook 2023\r\nProcessor: Apple M3 Chip 8-core CPU\r\nRAM: 8GB unified memory\r\nStorage: 256GB SSD\r\nDisplay: 13.6\" 2560 x 1664 Liquid Retina Display\r\nGraphic: 8-Core GPU | 16-Core Neural Engine\r\nWarranty: 1 Year Authorised Warranty\r\nspecifications\r\ndescription\r\nreviews', 155000.00, 22, '1735238167_059dc2dd-b301-4739-9ced-29e45ec4db82.webp', 2, '2024-12-26 18:36:07'),
(30, 'HP Notebook 15-FC0039WM Ryzen 5 7520U', 'Key Features\r\nModel: HP Notebook 15-FC0039WM\r\nProcessor: AMD Ryzen 5 7520U\r\nRAM: 8 GB DDR4-3200 MHz RAM\r\nStorage: 256GB SSD\r\nDisplay: 15.6-inch IPS display; Full-HD, 250nits\r\nGraphic: AMD Radeon Graphics\r\nWarranty: 1 Year Warranty', 62000.00, 14, '1735238279_].webp', 3, '2024-12-26 18:37:59'),
(31, 'Dell Inspiron 15 3530 - i5 1335U', 'Key Specifications:\r\nModel: Dell Inspiron 15 3530\r\nProcessor:  13th Gen Intel I5 1335U Processor\r\nRAM: 8GB DDR4 RAM\r\nStorage: 512GB SSD\r\nDisplay: 15.6-inch IPS display; Full-HD, 60hz\r\nGraphic: Intel UHD Graphics\r\nWarranty: 2 Year Warranty, Authorized Product', 88000.00, 152, '1735238340_40c25473-6aaf-466e-ab27-79adc94f7ccc.webp', 3, '2024-12-26 18:39:00'),
(32, 'Lenovo ThinkPad X13 Gen 3', 'Key Features\r\nModel: Lenovo ThinkPad X13 Gen 3 2022\r\nProcessor:  AMD Ryzen 5 Pro 6650U processor\r\nRAM: 16GB Soldered LPDDR5-6400\r\nStorage: 512GB SSD\r\nDisplay: 13.3\" WUXGA (1920x1200) IPS 300nits Anti-glare, 72% NTSC\r\nGraphic: Integrated AMD Radeon™ 660M Graphics\r\nWarranty: 1 Year Warranty', 127000.00, 14, '1735238396_ec154b6f-b1de-4d14-a782-6fb90bb64946.webp', 2, '2024-12-26 18:39:56'),
(33, 'Apple M2 MacBook Air 2022', 'Key Features\r\nModel: Apple M2 Air MacBook 2022\r\nProcessor: Apple M2 Chip 8-core CPU\r\nRAM: 8GB unified memory\r\nStorage: 256GB SSD\r\nDisplay: 13.6-inch (diagonal) LED-backlit \r\nGraphic: 10-core GPU\r\nWarranty: 1 Year Warranty', 139000.00, 100, '1735303338_ead0ef4e-9701-495b-a0ba-10f3f35df928.webp', 2, '2024-12-27 12:42:18'),
(34, 'Apple M2 MacBook Air - Apple M2 Chip', 'Key Features\r\nModel: Apple M2 MacBook Air 15 2023 \r\nProcessor: Apple M2 Chip 8-core CPU\r\nRAM: 8GB unified memory\r\nStorage: 256GB SSD\r\nDisplay: 15.3-inch (diagonal) LED-backlit \r\nGraphic: 10-core GPU\r\nWarranty: 1 Year Warranty', 199000.00, 100, '1735303450_85ddd4dd-b444-40b5-940f-54c93efb4e07.webp', 2, '2024-12-27 12:43:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `phone`, `address`, `is_admin`, `created_at`, `last_login`) VALUES
(9, '1', '$2y$10$8d4oEcNzHhnHdaYZDy4UAunEp3WkOGGn9c1SrjeCbJrbF2MQtC4U.', NULL, NULL, NULL, 1, '2024-12-26 17:59:00', NULL),
(13, '123', '$2y$10$H8kJlOOOp0GgX2V/bRsTfemFXXPszwcIDfOg00tQ5KYi9trjX0ltu', NULL, NULL, NULL, 1, '2024-12-28 03:13:34', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_customer_fk` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
