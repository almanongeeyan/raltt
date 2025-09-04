-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 03:38 PM
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
(4, 'Samaria', 14.76580311, 121.06563606, '2025-08-25 14:16:31'),
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
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `sku` varchar(50) DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
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

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tile_categories`
--

CREATE TABLE `tile_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tile_categories`
--

INSERT INTO `tile_categories` (`category_id`, `category_name`) VALUES
(4, 'Black and White'),
(2, 'Floral'),
(3, 'Indoor'),
(1, 'Minimalist'),
(5, 'Modern'),
(6, 'Pool');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `house_address` varchar(255) DEFAULT NULL,
  `full_address` varchar(255) DEFAULT NULL,
  `referral_code` varchar(10) DEFAULT NULL,
  `referral_coins` int(11) DEFAULT 0,
  `has_used_referral_code` varchar(12) DEFAULT 'FALSE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `account_status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `password_hash`, `full_name`, `email`, `phone_number`, `house_address`, `full_address`, `referral_code`, `referral_coins`, `has_used_referral_code`, `created_at`, `updated_at`, `last_login`, `account_status`) VALUES
(2, NULL, '$2y$10$fBdraO34pWDovpDY5RwFKuqwZAWKQFWBAgasjJd8hx.mColV/JBCq', 'Gyan Gee Almanon', '', '+639916354209', 'Ph7b Pkg4 Lot3 Blk21 Solido St.', 'Solido Street, Barangay 176-C, Zone 15, Bagong Silang, District 1, Caloocan, Northern Manila District, Metro Manila, 1438, Philippines', '', 0, '0', '2025-08-29 12:49:26', '2025-08-29 13:10:36', '2025-08-29 13:10:36', 'active'),
(4, '100915058228832831360', NULL, 'Just Don\'t Let Mom Just Do', 'almanonsinb@gmail.com', NULL, NULL, NULL, 'roTceP', 95, 'FALSE', '2025-08-29 13:29:31', '2025-08-30 13:03:30', '2025-08-30 13:03:30', 'active'),
(5, '102646258648190989265', NULL, 'Gyan Gee Almanon', 'almanongeeyan@gmail.com', NULL, NULL, NULL, 'fFmQWH', 135, 'FALSE', '2025-08-29 13:40:24', '2025-08-30 12:45:09', '2025-08-30 12:40:23', 'active');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    DECLARE new_referral_code VARCHAR(6);
    DECLARE code_exists INT;
    DECLARE alphabet VARCHAR(52) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    -- Loop until a unique code is found
    REPEAT
        -- Generate a new 6-character code
        SET new_referral_code = '';
        SET code_exists = 0;
        
        SET new_referral_code = CONCAT(
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1),
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1),
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1),
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1),
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1),
            SUBSTRING(alphabet, 1 + FLOOR(RAND() * 52), 1)
        );

        -- Check if the generated code already exists in the table
        SELECT COUNT(*) INTO code_exists
        FROM users
        WHERE referral_code = new_referral_code;

    UNTIL code_exists = 0 END REPEAT;

    -- Assign the unique code to the new user
    SET NEW.referral_code = new_referral_code;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_recommendations`
--

CREATE TABLE `user_recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_recommendations`
--

INSERT INTO `user_recommendations` (`recommendation_id`, `user_id`, `category_id`, `rank`) VALUES
(16, 4, 2, 1),
(17, 4, 4, 2),
(18, 4, 3, 3);

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
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

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
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tile_categories`
--
ALTER TABLE `tile_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD UNIQUE KEY `referral_code` (`referral_code`);

--
-- Indexes for table `user_recommendations`
--
ALTER TABLE `user_recommendations`
  ADD PRIMARY KEY (`recommendation_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`rank`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tile_categories`
--
ALTER TABLE `tile_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_recommendations`
--
ALTER TABLE `user_recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_staff`
--
ALTER TABLE `branch_staff`
  ADD CONSTRAINT `branch_staff_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `product_branches`
--
ALTER TABLE `product_branches`
  ADD CONSTRAINT `product_branches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_branches_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `tile_categories` (`category_id`);

--
-- Constraints for table `user_recommendations`
--
ALTER TABLE `user_recommendations`
  ADD CONSTRAINT `user_recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_recommendations_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `tile_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
