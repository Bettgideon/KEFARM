-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2025 at 08:48 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kefarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `farm_items`
--

CREATE TABLE `farm_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `farm_items`
--

INSERT INTO `farm_items` (`id`, `name`, `category`, `quantity`, `price`, `description`, `created_at`) VALUES
(2, 'Asia', 'Crop', 2000, '27770.00', 'Much', '2025-03-21 19:50:50'),
(3, 'Asia', 'Crop', 200, '27770.00', 'Much', '2025-03-21 19:50:59'),
(8, 'Freasian', 'Livestock', 200, '100000.00', 'Milk', '2025-03-22 07:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `stock_level_alert` int(11) DEFAULT '10',
  `supplier` varchar(255) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_name`, `category`, `quantity`, `unit_price`, `stock_level_alert`, `supplier`, `added_at`, `updated_at`) VALUES
(1, 'Cassava', 'Food', 30000, '400000.00', NULL, NULL, '2025-03-17 16:41:21', '2025-03-22 20:00:27'),
(2, 'Maize', 'Farm input', 12, '40000.00', NULL, NULL, '2025-03-17 16:45:50', '2025-03-18 07:47:25'),
(4, 'Beans', 'Grains', 2000, '30000.00', NULL, NULL, '2025-03-19 04:45:26', '2025-03-19 04:45:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_audit`
--

CREATE TABLE `inventory_audit` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `change_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `product_name`, `quantity`, `total_price`, `order_date`, `status`) VALUES
(1, 'Gideon', 'Maize', 3000, '200000.00', '2025-03-16 08:50:04', 'Pending'),
(2, 'Enock', 'Goats', 20000, '100000.00', '2025-03-16 09:39:35', 'Completed'),
(3, 'Sandra', 'Chicken', 2000, '1200000.00', '2025-03-16 10:10:38', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Gideon Bett', 'kiprotichgideonbett@gmail.com', '$2y$10$zwP0JZzckMVIzHt1jeV31uXfkSQSAEZaQSmQI9m/4/ybtrmsjnz8q', '2025-03-14 09:52:28'),
(2, 'Gideon Bett', 'gedionbett@kibwezo.co.ke', '$2y$10$AikXSX8gU0W7jMyAegAm8uMTO2LxTKIsSQDvh74qSFyEflDloR11m', '2025-03-14 10:00:07'),
(3, 'Gideon Bett', 'gidcomtechnologies@gmail.com', '$2y$10$npsnvmeqwYQ5zmLCvmkeZu4ikczNUZfHQlhI/a0/zitpAGjS0U0wq', '2025-03-14 10:04:13'),
(4, 'Gideon', 'charlse@gmail.com', '$2y$10$Wyz8vlXqX4kVGMgv6xaztu3fG4xCk4kqJkk7qhga9Eq3DhR2EgtL.', '2025-03-14 10:13:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `farm_items`
--
ALTER TABLE `farm_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_audit`
--
ALTER TABLE `inventory_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_id` (`inventory_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `farm_items`
--
ALTER TABLE `farm_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory_audit`
--
ALTER TABLE `inventory_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_audit`
--
ALTER TABLE `inventory_audit`
  ADD CONSTRAINT `inventory_audit_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_audit_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
