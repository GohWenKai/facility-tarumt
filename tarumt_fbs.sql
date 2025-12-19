-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 11:54 AM
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
-- Database: `tarumt_fbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `condition` varchar(50) DEFAULT 'working',
  `maintenance_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `facility_id`, `name`, `type`, `serial_number`, `condition`, `maintenance_note`, `created_at`, `updated_at`) VALUES
(14, 12, 'Projector', 'Equipment', 'SN-2025-003', 'Fair', NULL, '2025-12-19 01:35:50', '2025-12-19 02:01:51'),
(15, 12, 'Chair', 'Furniture', 'CH001', 'Good', NULL, '2025-12-19 02:02:30', '2025-12-19 02:02:30');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 6, 'created', 'App\\Models\\Asset', 12, NULL, '{\"facility_id\":\"1\",\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-001\",\"condition\":\"Good\",\"maintenance_note\":null,\"updated_at\":\"2025-12-19T07:30:09.000000Z\",\"created_at\":\"2025-12-19T07:30:09.000000Z\",\"id\":12}', '127.0.0.1', '2025-12-18 23:30:09', '2025-12-18 23:30:09'),
(2, 6, 'updated', 'App\\Models\\Asset', 8, '{\"id\":8,\"facility_id\":1,\"name\":\"Whiteboard\",\"type\":\"Equipment\",\"serial_number\":\"WB-001\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-11-30T04:50:17.000000Z\",\"updated_at\":\"2025-11-30T04:50:42.000000Z\"}', '{\"condition\":\"Fair\",\"updated_at\":\"2025-12-19 08:53:46\"}', '127.0.0.1', '2025-12-19 00:53:46', '2025-12-19 00:53:46'),
(3, 6, 'created', 'App\\Models\\Asset', 13, NULL, '{\"facility_id\":\"9\",\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-002\",\"condition\":\"Good\",\"maintenance_note\":null,\"updated_at\":\"2025-12-19T09:21:30.000000Z\",\"created_at\":\"2025-12-19T09:21:30.000000Z\",\"id\":13}', '127.0.0.1', '2025-12-19 01:21:30', '2025-12-19 01:21:30'),
(4, 6, 'created', 'App\\Models\\Asset', 14, NULL, '{\"facility_id\":\"12\",\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-003\",\"condition\":\"Good\",\"maintenance_note\":null,\"updated_at\":\"2025-12-19T09:35:50.000000Z\",\"created_at\":\"2025-12-19T09:35:50.000000Z\",\"id\":14}', '127.0.0.1', '2025-12-19 01:35:50', '2025-12-19 01:35:50'),
(5, 6, 'updated', 'App\\Models\\Asset', 8, '{\"id\":8,\"facility_id\":1,\"name\":\"Whiteboard\",\"type\":\"Equipment\",\"serial_number\":\"WB-001\",\"condition\":\"Fair\",\"maintenance_note\":null,\"created_at\":\"2025-11-30T04:50:17.000000Z\",\"updated_at\":\"2025-12-19T08:53:46.000000Z\"}', '{\"condition\":\"Damaged\",\"maintenance_note\":\"Bulb Problem\",\"updated_at\":\"2025-12-19 09:54:31\"}', '127.0.0.1', '2025-12-19 01:54:31', '2025-12-19 01:54:31'),
(6, 6, 'updated', 'App\\Models\\Asset', 8, '{\"id\":8,\"facility_id\":1,\"name\":\"Whiteboard\",\"type\":\"Equipment\",\"serial_number\":\"WB-001\",\"condition\":\"Damaged\",\"maintenance_note\":\"Bulb Problem\",\"created_at\":\"2025-11-30T04:50:17.000000Z\",\"updated_at\":\"2025-12-19T09:54:31.000000Z\"}', '{\"condition\":\"Good\",\"maintenance_note\":null,\"updated_at\":\"2025-12-19 09:56:31\"}', '127.0.0.1', '2025-12-19 01:56:31', '2025-12-19 01:56:31'),
(7, 6, 'updated', 'App\\Models\\Asset', 8, '{\"id\":8,\"facility_id\":1,\"name\":\"Whiteboard\",\"type\":\"Equipment\",\"serial_number\":\"WB-001\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-11-30T04:50:17.000000Z\",\"updated_at\":\"2025-12-19T09:56:31.000000Z\"}', '{\"type\":\"Furniture\",\"updated_at\":\"2025-12-19 10:01:40\"}', '127.0.0.1', '2025-12-19 02:01:40', '2025-12-19 02:01:40'),
(8, 6, 'updated', 'App\\Models\\Asset', 14, '{\"id\":14,\"facility_id\":12,\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-003\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-12-19T09:35:50.000000Z\",\"updated_at\":\"2025-12-19T09:35:50.000000Z\"}', '{\"condition\":\"Fair\",\"updated_at\":\"2025-12-19 10:01:51\"}', '127.0.0.1', '2025-12-19 02:01:51', '2025-12-19 02:01:51'),
(9, 6, 'created', 'App\\Models\\Asset', 15, NULL, '{\"facility_id\":\"12\",\"name\":\"Chair\",\"type\":\"Furniture\",\"serial_number\":\"CH001\",\"condition\":\"Good\",\"maintenance_note\":null,\"updated_at\":\"2025-12-19T10:02:30.000000Z\",\"created_at\":\"2025-12-19T10:02:30.000000Z\",\"id\":15}', '127.0.0.1', '2025-12-19 02:02:30', '2025-12-19 02:02:30'),
(10, 6, 'deleted', 'App\\Models\\Asset', 8, '{\"id\":8,\"facility_id\":1,\"name\":\"Whiteboard\",\"type\":\"Furniture\",\"serial_number\":\"WB-001\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-11-30T04:50:17.000000Z\",\"updated_at\":\"2025-12-19T10:01:40.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:08:53', '2025-12-19 02:08:53'),
(11, 6, 'deleted', 'App\\Models\\Asset', 9, '{\"id\":9,\"facility_id\":5,\"name\":\"Whiteboard\",\"type\":\"Equipment\",\"serial_number\":\"WB-002\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-11-30T04:53:01.000000Z\",\"updated_at\":\"2025-11-30T04:53:01.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:08:55', '2025-12-19 02:08:55'),
(12, 6, 'deleted', 'App\\Models\\Asset', 10, '{\"id\":10,\"facility_id\":4,\"name\":\"Shao Heng is Gay\",\"type\":\"Other\",\"serial_number\":\"SHIG-67\",\"condition\":\"Damaged\",\"maintenance_note\":\"Shao Heng Start Gaying people\",\"created_at\":\"2025-11-30T17:10:10.000000Z\",\"updated_at\":\"2025-12-01T17:55:31.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:08:56', '2025-12-19 02:08:56'),
(13, 6, 'deleted', 'App\\Models\\Asset', 11, '{\"id\":11,\"facility_id\":2,\"name\":\"Liang Is Brightdd\",\"type\":\"Electronics\",\"serial_number\":\"LIB-002\",\"condition\":\"Fair\",\"maintenance_note\":null,\"created_at\":\"2025-12-01T18:09:05.000000Z\",\"updated_at\":\"2025-12-03T04:02:13.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:08:58', '2025-12-19 02:08:58'),
(14, 6, 'deleted', 'App\\Models\\Asset', 12, '{\"id\":12,\"facility_id\":1,\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-001\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-12-19T07:30:09.000000Z\",\"updated_at\":\"2025-12-19T07:30:09.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:08:59', '2025-12-19 02:08:59'),
(15, 6, 'deleted', 'App\\Models\\Asset', 13, '{\"id\":13,\"facility_id\":9,\"name\":\"Projector\",\"type\":\"Equipment\",\"serial_number\":\"SN-2025-002\",\"condition\":\"Good\",\"maintenance_note\":null,\"created_at\":\"2025-12-19T09:21:30.000000Z\",\"updated_at\":\"2025-12-19T09:21:30.000000Z\"}', NULL, '127.0.0.1', '2025-12-19 02:11:12', '2025-12-19 02:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_cost` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `facility_id`, `start_time`, `end_time`, `total_cost`, `status`, `created_at`, `updated_at`) VALUES
('1c291a26-98f3-46b6-93e3-f7a75b7922fe', 2, 2, '2025-11-29 00:43:00', '2025-11-29 01:43:00', 1, 'rejected', '2025-11-28 08:43:43', '2025-11-28 08:45:06'),
('236c66a5-1fda-42b0-a14c-789c470e56b5', 2, 3, '2025-11-30 12:30:00', '2025-11-30 14:30:00', 1, 'approved', '2025-11-29 21:23:02', '2025-11-29 21:23:42'),
('4c96eb1d-3e02-489b-816f-0f75b91d90d6', 2, 1, '2025-12-01 08:00:00', '2025-12-01 08:30:00', 1, 'rejected', '2025-12-01 09:50:58', '2025-12-02 18:51:11'),
('627fc7a5-7122-4f65-8c50-5256971dca02', 2, 1, '2025-11-29 08:00:00', '2025-11-29 08:30:00', 1, 'rejected', '2025-11-28 20:32:33', '2025-11-28 20:51:00'),
('6fc21de3-49fc-4b3c-b9ee-8c4e69b8ff1d', 2, 2, '2025-11-28 08:00:00', '2025-11-28 11:00:00', 1, 'rejected', '2025-11-28 10:45:30', '2025-11-28 10:46:27'),
('8c88eeef-b7e5-406e-90b9-8b4441a7eaf5', 2, 2, '2025-11-28 14:08:00', '2025-11-28 16:09:00', 3, 'rejected', '2025-11-28 10:09:19', '2025-11-28 10:46:24'),
('bad4178e-b04e-471e-b428-4ff26fc9caf1', 2, 2, '2025-11-28 08:30:00', '2025-11-28 10:00:00', 1, 'rejected', '2025-11-28 10:56:24', '2025-11-28 20:51:03'),
('d0ad137d-9464-4de0-a733-b5723c46bbd6', 2, 1, '2025-12-03 08:00:00', '2025-12-03 08:30:00', 1, 'approved', '2025-12-03 09:50:46', '2025-12-03 09:51:41'),
('d1148825-1714-4249-a4d9-e33ed2886da2', 2, 2, '2025-11-30 12:00:00', '2025-11-30 13:30:00', 1, 'rejected', '2025-11-29 21:15:11', '2025-11-29 21:22:36');

-- --------------------------------------------------------

--
-- Table structure for table `booking_approvals`
--

CREATE TABLE `booking_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` char(36) NOT NULL,
  `approver_id` bigint(20) UNSIGNED NOT NULL,
  `comments` text DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `location`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Block A (Admin)', 'Main Campus', NULL, '2025-11-20 12:06:35', NULL),
(2, 'Block H (Technology)', 'East Campus', NULL, '2025-11-20 12:06:35', NULL),
(3, 'Block L (Library)', 'Central', NULL, '2025-11-20 12:06:35', NULL),
(7, 'Block J', '--', NULL, '2025-12-19 01:35:02', '2025-12-19 01:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'active',
  `start_time` time DEFAULT '08:00:00',
  `end_time` time NOT NULL DEFAULT '22:00:00',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `building_id`, `name`, `type`, `capacity`, `status`, `start_time`, `end_time`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 2, 'H101 - Computer Lab', 'Lab', 30, 'Available', '08:00:00', '08:30:00', 'facilities/DY3xKmzpfKwYmaRcePWDejsPrU5xidvEnDIAwWo5.png', '2025-11-20 12:06:50', '2025-11-28 11:48:23'),
(2, 2, 'H204 - Lecture Hall', 'Room', 150, 'Available', '08:00:00', '22:00:00', 'facilities/pZWstQ83SZT5BpqZiHQctEMyFTYj6Krr8QGJxkUh.png', '2025-11-20 12:06:50', '2025-11-23 19:40:07'),
(3, 3, 'L-Discussion Room 1', 'Room', 6, 'Available', '08:00:00', '22:00:00', NULL, '2025-11-20 12:06:50', '2025-11-23 19:49:43'),
(4, 3, 'L-Discussion Room 2', 'Room', 6, 'Closed', '08:00:00', '22:00:00', NULL, '2025-11-20 12:06:50', '2025-11-23 19:50:43'),
(5, 1, 'A101 - Meeting Room', 'Room', 20, 'Available', '08:00:00', '22:00:00', NULL, '2025-11-20 12:06:50', '2025-11-23 19:49:57'),
(9, 3, 'Shao Heng is Gay', 'Room', 10, 'Closed', '08:00:00', '22:00:00', 'facilities/X6xIDbDeEMKQVuZ77jpZn2nUXZphotdsb9CdDJSK.png', '2025-12-01 09:58:51', '2025-12-01 09:58:51'),
(12, 7, 'J001', 'Room', 25, 'Available', '08:00:00', '10:00:00', NULL, '2025-12-19 01:35:30', '2025-12-19 01:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', 'b2fd1038fd61221d8214bcb614da93199157ae5548617feab911f75995c9188e', '[\"*\"]', NULL, NULL, '2025-11-25 10:22:02', '2025-11-25 10:22:02'),
(2, 'App\\Models\\User', 1, 'auth_token', '82f3295cb0f775f1752bf0d4ae870aab1614e2c71f728971d1c9e21be102981e', '[\"*\"]', NULL, NULL, '2025-11-25 11:40:49', '2025-11-25 11:40:49'),
(3, 'App\\Models\\User', 1, 'auth_token', 'd1e2a46eaae330e225cf44dcabca5773a16b1e2d39c87d0f5d727f88e2d08e41', '[\"*\"]', NULL, NULL, '2025-11-25 11:50:08', '2025-11-25 11:50:08'),
(4, 'App\\Models\\User', 1, 'auth_token', '8cbe5dd4e05f0136957890f65a70d9f9d86e57f6abe707a1fcda3b9566c3a137', '[\"*\"]', NULL, NULL, '2025-11-25 11:50:47', '2025-11-25 11:50:47'),
(5, 'App\\Models\\User', 1, 'auth_token', '55a35aa098ace2af5f941f7aef3b3d023c75366ebf89a889c69b4a3b109bd697', '[\"*\"]', NULL, NULL, '2025-11-25 11:52:18', '2025-11-25 11:52:18'),
(6, 'App\\Models\\User', 1, 'auth_token', '4ec039dde4449c37383621a852ea85348b4c6d9f4130ec5d8cfa7a4ac3d189fe', '[\"*\"]', NULL, NULL, '2025-11-25 11:59:40', '2025-11-25 11:59:40'),
(7, 'App\\Models\\User', 1, 'auth_token', '1718338eac525ed2836d6608e2d26524526d1d04011ebe0b3889518a837d2f64', '[\"*\"]', NULL, NULL, '2025-11-25 12:00:37', '2025-11-25 12:00:37'),
(8, 'App\\Models\\User', 1, 'auth_token', 'e3dd8703824a97c0384561a4048ae33b7adefc6c3506af30945897bcedf17768', '[\"*\"]', NULL, NULL, '2025-11-25 12:02:36', '2025-11-25 12:02:36'),
(9, 'App\\Models\\User', 1, 'auth_token', '4a3c5d684e407007d15246ac96bcca27adb43a853dd53077e897bf24416a2edf', '[\"*\"]', NULL, NULL, '2025-11-25 12:27:56', '2025-11-25 12:27:56'),
(10, 'App\\Models\\User', 1, 'auth_token', '055e6e6d911089e6921bea6a599acd0c865a1744bf723aada5e0abfbeac29430', '[\"*\"]', NULL, NULL, '2025-11-25 12:52:00', '2025-11-25 12:52:00'),
(11, 'App\\Models\\User', 1, 'auth_token', 'da1f1bdf421fd59cb50b08b7cdd0a36aac054801c778022b52b5f9ec73e2b66c', '[\"*\"]', NULL, NULL, '2025-11-25 12:52:37', '2025-11-25 12:52:37'),
(12, 'App\\Models\\User', 1, 'auth_token', 'e581494e691e2638d04519cb66b5b5f95a86b652058ed66aade5f323a11a17f7', '[\"*\"]', NULL, NULL, '2025-11-25 13:08:16', '2025-11-25 13:08:16'),
(13, 'App\\Models\\User', 1, 'auth_token', 'de58c42c21fcb7d42b2b3dc251dbeb287ccd76e5d63eb31414cd658b80142ade', '[\"*\"]', NULL, NULL, '2025-11-25 13:11:55', '2025-11-25 13:11:55'),
(14, 'App\\Models\\User', 1, 'auth_token', '2299ab3f173f96c146d3f9fc91eb409cb2470f7e9b179db8c23ff3cc4efe7362', '[\"*\"]', NULL, NULL, '2025-11-25 17:39:23', '2025-11-25 17:39:23'),
(15, 'App\\Models\\User', 2, 'auth_token', '0249ef717d1424edb5fba55c45ea54374199b89de3a4526cc24300a1fa93cbeb', '[\"*\"]', NULL, NULL, '2025-11-25 18:28:11', '2025-11-25 18:28:11'),
(16, 'App\\Models\\User', 1, 'auth_token', '936f3c6024c356faf220ec15f19b955179ca608330246c7a3b41d6c4e5a7c8f8', '[\"*\"]', NULL, NULL, '2025-11-25 18:41:37', '2025-11-25 18:41:37'),
(17, 'App\\Models\\User', 1, 'auth_token', '74240b5e65068c70b2d304acd98f0fe08f4b8b72ac67d912764b3253d935e672', '[\"*\"]', NULL, NULL, '2025-11-28 07:57:16', '2025-11-28 07:57:16'),
(18, 'App\\Models\\User', 2, 'auth_token', 'e4bed1ca879503001fa10d2f4712ffaf223f884ce74a6549124d67a4c72f6b43', '[\"*\"]', NULL, NULL, '2025-11-28 07:58:35', '2025-11-28 07:58:35'),
(19, 'App\\Models\\User', 1, 'auth_token', 'e8f690ab826214fdcab5be60c3aa1c472c423f091843824b2909079bad164e8d', '[\"*\"]', NULL, NULL, '2025-11-28 07:58:52', '2025-11-28 07:58:52'),
(20, 'App\\Models\\User', 2, 'auth_token', 'b9de57a9791d3234f16d6f60e9fb6eb44c15722cbb085e22f95147f979d68692', '[\"*\"]', NULL, NULL, '2025-11-28 08:06:28', '2025-11-28 08:06:28'),
(21, 'App\\Models\\User', 1, 'auth_token', '7ae39b065b9b6f758e19f1017df611041bebe594bd92bc540d04dcb03de4f8e1', '[\"*\"]', NULL, NULL, '2025-11-28 08:06:40', '2025-11-28 08:06:40'),
(22, 'App\\Models\\User', 2, 'auth_token', 'b707364dcd16e736bff76f57e67da30ce178ffe0c8123d160eae88d581eba48c', '[\"*\"]', NULL, NULL, '2025-11-28 08:09:36', '2025-11-28 08:09:36'),
(23, 'App\\Models\\User', 1, 'auth_token', '3d6f2c999fab30121e26445ce6609449311799d3a370aa20f8699012acc64008', '[\"*\"]', NULL, NULL, '2025-11-28 08:09:49', '2025-11-28 08:09:49'),
(24, 'App\\Models\\User', 2, 'auth_token', '7e04917d9ff0f6bdd0e8faa99f3860271fa71f59bcfc3226858a895bf64be599', '[\"*\"]', NULL, NULL, '2025-11-28 08:13:21', '2025-11-28 08:13:21'),
(25, 'App\\Models\\User', 2, 'auth_token', 'a4f4e2af4dddf6b57cb52e479e445762921d37cfd241304323f230c2aa99a5fb', '[\"*\"]', NULL, NULL, '2025-11-28 08:13:32', '2025-11-28 08:13:32'),
(26, 'App\\Models\\User', 1, 'auth_token', '5c441ab5450efd3bd9985ce0dd3e3556925b69804f278c2365fb20664a8a842b', '[\"*\"]', NULL, NULL, '2025-11-28 08:13:42', '2025-11-28 08:13:42'),
(27, 'App\\Models\\User', 2, 'auth_token', '8bf173e2ee52a4ce264606054cb4336e9a3bdcdb21fb05279cc04d8e050a08df', '[\"*\"]', NULL, NULL, '2025-11-28 08:18:21', '2025-11-28 08:18:21'),
(28, 'App\\Models\\User', 1, 'auth_token', 'bc958c9630831076226d46a00a9f3eed868b01078f79815e6c9571160dc8f323', '[\"*\"]', NULL, NULL, '2025-11-28 08:18:55', '2025-11-28 08:18:55'),
(29, 'App\\Models\\User', 2, 'auth_token', '1ae7fee2ce2c2d86da494e8bbe5bb1e5746731d35daaee845f20968f57e627b7', '[\"*\"]', NULL, NULL, '2025-11-28 08:26:32', '2025-11-28 08:26:32'),
(30, 'App\\Models\\User', 1, 'auth_token', 'fb904e62bac7f49674f3772d9bcb71081e1cd0bc103fc32c06be39786ecfdf8d', '[\"*\"]', NULL, NULL, '2025-11-28 08:26:49', '2025-11-28 08:26:49'),
(31, 'App\\Models\\User', 2, 'auth_token', '12040ac34ef730527bfe716aa2cb5691f757c1aafb21a68420174cc982f2a75c', '[\"*\"]', NULL, NULL, '2025-11-28 08:37:33', '2025-11-28 08:37:33'),
(32, 'App\\Models\\User', 1, 'auth_token', '7b22165f9d6c18a910d25760614cb8c2320dac6a40f7522b82a74cdd1a1c0d44', '[\"*\"]', NULL, NULL, '2025-11-28 08:38:12', '2025-11-28 08:38:12'),
(33, 'App\\Models\\User', 2, 'auth_token', 'b32eda6f3f5fbceef7bb69500974fbdbe70dfc4ec0b4814cb8841d95a8f1b18e', '[\"*\"]', NULL, NULL, '2025-11-28 08:38:29', '2025-11-28 08:38:29'),
(34, 'App\\Models\\User', 1, 'auth_token', 'a7e1c89e5557e6c44f24279f79822d12813e02ed41a09f3a0a8f09784f3ff1bc', '[\"*\"]', NULL, NULL, '2025-11-28 08:43:58', '2025-11-28 08:43:58'),
(35, 'App\\Models\\User', 2, 'auth_token', 'd7783fa6fbbe3244f9a900106da8549a650698bdb20837762eb5ded22633c0e1', '[\"*\"]', NULL, NULL, '2025-11-28 08:44:13', '2025-11-28 08:44:13'),
(36, 'App\\Models\\User', 1, 'auth_token', '806784ffbceeb705717d8ac40f627f3225553603ebc8e9922c7b2f3b050c684e', '[\"*\"]', NULL, NULL, '2025-11-28 08:44:55', '2025-11-28 08:44:55'),
(37, 'App\\Models\\User', 2, 'auth_token', 'c946ea88e6405715123062c8e936b99d5c82e1f453ecf0edb32cc71f9de896ac', '[\"*\"]', NULL, NULL, '2025-11-28 09:06:23', '2025-11-28 09:06:23'),
(38, 'App\\Models\\User', 1, 'auth_token', '43ac135a350b28ec9f2193bc1968e9e1c6f1dd36588a31286b984eb706844944', '[\"*\"]', NULL, NULL, '2025-11-28 09:20:21', '2025-11-28 09:20:21'),
(39, 'App\\Models\\User', 2, 'auth_token', '34ea69688ffac19efb2b45a1de4d6338d999184c4d21b708d669ef8412f85866', '[\"*\"]', NULL, NULL, '2025-11-28 09:28:02', '2025-11-28 09:28:02'),
(40, 'App\\Models\\User', 1, 'auth_token', '50b04c671c4b7962d5d58b54601be641b606aa8fca2fbab55b3a3b2960b3426d', '[\"*\"]', NULL, NULL, '2025-11-28 10:46:20', '2025-11-28 10:46:20'),
(41, 'App\\Models\\User', 2, 'auth_token', '1d738a14f814ac9261c02c972e7534c75a0aa48106319ab81f4ba9f188b04d56', '[\"*\"]', NULL, NULL, '2025-11-28 10:46:40', '2025-11-28 10:46:40'),
(42, 'App\\Models\\User', 2, 'auth_token', 'ba551fceebab30c2c20318ed0a946d663c5554dbccf5bdb0fc7909254ec6c0f6', '[\"*\"]', NULL, NULL, '2025-11-28 10:55:42', '2025-11-28 10:55:42'),
(43, 'App\\Models\\User', 1, 'auth_token', '6b4553403f49bdd29753b2b908557f280cba42db465abbea6d32df84ca681e1e', '[\"*\"]', NULL, NULL, '2025-11-28 11:13:03', '2025-11-28 11:13:03'),
(44, 'App\\Models\\User', 2, 'auth_token', 'f35f4ba7e89f55f690c230b10181e90e4e6c492c34918b27ff3f573444211b04', '[\"*\"]', NULL, NULL, '2025-11-28 12:02:17', '2025-11-28 12:02:17'),
(45, 'App\\Models\\User', 1, 'auth_token', '452f8b6180d93b26e6ef2a776dce4e9fda96a31217050f3f686766dd480b96bb', '[\"*\"]', NULL, NULL, '2025-11-28 13:14:33', '2025-11-28 13:14:33'),
(46, 'App\\Models\\User', 1, 'auth_token', '7539b5524be5f02f20d30ad60036884158eb4548e2350892d755a8e691b2c5e9', '[\"*\"]', NULL, NULL, '2025-11-28 14:40:14', '2025-11-28 14:40:14'),
(47, 'App\\Models\\User', 1, 'auth_token', 'e680f18d196c07adccf2a1650a087cf0f409309aa3032570e998f2cca4f9ee8c', '[\"*\"]', NULL, NULL, '2025-11-28 14:41:31', '2025-11-28 14:41:31'),
(48, 'App\\Models\\User', 1, 'auth_token', '2d05ae3afbab1315593e000f885eb3ba7e115bf148bc239463ecfca9e020fe5a', '[\"*\"]', NULL, NULL, '2025-11-28 14:52:43', '2025-11-28 14:52:43'),
(49, 'App\\Models\\User', 2, 'auth_token', '74eb72107dd06ef43b5e7f90a3c3f2b415b9e144299cc3e9fb6ba3597a9ad9ee', '[\"*\"]', NULL, NULL, '2025-11-28 20:08:50', '2025-11-28 20:08:50'),
(50, 'App\\Models\\User', 1, 'auth_token', '0a7880e94514e931d902d6bf49bfd4c89fa522d421a441455af3d7599fc32202', '[\"*\"]', NULL, NULL, '2025-11-28 20:39:08', '2025-11-28 20:39:08'),
(51, 'App\\Models\\User', 2, 'auth_token', 'deef9876192920d17254d1007a70322a0a79100d9d70182fa02e066e2ef57efb', '[\"*\"]', NULL, NULL, '2025-11-28 20:40:25', '2025-11-28 20:40:25'),
(52, 'App\\Models\\User', 1, 'auth_token', '9c53eae210280f5493fe277bb893f3e033431c1097140f7bff77081677e1aa87', '[\"*\"]', NULL, NULL, '2025-11-28 20:50:57', '2025-11-28 20:50:57'),
(53, 'App\\Models\\User', 2, 'auth_token', 'c2c891233308fd9b0b228edec7755a24170ac24c715a2727c1141b817ad1ac97', '[\"*\"]', NULL, NULL, '2025-11-28 20:51:12', '2025-11-28 20:51:12'),
(54, 'App\\Models\\User', 1, 'auth_token', '0ab8b721e15fe57f9fe4094f7b22d363af1885efe31344d786bdf7655baca027', '[\"*\"]', NULL, NULL, '2025-11-29 17:17:43', '2025-11-29 17:17:43'),
(55, 'App\\Models\\User', 2, 'auth_token', '46020bc8befc11f89239cb6316c963039d9af05b76c835276a23926e6e1704a4', '[\"*\"]', NULL, NULL, '2025-11-29 21:14:29', '2025-11-29 21:14:29'),
(56, 'App\\Models\\User', 1, 'auth_token', 'd79934a68c06a620ede4724d4fbb50d106e65b2ab0ecf8cbb54f69809702a950', '[\"*\"]', NULL, NULL, '2025-11-29 21:15:35', '2025-11-29 21:15:35'),
(57, 'App\\Models\\User', 2, 'auth_token', 'a27bf6947e1f9f6922cf47e4267fe7f918cb22807ae482564637a839dbb0be11', '[\"*\"]', NULL, NULL, '2025-11-29 21:22:49', '2025-11-29 21:22:49'),
(58, 'App\\Models\\User', 1, 'auth_token', '372806365d31757d09c4a50f66f1f0c3578ff2c382b76fa963a40e80518809c6', '[\"*\"]', NULL, NULL, '2025-11-29 21:23:39', '2025-11-29 21:23:39'),
(59, 'App\\Models\\User', 1, 'auth_token', '8dda185a301748cad523d199643bfc38042c4e37f5f3c293e271f08aa8b2e0c1', '[\"*\"]', NULL, NULL, '2025-11-29 21:32:57', '2025-11-29 21:32:57'),
(60, 'App\\Models\\User', 2, 'auth_token', 'b110817fa9b5cf4675f42c52c4bd457a60a14cc5993f2226a259d02ea4431aa7', '[\"*\"]', NULL, NULL, '2025-11-30 06:27:27', '2025-11-30 06:27:27'),
(61, 'App\\Models\\User', 1, 'auth_token', '0a82115366c64a34bb5a168574ead048ecdd9875ad697614446041f2481b656f', '[\"*\"]', NULL, NULL, '2025-11-30 06:31:43', '2025-11-30 06:31:43'),
(62, 'App\\Models\\User', 2, 'auth_token', '0243d738cf4e21a1f38afcdc53226cd09006bbdb30c1832c3aac7005a660c4de', '[\"*\"]', NULL, NULL, '2025-11-30 07:27:00', '2025-11-30 07:27:00'),
(63, 'App\\Models\\User', 1, 'auth_token', 'bccf20f3c9b6b0bb09d2492159f40354fa868577816f72d4c441b73aebde2cb7', '[\"*\"]', NULL, NULL, '2025-11-30 07:34:37', '2025-11-30 07:34:37'),
(64, 'App\\Models\\User', 1, 'auth_token', '38b9aa52e53ff56439737f335381a8595471bf62046fa730469bd700327e0949', '[\"*\"]', NULL, NULL, '2025-11-30 09:27:08', '2025-11-30 09:27:08'),
(65, 'App\\Models\\User', 2, 'auth_token', 'd2a93d2f4ea552233a7c790b2cad20abc89d7b293cc3607dd84375f4ccb67a26', '[\"*\"]', NULL, NULL, '2025-11-30 09:58:13', '2025-11-30 09:58:13'),
(66, 'App\\Models\\User', 1, 'auth_token', 'd70c37ddf0023a1c2a3053e7c97044c90f34cdaa45e5a0c7dc9934fc8d872d5e', '[\"*\"]', NULL, NULL, '2025-11-30 09:59:48', '2025-11-30 09:59:48'),
(67, 'App\\Models\\User', 2, 'auth_token', 'cbd1a0c85eb6b6bb58390ae371f6d5fd46813f76525c98e62a307bbf18ce94dd', '[\"*\"]', NULL, NULL, '2025-11-30 10:43:44', '2025-11-30 10:43:44'),
(68, 'App\\Models\\User', 1, 'auth_token', 'bcf3db3dbd1f63ef5d7be5f81b956b6895b03e40158a4e92105f4ea88d4ebd89', '[\"*\"]', NULL, NULL, '2025-11-30 11:00:49', '2025-11-30 11:00:49'),
(69, 'App\\Models\\User', 1, 'auth_token', '1b1a3c48370c2aa67e7ee5a10b76a679f1fdec5c7d0601a10250c62b18568f1e', '[\"*\"]', NULL, NULL, '2025-11-30 18:04:09', '2025-11-30 18:04:09'),
(70, 'App\\Models\\User', 1, 'auth_token', '7e5aad0ac6e547c89b71a0fc6776372f5fcf7c8e1a4a916d401c1e1270e8455f', '[\"*\"]', NULL, NULL, '2025-12-01 09:48:54', '2025-12-01 09:48:54'),
(71, 'App\\Models\\User', 2, 'auth_token', '097c9b8b0b0bd0ed080df3fcb95f95233d3230a7a2061928097020d94a078e02', '[\"*\"]', NULL, NULL, '2025-12-01 09:50:02', '2025-12-01 09:50:02'),
(72, 'App\\Models\\User', 1, 'auth_token', '96e1741f5672146949d6c53b2dcd48b8dc0f0ab15bff1e1c57768175828b37e3', '[\"*\"]', NULL, NULL, '2025-12-01 09:53:32', '2025-12-01 09:53:32'),
(73, 'App\\Models\\User', 1, 'auth_token', '340b1d004b55fd9d89ead34e9f5c80417d6483b6f58586e89dd94ab6984a70cb', '[\"*\"]', NULL, NULL, '2025-12-01 11:34:17', '2025-12-01 11:34:17'),
(74, 'App\\Models\\User', 2, 'auth_token', 'ae4d6bf96e032165f3f98e1d0a6109f6e9cde1a89c7211333a022ba50459b2f3', '[\"*\"]', NULL, NULL, '2025-12-01 11:59:07', '2025-12-01 11:59:07'),
(75, 'App\\Models\\User', 2, 'auth_token', '914a49fcf67f4d7703f9743133b305b1af2444e494ce2402e52ca1e8048f725b', '[\"*\"]', NULL, NULL, '2025-12-02 17:53:25', '2025-12-02 17:53:25'),
(76, 'App\\Models\\User', 1, 'auth_token', '532c1163179f9ee1f43a00cf8e7a8d4f1f82e790a9b2ac7223389e28595bf56f', '[\"*\"]', NULL, NULL, '2025-12-02 18:22:37', '2025-12-02 18:22:37'),
(77, 'App\\Models\\User', 2, 'auth_token', '385c4a50c208138c220e7bb14fc93f056b501864766a78a157f39523395e6e03', '[\"*\"]', NULL, NULL, '2025-12-02 18:39:48', '2025-12-02 18:39:48'),
(78, 'App\\Models\\User', 1, 'auth_token', '582d0fd638158b2b9521808f3934e0006c0c69cd888b6cb8dc6da12df4326141', '[\"*\"]', NULL, NULL, '2025-12-02 18:51:03', '2025-12-02 18:51:03'),
(79, 'App\\Models\\User', 1, 'auth_token', '8eec42d4eb1f723bf5ba7e59ffb1bbf56ad6e528e3a247ed78262298ef2628ce', '[\"*\"]', NULL, NULL, '2025-12-03 09:44:48', '2025-12-03 09:44:48'),
(80, 'App\\Models\\User', 1, 'auth_token', 'b0a907c731eb1daeea150078e44eafe9d0665d9d06167ffe7914928f62530b35', '[\"*\"]', NULL, NULL, '2025-12-03 09:47:38', '2025-12-03 09:47:38'),
(81, 'App\\Models\\User', 2, 'auth_token', '43aa53cd979257453183b43aafce4004be57efd8bf4116e6847b386550c0c95d', '[\"*\"]', NULL, NULL, '2025-12-03 09:49:13', '2025-12-03 09:49:13'),
(82, 'App\\Models\\User', 1, 'auth_token', 'c730c1b22c38c57ff2f936f110582f74be81b74a48cefa137c31b2d160de96fa', '[\"*\"]', NULL, NULL, '2025-12-03 09:49:58', '2025-12-03 09:49:58'),
(83, 'App\\Models\\User', 2, 'auth_token', 'dd87b3bbe40136c6f0aa529555c55ad752209058cce479e620628e35073ebe83', '[\"*\"]', NULL, NULL, '2025-12-03 09:50:18', '2025-12-03 09:50:18'),
(84, 'App\\Models\\User', 1, 'auth_token', 'c783cc0f5716ee57a4f3ebdac29fdc0943570498a54de7c148ae504a3a4e3f11', '[\"*\"]', NULL, NULL, '2025-12-03 09:51:28', '2025-12-03 09:51:28'),
(85, 'App\\Models\\User', 2, 'auth_token', '2b414abc5f54b9f04b6f1b14f266417df1934d40030f7643aed91572a84d3ef3', '[\"*\"]', NULL, NULL, '2025-12-03 09:52:10', '2025-12-03 09:52:10'),
(86, 'App\\Models\\User', 1, 'auth_token', '92ad6832d0280ef2c1848431cb158ca67abdfdc91cb10788d694acb85eef1abe', '[\"*\"]', NULL, NULL, '2025-12-03 10:03:36', '2025-12-03 10:03:36'),
(87, 'App\\Models\\User', 1, 'auth_token', 'a9c33073bf77fd17e801c7a51713d9b0d5c3ca6326d51ed8b46000b24465de74', '[\"*\"]', NULL, NULL, '2025-12-03 10:15:24', '2025-12-03 10:15:24'),
(88, 'App\\Models\\User', 2, 'auth_token', '805c7fad279f3457d90aa46f5f9f08fdee09715762544bae633b019aaddf3206', '[\"*\"]', NULL, NULL, '2025-12-03 10:19:47', '2025-12-03 10:19:47'),
(89, 'App\\Models\\User', 5, 'auth_token', '05c7e1f2759b7a268b2d9a9cf2fd90fd632f120dbc1871afafceae48ce12fb57', '[\"*\"]', NULL, NULL, '2025-12-03 10:22:20', '2025-12-03 10:22:20'),
(90, 'App\\Models\\User', 1, 'auth_token', '7f5e70484daffdd68ec86fff34868baad56fc567c90823e7914da33cbb17570d', '[\"*\"]', NULL, NULL, '2025-12-03 10:32:26', '2025-12-03 10:32:26'),
(91, 'App\\Models\\User', 2, 'auth_token', '1f9560892e32dee6e7ce4ddbee60e9c2f0b8ab4739a76a38bcd7ff20070b5088', '[\"*\"]', NULL, NULL, '2025-12-03 10:34:04', '2025-12-03 10:34:04'),
(92, 'App\\Models\\User', 6, 'auth_token', '82b3085ae45fe6b2abe379f7ebfc4bc560db3719a04d3f3fedb251c01782c23c', '[\"*\"]', NULL, NULL, '2025-12-18 21:47:45', '2025-12-18 21:47:45'),
(93, 'App\\Models\\User', 6, 'auth_token', '439b6cb87efd758868c2bce41db234851e5be25f8b9a28fad326bb87dabf2a31', '[\"*\"]', NULL, NULL, '2025-12-18 22:56:46', '2025-12-18 22:56:46'),
(94, 'App\\Models\\User', 6, 'auth_token', 'f0a4b582169578639688d042177a4410395a27d4bb89296204ef7d33356d6bc1', '[\"*\"]', NULL, NULL, '2025-12-18 23:25:21', '2025-12-18 23:25:21'),
(95, 'App\\Models\\User', 6, 'auth_token', '6577d366ec9bec09bf4ab05d28608ff285f11982f532d471145377c9d0ec852f', '[\"*\"]', NULL, NULL, '2025-12-19 01:49:33', '2025-12-19 01:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('c4Zd3UDOIngJIAErqcLdzAKH51n02ZHA54qGQZUi', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiNElTbXpiT0tEbnNvSmd2TnhFY0QyOXF2NHVoRmg5TFZ0RUZJTlExYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9hdWRpdC1sb2dzL3BvbGw/c2luY2VfaWQ9MTUiO3M6NToicm91dGUiO3M6MjE6ImFkbWluLmF1ZGl0X2xvZ3MucG9sbCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7czoxNDoiYWN0aW9uX2NvdW50XzYiO2k6OTtzOjE0OiJkZWxldGVfY291bnRfNiI7aTo2O30=', 1766139221);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `tarumt_id` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'student',
  `credits` int(11) DEFAULT 10,
  `ip_address` varchar(45) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(2000) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `tarumt_id`, `email`, `password`, `role`, `credits`, `ip_address`, `last_login_at`, `failed_login_attempts`, `created_at`, `updated_at`, `address`, `tel`) VALUES
