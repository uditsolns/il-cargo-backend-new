-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 28, 2025 at 10:19 PM
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
-- Table structure for table `phase_zones`
--

CREATE TABLE `phase_zones` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `phase_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `phase_zones`
--

INSERT INTO `phase_zones` (`id`, `zone_id`, `phase_id`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 2, 2, 92, '2024-07-16 20:49:46', '2024-07-16 13:49:46', NULL),
(4, 3, 2, 92, '2024-07-16 20:49:46', '2024-07-16 13:49:46', NULL),
(5, 4, 2, 92, '2024-07-16 20:49:46', '2024-07-16 13:49:46', NULL),
(6, 2, 2, 80, '2024-07-18 09:45:42', '2024-07-18 02:45:42', NULL),
(7, 3, 2, 80, '2024-07-18 09:45:42', '2024-07-18 02:45:42', NULL),
(8, 4, 2, 80, '2024-07-18 09:45:56', '2024-07-18 02:45:56', NULL),
(9, 7, 5, 93, '2024-07-18 21:59:39', '2024-07-18 14:59:39', NULL),
(19, 6, 1, 98, '2024-08-13 22:53:08', '2024-08-13 15:53:08', NULL),
(22, 16, 11, 91, '2024-08-13 23:00:09', '2024-08-13 16:00:09', NULL),
(23, 15, 11, 91, '2024-08-13 23:00:09', '2024-08-13 16:00:09', NULL),
(24, 14, 11, 99, '2024-08-13 23:06:12', '2024-08-13 16:06:12', NULL),
(25, 15, 11, 99, '2024-08-13 23:06:12', '2024-08-13 16:06:12', NULL),
(36, 16, 11, 75, '2024-08-14 14:13:43', '2024-08-14 07:13:43', NULL),
(37, 15, 11, 75, '2024-08-14 14:13:43', '2024-08-14 07:13:43', NULL),
(38, 14, 11, 95, '2024-08-14 14:15:08', '2024-08-14 07:15:08', NULL),
(39, 15, 11, 95, '2024-08-14 14:15:10', '2024-08-14 07:15:10', NULL),
(40, 16, 11, 95, '2024-08-14 14:15:10', '2024-08-14 07:15:10', NULL),
(41, 17, 1, 74, '2024-09-19 18:39:06', '2024-09-19 11:39:06', NULL),
(42, 6, 1, 74, '2024-09-19 18:39:07', '2024-09-19 11:39:07', NULL),
(45, 18, 13, 101, '2024-10-24 18:52:17', '2024-10-24 11:52:17', NULL),
(46, 19, 13, 101, '2024-10-24 18:52:17', '2024-10-24 11:52:17', NULL),
(48, 12, 10, 1, '2024-10-28 18:09:47', '2024-10-28 11:09:47', NULL),
(49, 13, 10, 1, '2024-10-28 18:09:47', '2024-10-28 11:09:47', NULL),
(56, 22, 16, 102, '2024-12-19 15:28:52', '2024-12-19 08:28:52', NULL),
(57, 23, 16, 102, '2024-12-19 15:28:52', '2024-12-19 08:28:52', NULL),
(58, 25, 18, 103, '2025-02-24 17:46:46', '2025-02-24 10:46:46', NULL),
(59, 24, 18, 103, '2025-02-24 17:46:46', '2025-02-24 10:46:46', NULL),
(60, 27, 19, 104, '2025-03-04 21:16:41', '2025-03-04 14:16:41', NULL),
(61, 26, 19, 104, '2025-03-04 21:16:41', '2025-03-04 14:16:41', NULL),
(65, 20, 14, 107, '2025-05-16 06:09:10', '2025-05-15 23:09:10', NULL),
(66, 21, 15, 108, '2025-05-16 06:11:48', '2025-05-15 23:11:48', NULL),
(67, 28, 20, 109, '2025-05-26 17:52:16', '2025-05-26 10:52:16', NULL),
(68, 29, 20, 109, '2025-05-26 17:52:16', '2025-05-26 10:52:16', NULL),
(74, 23, 16, 110, '2025-06-16 18:20:44', '2025-06-16 11:20:44', NULL),
(75, 22, 16, 110, '2025-06-16 18:20:44', '2025-06-16 11:20:44', NULL),
(76, 32, 22, 112, '2025-06-19 12:50:33', '2025-06-19 05:50:33', NULL),
(77, 33, 22, 112, '2025-06-19 12:50:33', '2025-06-19 05:50:33', NULL),
(78, 34, 22, 112, '2025-06-19 12:50:33', '2025-06-19 05:50:33', NULL),
(87, 30, 21, 111, '2025-07-03 17:28:53', '2025-07-03 10:28:53', NULL),
(88, 31, 21, 111, '2025-07-03 17:28:53', '2025-07-03 10:28:53', NULL),
(92, 16, 11, 116, '2025-07-08 14:08:55', '2025-07-08 07:08:55', NULL),
(93, 15, 11, 116, '2025-07-08 14:08:55', '2025-07-08 07:08:55', NULL),
(94, 14, 11, 116, '2025-07-08 14:08:55', '2025-07-08 07:08:55', NULL),
(95, 41, 25, 117, '2025-07-21 14:30:24', '2025-07-21 07:30:24', NULL),
(96, 42, 25, 117, '2025-07-21 14:30:24', '2025-07-21 07:30:24', NULL),
(97, 43, 26, 118, '2025-07-21 14:31:56', '2025-07-21 07:31:56', NULL),
(98, 44, 26, 118, '2025-07-21 14:31:56', '2025-07-21 07:31:56', NULL),
(102, 46, 29, 124, '2025-08-12 13:00:32', '2025-08-12 06:00:32', NULL),
(103, 46, 29, 125, '2025-08-12 13:24:55', '2025-08-12 06:24:55', NULL),
(107, 45, 27, 119, '2025-08-12 19:36:05', '2025-08-12 12:36:05', NULL),
(108, 46, 29, 126, '2025-08-12 20:02:06', '2025-08-12 13:02:06', NULL),
(109, 48, 30, 127, '2025-09-11 17:52:59', '2025-09-11 10:52:59', NULL),
(110, 47, 30, 127, '2025-09-11 17:52:59', '2025-09-11 10:52:59', NULL),
(111, 46, 29, 128, '2025-09-19 11:49:09', '2025-09-19 04:49:09', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `phase_zones`
--
ALTER TABLE `phase_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `phase_id` (`phase_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phase_zones`
--
ALTER TABLE `phase_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `phase_zones`
--
ALTER TABLE `phase_zones`
  ADD CONSTRAINT `phase_zones_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phase_zones_ibfk_2` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phase_zones_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
