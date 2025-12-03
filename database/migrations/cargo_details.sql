-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 04:16 AM
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
-- Table structure for table `cargo_details`
--

CREATE TABLE `cargo_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `veh_reg_no` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cargo_unit_serial_no` varchar(255) DEFAULT NULL,
  `driver_lic_no` text DEFAULT NULL,
  `veh_fitness_cert` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `veh_carrying_capacity` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `invoice` text DEFAULT NULL,
  `packing_list` text DEFAULT NULL,
  `serial_no` text DEFAULT NULL,
  `invoice_value` varchar(200) DEFAULT NULL,
  `dispatch_lat` varchar(200) DEFAULT NULL,
  `dispatch_long` varchar(200) DEFAULT NULL,
  `destination_long` varchar(200) DEFAULT NULL,
  `destination_lat` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `value_add` varchar(200) DEFAULT NULL,
  `date_transit` date DEFAULT NULL,
  `pending_servey` tinyint(4) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `dispatch_type` varchar(255) DEFAULT NULL,
  `flat_track_number` varchar(255) DEFAULT NULL,
  `destination_pin` varchar(255) DEFAULT NULL,
  `origin_pin` varchar(255) DEFAULT NULL,
  `destination_address` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `dispatch_id` varchar(255) DEFAULT NULL,
  `channel_partner_id` int(11) DEFAULT NULL,
  `consignee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cargo_details`
--

INSERT INTO `cargo_details` (`id`, `veh_reg_no`, `cargo_unit_serial_no`, `driver_lic_no`, `veh_fitness_cert`, `veh_carrying_capacity`, `invoice`, `packing_list`, `serial_no`, `invoice_value`, `dispatch_lat`, `dispatch_long`, `destination_long`, `destination_lat`, `created_at`, `updated_at`, `value_add`, `date_transit`, `pending_servey`, `address`, `dispatch_type`, `flat_track_number`, `destination_pin`, `origin_pin`, `destination_address`, `user_id`, `deleted_at`, `group_id`, `dispatch_id`, `channel_partner_id`, `consignee_id`, `remarks`) VALUES
(121, '52353', '447', NULL, NULL, '46445', NULL, NULL, '447', '54545', '19.2046266', '72.9462411', 'null', 'null', '2024-07-16 20:33:17', '2024-07-20 12:11:21', '5000', '2024-07-31', 1, 'Thane', 'Flat Track', '447', '400604', '400604', 'Thane', 1, NULL, 9, 'AD001160724', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cargo_details`
--
ALTER TABLE `cargo_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_constraint_name` (`user_id`),
  ADD KEY `fk_channel_partner` (`channel_partner_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cargo_details`
--
ALTER TABLE `cargo_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=482;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cargo_details`
--
ALTER TABLE `cargo_details`
  ADD CONSTRAINT `fk_channel_partner_new_new` FOREIGN KEY (`channel_partner_id`) REFERENCES `channel_partners` (`id`),
  ADD CONSTRAINT `fk_constraint_name` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
