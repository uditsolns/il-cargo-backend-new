-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 04:18 AM
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
-- Table structure for table `checklist_masters`
--

CREATE TABLE `checklist_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phase_id` int(10) DEFAULT NULL,
  `zone_id` int(10) DEFAULT NULL,
  `question` longtext NOT NULL,
  `instruction` longtext NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `answer` longtext DEFAULT NULL,
  `preferred_compliance` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `answer_type` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `checklist_masters`
--

INSERT INTO `checklist_masters` (`id`, `phase_id`, `zone_id`, `question`, `instruction`, `group_id`, `answer`, `preferred_compliance`, `created_at`, `updated_at`, `deleted_at`, `remarks`, `answer_type`) VALUES
(15, 2, 2, 'Driver\'s License and veh fitness is VALID', '5) Warranted the Driver’s licence is in valid state, vehicle has its fitness certificate and insurance certificate valid.', 9, NULL, NULL, '2024-01-04 04:49:49', '2024-07-25 17:40:32', NULL, NULL, 3),
(16, 2, 3, 'Confirm the weight carryiong capacity of veh is greater or equal to the Cargo weight- NO OVERLOADING', '3) Warranted the Insured ensure there is NO overloading', 9, NULL, NULL, '2024-01-04 04:50:32', '2024-07-25 17:40:37', NULL, NULL, 3),
(17, 2, 4, 'Confirm vehicle is Low bed trailer', '4) Warranted the Insured shall use low bed vehicle if the ODC unit height is above 12 feet from the ground level to the top most part of the cargo', 9, NULL, NULL, '2024-01-04 04:50:58', '2024-07-25 17:40:43', NULL, NULL, 3),
(18, 2, 5, 'Is the Cargo Height from the ground level to top side below 12feet', 'If the answer is YES , the OK, If its NO then Pop up  <If the cargo ht from ground is above 12 feet until the top side, a low bed carrier vehicle (mechanical trailer) is mandatory  to comply warranties. Ignore if it’s a hydraulic axle vehicle', 9, NULL, NULL, '2024-01-04 04:51:29', '2024-07-25 17:40:49', NULL, NULL, 3),
(19, 2, 2, 'Confirm Unit is secured with Chains with 8 point contact as per Policy warranty requirement', 'C1 - 1) Warranted the Insured, post loading and lifting the cargo unit shall secure the cargo with 2 chains on left side and right side respectively each to achieve a vertical tightening of the cargo unit from its top side to the rigid eye structure that should be a rigid point of the carrier vehicle’s deck side and additionally 2 chains from the top side to the carrier vehicle deck side  rigid lashing point with a lead stretching forward and rearside respectively to maintain the cargo unit adequately secured to restrict the shift fore & rear wards, laterally and longitudinally.', 9, NULL, NULL, '2024-01-04 04:57:54', '2024-07-25 17:40:56', NULL, NULL, 3),
(20, 2, 3, 'Angle bars in harozontal position are welded to restrict shift at both end', '2) Warranted the base side of the unit at the bottom side shall have an angle bars (with 50mm width) welded horizontally covering a minimum of 50% bottom deck of the cargo unit to restrict sliding force. The angles bars can be substituted if there is additional wooden play placed on the bottom deck of the vehicle deck to avoid metal to metal contact.', 9, NULL, NULL, '2024-01-04 05:13:19', '2024-07-25 17:41:01', NULL, NULL, 3),
(22, 2, 4, 'SWL of securing appliances is 1.2 times the mass of the cargo unit secured', '7) Warranted that securing appliances used shall be chains only and not straps. The shackles, turnbuckles, bottle screw and similar equipment used shall have the SWL (safe working load) equal to or greater than 1.2 times the gross weight of the unit collectively', 9, NULL, NULL, '2024-01-04 05:14:29', '2024-07-25 17:41:07', NULL, NULL, 3),
(23, 2, 5, 'Is the Lifting arrangement as per the Insurance policy conditions', '8) Warranted Loading and Unloading shall be covered only if (i)Lifting appliances/apparatus and the crane\'s SWL (safe working load) shall be greater than 1.2 times of the total mass weight of the cargo along with any other items attached to it, i.e dunnage, packing material etc  that shall be lifted. (ii) Insured shall ensure 2 pairs of lifting chains only are used for lifting of the unit insured. (iii) The Lifting operations shall be carried out on a safe ground that is either concrete or dedicated lifting premises. If lifting ground is Not concrete then steel plates shall be placed on muddy ground. (iv) Lifting shall be carried out with only one crane and NOT with 2 cranes in pair or as a tandem operation at the unloading site. If 2 cranes are involved, their (each crane)SWL should be 1.5 times more than the mass of the cargo unit and these cranes shall be operated on concrete ground and where eth ground is not concrete, steel plates shall be placed on the ground prior cranes are placed for lifting purpose.', 9, NULL, NULL, '2024-01-04 05:14:59', '2024-07-25 17:41:12', NULL, NULL, 3),
(24, 2, 2, 'Confirm High visibility reflected tape/ red LED lights are rigged if width is above 2.5m', '9) High visibility reflective tapes  or led LED rights or flash light shall be pasted/ rigged  on the edges of the unit which has width or height greater than 2.5 meters. When these units are transported on carrier vehicle from the origin to the final destination, additionally red LED lights shall be placed on the cargo unit on the edges that are protruding outwards the carrier vehicle deck.', 9, NULL, NULL, '2024-01-04 05:15:28', '2024-07-25 17:41:17', NULL, NULL, 3),
(26, 2, 3, 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 'These straps and / or  chains shall be good quality free of any signs of breakage, damage/ knots, strand defects or crack and will stretch from the left side of the carrier vehicle deck on rigid point or lashing point, extending from the top side of the cargo unit and stretching further to the right side of the flat rack deck to secure the cargo unit from shift during transit. chain/straps shall be rigged from forward to rear side  as a series patter with a distance of not more than 3feet  from each other. Securing appliances shall be tightened with rachets or bottle screw or similar equipment. The \'MSL\' maximum securing load or \'SWL\' safe working load of each securing appliance shall be uniform and together shall be greater than 1.25 times the total  The Insured shall enter the Serial no of the flat racks lined up for transportation.', 9, NULL, NULL, '2024-02-15 06:53:34', '2024-07-25 17:41:21', NULL, NULL, 3),
(27, 2, 4, 'Each unit when placed/ stowed on the carrier vehicle shall be lashed individually to resist internal shift.', 'The securing shall be such that it should resist the sliding stresses  in an event when the driver of the carrier vehicle may tilt the vehicle at one side due to road conditions. This kind of shift / slide may be prevented with angle bars welded at the edge of eth carrier vehicle deck. Angle bars shall be 3 to 5mm in thickness and 30to 50mm width. An ‘L’ shape where there is space between the vehicle deck and the cargo side wall/structure  or ‘I’ shape vertically where cargo width is upto the carrier vehicle deck edge. The Insured shall weld angle bars vertically or horizontally to fulfil the sole requirement of preventing sliding/ shift in an event of securing appliances failure.', 9, NULL, NULL, '2024-02-15 06:55:07', '2024-07-25 17:41:26', NULL, NULL, 3),
(28, NULL, NULL, 'The Insured shall ensure the cargo unit when stowed/ placed on the flat rack deck is stable, i.e the COG point lies on the center line of the flat rack.', 'The Insured shall ensure the cargo unit when stowed/ placed on the flat rack deck is stable, i.e the COG point lies on the center line of the flat rack.', 17, NULL, NULL, '2024-02-16 09:09:33', '2024-02-16 09:09:33', NULL, NULL, NULL),
(29, NULL, NULL, 'The cargo unit, post loading on the flat rack shall be strapped or secured with chain (securing appliances).', 'These straps and chains shall be good quality free of any signs of breakage, damage/ knots, strand defects or crack and will stretch from teh left side of the flat rack deck on rigid point or lashing point, extending from the top side of the cargo unit and strecthing further to the right side of the flat rack deck to secure the cargo unit from shift during transit.chain/straps shall be rigged from forward to rear side  as a series patter with a distance of not more than 3feet  from each other.', 17, NULL, NULL, '2024-02-16 09:11:49', '2024-02-16 09:11:49', NULL, NULL, NULL),
(30, NULL, NULL, 'Securing appliances shall be tightened with rachets or bottle screw or similar equipment\'s.', 'Securing appliances shall be tightened with rachets or bottle screw or similar equipments.', 17, NULL, NULL, '2024-02-16 09:12:36', '2024-02-16 09:12:36', NULL, NULL, NULL),
(31, NULL, NULL, 'carrier vehicle that shall be having twist locks operations at eth 4 corners', 'The Insured shall ensure that the LSP / CHA or whoever is assigned to line up carrier vehicle at eth POL and POD shall only use carrier vehicle that shall be having twist locks operations at eth 4 corners. Only operational twist locking lever vehicle shall be  used', 17, NULL, NULL, '2024-02-16 11:13:17', '2024-02-16 11:13:17', NULL, NULL, NULL),
(32, NULL, NULL, 'External wooden box to have markings of critical warning, i.e NO lift, No stress / lashing or dedicate place of lifting and lashing.', 'Prior packing the unit, a right round inspection shall be carried out, fragile component to  be adequately cushioned and packed. Wooden material shall be of good quality and  density. Where ever there are delicate components, cushion material such as bubble  wraps, additional dunnage to be used. External wooden box to have markings of critical  warning, i.e NO lift, No stress / lashing or dedicate place of lifting and lashing.  The COG to be labelled at the external side', 17, NULL, NULL, '2024-02-16 11:16:00', '2024-02-16 11:16:00', NULL, NULL, NULL),
(33, NULL, NULL, 'The carrier vehicle shall NOT be overloaded, and fitness certificate shall be valid.', 'The tyres of the carrier vehicle shall be in good condition, i.e e (Good state means – the  tyre is not worn out and tube portion', 17, NULL, NULL, '2024-02-16 11:17:38', '2024-02-16 11:17:38', NULL, NULL, NULL),
(52, NULL, NULL, 'Please upload the photo', 'Yes there are some important photo', 17, NULL, NULL, '2024-04-02 04:06:48', '2025-08-07 17:00:30', '2025-08-07 17:00:30', NULL, 1),
(53, NULL, NULL, 'Please upload the videos', 'Yes there are some important video for std', 17, NULL, NULL, '2024-04-02 04:08:05', '2025-08-07 17:00:25', '2025-08-07 17:00:25', NULL, 2),
(54, NULL, NULL, 'Please give the your answer', 'Yes there are some important instrution for u', 17, NULL, NULL, '2024-04-02 04:09:50', '2025-08-07 17:00:15', '2025-08-07 17:00:15', NULL, 3),
(55, NULL, NULL, 'Please give the your answer', 'Yes there are some important instrution for u', 17, NULL, NULL, '2024-04-02 04:10:13', '2025-08-07 17:00:04', '2025-08-07 17:00:04', NULL, 4),
(56, NULL, NULL, 'Please give the your answer', 'Yes there are some important videos', 17, NULL, NULL, '2024-04-02 04:11:02', '2025-08-07 16:59:56', '2025-08-07 16:59:56', NULL, 2),
(57, NULL, NULL, 'Please upload the photo', 'Yes there are some important', 17, NULL, NULL, '2024-04-02 04:11:25', '2025-08-07 16:59:47', '2025-08-07 16:59:47', NULL, 1),
(58, NULL, NULL, 'Please give the your answer', 'Yes there are some important videos', 17, NULL, NULL, '2024-04-02 09:34:39', '2025-07-31 14:51:39', '2025-07-31 14:51:39', NULL, 4),
(72, NULL, NULL, 'Confirm  that the cargo is inspected from all  4 sides and the top side and nil deformities observed', 'Refer to DRA condition no1 of SOP', 69, NULL, NULL, '2024-07-18 21:34:01', '2024-07-18 21:42:24', NULL, NULL, 3),
(73, NULL, NULL, 'Confirm  that the cargo is inspected from all  4 sides and the top side and nil deformities observed', 'Refer to DRA condition no1 of SOP', 69, NULL, NULL, '2024-07-18 21:39:29', '2024-07-18 21:42:15', NULL, NULL, 1),
(74, NULL, NULL, 'Confirm the vehicle is not overloaded', 'Refer to DRA Condition no 2 of SOP', 72, NULL, NULL, '2024-07-18 21:47:09', '2024-07-18 22:44:22', NULL, NULL, 3),
(75, NULL, NULL, 'Kindly upload the picture of the cargo on the vehicle, post loading and securing covering right, left,FWD and rear view', 'Refer to DRA Condition no 6 of SOP', 72, NULL, NULL, '2024-07-18 21:52:08', '2024-07-18 22:44:37', NULL, NULL, 1),
(76, NULL, NULL, 'Kindly confirm cargo is adequately secured by the lashing materials', 'Refer to DRA Condition no 4 of SOP', 70, NULL, NULL, '2024-07-18 21:56:23', '2024-07-18 22:45:26', NULL, NULL, 3),
(77, NULL, NULL, 'Confirm the cargo units are safely loaded onboard vessel without any deformity', 'Conduct a visual inspection', 70, NULL, NULL, '2024-07-18 22:09:18', '2024-07-18 22:45:56', NULL, NULL, 3),
(78, NULL, NULL, 'Ensure the cargo is well lashed as per the DRA condition VI of the SOP', 'Upload pictures of teh cargo units when lashed adequately on the vessel', 70, NULL, NULL, '2024-07-18 22:11:03', '2024-07-18 22:46:44', NULL, NULL, 1),
(79, NULL, NULL, 'Confirm there was NO damage sighted when inspected  at the wharf or port or vessel', 'If any damages inform the insurer as per claim intimation process', 71, NULL, NULL, '2024-07-18 22:12:04', '2024-07-18 22:47:20', NULL, NULL, 3),
(80, NULL, NULL, 'Confirm the cargo units received at the delivery point is in good condition and no deformity', 'if NO then inform the Insurer immediately', 71, NULL, NULL, '2024-07-18 22:13:17', '2024-07-18 22:47:45', NULL, NULL, 3),
(81, NULL, NULL, 'Upload the pictures of the cargo unit as if basis at the delivery point', 'Upload Pictures LEFT Right FWD and Rear view', 71, NULL, NULL, '2024-07-18 22:14:17', '2024-07-18 22:47:58', NULL, NULL, 1),
(82, NULL, NULL, 'The Cargo unit is safely delivered NIL damage', 'If any damage inform the Insurer immediately', 71, NULL, NULL, '2024-07-18 22:15:01', '2024-07-18 22:48:17', NULL, NULL, 3),
(83, 2, 5, 'Photo', 'NA', 9, NULL, NULL, '2024-07-25 12:29:10', '2024-07-25 12:29:10', NULL, NULL, 1),
(84, 2, 2, 'Video', 'Video', 9, NULL, NULL, '2024-07-25 12:29:43', '2024-07-25 12:29:43', NULL, NULL, 2),
(85, 2, 3, 'Yes No', 'NA', 9, NULL, NULL, '2024-07-25 12:30:11', '2024-07-25 12:30:11', NULL, NULL, 3),
(86, 2, 4, 'Yes No Or May BE', 'Yes No Or May BE Instru', 9, NULL, NULL, '2024-07-25 12:30:36', '2024-07-25 12:30:36', NULL, NULL, 4),
(87, NULL, NULL, 'Test Photo', 'NA', 73, NULL, NULL, '2024-07-25 14:51:59', '2024-08-13 14:17:32', '2024-08-13 14:17:32', NULL, 1),
(88, NULL, NULL, 'Test Video', 'NA', 73, NULL, NULL, '2024-07-25 14:52:19', '2024-08-13 14:17:27', '2024-08-13 14:17:27', NULL, 2),
(89, NULL, NULL, 'Test Yes NO', 'NA', 73, NULL, NULL, '2024-07-25 14:52:39', '2024-08-13 14:17:24', '2024-08-13 14:17:24', NULL, 3),
(90, NULL, NULL, 'Test Yes No May be', 'NA', 73, NULL, NULL, '2024-07-25 14:53:03', '2024-08-13 14:17:17', '2024-08-13 14:17:17', NULL, 4),
(91, 11, 14, 'Is the vehicle registration checked and found to be invalid?', 'Condition No :IV ( Sub Condition 1 to 5)', 75, NULL, 'No', '2024-08-13 14:18:31', '2025-08-04 19:42:15', NULL, NULL, 3),
(92, 11, 14, 'Is the vehicle insurance checked and found to be invalid?', 'Condition No :IV ( Sub Condition 1 to 5)', 75, NULL, 'No', '2024-08-13 14:20:41', '2025-08-04 19:42:36', NULL, NULL, 3),
(93, 11, 14, 'Is the driver’s license copy valid?', 'Condition No :IV ( Sub Condition 1 to 5)', 75, NULL, 'Yes', '2024-08-13 14:21:21', '2025-08-04 19:43:07', NULL, NULL, 3),
(94, 11, 14, 'Is the vehicle\'s fitness certificate invalid, and does it clearly indicate that the load-carrying capacity exceeds the permissible limit?', 'Condition No :IV ( Sub Condition 1 to 5)', 75, NULL, 'No', '2024-08-13 14:21:50', '2025-08-04 19:43:32', NULL, NULL, 3),
(95, 11, 14, 'Is the vehicle currently overloaded?', 'Condition No :I ( Sub Condition 1 to 10)', 75, NULL, 'No', '2024-08-13 14:22:34', '2025-08-04 19:43:48', NULL, NULL, 3),
(96, 11, 15, 'Has a semi low-bed trailer and adequate support equipment been arranged for transporting the oversized cargo?', 'Condition No :I ( Sub Condition 1 to 10)', 75, NULL, 'Yes', '2024-08-13 14:23:05', '2025-08-04 19:44:13', NULL, NULL, 3),
(97, 11, 14, 'Will lifting appliances be used without valid certification, or with an SWL (Safe Working Load) less than 1.2 times the gross weight of the cargo?', 'Condition No :III ( Sub Condition 13 & 16 )', 75, NULL, 'No', '2024-08-13 14:23:42', '2025-08-04 19:44:37', NULL, NULL, 3),
(98, 11, 15, 'Is the cargo loaded with uneven weight distribution?', 'Condition No :IV ( Sub Condition 18 )', 75, NULL, 'No', '2024-08-13 14:24:19', '2025-08-04 19:45:01', NULL, NULL, 3),
(99, 11, 15, 'Have all lashing and securing devices (e.g., heavy-duty straps, chains, support structures) been inspected for valid test certificates and SWL greater than 1.2 times the gross weight of the cargo unit? (Applicable only to ODC units)', 'Condition No :IV ( Sub Condition 20 & 21 )', 75, NULL, 'Yes', '2024-08-13 14:24:52', '2025-08-04 19:45:38', NULL, NULL, 3),
(100, 11, 15, 'Is the cargo adequately secured as per the approved securing plan, and is no structural integrity check required before transportation?', 'Condition No :IV ( Sub Condition 20 & 21 )', 75, NULL, 'No', '2024-08-13 14:25:43', '2025-08-04 19:46:08', NULL, NULL, 3),
(101, 11, 15, 'If the consignment is cylindrical or oval in shape, have angle bars with sufficient strength been welded around the unit to prevent rolling due to motion or swell effects?', 'Condition No :IV ( Sub Condition 20 & 21 )', 75, NULL, 'Yes', '2024-08-13 14:26:21', '2025-08-04 19:46:34', NULL, NULL, 3),
(102, 11, 15, 'Are weather-resistant coverings in place to protect the cargo (e.g., boiler) from environmental exposure, and is additional securing not required for wind-induced movement?', 'Condition No :IV ( Sub Condition 20 & 21 )', 75, NULL, 'No', '2024-08-13 14:26:57', '2025-08-04 19:47:13', NULL, NULL, 3),
(103, NULL, NULL, 'The weather forecasts is monitored closely. The decision shall be taken to avoid transportation during adverse conditions', 'Condition No :IV ( Sub Condition 20 & 21 )', 75, NULL, NULL, '2024-08-13 14:27:40', '2025-06-27 14:55:29', '2025-06-27 14:55:29', NULL, 3),
(104, 11, 14, 'Has a route survey been conducted and documented by the concerned authority?', 'Condition No :II ( Sub Condition 11 & 12 )', 75, NULL, 'No', '2024-08-13 14:28:08', '2025-08-04 19:48:13', NULL, NULL, 3),
(105, 11, 14, 'Have all necessary transportation permits been obtained, and have local authorities been coordinated for any required road closures or route modifications?', 'Condition No :II ( Sub Condition 11 & 12 )', 75, NULL, 'Yes', '2024-08-13 14:28:39', '2025-08-04 19:48:35', NULL, NULL, 3),
(106, NULL, NULL, 'Confirm Carrier vehicle fitness certificate clearly indicating the load carrying permissible limit, vehicle’s driver license copy (To be valid), insurance (to be valid)', 'Condition No :I ( Sub Condition 1 to 5)', 68, NULL, NULL, '2024-09-19 18:56:19', '2024-09-19 18:56:19', NULL, NULL, 3),
(107, NULL, NULL, 'Confirm the vehicle is not overloaded more than 15%', 'Condition No :I ( Sub Condition 1 to 10)', 68, NULL, NULL, '2024-09-19 18:58:11', '2024-09-19 18:58:11', NULL, NULL, 3),
(108, NULL, NULL, 'Confirm Use specialized semi low bed trailers and equipment designed to support heavy and oversized loads. Ensure proper load distribution and secure the boiler with heavy-duty straps, chains, and support structures. Conduct a thorough structural integrity check before transportation.', 'Condition No :IV ( Sub Condition 18 )', 68, NULL, NULL, '2024-09-19 19:00:13', '2024-09-19 19:00:13', NULL, NULL, 3),
(109, NULL, NULL, 'Kindly confirm cargo is adequately secured by the lashing materials..', 'Condition No :IV ( Sub Condition 20 & 21', 68, NULL, NULL, '2024-09-19 19:01:57', '2024-09-19 19:01:57', NULL, NULL, 3),
(110, NULL, NULL, 'Confirm Safe working load certificate of the lashing equipment’s/ lifting gears / securing apparatus. The SWL shall be greater than 1.2 times the gross weight of the cargo unit. (Applicable for ODC units only', 'Condition No :IV ( Sub Condition 20 & 21', 68, NULL, NULL, '2024-09-19 19:02:56', '2024-09-19 19:02:56', NULL, NULL, 3),
(111, NULL, NULL, 'Confirm detailed route surveys to identify and plan for potential clearance for overhead obstacles. Obtain necessary permits for the transportation route and coordinate with local authorities for road closures or modifications if needed', 'Condition No :II ( Sub Condition 11 & 12 )', 68, NULL, NULL, '2024-09-19 19:05:20', '2024-09-19 19:05:20', NULL, NULL, 3),
(112, NULL, NULL, 'Lifting appliances to be in good conditions', 'Condition No :III ( Sub Condition 13 & 16 )', 68, NULL, NULL, '2024-09-19 19:06:09', '2024-09-19 19:06:09', NULL, NULL, 3),
(113, NULL, NULL, 'Confirm that the cargo is inspected from all 4 sides and the top side and nil deformities observed', 'NA', 68, NULL, NULL, '2024-09-19 19:08:43', '2024-09-19 19:08:43', NULL, NULL, 3),
(114, NULL, NULL, 'Kindly upload the picture of the cargo on the vehicle, post loading and securing covering right, left, FWD and rear view', 'Stages-  Post Lifting, Post Securing', 68, NULL, NULL, '2024-09-19 19:09:22', '2024-09-19 19:09:22', NULL, NULL, 1),
(115, NULL, NULL, 'No pre-existing damages of the solar panels and the pallets', 'Pre-existing damages to be identify', 76, NULL, NULL, '2024-10-24 19:24:17', '2024-10-24 19:24:17', NULL, NULL, 1),
(116, NULL, NULL, 'Note the external condition of the box placed on the pallet to be sound condition. If no, proceed for repair / replacement', 'External condition of the Pallets', 76, NULL, NULL, '2024-10-24 19:25:42', '2024-10-24 19:25:42', NULL, NULL, 3),
(117, NULL, NULL, 'The securing belts (2 nos.) on the pallet are in sound condition. If no, proceed for repair / replacement', '2 No of securing belts for each Pallets', 76, NULL, NULL, '2024-10-24 19:26:51', '2024-10-24 19:26:51', NULL, NULL, 3),
(118, NULL, NULL, 'Ensure the wooden pallet is in sound condition. If no, proceed for repair / replacement.', 'wooden pallet External condition need to be checked', 76, NULL, NULL, '2024-10-24 19:27:54', '2024-10-24 19:27:54', NULL, NULL, 3),
(119, NULL, NULL, 'The Carrier Vehicle Statutory documents to be checked ( driver licence, fitness certificate, etc..)', 'Carrier Vehicle Statutory documents', 76, NULL, NULL, '2024-10-24 19:28:27', '2024-10-24 19:28:27', NULL, NULL, 3),
(120, NULL, NULL, 'The Carrier Vehicle bed / floor for any deformities or protruding ends', 'No deformities at carrier Vehicle bed', 76, NULL, NULL, '2024-10-24 19:29:48', '2024-10-24 19:29:48', NULL, NULL, 3),
(121, NULL, NULL, 'The conditions of all the tyres to be checked of the Carrier Vehicle for any deep cut / wear and tear. If yes, proceed for repair / replacement.', 'Tyre condition of carrier vehicles', 76, NULL, NULL, '2024-10-24 19:30:33', '2024-10-24 19:30:33', NULL, NULL, 3),
(122, NULL, NULL, 'The lifting gears to be checked to be in good conditions and the sum of SWL of the lifting gear is more than the weight of the pallet. If no, proceed for repair / replacement', 'lifting gears of Carrier Vehicles', 76, NULL, NULL, '2024-10-24 19:31:10', '2024-10-24 19:31:10', NULL, NULL, 3),
(123, NULL, NULL, 'The fork lift used for lifting should be checked for adequate suitability.', 'Lifting Capacity of Fork Lift', 76, NULL, NULL, '2024-10-24 19:31:57', '2024-10-24 19:31:57', NULL, NULL, 3),
(124, NULL, NULL, 'Adequate dunnage material applied in void spaces', 'Adequate dunnage for safeguarding jerks', 76, NULL, NULL, '2024-10-24 19:34:32', '2024-10-24 19:34:32', NULL, NULL, 3),
(125, NULL, NULL, 'The corners of the pallets be placed in-line with the corners of the second pallet, if stacked on one another', 'Stacking procedure of solar pallets', 76, NULL, NULL, '2024-10-24 19:35:19', '2024-10-24 19:35:19', NULL, NULL, 3),
(126, NULL, NULL, 'Adequate no. of securing belts be applied to the pallets attached to the carrier vehicle body', '2 securing belts to be used for each pallets', 76, '1', NULL, '2024-10-24 19:36:02', '2024-10-29 22:15:14', NULL, NULL, 3),
(127, 16, 22, 'Nominated vehicle’s cargo floor bed shall not have any holes, inward dents or excessive rust', 'Record and report even minor damages,Carrier vehicle’s tail lights / break lights shall be in working conditions,•	Carrier vehicle’s all tyres shall be in good state (Good state means – the tyre is not worn out.', 77, NULL, NULL, '2024-12-10 00:24:47', '2025-06-27 14:54:18', NULL, NULL, 3),
(128, 16, 22, 'Truck shall not be overloaded', 'Close body truck needed for server transportation,•	All documents of the Nominated vehicle to be valid for the period of Transit', 77, NULL, NULL, '2024-12-10 00:26:14', '2025-06-27 14:54:04', NULL, NULL, 3),
(129, 16, 22, 'Ensure a proper and safe loading manner is being used', 'The packages loading to be in such a manner that no damage is done to the packages during loading', 77, NULL, NULL, '2024-12-10 00:27:17', '2025-06-27 14:53:52', NULL, NULL, 3),
(130, 16, 22, 'The weight of the packages to be evenly distributed', 'Pay extra attention while resting the cargo package on the vehicle’s bed', 77, NULL, NULL, '2024-12-10 00:28:14', '2025-06-27 14:53:37', NULL, NULL, 3),
(131, 16, 22, 'Monitor closely the loading operation from uplifting of the cargo package from the storage area to resting on the vehicle to prevent the cargo package from any damages.', 'Cushioning material needs to be used between the two servers to prevent internal damage.', 77, NULL, NULL, '2024-12-10 00:31:53', '2025-06-27 14:53:19', NULL, NULL, 3),
(132, 16, 22, 'Air bag need to place on gap between stacked box packages, to prevent movement of packages', 'Air bag need to place on gap', 77, NULL, NULL, '2024-12-10 00:33:15', '2025-06-27 14:53:02', NULL, NULL, 3),
(133, 16, 22, 'Adequate and proper lashing of the cargo package should be done with the help of cargo securing net to restrict its movement during transit.', 'cargo securing net should be good condition', 77, NULL, NULL, '2024-12-10 00:34:17', '2025-06-27 14:52:47', NULL, NULL, 3),
(134, 16, 22, 'Gap between stacked box packages shall not be considered', '•	Gap between stacked box packages shall not be considered', 77, NULL, NULL, '2024-12-10 00:35:16', '2025-06-27 14:52:30', NULL, NULL, 3),
(135, NULL, NULL, 'The Vehicle fitness certificate is valid and clearly indicating the load carrying permissible limit.', 'The Vehicle fitness certificate is valid and clearly indicating the load carrying permissible limit.', 78, NULL, NULL, '2025-02-24 17:48:46', '2025-06-27 14:43:38', '2025-06-27 14:43:38', NULL, 3),
(136, NULL, NULL, 'The Vehicle’s driver license copy Insurance is checked and is valid.', 'The Vehicle’s driver license copy Insurance is checked and is valid.', 78, NULL, NULL, '2025-02-24 17:49:11', '2025-06-27 14:43:46', '2025-06-27 14:43:46', NULL, 3),
(137, NULL, NULL, 'Lifting appliances to be in good conditions', 'Lifting appliances to be in good conditions', 78, NULL, NULL, '2025-02-24 17:49:49', '2025-06-27 14:43:51', '2025-06-27 14:43:51', NULL, 3),
(138, NULL, NULL, 'Each unit when placed/ stowed on the carrier vehicle shall be lashed individually to resist internal shift.', 'Each unit when placed/ stowed on the carrier vehicle shall be lashed individually to resist internal shift.', 78, NULL, NULL, '2025-02-24 18:04:23', '2025-06-27 14:44:41', '2025-06-27 14:44:41', NULL, 3),
(139, NULL, NULL, 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 78, NULL, NULL, '2025-02-24 18:05:04', '2025-06-27 14:44:36', '2025-06-27 14:44:36', NULL, 3),
(140, NULL, NULL, 'The Cargo is adequately secured as per securing plan and structural integrity check before transportation. Red lights / Red strips are installed on all the protruding ends.', 'The Cargo is adequately secured as per securing plan and structural integrity check before transportation. Red lights / Red strips are installed on all the protruding ends.', 78, NULL, NULL, '2025-02-24 18:06:36', '2025-06-27 14:44:29', '2025-06-27 14:44:29', NULL, 3),
(141, NULL, NULL, 'Weather-resistant coverings to protect the cargo from environmental exposure. Adequate securing is done for movement caused by strong winds.', 'Weather-resistant coverings to protect the cargo from environmental exposure. Adequate securing is done for movement caused by strong winds.', 78, NULL, NULL, '2025-02-24 18:07:19', '2025-06-27 14:44:20', '2025-06-27 14:44:20', NULL, 3),
(142, NULL, NULL, 'The lashing equipment’s/ securing apparatus like heavy-duty straps, chains, and support structures are checked for test certificates and SWL (Safe working load). The SWL shall be greater than 1.2 times the gross weight of the cargo unit. (Applicable for ODC units only)', 'The lashing equipment’s/ securing apparatus like heavy-duty straps, chains, and support structures are checked for test certificates and SWL (Safe working load). The SWL shall be greater than 1.2 times the gross weight of the cargo unit. (Applicable for ODC units only)', 78, NULL, NULL, '2025-02-24 18:08:23', '2025-06-27 14:44:12', '2025-06-27 14:44:12', NULL, 3),
(143, NULL, NULL, 'The Cargo is loaded with equal distribution of weight.', 'The Cargo is loaded with equal distribution of weight.', 78, NULL, NULL, '2025-02-24 18:08:52', '2025-06-27 14:44:06', '2025-06-27 14:44:06', NULL, 3),
(144, NULL, NULL, 'Angle bars in horizontal position are welded to restrict shift at both end', 'Angle bars in horizontal position are welded to restrict shift at both end', 78, NULL, NULL, '2025-02-24 18:09:27', '2025-06-27 14:44:02', '2025-06-27 14:44:02', NULL, 3),
(145, 19, 26, 'The Vehicle fitness certificate is valid and clearly indicating the load carrying permissible limit.', 'The Vehicle fitness certificate is valid and clearly indicating the load carrying permissible limit.', 79, NULL, NULL, '2025-03-04 21:18:07', '2025-06-27 14:41:32', NULL, NULL, 3),
(146, 19, 26, 'The Vehicle’s driver license copy Insurance is checked and is valid.', 'The Vehicle’s driver license copy Insurance is checked and is valid.', 79, NULL, NULL, '2025-03-04 21:19:43', '2025-06-27 14:41:22', NULL, NULL, 3),
(147, 19, 26, 'Lifting appliances to be in good conditions', 'Lifting appliances to be in good conditions', 79, NULL, NULL, '2025-03-04 21:21:57', '2025-06-27 14:41:11', NULL, NULL, 3),
(148, 19, 27, 'Each unit when placed/ stowed on the carrier vehicle shall be lashed individually to resist internal shift.', 'Each unit when placed/ stowed on the carrier vehicle shall be lashed individually to resist internal shift.', 79, NULL, NULL, '2025-03-04 21:23:33', '2025-06-27 14:40:31', NULL, NULL, 3),
(149, 19, 27, 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 79, NULL, NULL, '2025-03-04 21:26:41', '2025-06-27 14:40:09', NULL, NULL, 3),
(150, 19, 27, 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 'The cargo unit, post loading on the carrier vehicle shall be strapped or secured with chain (securing appliances).', 79, NULL, NULL, '2025-03-04 21:27:45', '2025-06-27 14:39:21', NULL, NULL, 3),
(151, 19, 27, 'The Cargo is adequately secured as per securing plan and structural integrity check before transportation. Red lights / Red strips are installed on all the protruding ends.', 'The Cargo is adequately secured as per securing plan and structural integrity check before transportation. Red lights / Red strips are installed on all the protruding ends.', 79, NULL, NULL, '2025-03-04 21:33:50', '2025-06-27 14:39:02', NULL, NULL, 3),
(152, 19, 27, 'Weather-resistant coverings to protect the cargo from environmental exposure. Adequate securing is done for movement caused by strong winds.', 'Weather-resistant coverings to protect the cargo from environmental exposure. Adequate securing is done for movement caused by strong winds.', 79, NULL, NULL, '2025-03-04 21:37:05', '2025-06-27 14:38:48', NULL, NULL, 3),
(153, 19, 27, 'The lashing equipment’s/ securing apparatus like heavy-duty straps, chains, and support structures are checked for test certificates and SWL (Safe working load). The SWL shall be greater than 1.2 times the gross weight of the cargo unit. (Applicable for ODC units only)', 'The lashing equipment’s/ securing apparatus like heavy-duty straps, chains, and support structures are checked for test certificates and SWL (Safe working load). The SWL shall be greater than 1.2 times the gross weight of the cargo unit. (Applicable for ODC units only)', 79, NULL, NULL, '2025-03-04 21:38:07', '2025-06-27 14:38:29', NULL, NULL, 3),
(154, 19, 27, 'The Cargo is loaded with equal distribution of weight.', 'The Cargo is loaded with equal distribution of weight.', 79, NULL, NULL, '2025-03-04 21:38:35', '2025-06-27 14:38:14', NULL, NULL, 3),
(155, 19, 27, 'Angle bars in horizontal position are welded to restrict shift at both end', 'Angle bars in horizontal position are welded to restrict shift at both end', 79, NULL, NULL, '2025-03-04 21:44:08', '2025-03-04 21:44:08', NULL, NULL, 3),
(156, 20, 29, 'The machinery should be placed in such a manner by taking into account the center of gravity.', 'The machinery should be placed in such a manner by taking into account the center of gravity.', 80, NULL, 'Yes', '2025-05-26 17:53:12', '2025-08-29 14:22:02', NULL, NULL, 3),
(157, 20, 29, 'Adequate lashing to be done with D- shackles by chain or nylon  rope the D-shackles capacity should be adequate enough to match the weight of the machinery .It should never be less than the weight of the machinery', 'Adequate lashing to be done with D- shackles by chain or nylon  rope the D-shackles capacity should be adequate enough to match the weight of the machinery .It should never be less than the weight of the machinery', 80, NULL, 'Yes', '2025-05-26 17:53:41', '2025-08-13 17:52:03', NULL, NULL, 3),
(158, 20, 29, 'The chain or  the rope should be without any knotting or any welding.It should not be worn out or rusted .', 'The chain or  the rope should be without any knotting or any welding. It should not be worn out or rusted .', 80, NULL, NULL, '2025-05-26 17:54:09', '2025-06-27 14:36:38', NULL, NULL, 3),
(159, 20, 29, 'There should  be overloading.', 'There should be overloading.', 80, NULL, 'No', '2025-05-26 17:54:40', '2025-08-13 17:51:36', NULL, NULL, 3),
(160, 20, 28, 'R', 'r', 80, NULL, NULL, '2025-05-26 18:39:17', '2025-05-26 18:39:45', '2025-05-26 18:39:45', NULL, 3),
(161, 20, 28, 'rrr', 'rrr', 80, NULL, NULL, '2025-05-26 18:41:35', '2025-05-26 18:42:14', '2025-05-26 18:42:14', NULL, 3),
(162, 20, 29, 'The trailer should be lengthy enough  for the size of the machinery .', 'The trailer should be lengthy enough  for the size of the machinery .', 80, NULL, 'Yes', '2025-05-26 18:42:06', '2025-08-29 14:21:41', NULL, NULL, 3),
(163, 20, 29, 'The exposed areas of the machinery and especially electronic components should be well protected with plastic sheets and tarpaulin', 'The exposed areas of the machinery and especially electronic components should be well protected with plastic sheets and tarpaulin', 80, NULL, 'Yes', '2025-05-26 18:42:52', '2025-08-29 14:21:27', NULL, NULL, 3),
(164, 20, 29, 'The reflector sticker to be placed on all the sides.', 'The reflector sticker to be placed on all the sides.', 80, NULL, 'Yes', '2025-05-26 18:43:15', '2025-08-29 14:21:02', NULL, NULL, 3),
(165, 20, 29, 'The driver should be adequately advised to drive at the proper speed and maintain tyre pressure.', 'The driver should be adequately advised to drive at the proper speed and maintain tyre pressure.', 80, NULL, 'Yes', '2025-05-26 19:05:07', '2025-08-13 17:50:50', NULL, NULL, 3),
(166, 20, 29, 'The entire loading and lashing photos in all angles with capacity of the D- shackles,to be taken and kept for records', 'The entire loading and lashing photos in all angles with capacity of the D- shackles,to be taken and kept for records', 80, NULL, 'Yes', '2025-05-26 19:17:10', '2025-08-13 17:50:18', NULL, NULL, 3),
(167, 20, 29, 'All the vehicle documents such as RC, fitness certificate, permit, insurance and driving licence to be obtained and kept in records', 'All the vehicle documents such as RC, fitness certificate, permit, insurance and driving licence to be obtained and kept in records', 80, NULL, 'Yes', '2025-05-26 19:17:30', '2025-08-13 17:49:34', NULL, NULL, 3),
(168, 21, 31, 'Warranted that lifting appliances and securing appliances  shall have SWL 1.1 times of the total lifting weight. The lifting/securing appliances such as straps, chains etc shall have a breaking strength or safe working load weight equally to or greater than the cargo weight by 1.1 times', '1.	Warranted that lifting appliances and securing appliances  shall have SWL 1.1 times of the total lifting weight. The lifting/securing appliances such as straps, chains etc shall have a breaking strength or safe working load weight equally to or greater than the cargo weight by 1.1 times', 81, NULL, NULL, '2025-06-09 20:18:14', '2025-06-27 14:33:44', NULL, NULL, 3),
(169, 21, 30, 'Insured shall ensure 2 pairs of lifting chains/straps/slings or applicable lifting apparatus shall be used to lift the unit insured', 'Insured shall ensure 2 pairs of lifting chains/straps/slings or applicable lifting apparatus shall be used to lift the unit insured', 81, NULL, NULL, '2025-06-09 20:19:05', '2025-06-17 12:34:02', NULL, NULL, 3),
(170, 21, 30, 'The Lifting operations shall be carried out on a safe ground that is either concrete or steel plates shall be placed on muddy ground', 'The Lifting operations shall be carried out on a safe ground that is either concrete or steel plates shall be placed on muddy ground', 81, NULL, NULL, '2025-06-09 20:20:04', '2025-06-27 14:33:20', NULL, NULL, 3),
(171, 21, 30, 'Lifting shall be carried out with only one crane and NOT with 2 cranes in pair or as a tandem operation.', 'Lifting shall be carried out with only one crane and NOT with 2 cranes in pair or as a tandem operation.', 81, NULL, NULL, '2025-06-09 20:21:04', '2025-06-27 14:33:07', NULL, NULL, 3),
(172, 21, 31, 'Warranted ODC units shall have high visibility / reflective tapes pasted at the rear end and side areas which are above 2.5m as width', 'Warranted ODC units shall have high visibility / reflective tapes pasted at the rear end and side areas which are above 2.5m as width', 81, NULL, NULL, '2025-06-09 20:24:13', '2025-06-27 14:32:52', NULL, NULL, 3),
(173, 21, 30, 'Warranted ODC unit with height >3.5m shall be loaded on low bed carrier vehicle or hydraulic axle vehicle', 'Warranted ODC unit with height >3.5m shall be loaded on low bed carrier vehicle or hydraulic axle vehicle', 81, NULL, NULL, '2025-06-09 20:25:10', '2025-06-27 14:32:32', NULL, NULL, 3),
(174, 21, 31, 'Warranted ODC unit which do not have a flat bottom shall have saddle-arrangements welded on the carrier deck or rigidly secured on carrier vehicle deck and ODC unit shall be stacked on eth saddle-arrangement for uniform stability and adequate securing', 'Warranted ODC unit which do not have a flat bottom shall have saddle-arrangements welded on the carrier deck or rigidly secured on carrier vehicle deck and ODC unit shall be stacked on eth saddle-arrangement for uniform stability and adequate securing', 81, NULL, NULL, '2025-06-09 20:26:12', '2025-06-27 14:31:57', NULL, NULL, 3),
(175, 21, 31, 'Warranted ODC loaded on carrier vehicles shall have angle bars with 35mm to 50mm thickness, welded vertically upto a height of 2 feet at the side of the deck to prevent sliding in case of lashing failure when vehicle tends to list at one side due to uneven roads or wrong manoeuvring of the driver', 'Warranted ODC loaded on carrier vehicles shall have angle bars with 35mm to 50mm thickness, welded vertically upto a height of 2 feet at the side of the deck to prevent sliding in case of lashing failure when vehicle tends to list at one side due to uneven roads or wrong manoeuvring of the driver', 81, NULL, NULL, '2025-06-09 20:27:09', '2025-06-27 14:31:29', NULL, NULL, 3),
(176, 21, 31, 'Each unit if ODC, shall be secured on carrier vehicle deck with securing appliances lashed on the rigid point of the carrier vehicle bed i.e  eye bolts, lashing rings which is fixed to the vehicle and dedicated lifting/securing point of the cargo and if it’s a package / parcel / unit which do not have a securing point, lashing appliances shall be used from left to right side such that internal shift when in transit shall be restrict in all direction of forces acting on the cargo', 'Each unit if ODC, shall be secured on carrier vehicle deck with securing appliances lashed on the rigid point of the carrier vehicle bed i.e  eye bolts, lashing rings which is fixed to the vehicle and dedicated lifting/securing point of the cargo and if it’s a package / parcel / unit which do not have a securing point, lashing appliances shall be used from left to right side such that internal shift when in transit shall be restrict in all direction of forces acting on the cargo', 81, NULL, NULL, '2025-06-09 20:29:01', '2025-06-27 14:30:37', NULL, NULL, 3),
(177, NULL, NULL, '1', '1', 81, NULL, NULL, '2025-06-16 18:34:00', '2025-06-16 18:34:04', '2025-06-16 18:34:04', NULL, 1),
(181, 25, 41, 'Test pre lifting question for mumbai', 'Test instruction', 83, NULL, 'No', '2025-07-21 14:32:33', '2025-07-21 14:32:33', NULL, NULL, 3),
(182, 25, 42, 'Test post lifting question for mumbai', 'Test instruction', 83, NULL, 'Maybe', '2025-07-21 14:33:01', '2025-07-21 14:33:01', NULL, NULL, 4),
(183, 26, 43, 'Test pre lifting question for pune', 'Test instruction', 83, NULL, 'No', '2025-07-21 14:34:04', '2025-07-21 14:34:04', NULL, NULL, 3),
(184, 26, 44, 'Test post lifting question for pune', 'Test instruction', 83, NULL, 'Yes', '2025-07-21 14:34:30', '2025-07-21 14:34:30', NULL, NULL, 4),
(185, 27, 45, 'Vehicle check completed for any protuding end and the truck floor is clean, dry, and free of debris or oil.', 'Vehicle check', 84, NULL, 'Yes', '2025-08-08 17:58:12', '2025-08-08 17:58:12', NULL, NULL, 3),
(186, 27, 45, 'Pallets (if used) are in good condition and properly aligned with cargo and adequetly strapped.', 'Pallets Condition', 84, NULL, 'Yes', '2025-08-08 18:14:14', '2025-08-08 18:14:14', NULL, NULL, 3),
(187, 27, 45, 'The truck’s used are Full Body Trucks and sidewalls or barriers are intact and capable of containing the load.', 'Trucks and sidewalls Condition', 84, NULL, 'Yes', '2025-08-08 18:15:19', '2025-08-08 18:15:19', NULL, NULL, 3),
(188, 27, 45, 'The paint drums are stacked on truck floor in two layers with 6 x 2 nos strapped together with polyster strap and rachet.', 'Cargo Strapping', 84, NULL, 'Yes', '2025-08-08 18:16:25', '2025-08-08 18:16:25', NULL, NULL, 3),
(189, 27, 45, 'Above every 2 layer of paint drums a wooden ply placement is not required for support.', 'wooden ply placement', 84, NULL, 'No', '2025-08-08 18:17:55', '2025-08-08 18:17:55', NULL, NULL, 3),
(190, 27, 45, 'Wooden Plank used as a partition are broken / damaged / worn out.', 'Wooden Plank Condition', 84, NULL, 'No', '2025-08-08 18:21:37', '2025-08-08 18:21:37', NULL, NULL, 3),
(191, 27, 45, 'Maximum stacking is only 2 layer partion ply used. No loading above it is noted', 'Maximum stacking Instruction', 84, NULL, 'Yes', '2025-08-08 18:23:37', '2025-08-08 18:23:37', NULL, NULL, 3),
(192, 27, 45, 'Dunnage of cardbords placed between the sidebody and the drums.', 'Dunnage insertion condition', 84, NULL, 'Yes', '2025-08-08 18:24:53', '2025-08-08 18:24:53', NULL, NULL, 3),
(193, 27, 45, 'The paint drum are loaded only till the height of the full body truck.', 'Loading Hight in carrier vehicle', 84, NULL, 'Yes', '2025-08-08 18:25:50', '2025-08-08 18:25:50', NULL, NULL, 3),
(194, 27, 45, 'Polyster strap used are tied by knots or broken in between.', 'Polyster strap', 84, NULL, 'No', '2025-08-08 18:47:28', '2025-08-08 18:47:28', NULL, NULL, 3),
(195, 27, 45, 'Wooden Plank used as a partition above the bottom tier are broken / damaged / worn out.', 'Wooden Plank condition', 84, NULL, 'No', '2025-08-08 18:48:26', '2025-08-08 18:48:26', NULL, NULL, 3),
(196, 27, 45, 'The driver has received proper documentation, driving and handling instructions.', 'driver instructions', 84, NULL, 'Yes', '2025-08-08 18:49:11', '2025-08-08 18:49:11', NULL, NULL, 3),
(197, 27, 45, 'The vehicle is covered with 3 Layer tarpaulin secured with adequate lashing ropes / belts and 6 nos of strip locks', 'Tarpaulin  condition', 84, NULL, 'Yes', '2025-08-08 18:50:07', '2025-08-08 18:50:07', NULL, NULL, 3),
(198, 29, 46, 'Vehicle check for any damages to body or any tron tarpauline patch or strip locks broken.', 'Vehicle check', 84, NULL, 'No', '2025-08-11 14:26:29', '2025-08-11 14:28:12', NULL, NULL, 3),
(199, 29, 46, 'Pallets (if used) are in good condition and properly aligned with cargo and adequetly strapped.', 'Pallets  Condition', 84, NULL, 'Yes', '2025-08-11 14:27:52', '2025-08-11 14:27:52', NULL, NULL, 3),
(200, 29, 46, 'Was the truck’s sidewalls or barriers are intact and capable of containing the load.', 'Truck’s sidewalls', 84, NULL, 'Yes', '2025-08-11 14:28:58', '2025-08-11 14:28:58', NULL, NULL, 3),
(201, 29, 46, 'The paint drums are stacked on truck floor in two layers with 6 x 2 nos strapped together with polyster strap and rachet', 'Stacking Condition', 84, NULL, 'Yes', '2025-08-11 14:30:07', '2025-08-11 14:30:07', NULL, NULL, 3),
(202, 29, 46, 'Above every 2 layer of paint drums a wooden ply is placed above for support.', 'Wooden Ply support', 84, NULL, 'Yes', '2025-08-11 14:30:59', '2025-08-11 14:30:59', NULL, NULL, 3),
(203, 29, 46, 'Wooden Plank used as a partition are broken / damaged / worn out.', 'Wooden Plank Condition', 84, NULL, 'No', '2025-08-11 14:31:54', '2025-08-11 14:31:54', NULL, NULL, 3),
(204, 29, 46, 'Maximum stacking is only 3 tier with 2 layer partion ply used. No loading above it is noted', 'Maximum stacking', 84, NULL, 'Yes', '2025-08-11 14:32:38', '2025-08-11 14:32:38', NULL, NULL, 3),
(205, 29, 46, 'Dunnage of cardboards placed between the side body and the drums.', 'Dunnage of cardboards', 84, NULL, 'Yes', '2025-08-11 14:33:28', '2025-08-11 14:33:28', NULL, NULL, 3),
(206, 29, 46, 'The paint drum are loaded only till the height of the full body truck.', 'Drum Loading condition', 84, NULL, 'Yes', '2025-08-11 14:34:24', '2025-08-11 14:34:24', NULL, NULL, 3),
(207, 29, 46, 'Polyster strap used are tied by knots or broken in between.', 'Polyster strap condition', 84, NULL, 'No', '2025-08-11 14:38:40', '2025-08-11 14:38:40', NULL, NULL, 3),
(208, 29, 46, 'The driver has received proper documentation, driving and handling instructions.', 'Driver awareness', 84, NULL, 'Yes', '2025-08-11 14:39:58', '2025-08-11 14:39:58', NULL, NULL, 3),
(209, 29, 46, 'The vehicle is covered with 3 Layer tarpaulin secured with adequate lashing ropes / belts and 6 nos of strip locks', 'Tarpaulin condition', 84, NULL, 'Yes', '2025-08-11 14:40:55', '2025-08-11 14:40:55', NULL, NULL, 3),
(210, 30, 48, 'Insured\'s  RPAS confirms the carrier vehicle is lined up as per SOP 8.1.1 and  / or 8.1.2 condition specifications', 'Carrier vehicle', 85, NULL, 'Yes', '2025-09-11 17:54:15', '2025-09-11 17:54:15', NULL, NULL, 3),
(211, 30, 48, 'The Vehicle is satisfactory as per the requirement 8.2 of the SOP. Insured\'s  RPAS confirms', 'Vehicle check', 85, NULL, 'Yes', '2025-09-11 17:55:12', '2025-09-11 17:55:12', NULL, NULL, 3),
(212, 30, 48, 'The Insured RPAS is aware of the point 7.2.7 and point 11 of the SOP', 'Overloading Condition', 85, NULL, 'Yes', '2025-09-11 17:59:24', '2025-09-11 17:59:24', NULL, NULL, 3),
(213, 30, 47, 'Lifting appliances used as as per compliance of SOP 7.3 condition', 'Lifting appliances', 85, NULL, 'Yes', '2025-09-11 18:00:52', '2025-09-11 18:00:52', NULL, NULL, 3),
(214, 30, 48, 'The Securing is inspected and found to be in compliance as per SOP para 7.4 conditions', 'Securing condition', 85, NULL, 'Yes', '2025-09-11 18:02:35', '2025-09-11 18:02:35', NULL, NULL, 3),
(215, 30, 48, 'As per 7.5.9, The driver is explained in brief on the risk of overturning, toppling during turning and maneuvering of the vehicle at turns  and the risk of vehicle’s structural failure', 'Driver Intimation', 85, NULL, 'Yes', '2025-09-11 18:04:32', '2025-09-11 18:04:32', NULL, NULL, 3),
(216, 30, 48, 'As per SOP 7.5.10, RED light is rigged and flag is placed on the overhang portion of the tower component', 'RED light is rigged and flag is placed on the overhang portion', 85, NULL, 'Yes', '2025-09-11 18:05:58', '2025-09-11 18:05:58', NULL, NULL, 3),
(217, 30, 48, 'Confirm route survey is conducted as per the SOP of this transit and Driver will be using the same route.', 'Route Survey', 85, NULL, 'Yes', '2025-09-11 18:07:08', '2025-09-11 18:07:08', NULL, NULL, 3),
(218, 30, 48, 'The route does not have a rise of gradient more than 5% gradient, i.e 1 meter change in road altitude level in a distance of 20 meters, excluding flyovers', 'The route does not have a rise of gradient more than 5%', 85, NULL, 'Yes', '2025-09-11 18:08:35', '2025-09-11 18:08:35', NULL, NULL, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklist_masters`
--
ALTER TABLE `checklist_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checklist_masters_group_id_foreign` (`group_id`),
  ADD KEY `checklist_masters_phase_id_foreign` (`phase_id`),
  ADD KEY `checklist_masters_zone_id_foreign` (`zone_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checklist_masters`
--
ALTER TABLE `checklist_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklist_masters`
--
ALTER TABLE `checklist_masters`
  ADD CONSTRAINT `checklist_masters_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `checklist_masters_phase_id_foreign` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`),
  ADD CONSTRAINT `checklist_masters_zone_id_foreign` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
