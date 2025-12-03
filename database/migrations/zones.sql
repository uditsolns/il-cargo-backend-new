-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 04:20 AM
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
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `phase_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `description`, `group_id`, `zone_id`, `created_at`, `updated_at`, `deleted_at`, `phase_id`) VALUES
(2, 'Pre Lifting', NULL, 9, NULL, '2024-07-16 13:14:39', '2024-07-16 13:52:17', NULL, 2),
(3, 'Post Lifting', NULL, 9, NULL, '2024-07-16 13:14:54', '2024-07-16 13:50:56', NULL, 2),
(4, 'Ready to Dispatch', NULL, 9, NULL, '2024-07-16 13:15:07', '2024-07-16 13:51:05', NULL, 2),
(5, 'Kalyan', 'NA', 9, NULL, '2024-07-16 13:45:45', '2024-07-16 13:45:45', NULL, 3),
(6, 'Post Lifting', 'NA', 68, NULL, '2024-07-18 06:59:26', '2024-09-19 11:35:53', NULL, 1),
(7, 'Cargo Condition at the warehouse', 'Origin', 69, NULL, '2024-07-18 14:05:49', '2024-07-18 15:31:17', NULL, 5),
(8, 'Dispatch Status on vehicle', 'Securing Inspection on Vehicle', 69, NULL, '2024-07-18 14:07:01', '2024-07-22 06:45:28', NULL, 5),
(9, 'Securing Check onboard Vessel', 'Onboard vessel inspection', 70, NULL, '2024-07-18 14:08:08', '2024-07-22 06:56:50', NULL, 6),
(11, 'Proof of delivery', 'Final Destination', 71, NULL, '2024-07-18 14:11:17', '2024-07-22 06:58:00', NULL, 7),
(12, 'Phase 1', 'NA', 76, NULL, '2024-07-25 06:56:04', '2024-10-30 04:14:14', NULL, 10),
(13, 'Phase 2', 'Na', 76, NULL, '2024-07-25 06:56:20', '2024-10-30 04:14:00', NULL, 10),
(14, 'Pre Lifting', 'Origin- in the Warehouse', 75, NULL, '2024-08-12 05:57:18', '2024-08-12 05:57:18', NULL, 11),
(15, 'Post Lifting', 'Origin- in the Warehouse', 75, NULL, '2024-08-12 05:57:58', '2025-07-08 08:50:42', NULL, 11),
(16, 'Ready to Dispatch', 'Origin- in the Warehouse', 75, NULL, '2024-08-12 05:58:23', '2024-08-12 05:58:23', NULL, 11),
(17, 'Ready To Dispatch', 'NA', 68, NULL, '2024-08-13 15:55:46', '2024-09-19 11:36:38', NULL, 1),
(18, 'Loading Inspection', 'Loading Site', 76, NULL, '2024-10-24 11:48:43', '2024-10-24 11:48:43', NULL, 13),
(19, 'Un-Loading Inspection', 'Un-Loading Site', 76, NULL, '2024-10-24 11:49:30', '2024-10-24 11:51:52', NULL, 13),
(20, 'Loading Inspection', 'Loading Site', 77, NULL, '2024-12-09 08:01:14', '2024-12-09 08:01:14', NULL, 14),
(21, 'Un-Loading Inspection', 'Un-Loading Site', 77, NULL, '2024-12-09 08:01:35', '2024-12-09 08:01:35', NULL, 15),
(22, 'Mumbai 1 Loading Inspection', 'Mumbai Loading site', 77, NULL, '2024-12-19 08:19:44', '2024-12-19 08:19:44', NULL, 16),
(23, 'Mumbai 2 Unloading Inspection', 'Mumbai Unloading site', 77, NULL, '2024-12-19 08:20:25', '2024-12-19 08:26:47', NULL, 16),
(24, 'Loading Inspection- Pre Lifting', 'Loading Site Inspection before Lifting the cargo', 78, NULL, '2025-02-24 10:37:17', '2025-02-24 10:37:17', NULL, 18),
(25, 'Loading Inspection- Post Lifting', 'Loading Site Inspection after Lifting the cargo', 78, NULL, '2025-02-24 10:37:58', '2025-02-24 10:37:58', NULL, 18),
(26, 'Loading Inspection- Pre Lifting', 'Loading Site Inspection before Lifting the cargo', 79, NULL, '2025-03-03 07:42:38', '2025-03-03 07:42:38', NULL, 19),
(27, 'Loading Inspection- Post Lifting', 'Loading Site Inspection after Lifting the cargo', 79, NULL, '2025-03-03 07:42:58', '2025-03-03 07:42:58', NULL, 19),
(28, 'Loading Inspection- Pre Lifting', 'Loading Site Inspection before Lifting the cargo', 80, NULL, '2025-05-26 10:46:08', '2025-05-26 10:46:08', NULL, 20),
(29, 'Loading Inspection- Post Lifting', 'Loading Site Inspection after Lifting the cargo', 80, NULL, '2025-05-26 10:46:34', '2025-05-26 10:46:34', NULL, 20),
(30, 'Loading Inspection- Lifting Stage', 'Loading Site Inspection before Lifting the cargo', 81, NULL, '2025-06-09 13:11:32', '2025-06-09 13:11:32', NULL, 21),
(31, 'Loading Inspection- Post Lifting', 'Loading Site Inspection after Lifting the cargo', 81, NULL, '2025-06-09 13:12:02', '2025-06-09 13:12:46', NULL, 21),
(41, 'Pre lifting', 'Test pre lifting zone for mumbai', 83, NULL, '2025-07-21 07:25:48', '2025-07-21 07:25:48', NULL, 25),
(42, 'Post lifting', 'Test post lifting phase for mumbai', 83, NULL, '2025-07-21 07:26:28', '2025-07-21 07:26:28', NULL, 25),
(43, 'Pre lifting', 'Test pre lifting phase for pune', 83, NULL, '2025-07-21 07:27:09', '2025-07-21 07:27:09', NULL, 26),
(44, 'Post lifting', 'Test post lifting phase for pune', 83, NULL, '2025-07-21 07:27:34', '2025-07-21 07:27:34', NULL, 26),
(45, 'Loading Survey- Surface', 'Origin', 84, NULL, '2025-08-08 10:55:53', '2025-08-08 10:55:53', NULL, 27),
(46, 'Un Loading Survey- Surface', 'Destination', 84, NULL, '2025-08-11 07:25:07', '2025-08-11 07:25:07', NULL, 29),
(47, 'Lifting Phase', 'Lifting Survey', 85, NULL, '2025-09-11 10:45:59', '2025-09-11 10:45:59', NULL, 30),
(48, 'Loading Phase', 'Loading Survey', 85, NULL, '2025-09-11 10:46:51', '2025-09-11 10:46:51', NULL, 30),
(49, 'Unloading Phase', 'Unloading Survey', 85, NULL, '2025-09-11 10:48:46', '2025-09-11 10:48:46', NULL, 31);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
