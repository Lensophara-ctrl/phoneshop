-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251014.c784570216
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2026 at 05:22 PM
-- Server version: 8.4.3
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phoneshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_tokens`
--

CREATE TABLE `biometric_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `created_at`, `updated_at`) VALUES
(1, 'iPhone', 'categories/QWimDaHkujmwKyOvdq2VH7tkQBJ6ObUDRStV4Ak6.jpg', '2026-02-21 00:37:28', '2026-04-06 15:24:40'),
(2, 'Samsung', 'categories/feDe8Vb3cSavdG3qFUVI2QlVreKlPwDHWHBi6Udj.jpg', '2026-02-21 00:37:28', '2026-04-06 15:25:03'),
(3, 'OnePlus', 'categories/Hvbm6en4dpsHricQDFKLYDFjGlmD6R4w1mLa7e04.jpg', '2026-02-21 00:37:28', '2026-04-06 15:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locked_reports`
--

CREATE TABLE `locked_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `report_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked_by` bigint UNSIGNED NOT NULL,
  `locked_at` timestamp NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_20_063827_create_categories_table', 1),
(5, '2026_02_20_063841_create_phones_table', 1),
(6, '2026_02_20_072257_create_sales_table', 1),
(7, '2026_02_20_083735_add_bill_no_to_sales_table', 1),
(8, '2026_02_20_085419_create_settings_table', 1),
(9, '2026_02_20_085419_create_slides_table', 1),
(10, '2026_02_21_061353_add_role_to_users_table', 1),
(11, '2024_03_23_000001_create_otps_table', 2),
(12, '2024_03_23_000002_create_biometric_tokens_table', 2),
(13, '2024_03_23_000003_add_phone_address_to_users_table', 2),
(14, '2026_02_21_074936_add_profile_image_to_users_table', 2),
(15, '2026_02_21_084152_add_description_to_phones_table', 2),
(16, '2026_02_23_082708_create_payments_table', 2),
(17, '2026_02_25_154846_create_sale_items_table', 2),
(18, '2026_02_25_154930_refactor_sales_table_for_pos', 2),
(19, '2026_02_25_161918_add_currency_and_exchange_rate_to_sales_table', 2),
(20, '2026_02_26_000000_add_payment_md5_to_sales_table', 2),
(21, '2026_03_10_133457_add_detail_images_to_phones_table', 2),
(22, '2026_03_12_134241_create_api_keys_table', 2),
(23, '2026_03_14_004911_add_about_us_fields_to_settings_table', 3),
(24, '2026_03_14_225210_add_cv_and_telegram_fields_to_settings_table', 3),
(25, '2026_04_01_000000_add_customer_info_to_sales_table', 3),
(26, '2026_04_01_000001_add_delivery_fields_to_sales_table', 3),
(27, '2026_04_06_000000_add_receipt_path_to_sales_table', 4),
(28, '2026_04_06_000001_add_approval_fields_to_sales_table', 4),
(29, '2026_04_06_000002_create_locked_reports_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint UNSIGNED NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'login',
  `expires_at` timestamp NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('visa_card','mastercard','bakong','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visa_card',
  `gateway` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2checkout',
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `response_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_images` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`id`, `name`, `category_id`, `price`, `qty`, `image`, `detail_images`, `description`, `created_at`, `updated_at`) VALUES
(8, '17ProMax', 3, 0.30, 2, 'phones/dQMnmRmLUDRz9wdqIFfGIBEPjZ4Dqa3zXstK8fmu.jpg', '[\"phones/details/YPoHBOjwl48mWAJat3yM58v4yDTWZDoWXwMgVrxk.jpg\"]', '1TB', '2026-02-21 00:37:28', '2026-04-06 17:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `bill_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `customer_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_latitude` decimal(10,8) DEFAULT NULL,
  `delivery_longitude` decimal(11,8) DEFAULT NULL,
  `order_notes` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(10,2) NOT NULL DEFAULT '4100.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_md5` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MD5 hash of KHQR for payment verification',
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `delivery_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivery_estimated_at` timestamp NULL DEFAULT NULL,
  `delivery_completed_at` timestamp NULL DEFAULT NULL,
  `delivery_driver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_driver_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `bill_no`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `customer_city`, `customer_postal_code`, `delivery_latitude`, `delivery_longitude`, `order_notes`, `subtotal`, `tax`, `total_price`, `currency`, `exchange_rate`, `payment_method`, `payment_md5`, `receipt_path`, `status`, `approval_status`, `approved_by`, `approved_at`, `rejection_reason`, `delivery_status`, `delivery_estimated_at`, `delivery_completed_at`, `delivery_driver_name`, `delivery_driver_phone`, `created_at`, `updated_at`) VALUES
(1, 'BILL-1001', 1, 'Customer 1001', 'customer1001@example.com', '0123451001', NULL, NULL, NULL, NULL, NULL, NULL, 1200.00, 120.00, 1320.00, 'USD', 4100.00, 'card', NULL, NULL, 'completed', 'pending', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-01 10:06:00', '2026-04-01 03:42:00'),
(2, 'BILL-1002', 1, 'Customer 1002', 'customer1002@example.com', '0123451002', NULL, NULL, NULL, NULL, NULL, NULL, 850.00, 85.00, 935.00, 'USD', 4100.00, 'cash', NULL, NULL, 'completed', 'pending', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-02 06:36:00', '2026-04-02 04:46:00'),
(3, 'BILL-1003', 1, 'Customer 1003', 'customer1003@example.com', '0123451003', NULL, NULL, NULL, NULL, NULL, NULL, 1500.00, 150.00, 1650.00, 'USD', 4100.00, 'bakong', NULL, NULL, 'completed', 'pending', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-03 03:04:00', '2026-04-03 09:48:00'),
(4, 'INV-69D3DB38B3667', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.30, 0.00, 0.30, 'KHR', 4100.00, 'bakong', '64d79fb5f6b0285f2f67128a35e6f3ed', NULL, 'pending', 'approved', 1, '2026-04-06 16:29:00', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:11:36', '2026-04-06 16:29:00'),
(5, 'INV-69D3DB565CA60', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.30, 0.00, 0.30, 'KHR', 4100.00, 'bakong', '09ffb6641377444db5899cbd3c6795e2', NULL, 'pending', 'approved', 1, '2026-04-06 16:28:58', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:12:06', '2026-04-06 16:28:58'),
(6, 'INV-69D3DBFC1DF83', 3, 'kimo', 'lazyra867@gmail.com', 'lazyra867@gmail.com', '123 Khan Sen Sok', 'Phnom Penh', '12000', NULL, NULL, 'mm', 0.30, 0.00, 0.30, 'USD', 4100.00, 'bakong', 'd8f51ae98356f1a6f53450f27a49c634', NULL, 'completed', 'approved', 1, '2026-04-06 16:28:51', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:14:52', '2026-04-06 16:28:51'),
(7, 'INV-69D3E4DBC7BA7', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.30, 0.00, 0.30, 'USD', 4100.00, 'bakong', 'e23753f1f7083e1ea95aaddc52fdc576', NULL, 'completed', 'approved', 1, '2026-04-06 16:53:28', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:52:43', '2026-04-06 16:53:28'),
(8, 'INV-69D3E5D32A1A3', 3, 'kimo', 'lazyra867@gmail.com', 'lazyra867@gmail.com', '123 Khan Sen Sok', 'Phnom Penh', '12000', NULL, NULL, 'sjsjjs', 0.30, 0.00, 0.30, 'USD', 4100.00, 'bakong', '6e9fbf8856a5bfb0ca832ad892cc68e4', NULL, 'completed', 'pending', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:56:51', '2026-04-06 16:57:08'),
(9, 'INV-69D3E669D7ECA', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.30, 0.00, 0.30, 'USD', 4100.00, 'bakong', '14832e4567c0b5d39a1c181a96192e44', NULL, 'completed', 'approved', 1, '2026-04-06 17:00:13', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-04-06 16:59:21', '2026-04-06 17:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `phone_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `phone_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 4, 8, 1, 0.30, 0.30, '2026-04-06 16:11:36', '2026-04-06 16:11:36'),
(2, 5, 8, 1, 0.30, 0.30, '2026-04-06 16:12:06', '2026-04-06 16:12:06'),
(3, 6, 8, 1, 0.30, 0.30, '2026-04-06 16:14:52', '2026-04-06 16:14:52'),
(4, 7, 8, 1, 0.30, 0.30, '2026-04-06 16:52:43', '2026-04-06 16:52:43'),
(5, 8, 8, 1, 0.30, 0.30, '2026-04-06 16:56:51', '2026-04-06 16:56:51'),
(6, 9, 8, 1, 0.30, 0.30, '2026-04-06 16:59:21', '2026-04-06 16:59:21');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `about_hero_title` text COLLATE utf8mb4_unicode_ci,
  `about_hero_subtitle` text COLLATE utf8mb4_unicode_ci,
  `about_story_title` text COLLATE utf8mb4_unicode_ci,
  `about_story_content` text COLLATE utf8mb4_unicode_ci,
  `about_stat_customers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1000+',
  `about_stat_products` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '500+',
  `about_stat_authentic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100%',
  `about_stat_support` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '24/7',
  `about_mission` text COLLATE utf8mb4_unicode_ci,
  `about_vision` text COLLATE utf8mb4_unicode_ci,
  `about_cv_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `about_cv_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_cv_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_cv_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_cv_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_cv_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_cv_bio` text COLLATE utf8mb4_unicode_ci,
  `about_cv_skills` text COLLATE utf8mb4_unicode_ci,
  `about_cv_education` text COLLATE utf8mb4_unicode_ci,
  `about_cv_experience` text COLLATE utf8mb4_unicode_ci,
  `telegram_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `telegram_bot_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `title`, `description`, `button_text`, `button_link`, `image`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Phone Shop', 'Welcome to Phara Shop', 'View iPhones', '/category/1', 'slides/VNLuixwpf4BgGL77UtqwYiCxBpEeRsDAvNKAinYE.jpg', 1, 1, '2026-04-06 15:26:56', '2026-04-06 15:26:56'),
(2, 'Hii', 'Hello', 'Shop Now', '#latest-phones', 'slides/WIReIlxZ6kSzhnY9PV2roLIqvjstlqWde7cZ2SqX.jpg', 1, 1, '2026-04-06 15:27:35', '2026-04-06 15:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `email_verified_at`, `password`, `role`, `profile_image`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, NULL, NULL, '$2y$12$11G2O1J6fNfBhZEQjkabLOvMWryJ.nubrZa.Sqc4eMZJy1SAzpoHC', 'admin', NULL, NULL, '2026-02-21 00:37:28', '2026-02-21 00:37:28'),
(3, 'kimo', 'lazyra867@gmail.com', 'lazyra867@gmail.com', '123 Khan Sen Sok', NULL, '$2y$12$pbUV5.Zb9ZI1bg31YJywFe311tvWBNhtxo/.1fB6CXfnq2jFdzw9W', 'customer', NULL, NULL, '2026-04-06 16:13:50', '2026-04-06 16:13:50'),
(4, 'Len Sophara', 'Lensophara@gmail.com', NULL, NULL, NULL, '$2y$12$4o6XnzseSZZaxIJVyiJC5u0eeCXqoO4P7nLZSuD5s8cpCqVRc3Xhi', 'admin', NULL, NULL, '2026-04-06 17:11:25', '2026-04-06 17:11:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_keys_key_unique` (`key`),
  ADD KEY `api_keys_key_index` (`key`),
  ADD KEY `api_keys_user_id_index` (`user_id`);

--
-- Indexes for table `biometric_tokens`
--
ALTER TABLE `biometric_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `biometric_tokens_device_id_unique` (`device_id`),
  ADD KEY `biometric_tokens_user_id_device_id_is_active_index` (`user_id`,`device_id`,`is_active`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locked_reports`
--
ALTER TABLE `locked_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locked_reports_user_id_report_type_unique` (`user_id`,`report_type`),
  ADD KEY `locked_reports_locked_by_foreign` (`locked_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otps_identifier_code_verified_index` (`identifier`,`code`,`verified`),
  ADD KEY `otps_expires_at_index` (`expires_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_bill_no_index` (`bill_no`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phones_category_id_foreign` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `sales_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_phone_id_foreign` (`phone_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `biometric_tokens`
--
ALTER TABLE `biometric_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locked_reports`
--
ALTER TABLE `locked_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD CONSTRAINT `api_keys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `biometric_tokens`
--
ALTER TABLE `biometric_tokens`
  ADD CONSTRAINT `biometric_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `locked_reports`
--
ALTER TABLE `locked_reports`
  ADD CONSTRAINT `locked_reports_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `locked_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `phones_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_phone_id_foreign` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
