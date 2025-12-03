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
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(20) NOT NULL,
  `gst` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sop` varchar(255) DEFAULT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `additional_emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_emails`)),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `channel_partner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `address`, `city`, `gst`, `created_at`, `updated_at`, `photo`, `sop`, `parent_user_id`, `additional_emails`, `deleted_at`, `channel_partner_id`) VALUES
(9, 'MPP Technologies', 'Peenya Industrial Area Bangalore', 'Bangalore', 'NA', '2023-12-29 07:15:25', '2023-12-29 12:22:57', '/tmp/phpc0yTzi', NULL, NULL, NULL, NULL, NULL),
(17, 'Exide Energy', 'Phase II, Hi-Tech Defence', 'Karnataka', '12345', '2024-02-16 08:31:45', '2024-02-16 10:25:18', 'Exide Energy SOP for flat Rake Conatiner Import Material.pdf', NULL, NULL, NULL, NULL, NULL),
(68, 'Octagon Enterprises Pvt. Ltd Ltd', '54, Ritchie Road, Kolkata - 700019', 'Kolkata', '0', '2024-05-30 09:24:06', '2024-09-19 18:30:53', NULL, NULL, NULL, '[\"sidhartha.octagon@gmail.com\"]', NULL, NULL),
(69, 'Afcons Infrastructure Ltd-(warehouse)', '16, Shah Industrial Estate,Andheri (West), Mumbai - 400053', 'Mumbai', '#####', '2024-07-18 20:43:18', '2024-07-18 22:40:01', NULL, NULL, NULL, '[\"corporatecommunications@afcons.com\"]', NULL, NULL),
(70, 'Afcons Infrastructure Ltd-(Origin Port)', '16, Shah Industrial Estate,Andheri (West), Mumbai - 400053', 'Mumbai', '####', '2024-07-18 22:41:03', '2024-07-18 22:41:03', NULL, NULL, NULL, '[\"corporatecommunications@afcons.com\"]', NULL, NULL),
(71, 'Afcons Infrastructure Ltd-(Destination Port)', '16, Shah Industrial Estate,Andheri (West), Mumbai - 400053', 'Mumbai', '####', '2024-07-18 22:42:00', '2024-07-18 22:42:00', NULL, NULL, NULL, '[\"corporatecommunications@afcons.com\"]', NULL, NULL),
(72, 'Afcons Infrastructure Ltd-(Warehouse Exit)', '16, Shah Industrial Estate,Andheri (West), Mumbai - 400053', 'Mumbai', '####', '2024-07-18 22:42:41', '2024-07-18 22:42:41', NULL, NULL, NULL, '[null]', NULL, NULL),
(73, 'Sujeet', 'Borivali', 'Borivali', '1212', '2024-07-19 13:28:11', '2024-07-19 13:28:11', NULL, NULL, NULL, '[null]', NULL, NULL),
(74, 'Tester', 'Pune', 'Pune', '0', '2024-07-25 13:54:39', '2024-07-25 13:54:39', NULL, NULL, NULL, '[null]', NULL, NULL),
(75, 'GOODS CARRIER OF INDIA', 'Sect No 25 Pl No 196 Nigadi Pradhikaran, Pune, Maharashtra Pin­ 411044', 'Pune', 'NA', '2024-08-12 12:52:21', '2024-08-14 14:21:59', 'INLAND-roadtransit-marine-risk-mgmt_2023-24-002SB-ODC-Goods Carrier.docx', NULL, NULL, '[\"gcipune@rediffmail.com\"]', NULL, NULL),
(76, 'KEC International  Limited', 'Bhadla Solar  Project, Rajasthan', 'Rajasthan', 'NA', '2024-10-24 18:38:25', '2024-10-24 18:38:25', NULL, NULL, NULL, '[\"singha31@KECRPG.COM\"]', NULL, NULL),
(77, 'PhonePe Private Limited', 'PhonePe Private Limited Salarpuria Softzone, Floor Nos, 4,5,6, Wing A of office 2 in Block A, Survey Nos. 80/1, 81/1 and 81/2, Varthur Hobli, Bellandur Village, Bangalore', 'Bangalore', 'NA', '2024-12-09 14:58:49', '2024-12-09 14:58:49', NULL, NULL, NULL, '[\"preethi.s@phonepe.com\"]', NULL, NULL),
(78, 'Mass Rollpro Technologies Ltd', 'Guru harkrishna Marg, Pitampura, Delhi - 110000', 'Delhi', 'NA', '2025-02-24 16:23:11', '2025-02-24 16:59:24', NULL, NULL, NULL, '[\"accounts@massgroup.in\"]', NULL, NULL),
(79, 'MAG India Industrial Automation Systems Pvt. Ltd', 'Plot No.52, Hitech, Defence and Aerospace Park(IT Sector) Arebinnamangala Village, Jala Hobli, Bengaluru North Taluk', 'Bangalore', 'NA', '2025-03-03 14:39:40', '2025-03-03 14:39:40', NULL, NULL, NULL, '[\"rupa.s@firstpolicy.com\"]', NULL, NULL),
(80, 'Propel Industries Pvt ltd', 'Trichy Road, Sulur.', 'Coimbatore', 'NA', '2025-05-26 17:39:26', '2025-05-26 17:39:26', NULL, NULL, NULL, '[\"selfsurvey@propelindia.com\"]', NULL, NULL),
(81, 'Dynapac Road Construction Equipment India Pvt. Ltd', 'Phulgaon, Wadhu Kh., Maharashtra 412216', 'Pune', 'NA', '2025-06-09 19:51:15', '2025-06-09 19:51:15', NULL, NULL, NULL, '[\"akshay.belote@dynapac.com\"]', NULL, NULL),
(83, 'Test Client', 'customer@test.com', 'Mumbai', 'GST1234', '2025-07-21 14:21:03', '2025-07-21 14:45:40', '8815622194.pdf', '8815622194_canceled.pdf1753082463.pdf', NULL, '[\"test@gmail.com\"]', NULL, NULL),
(84, 'JSW Paint Ltd', 'JSW Centre, Bandra Kurla Complex, Bandra (East), Mumbai – 400051 | Maharashtra| India', 'DADAR', '12345', '2025-08-08 17:21:59', '2025-08-08 17:21:59', NULL, NULL, NULL, '[\"loadingsurveyor@Jswpaint.com\"]', NULL, NULL),
(85, 'ACME SOLAR HOLDINGS LTD', 'Anjar', 'Gujrat', 'NA', '2025-09-11 17:39:29', '2025-09-11 17:39:29', NULL, 'INLAND-roadtransit-marine-risk-mgmt_2025-26-002SB-ODC - ACME FINAL..pdf1757587169.pdf', NULL, '[\"ankitsethi@hotmail.com\"]', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_channel_partner_new` (`channel_partner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `fk_channel_partner_new` FOREIGN KEY (`channel_partner_id`) REFERENCES `channel_partners` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
