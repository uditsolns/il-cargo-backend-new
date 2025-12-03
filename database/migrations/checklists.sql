-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 04:17 AM
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
-- Table structure for table `checklists`
--

CREATE TABLE `checklists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phase_id` int(10) DEFAULT NULL,
  `zone_id` int(10) DEFAULT NULL,
  `question` longtext NOT NULL,
  `answer` longtext NOT NULL,
  `instruction` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cargo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `answer_type` tinyint(4) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `preferred_compliance` varchar(255) DEFAULT NULL,
  `is_sop_breached` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `checklists`
--

INSERT INTO `checklists` (`id`, `phase_id`, `zone_id`, `question`, `answer`, `instruction`, `created_at`, `updated_at`, `cargo_id`, `deleted_at`, `answer_type`, `type`, `preferred_compliance`, `is_sop_breached`) VALUES
(21, NULL, NULL, 'Confirm High visibility reflected tape/ red LED lights are rigged if width is above 2.5m', '0', '9) High visibility reflective tapes  or led LED rights or flash light shall be pasted\\/ rigged  on the edges of the unit which has width or height greater than 2.5 meters. When these units are transported on carrier vehicle from the origin to the final destination, additionally red LED lights shall be placed on the cargo unit on the edges that are protruding outwards the carrier vehicle deck.', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL),
(22, NULL, NULL, 'Is the Lifting arrangement as per the Insurance policy conditions', '0', '8) Warranted Loading and Unloading shall be covered only if (i)Lifting appliances\\/apparatus and the crane\'s SWL (safe working load) shall be greater than 1.2 times of the total mass weight of the cargo along with any other items attached to it, i.e dunnage, packing material etc  that shall be lifted. (ii) Insured shall ensure 2 pairs of lifting chains only are used for lifting of the unit insured. (iii) The Lifting operations shall be carried out on a safe ground that is either concrete or dedicated lifting premises. If lifting ground is Not concrete then steel plates shall be placed on muddy ground. (iv) Lifting shall be carried out with only one crane and NOT with 2 cranes in pair or as a tandem operation at the unloading site. If 2 cranes are involved, their (each crane)SWL should be 1.5 times more than the mass of the cargo unit and these cranes shall be operated on concrete ground and where eth ground is not concrete, steel plates shall be placed on the ground prior cranes are placed for lifting purpose.', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL),
(23, NULL, NULL, 'SWL of securing appliances is 1.2 times the mass of the cargo unit secured', '0', '7) Warranted that securing appliances used shall be chains only and not straps. The shackles, turnbuckles, bottle screw and similar equipment used shall have the SWL (safe working load) equal to or greater than 1.2 times the gross weight of the unit collectively', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklists`
--
ALTER TABLE `checklists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cargo_id` (`cargo_id`),
  ADD KEY `checklists_phase_id_foriegn` (`phase_id`),
  ADD KEY `checklists_zone_id_foriegn` (`zone_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checklists`
--
ALTER TABLE `checklists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18003;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklists`
--
ALTER TABLE `checklists`
  ADD CONSTRAINT `checklists_phase_id_foriegn` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`),
  ADD CONSTRAINT `checklists_zone_id_foriegn` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
