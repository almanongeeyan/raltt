-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2025 at 11:41 AM
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
-- Table structure for table `google_accounts`
--

CREATE TABLE `google_accounts` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `profile_picture` text DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `google_accounts`
--

INSERT INTO `google_accounts` (`id`, `google_id`, `name`, `email`, `profile_picture`, `number`, `created_at`) VALUES
(1, '100915058228832831360', 'Just Don\'t Let Mom Just Do', 'almanonsinb@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocIZNs7-RSH348RY7veIcrn8HUhxACav3zT9P9GRyXCCp7yqh08=s96-c', NULL, '2025-08-10 10:09:14'),
(2, '102646258648190989265', 'Gyan Gee Almanon', 'almanongeeyan@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocKfTVRceVK6M2IPLJ9m8aOKQk70HIcseQw6oSf6em4PKOrl0WeQ=s96-c', NULL, '2025-08-10 13:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `manual_accounts`
--

CREATE TABLE `manual_accounts` (
  `id` int(11) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `house_address` text NOT NULL,
  `full_address` text NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `account_status` enum('active','pending','suspended','deleted') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manual_accounts`
--

INSERT INTO `manual_accounts` (`id`, `user_id`, `full_name`, `phone_number`, `house_address`, `full_address`, `password_hash`, `created_at`, `updated_at`, `account_status`, `last_login`) VALUES
(1, 'user_45b65f45a35b083a', 'Gyan Gee Almanon', '+639916354209', 'Ph7b Pkg4 Lot3 Blk21 Solido St.', 'Solido Street, Barangay 176-C, Zone 15, Bagong Silang, District 1, Caloocan, Northern Manila District, Metro Manila, 1438, Philippines', '$2y$10$mbguG0qLL.Awcn14lhJai.RwBbfw8M1sRyTIa68N3L.rBtAgC2gLi', '2025-08-15 21:16:36', '2025-08-16 22:50:44', 'active', '2025-08-16 22:50:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `google_accounts`
--
ALTER TABLE `google_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Indexes for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_phone` (`phone_number`),
  ADD UNIQUE KEY `unique_user_id` (`user_id`),
  ADD KEY `idx_account_status` (`account_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `google_accounts`
--
ALTER TABLE `google_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
