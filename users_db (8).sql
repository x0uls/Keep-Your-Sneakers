-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 12:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `users_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `type` enum('billing','shipping') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_line1`, `address_line2`, `city`, `postal_code`, `country`, `type`) VALUES
(1, 30, 'B-07-05', 'PPR PINGGIRAN BUKIT JALIL', 'Kuala Lumpur', '58000', 'Malaysia', 'billing'),
(4, 31, 'B-07-05', 'PPR PINGGIRAN BUKIT JALIL', 'Kuala Lumpur', '58000', 'Malaysia', 'billing');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Men'),
(2, 'Women'),
(3, 'Kids');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Canceled','Failed') DEFAULT 'Paid',
  `shipping_status` enum('Pending','Shipped','In Transit','Delivered') DEFAULT 'Pending',
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `payment_id`, `order_date`, `payment_status`, `shipping_status`, `address`) VALUES
(10, 30, 'pi_3RHSONQM571Me8gB0s6saUdu', '2025-04-24 16:11:05', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(11, 30, 'pi_3RHjH2QM571Me8gB102btBrK', '2025-04-25 10:12:37', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(12, 30, 'pi_3RHjIAQM571Me8gB0Lz9zr8r', '2025-04-25 10:13:48', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(13, 30, 'pi_3RHjKRQM571Me8gB0Pr1SDt6', '2025-04-25 10:16:08', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(14, 30, 'pi_3RHjNjQM571Me8gB0xjhUiX8', '2025-04-25 10:19:33', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(15, 30, 'pi_3RHjQMQM571Me8gB0eCrp7iq', '2025-04-25 10:22:16', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(16, 30, 'pi_3RHjViQM571Me8gB0IABin0e', '2025-04-25 10:27:48', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(17, 30, 'pi_3RHjXDQM571Me8gB0RuTuB2B', '2025-04-25 10:29:20', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(18, 30, 'pi_3RHjbsQM571Me8gB0jAquBss', '2025-04-25 10:34:10', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(20, 30, 'pi_3RHjgYQM571Me8gB0s8uzF2h', '2025-04-25 10:38:59', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(21, 30, 'pi_3RHjiOQM571Me8gB08lbVowV', '2025-04-25 10:40:53', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(22, 30, 'pi_3RHjkUQM571Me8gB0dGFxEy7', '2025-04-25 10:43:02', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(23, 30, 'pi_3RHjsPQM571Me8gB0tY72uA3', '2025-04-25 10:51:12', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(24, 30, 'pi_3RHZC0QM571Me8gB0K4YKzr2', '2025-04-25 10:57:41', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(25, 30, 'pi_3RHjzBQM571Me8gB0H7bWBCp', '2025-04-25 10:58:13', 'Paid', 'Delivered', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(29, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:35', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(30, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:41', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(31, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:45', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(32, 30, 'pi_3RI6VjQM571Me8gB0Te0Mqdx', '2025-04-26 11:03:14', 'Paid', 'Shipped', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(33, 30, 'pi_3RISiNQM571Me8gB0d7cEg1l', '2025-04-27 10:45:51', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sold_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `size_label` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `size_label`, `category_id`) VALUES
(1, 'UK 6 (EU 40)', 1),
(2, 'UK 6.5', 1),
(3, 'UK 7', 1),
(4, 'UK 7.5', 1),
(5, 'UK 8', 1),
(6, 'UK 8.5', 1),
(7, 'UK 9', 1),
(8, 'UK 9.5', 1),
(9, 'UK 10', 1),
(10, 'UK 10.5', 1),
(11, 'UK 11', 1),
(12, 'UK 12', 1),
(13, 'UK 2.5', 2),
(14, 'UK 3', 2),
(15, 'UK 3.5', 2),
(16, 'UK 4', 2),
(17, 'UK 4.5', 2),
(18, 'UK 5', 2),
(19, 'UK 5.5', 2),
(20, 'UK 6', 2),
(21, 'UK 6.5', 2),
(22, 'UK 7', 2),
(23, 'UK 7.5', 2),
(24, 'UK 3', 3),
(25, 'UK 3.5', 3),
(26, 'UK 4', 3),
(27, 'UK 4.5', 3),
(28, 'UK 5', 3),
(29, 'UK 5.5', 3),
(30, 'UK 6 (EU 39)', 3),
(31, 'UK 6 (EU 40)', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_type` enum('reset') NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `token`, `token_type`, `expires_at`) VALUES
(13, 30, 'e8a05dd98a2ece54d7c03778b37c37049034ade1', 'reset', '2025-04-20 09:30:59'),
(14, 30, '781fb289424c3754545a64bad26d6e68fbe119d9', 'reset', '2025-04-20 09:51:56'),
(15, 30, '25ac3bba5cc391ba361c193414699ba66b8b1add', 'reset', '2025-04-20 09:55:07'),
(17, 30, 'b9fa248a5e9ad7bed54c8a962fb3826c87948994', 'reset', '2025-04-20 09:55:32'),
(18, 30, '940fd39598126bd550fec064bd92ec0b61510ae8', 'reset', '2025-04-20 10:00:56'),
(19, 30, 'd3d25135fc6ca462158f5135f37334200b77429c', 'reset', '2025-04-20 10:01:01'),
(20, 30, '9bdbbc5b185f9f6b7552403e7d354b76b5f86b4e', 'reset', '2025-04-20 10:02:27'),
(21, 30, '954d40d2f90fdf596f5c135256a03e1ce54d47c9', '', '2025-04-20 10:06:57'),
(22, 30, '60fc3b01023d6544610cc966a5bd4579c09989a7', 'reset', '2025-04-20 10:10:08'),
(23, 30, '37602c1c7cd704d49e76e76128a4a270197cdea1', '', '2025-04-20 12:48:06'),
(24, 30, '582c16696e38951af08ed71e887a0097b85ecdad', 'reset', '2025-04-20 12:49:45'),
(25, 30, '5314e4729d1254c95bd4405ba0207de62749bfe2', 'reset', '2025-04-22 15:39:54'),
(26, 30, 'a16922727fc45744156634b7895a5cdb436a7a25', 'reset', '2025-04-26 14:02:33'),
(27, 30, '078da61b0525e927acfa671d146716aae687dd3c', 'reset', '2025-04-26 14:02:37'),
(28, 30, '6c3313121faa86535dc56f2d38f08736e68823bb', 'reset', '2025-04-26 14:02:40'),
(29, 30, 'f65f22a34098d585bd09e3cd6e3a1fa4f3dd976c', 'reset', '2025-04-26 14:02:43'),
(30, 30, '7f322a59d285456752255a20653e72a02aa6cb97', 'reset', '2025-04-26 14:02:47'),
(31, 30, 'beee0dd38e6030cacf1437e4a8fa74a37a540ef7', 'reset', '2025-04-26 14:02:49'),
(32, 30, '475dbe4f1cb087aec03620db699389e3b3bc4d08', 'reset', '2025-04-26 14:02:52'),
(33, 30, 'c05ffbecca81b3222440df0f2c884394b437cbad', 'reset', '2025-04-26 14:02:55'),
(34, 30, '4b318338cb7da483f949f7062eb9a778f5c681a5', 'reset', '2025-04-26 14:02:58'),
(35, 30, '24bf9a13c617ce2dc8af4b7ceb3c44ed3f2b97c3', 'reset', '2025-04-26 14:03:01'),
(36, 30, '9d906f53566b5c304c863250f172a578ae5000ee', 'reset', '2025-04-26 14:03:04'),
(37, 30, '8db2ff7a9b8d3ec8717c440493eb813759d75f2a', 'reset', '2025-04-26 14:03:07'),
(38, 30, '3b0d65ce1bf012e3699a71fc3c42b8897a3d3c25', 'reset', '2025-04-26 14:03:10'),
(39, 30, 'f06244723d051428c09d2c13160348bf946070e2', 'reset', '2025-04-26 14:03:13'),
(40, 30, '53f6a38be3918a5d6d2ae383bb8d84e168ccee30', 'reset', '2025-04-26 14:03:15'),
(41, 30, '515f873a6ebb6ccb3ed6c44f7b4cea9b2c9ba946', 'reset', '2025-04-26 14:03:18'),
(42, 30, 'c8104b0192d501d2033334edb0a0bde32ad6ca0e', 'reset', '2025-04-26 14:03:21'),
(43, 30, 'e9c8e069e6c6d74d08fc80a60a3cf855d4bfceed', 'reset', '2025-04-26 14:03:45'),
(45, 30, '7e45eb324066fcdf90c73a6f2a0ee444ce2a8730', 'reset', '2025-04-27 12:16:37'),
(46, 30, 'dde8193547a199a681cc2c2d249f1a04eb24e9c2', 'reset', '2025-04-27 12:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `profile_picture` varchar(100) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_attempt_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `is_admin`, `profile_picture`, `failed_attempts`, `last_attempt_time`) VALUES
(30, 'x0uls', 'kennykoh20061027@gmail.com', '8ee396cfbac4eaf092c01855c35fdb9675535551', '2025-04-20 05:52:05', 0, '6809b9a422cbc.png', 0, NULL),
(31, 'admin1', 'admin@gmail.com', '9d25cff3b5eb849da42581b8011128d44027d07e', '2025-04-21 02:25:36', 1, '680cb92c5d1a0.png', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`,`size_id`),
  ADD UNIQUE KEY `unique_cart_combo` (`user_id`,`product_id`,`size_id`,`category_id`),
  ADD KEY `fk_cart_product` (`product_id`),
  ADD KEY `fk_cart_size` (`size_id`),
  ADD KEY `fk_cart_category` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`,`size_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `wishlist_ibfk_2` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`),
  ADD CONSTRAINT `fk_cart_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