(1, 'Super Admin', 'ADMIN001', 'superAdmin@tarumt.edu.my', '/ePW0WrOIRU0mhBTs13KlprRHXIjWfDBWFBo17S', 'admin', 0, '127.0.0.1', '2025-12-03 10:32:26', 0, '2025-11-28 04:36:38', '2025-12-18 21:38:28', 'Kopisan', '+60194723842'),
(2, 'Dr. Shao Gay', 'ABC123', 'jackfrostgoh@gmail.com', '$2y$12$ik.fZFRN/4Dw0GxMpqkKAeQYyb/q.mSFWk9ngQ3jRwyIYB7QL0i9a', 'lecturer', 10, '127.0.0.1', '2025-12-03 10:34:04', 0, '2025-11-28 04:36:38', '2025-12-03 10:34:04', '10, Jalan ShaoHengisGay,31009,Perak', '+60123456789'),
(3, 'GayBra', 'ABC123#', 'gayasfuck@gmail.com', '$2y$12$7EbqU2hsSDcu/PqefiShxu3nV1lpN5XlNX1Dd4VBtBAB230xUvHDe', 'student', 10, NULL, NULL, 0, '2025-11-30 12:39:08', '2025-11-30 12:39:08', '10, Jalan ShaoHengisGay,31000', '+60123456789'),
(5, 'Dr. Shao Gay', 'GAY1234', 'gayfuck@gmail.com', '$2y$12$FfyqCSyh3XJ6.iGB54u8Z.OrnF5MC3Rpu8Rd2V/Z3p6SLG9OwGktG', 'lecturer', 10, '127.0.0.1', '2025-12-03 10:22:20', 0, '2025-12-03 10:12:14', '2025-12-03 10:22:20', '10, Jalan ShaoHengisGay,31000,Perak', '+60123456789'),
(6, 'Admin', 'Admin002', 'admin@tarumt.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi\r\n', 'admin', 10, '127.0.0.1', '2025-12-19 01:49:33', 0, '2025-12-19 05:44:03', '2025-12-19 01:49:33', NULL, NULL),
(7, 'Gan Yong Zhe', '24AMR09999', 'ganyz-am22@student.tarc.edu.my', '$2y$12$9y2KprapcdEZmwddoY.vOOghXdON5.65L773sZX.UGr5Wk8qjX5t6', 'student', 10, NULL, NULL, 1, '2025-12-18 21:49:14', '2025-12-19 01:38:01', '-', '-');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_model` (`model_type`,`model_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `booking_approvals`
--
ALTER TABLE `booking_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `tarumt_id` (`tarumt_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_approvals`
--
ALTER TABLE `booking_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_approvals`
--
ALTER TABLE `booking_approvals`
  ADD CONSTRAINT `booking_approvals_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
