-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 04:47 PM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `manual_accounts`
--
ALTER TABLE `manual_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
