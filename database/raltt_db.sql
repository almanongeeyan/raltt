-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 04:27 PM
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
-- Database: `raltt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Deparo', 14.75243153, 121.01763335, '2025-08-25 14:16:31'),
(2, 'Vanguard', 14.75920200, 121.06286101, '2025-08-25 14:16:31'),
(3, 'Brixton', 14.76724928, 121.04104486, '2025-08-25 14:16:31'),
(4, 'Samaria', 14.75988501, 121.05887923, '2025-08-25 14:16:31'),
(5, 'Kiko', 14.75645265, 121.05822616, '2025-08-25 14:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `branch_staff`
--

CREATE TABLE `branch_staff` (
  `staff_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_staff`
--

INSERT INTO `branch_staff` (`staff_id`, `branch_id`, `username`, `password_hash`, `email`) VALUES
(6, 1, 'deparobranchstaff', '$2y$10$cc9d4aUZ.Rywx.SLtT1Y0uxeOoEQG2Mh.niLte1YAWx/rBx.pvtHq', NULL),
(7, 2, 'vanguardbranchstaff', '$2y$10$9xWfPQ4YDDo3yjnfifaA1ugWBt.8XrU4buU7S3cwbGLUt/VsX0tXe', NULL),
(8, 3, 'brixtonbranchstaff', '$2y$10$mMSg/n3rIzSVVWnEQ7Q1p.EcnmFQSveWID24.aQr.ivHnGdtoHuGC', NULL),
(9, 4, 'samariabranchstaff', '$2y$10$4zvGgh0xwW4urpvkMMj62eVupUx27h3P5ebrFllqA3SxabeFGI72q', NULL),
(10, 5, 'kikobranchstaff', '$2y$10$qAmTkHbEombAXoX/ERi.iulEVKch6toVqww78aHUfavDymcAhVM3y', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `google_accounts`
--

CREATE TABLE `google_accounts` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `house_address` varchar(255) DEFAULT NULL,
  `full_address` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `referral_code` varchar(100) DEFAULT NULL,
  `referral_coins` int(11) DEFAULT 0,
  `referral_count` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `google_accounts`
--

INSERT INTO `google_accounts` (`id`, `google_id`, `full_name`, `email`, `phone_number`, `house_address`, `full_address`, `created_at`, `last_login`, `referral_code`, `referral_coins`, `referral_count`) VALUES
(3, '100915058228832831360', 'Just Don\'t Let Mom Just Do', 'almanonsinb@gmail.com', NULL, NULL, NULL, '2025-08-25 11:26:39', '2025-08-26 15:57:56', '0hIFC5', 0, 1),
(4, '102646258648190989265', 'Gyan Gee Almanon', 'almanongeeyan@gmail.com', NULL, NULL, NULL, '2025-08-25 14:32:05', NULL, 'AL22vM', 0, 1);

--
-- Triggers `google_accounts`
--
DELIMITER $$
CREATE TRIGGER `referral_code_googlecreate` BEFORE INSERT ON `google_accounts` FOR EACH ROW BEGIN
    IF NEW.referral_code IS NULL OR NEW.referral_code = '' THEN
        SET NEW.referral_code = CONCAT(
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1)
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `manual_accounts`
--

CREATE TABLE `manual_accounts` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `house_address` varchar(255) DEFAULT NULL,
  `full_address` varchar(500) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `referral_code` varchar(100) DEFAULT NULL,
  `referral_coins` int(11) DEFAULT 0,
  `referral_count` int(11) DEFAULT 1,
  `account_status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manual_accounts`
--

INSERT INTO `manual_accounts` (`id`, `user_id`, `full_name`, `phone_number`, `house_address`, `full_address`, `password_hash`, `created_at`, `updated_at`, `last_login`, `referral_code`, `referral_coins`, `referral_count`, `account_status`) VALUES
(1, 'user_9f5b7189afeadf91', 'Gyan Gee Almanon', '+639916354208', 'Ph7b Pkg4 Lot3 Blk21 Solido St.', 'Solido Street, Barangay 176-C, Zone 15, Bagong Silang, District 1, Caloocan, Northern Manila District, Metro Manila, 1438, Philippines', '$2y$10$iXxXaex3283YmohuVwGimOK16C5vEcZTDZuQgeQDWBu9NkBHaoLa.', '2025-08-24 23:25:09', '2025-08-25 22:52:53', '2025-08-25 22:52:53', 'UrdTFi', 79, 1, 'active'),
(2, 'user_8110fd1ddc781ed4', 'Gyan Gee Almanon', '+639916354209', 'Ph7b Pkg4 Lot3 Blk21 Solido St.', 'Solido Street, Barangay 176-C, Zone 15, Bagong Silang, District 1, Caloocan, Northern Manila District, Metro Manila, 1438, Philippines', '$2y$10$/Th/U2x.5vQ3Go723knadO3tRoTgTaCJjhCXHgEPIN2uMGpiaWRw6', '2025-08-25 12:01:45', '2025-08-26 17:50:55', '2025-08-26 17:50:55', 'PPg2ya', 0, 1, 'active');

--
-- Triggers `manual_accounts`
--
DELIMITER $$
CREATE TRIGGER `referral_code_manualcreate` BEFORE INSERT ON `manual_accounts` FOR EACH ROW BEGIN
    IF NEW.referral_code IS NULL OR NEW.referral_code = '' THEN
        SET NEW.referral_code = CONCAT(
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1),
            SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', FLOOR(1 + (RAND() * 62)), 1)
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_type` enum('tile','other') NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_image` mediumblob DEFAULT NULL,
  `product_spec` text DEFAULT NULL,
  `tile_type` varchar(100) DEFAULT NULL,
  `tile_design` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_hot` tinyint(1) DEFAULT 0,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_branches`
--

CREATE TABLE `product_branches` (
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `stock_count` int(11) DEFAULT 0,
  `last_restock_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `branch_staff`
--
ALTER TABLE `branch_staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `google_accounts`
--
ALTER TABLE `google_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `product_branches`
--
ALTER TABLE `product_branches`
  ADD PRIMARY KEY (`product_id`,`branch_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branch_staff`
--
ALTER TABLE `branch_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `google_accounts`
--
ALTER TABLE `google_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_staff`
--
ALTER TABLE `branch_staff`
  ADD CONSTRAINT `branch_staff_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_branches`
--
ALTER TABLE `product_branches`
  ADD CONSTRAINT `product_branches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_branches_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
