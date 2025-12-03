-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 04:19 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ilcargo_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE `phases` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `phases`
--

INSERT INTO `phases` (`id`, `name`, `description`, `group_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Angul', 'Origin', 68, '2024-07-02 07:28:36', '2024-09-19 11:34:03', NULL),
(2, 'Bengaluru', 'MPP - Bengaluru', 9, '2024-07-16 13:14:18', '2024-07-16 13:14:18', NULL),
(4, 'Pune', 'NA', 68, '2024-07-18 06:58:37', '2024-07-18 06:58:37', NULL),
(5, 'Tuticorin Warehouse', 'Origin- in the Warehouse', 69, '2024-07-18 13:46:53', '2024-07-18 15:29:15', NULL),
(6, 'Tuticorin Port', 'Port of Loading', 70, '2024-07-18 13:48:04', '2024-07-22 06:55:09', NULL),
(7, 'Delivery point at Maldives', 'Port of Discharge', 71, '2024-07-18 13:49:38', '2024-07-22 06:57:45', NULL),
(9, 'Tuticorin warehouse- Cargo on carrier Vehicles', 'Ready for Dispatch on ROAD', 69, '2024-07-18 15:29:56', '2024-07-18 15:29:56', NULL),
(10, 'Mumbai', 'NA', 76, '2024-07-25 06:55:30', '2024-10-30 04:13:26', NULL),
(11, 'Pune', 'Origin- in the Warehouse', 75, '2024-08-12 05:55:56', '2024-08-12 05:55:56', NULL),
(12, 'Bhinsara', 'Un-Loading Site', 76, '2024-10-24 11:43:48', '2024-10-24 11:47:56', NULL),
(13, 'Bhadla', 'Loading Site', 76, '2024-10-24 11:47:41', '2024-10-24 11:47:41', NULL),
(14, 'Mumbai', 'Loading Site', 77, '2024-12-09 08:00:25', '2024-12-09 08:00:25', NULL),
(15, 'Bangalore', 'Un-Loading Site', 77, '2024-12-09 08:00:43', '2024-12-09 08:00:43', NULL),
(16, 'Mumbai 1', 'Loading site', 77, '2024-12-19 08:17:05', '2024-12-19 08:17:05', NULL),
(18, 'Loading Site', 'Loading Site', 78, '2025-02-24 10:36:20', '2025-02-24 10:36:20', NULL),
(19, 'Bangalore', 'Loading Site', 79, '2025-03-03 07:41:44', '2025-03-03 07:41:44', NULL),
(20, 'Coimbatore', 'Loading Site Inspection before Lifting the cargo', 80, '2025-05-26 10:45:17', '2025-05-26 10:45:17', NULL),
(21, 'Loading Inspection', 'Loading Site', 81, '2025-06-09 13:10:29', '2025-06-09 13:10:29', NULL),
(25, 'Mumbai', 'Mumbai zone for test client', 83, '2025-07-21 07:23:02', '2025-07-21 07:23:02', NULL),
(26, 'Pune', 'Pune zone for test client', 83, '2025-07-21 07:25:18', '2025-07-21 07:25:18', NULL),
(27, 'Mumbai', 'Origin- Plant', 84, '2025-08-08 10:54:39', '2025-08-08 10:54:39', NULL),
(29, 'Dealer Point', 'Destination Point', 84, '2025-08-11 07:24:04', '2025-08-11 07:24:04', NULL),
(30, 'HALOL', 'Loading Survey', 85, '2025-09-11 10:44:07', '2025-09-11 10:45:14', NULL),
(31, 'BHAVNAGAR', 'Unloading Survey', 85, '2025-09-11 10:45:01', '2025-09-11 10:45:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `phases`
--
ALTER TABLE `phases`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phases`
--
ALTER TABLE `phases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
