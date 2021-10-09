-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 29, 2020 at 12:41 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `gamestock`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `comment` varchar(255) DEFAULT NULL,
  `txn_type` enum('credit','debit') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `amount`, `comment`, `txn_type`, `created_at`, `updated_at`) VALUES
(1, 10, '100.00', 'Membership  puchase', 'credit', '2020-09-30 09:56:50', '2020-09-30 09:56:50'),
(2, 10, '100.00', 'Membership  puchase', 'credit', '2020-09-30 09:56:59', '2020-09-30 09:58:42'),
(3, 9, '500.00', 'membership upgrade', 'credit', '2020-09-30 16:05:10', '2020-09-30 16:05:10'),
(4, 10, '500.00', 'League joined', 'credit', '2020-10-01 08:23:59', '2020-10-01 08:23:59'),
(5, 10, '5000000.00', 'League joined', 'credit', '2020-10-01 11:49:57', '2020-10-01 11:49:57'),
(6, 10, '500.00', 'League joined', 'credit', '2020-10-02 08:25:15', '2020-10-02 08:25:15'),
(7, 12, '234.00', 'League joined', 'credit', '2020-10-02 11:05:02', '2020-10-02 11:05:02'),
(8, 10, '300.00', 'League joined', 'credit', '2020-10-03 11:56:48', '2020-10-03 11:56:48'),
(9, 10, '500.00', 'League joined', 'credit', '2020-10-03 12:24:02', '2020-10-03 12:24:02'),
(10, 4, '500.00', 'League joined', 'credit', '2020-10-03 14:06:04', '2020-10-03 14:06:04'),
(11, 10, '2500.00', 'League joined', 'credit', '2020-10-07 11:41:43', '2020-10-07 11:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `apis`
--

CREATE TABLE `apis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `apis`
--

INSERT INTO `apis` (`id`, `name`, `description`, `key`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'NBA', 'Sportradar\'s NBA API v7', 'vyhvrku4jsfwbqbv2qr3tehn', 'nba', '2020-08-10 13:37:04', '2020-08-10 13:37:04', NULL),
(2, 'NFL', 'Sportradar\'s NFL Official API v5', 'erpuk4xjayjn6dkzxhcwrus6', 'nfl', '2020-08-10 13:37:48', '2020-08-10 13:37:48', NULL),
(3, 'MLB', 'Sportradar\'s MLB API v6.6', '2x8g57cy7mytvxjm3zq5dpff', 'mlb', '2020-08-10 13:38:40', '2020-08-11 03:53:41', NULL),
(4, 'NHL', 'Sportradar\'s NHL Official API v7', '6pxephkrjmgby4mnfjdgx6ae', 'nhl', '2020-08-10 13:39:11', '2020-08-10 13:39:11', NULL),
(5, 'NCAAB', 'Sportradar\'s NCAAMB API v7', '773p6jah25pxqcwpdbrj4qha', 'ncaab', '2020-08-10 13:39:58', '2020-08-10 13:39:58', NULL),
(6, 'NCAAF', 'Sportradar\'s NCAA Football API v1', '6kydjqp94xe4zmn8v74c6nbz', 'ncaaf', '2020-08-10 13:40:33', '2020-08-10 13:40:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `api_requests`
--

CREATE TABLE `api_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` mediumtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `free_trade_counts`
--

CREATE TABLE `free_trade_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `sport_event_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sport_id` varchar(255) DEFAULT NULL,
  `competition_id` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `sport_id`, `competition_id`, `image`, `created_at`) VALUES
(1, 'NFL', 'sr:sport:16', 'sr:competition:31', 'football.png', '2020-10-05 05:20:03'),
(2, 'NBA', 'sr:sport:2', 'sr:competition:132', 'baseball.png', '2020-10-05 05:20:03'),
(3, 'NHL', 'sr:sport:4', 'sr:competition:234', 'hockey.png', '2020-10-05 05:20:03'),
(4, 'MLB', 'sr:sport:3', 'sr:competition:109', 'basketball.png', '2020-10-05 05:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `joined_leagues`
--

CREATE TABLE `joined_leagues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `league_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leagues`
--

CREATE TABLE `leagues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `founder` int(11) NOT NULL,
  `league_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_memebers` int(11) DEFAULT '0',
  `portfolio_value` bigint(20) DEFAULT '0',
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leagues`
--

INSERT INTO `leagues` (`id`, `name`, `founder`, `league_type`, `password`, `no_of_memebers`, `portfolio_value`, `duration`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PYPL', 1, 'public', NULL, 0, 234, '2020-09-24T15:17', 'league_1599218139.png', '2020-08-21 04:17:19', '2020-09-04 11:15:39', NULL),
(2, 'GHC', 1, 'public', NULL, 0, 150, '2020-09-23T15:20', 'league_1599225777.png', '2020-08-21 04:24:25', '2020-09-04 13:22:57', NULL),
(3, 'EMIRATES', 1, 'public', NULL, 0, 100, '2020-09-21T20:45', 'league_1599218350.png', '2020-08-21 04:44:09', '2020-09-04 11:19:10', NULL),
(4, 'FINNISH PREMIER', 1, 'public', NULL, 0, 50, '2020-09-20T15:00', 'league_1599225762.png', '2020-08-21 04:56:55', '2020-09-04 13:22:42', NULL),
(5, 'NHL', 2, 'public', NULL, 0, 300, '2020-09-25T15:00', 'league_1599218211.png', '2020-08-21 06:33:47', '2020-09-04 11:16:51', NULL),
(6, 'NFL', 2, 'public', NULL, 0, 500, '2020-09-27T16:30', 'league_1599218191.png', '2020-08-21 13:16:41', '2020-09-04 11:16:31', NULL),
(16, 'My Test league', 2, 'public', NULL, 0, 500, '2020-09-01T16:30', 'league_1599245345.jpg', '2020-09-04 18:49:05', '2020-09-04 18:49:05', NULL),
(19, 'My Test league 1', 2, 'public', NULL, 0, 500, '2020-09-01T16:30', 'league_1599247842.jpg', '2020-09-04 19:30:42', '2020-09-04 19:30:42', NULL),
(20, 'rajarobert', 10, 'public', NULL, 0, 500, '2020-09-27T11:54', NULL, '2020-09-25 11:54:38', '2020-09-25 11:54:38', NULL),
(21, 'rajarobert new', 10, 'public', NULL, 0, 500, '2020-09-27T11:55', NULL, '2020-09-25 11:55:42', '2020-09-25 11:55:42', NULL),
(22, 'test1', 2, 'public', '123456', 0, 500, '2020-09-01T16:30', NULL, '2020-09-25 12:01:59', '2020-09-25 12:01:59', NULL),
(23, 'shubham', 10, 'public', NULL, 0, 200, '2020-09-27T12:02', NULL, '2020-09-25 12:02:38', '2020-09-25 12:02:38', NULL),
(24, 'newest game', 10, 'public', NULL, 0, 5000, '2020-10-01T12:25', NULL, '2020-09-26 12:25:37', '2020-09-26 12:25:37', NULL),
(25, 'Premium League', 10, 'public', NULL, 0, 100, '2020-10-10T12:32', NULL, '2020-09-26 12:32:54', '2020-09-26 12:32:54', NULL),
(26, 'SAR', 11, 'public', NULL, 0, 100, '2020-10-01T18:09', NULL, '2020-09-28 18:09:45', '2020-09-30 05:09:36', '2020-09-30 05:09:36'),
(27, 'SARTEST', 11, 'public', NULL, 0, 200, '2020-10-02T07:48', NULL, '2020-09-29 07:48:01', '2020-09-30 05:09:44', '2020-09-30 05:09:44'),
(28, 'NATWEST', 10, 'private', NULL, 0, 10, '2020-10-13T08:04', NULL, '2020-09-29 08:04:06', '2020-09-29 08:04:06', NULL),
(29, 'ssssss', 10, 'public', NULL, 0, 5555550, '2020-10-01T11:37', NULL, '2020-09-29 11:37:41', '2020-09-29 11:37:41', NULL),
(30, 'special', 10, 'public', NULL, 0, 5000, '2021-09-30T14:02', NULL, '2020-09-30 14:02:22', '2020-09-30 14:02:22', NULL),
(31, 'samasya', 10, 'public', NULL, 0, 99999, '2021-09-30T14:06', NULL, '2020-09-30 14:06:26', '2020-09-30 14:06:26', NULL),
(32, 'maxmatch', 10, 'public', NULL, 0, 500, '2021-03-27T12:18', NULL, '2020-10-03 12:18:35', '2020-10-03 12:18:35', NULL),
(33, 'Indian Premium League', 10, 'public', NULL, 0, 2500, '2020-10-21T11:18', NULL, '2020-10-07 11:18:23', '2020-10-07 11:18:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `league_invitations`
--

CREATE TABLE `league_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `league_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`id`, `user_id`, `title`, `amount`, `currency`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 10, 'Thre year member ship', '3333.00', NULL, '2020-09-12', '2020-09-30', 0, '2020-09-28 14:22:05', '2020-09-30 09:30:51'),
(2, 2, 'Thre year member ship', '3333.00', NULL, '2020-09-12', '2020-09-12', 0, '2020-09-28 14:22:54', '2020-09-28 14:22:54'),
(3, 2, 'Thre year member ship', '3333.00', NULL, '2020-09-12', '2020-09-12', 0, '2020-09-29 11:00:07', '2020-09-29 11:00:07'),
(4, 9, 'Premium Mermbership', '419.00', NULL, '2020-09-29', '2020-10-29', 0, '2020-09-29 11:57:49', '2020-09-29 11:57:49'),
(5, 9, 'Premium Mermbership', '419.00', 'INR', '2020-09-30', '2020-10-30', 0, '2020-09-30 16:05:10', '2020-09-30 16:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(16, '2014_10_12_000000_create_users_table', 1),
(17, '2014_10_12_100000_create_password_resets_table', 1),
(18, '2019_08_19_000000_create_failed_jobs_table', 1),
(19, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(20, '2020_08_10_135221_create_apis_table', 1),
(21, '2020_08_14_172223_modify_name_surname_users_table', 2),
(22, '2020_08_14_172747_modify_phone_users_table', 3),
(23, '2020_08_15_073925_add_account_type_user_table', 4),
(24, '2020_08_15_075426_create_league_table', 5),
(25, '2020_08_18_082819_add_extra_fields_user_table', 6),
(26, '2020_08_18_083744_modify_firstname_address_fields_user_table', 7),
(27, '2020_08_18_085036_add_mobile_verified_at_user_table', 8),
(28, '2020_08_18_105704_add_verification_code_user_table', 9),
(29, '2020_08_21_072540_rename_league_table', 10),
(30, '2020_08_21_090406_modify_duration_league_table', 11),
(31, '2020_08_28_111526_create_league_invitations', 12),
(32, '2020_08_28_131132_create_league_joined', 13),
(33, '2020_09_03_144513_rename_league_userid_invitation_table', 14),
(34, '2020_09_03_144849_modify_mobile_number__invitation_table', 14),
(35, '2020_09_03_145653_add_device_token_and_type_users_table', 14),
(36, '2020_10_07_134059_add_image_games_table', 15),
(37, '2020_11_26_071907_create_free_trade_count_table', 16),
(38, '2020_11_26_094115_modify_sport_event_id_freecount_table', 17),
(39, '2020_12_28_130039_create_api_request_table', 18);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `league_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `sport_id` varchar(255) DEFAULT NULL,
  `competition_id` varchar(255) DEFAULT NULL,
  `sport_event_id` varchar(255) DEFAULT NULL,
  `season_id` varchar(255) DEFAULT NULL,
  `competitor_id` varchar(255) DEFAULT NULL,
  `txn_type` enum('buy','sell') DEFAULT NULL,
  `share` bigint(20) DEFAULT '0',
  `share_rate` decimal(10,2) DEFAULT '0.00',
  `share_paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('tarunupadhyay011@gmail.com', '$2y$10$Rpl.VE4Q8vGtS6VNNgvageUvY.T8UPPCmGAc6krmnCzotO1WRDXiu', '2020-08-15 05:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\User', 26, 'authToken', 'e1e36d1262b121d606cb5ba6399e980438156c063159767d8bbcbe7791d46c0b', '[\"*\"]', '2020-09-04 08:19:48', '2020-09-03 16:23:15', '2020-09-04 08:19:48'),
(3, 'App\\User', 2, 'authToken', '80597f4d841e9774e0a0f71d9c18026d24e678268208fc0e8631114cc97c0405', '[\"*\"]', '2020-09-03 20:20:50', '2020-09-03 17:51:33', '2020-09-03 20:20:50'),
(4, 'App\\User', 2, 'authToken', '2ab242fccf68cce6384a10c1c2150c74050cad408e3abeec1fdfd7604670df09', '[\"*\"]', '2020-09-04 19:30:42', '2020-09-03 20:23:15', '2020-09-04 19:30:42'),
(5, 'App\\User', 3, 'authToken', '7b54d17f80a029096fd3fef0d32d364b47c02da2da4a05ac65c1d6e5050ef774', '[\"*\"]', NULL, '2020-09-04 10:26:23', '2020-09-04 10:26:23'),
(7, 'App\\User', 3, 'authToken', 'e1678e928622295061fab6fdfc8c3c80417a38b71561780dcfd729332a17eae0', '[\"*\"]', '2020-09-04 12:05:23', '2020-09-04 10:40:25', '2020-09-04 12:05:23'),
(8, 'App\\User', 2, 'authToken', 'b1aa5628b8af835590e8253ee00f29e53443013e8af0f984939199e863a2a2d3', '[\"*\"]', '2020-09-16 12:22:43', '2020-09-04 11:10:33', '2020-09-16 12:22:43'),
(9, 'App\\User', 3, 'authToken', 'ddc5da698965e41377159a6143226d7e8d1c95e06d680edeb2206dfbc6408991', '[\"*\"]', '2020-09-04 12:07:14', '2020-09-04 12:06:43', '2020-09-04 12:07:14'),
(11, 'App\\User', 3, 'authToken', '001147a22862a6f40873d1a432362fef816999bb03f01c891a7af990c6308010', '[\"*\"]', '2020-09-04 18:47:49', '2020-09-04 17:37:01', '2020-09-04 18:47:49'),
(12, 'App\\User', 3, 'authToken', '3d52fd372fac0eeeac18c61bd433b50f039f4f0d43063dbb4e7ddc34ebeff48d', '[\"*\"]', '2020-09-04 18:27:21', '2020-09-04 18:26:12', '2020-09-04 18:27:21'),
(14, 'App\\User', 3, 'authToken', 'fe3ede51c7f7fd38dca131efc468bc9c5c0804f6202a4f01f222b1339384c0ca', '[\"*\"]', '2020-09-08 17:15:51', '2020-09-04 20:06:47', '2020-09-08 17:15:51'),
(15, 'App\\User', 2, 'authToken', 'fefeabc13e413335a2d01e8342769900dd2021c231cd81be800b69880a4d5c6b', '[\"*\"]', '2020-09-05 13:37:39', '2020-09-05 13:28:10', '2020-09-05 13:37:39'),
(18, 'App\\User', 4, 'authToken', 'bcf8ad636b0191b4969d6489f726fdca486d8861e6102ab4304979cfb7a9a518', '[\"*\"]', '2020-09-12 13:55:26', '2020-09-09 19:11:16', '2020-09-12 13:55:26'),
(20, 'App\\User', 3, 'authToken', '19cff43c8d7c8ac100d845982f235693609d2171b8a6f9f8326de6385bce50ee', '[\"*\"]', '2020-09-10 15:10:27', '2020-09-10 12:37:08', '2020-09-10 15:10:27'),
(21, 'App\\User', 3, 'authToken', 'fd081ef94663e9ecba304f63664b98a39ca31cabb9f968cac57b04afb47bccee', '[\"*\"]', '2020-09-11 17:43:32', '2020-09-10 17:21:20', '2020-09-11 17:43:32'),
(24, 'App\\User', 5, 'authToken', '7a62e40775be0d2965a038efdc3742917a5a99b2e05c084c81a32bf12a4f40ef', '[\"*\"]', '2020-09-11 18:16:16', '2020-09-11 18:09:29', '2020-09-11 18:16:16'),
(25, 'App\\User', 3, 'authToken', '3b12f0f7f006d72e10f8da9e70666f4343a50b0a6ec01cdb6a1b0bb69b91c8fd', '[\"*\"]', '2020-09-12 08:49:12', '2020-09-11 18:21:30', '2020-09-12 08:49:12'),
(26, 'App\\User', 3, 'authToken', '9e6583314cdbc94638c9b4dc7546cd762e26f52aa2eb785fc8b020f2cd3d649c', '[\"*\"]', '2020-09-12 09:16:50', '2020-09-12 09:16:40', '2020-09-12 09:16:50'),
(33, 'App\\User', 3, 'authToken', 'd9d5180214ca0b2e8764d84d8cd546515cde782fd2a36233dd6bc3acd77e9ac3', '[\"*\"]', '2020-09-15 08:26:34', '2020-09-14 18:05:09', '2020-09-15 08:26:34'),
(34, 'App\\User', 2, 'authToken', 'eaa51847401c0725796f8086d305f275666fea89af8be2856154f5831b207a8f', '[\"*\"]', '2020-09-16 12:25:08', '2020-09-16 12:21:44', '2020-09-16 12:25:08'),
(35, 'App\\User', 2, 'authToken', 'a3bc10f8e769fbb43c6ca7203f0c1564523c707dfc2848f3c6737eac6e040b3b', '[\"*\"]', NULL, '2020-09-16 12:32:44', '2020-09-16 12:32:44'),
(36, 'App\\User', 2, 'authToken', 'ca862a8039fe9f8bb5e7d9ca66f338c823ffdcf1bdf1bc7edfbd1926467f2964', '[\"*\"]', NULL, '2020-09-16 12:34:02', '2020-09-16 12:34:02'),
(37, 'App\\User', 2, 'authToken', '32aa743d0ee5b9bb8cb6b8a49a132345c2ee573bbbf3c19acbb25cbfb090a14c', '[\"*\"]', NULL, '2020-09-16 12:43:18', '2020-09-16 12:43:18'),
(39, 'App\\User', 3, 'authToken', 'e83aa8dddbabcb5ad72c7a4b847392205a933c5baf66eefd3bef2b4a79558fe4', '[\"*\"]', '2020-09-17 10:09:09', '2020-09-16 12:44:53', '2020-09-17 10:09:09'),
(40, 'App\\User', 6, 'authToken', 'fab8fd1bbc164ea1eafc0569cd36f5856267fc9339d5b46c7a5758bd62f9c8a5', '[\"*\"]', '2020-09-16 15:51:47', '2020-09-16 13:05:44', '2020-09-16 15:51:47'),
(42, 'App\\User', 8, 'authToken', '8910f4071a1ec1332e477355a1641724062a4373ef50b2bf54fcba8ad14b9835', '[\"*\"]', '2020-09-17 06:17:07', '2020-09-17 06:14:40', '2020-09-17 06:17:07'),
(43, 'App\\User', 8, 'authToken', '3c865d39f910b6c951629475bb594d33ede9f81e23e45133e28d39181722f3e7', '[\"*\"]', NULL, '2020-09-17 07:37:14', '2020-09-17 07:37:14'),
(44, 'App\\User', 8, 'authToken', 'fb1f90ebd322b7ac94c6b4507562cceaa800a9e33b73ca32ae46cb62b8b20d39', '[\"*\"]', NULL, '2020-09-17 07:49:53', '2020-09-17 07:49:53'),
(46, 'App\\User', 4, 'authToken', '09981a3e89b914c35169443bc1cf54df68b5bc1ce7c06ee4a05c7df5d441db51', '[\"*\"]', '2020-09-17 20:23:39', '2020-09-17 20:23:29', '2020-09-17 20:23:39'),
(47, 'App\\User', 2, 'authToken', '1256ae9675de80b7173ea51c368164b380616f722289fb199b671b8191b10e6e', '[\"*\"]', NULL, '2020-09-18 12:04:59', '2020-09-18 12:04:59'),
(48, 'App\\User', 2, 'authToken', 'fb93d6612c97458a401d33453bba21b70e809b4daac87dd0cb07306ce7a46f4e', '[\"*\"]', '2020-10-06 17:26:42', '2020-09-18 12:10:20', '2020-10-06 17:26:42'),
(49, 'App\\User', 4, 'authToken', 'b4f06c23b20a3cc22bb13f60fb618a95bead4fc9dc6bb3c87e8aa5809aee913b', '[\"*\"]', '2020-09-23 13:37:06', '2020-09-19 13:55:48', '2020-09-23 13:37:06'),
(50, 'App\\User', 3, 'authToken', '2fdb9ddd1cbe72df04d4882e363192c503c119f1d40ad3f7050a61302477879a', '[\"*\"]', '2020-09-23 08:51:36', '2020-09-23 08:22:37', '2020-09-23 08:51:36'),
(51, 'App\\User', 2, 'authToken', '068ab6dfe82cd7696ed8e86bc7f6d6b5040342234f5f325366f26881c38d78f2', '[\"*\"]', NULL, '2020-09-23 09:17:45', '2020-09-23 09:17:45'),
(52, 'App\\User', 2, 'authToken', '1274121ebc898360512d519989796acc86991ab5d1b1494821a97ba23d3ce60a', '[\"*\"]', '2020-09-26 09:01:50', '2020-09-23 09:20:04', '2020-09-26 09:01:50'),
(55, 'App\\User', 4, 'authToken', 'cea14c9178c5c033234af666681ffa7eff11829a75517989aadde830ff246c61', '[\"*\"]', '2020-10-06 22:28:00', '2020-09-23 13:38:51', '2020-10-06 22:28:00'),
(56, 'App\\User', 2, 'authToken', '4d0b7ca3536f9ce054f7b0f169a4cf88d739ef14aa985ce612af16c6993d8068', '[\"*\"]', '2020-09-24 08:21:07', '2020-09-24 07:49:52', '2020-09-24 08:21:07'),
(57, 'App\\User', 10, 'authToken', 'f770b78c052c55c100fc85a2b2d15b4432ab9dee321c019ba84cc69a576952c1', '[\"*\"]', '2020-09-24 14:00:55', '2020-09-24 13:56:45', '2020-09-24 14:00:55'),
(59, 'App\\User', 10, 'authToken', '84157ec4bdbdff71df71327e3a871236c4aa46d15162b9612dc9268818a0fc0d', '[\"*\"]', '2020-09-25 12:24:47', '2020-09-25 11:51:59', '2020-09-25 12:24:47'),
(60, 'App\\User', 2, 'authToken', '361406b15903a783dce7ba6ab861dfbc0efbebfff9897dd1fa58aa838198c345', '[\"*\"]', '2020-09-25 12:01:59', '2020-09-25 11:56:35', '2020-09-25 12:01:59'),
(63, 'App\\User', 10, 'authToken', '4a1dba58edc8d4f9291f45b242d6aad617ff5674630e3c2103df51dc2f155c45', '[\"*\"]', '2020-09-26 14:06:42', '2020-09-26 12:20:52', '2020-09-26 14:06:42'),
(65, 'App\\User', 2, 'authToken', '802161e676569b8ae3a0074e23481d947bf26f67ec32546c3049c2e1b58e3a99', '[\"*\"]', '2020-09-29 11:01:17', '2020-09-28 13:37:50', '2020-09-29 11:01:17'),
(66, 'App\\User', 9, 'authToken', '6724d3d371af154aff7649e04a9a6118897e06771476febff6d473cf78cbf826', '[\"*\"]', '2020-10-03 14:11:16', '2020-09-28 15:36:23', '2020-10-03 14:11:16'),
(67, 'App\\User', 10, 'authToken', 'c5eec1b2dddb2e2d0e67442f8831370e034a995e89b0d7914ec743520c72d42d', '[\"*\"]', '2020-09-30 14:12:30', '2020-09-29 07:39:41', '2020-09-30 14:12:30'),
(68, 'App\\User', 2, 'authToken', '869ab617b1e3d8afaab247599b3dc8f27bf766b734dbb3f4a7eafdbe09c82160', '[\"*\"]', '2020-09-29 13:01:15', '2020-09-29 11:39:22', '2020-09-29 13:01:15'),
(70, 'App\\User', 10, 'authToken', '0b84a8175c5f917b7d6d3019c95f99be333dc438dc6de27b2ed77d10a8cc7e36', '[\"*\"]', '2020-09-29 13:21:45', '2020-09-29 13:02:08', '2020-09-29 13:21:45'),
(72, 'App\\User', 10, 'authToken', 'c76ace8a922b9a04bde4fb3f2cd62914b54ba86c4e6e3f575e4cbeee78a497d4', '[\"*\"]', '2020-10-06 09:41:40', '2020-09-30 09:16:57', '2020-10-06 09:41:40'),
(73, 'App\\User', 12, 'authToken', 'fa1f039f252daec198f701cd2aab9fa0249c96cf7d9f406069ab1062b242b7e5', '[\"*\"]', '2020-10-04 06:21:31', '2020-09-30 11:25:00', '2020-10-04 06:21:31'),
(74, 'App\\User', 9, 'authToken', '0406f69a2a7398876f8a7050a7325a586dfe536d1e3aa1927df252fa837fd797', '[\"*\"]', '2020-10-02 09:17:25', '2020-09-30 12:29:29', '2020-10-02 09:17:25'),
(75, 'App\\User', 10, 'authToken', '44bba5a88a99ac93b6efc7509071b9a295429a0baad64c8e2828124c79a7c64c', '[\"*\"]', '2020-10-02 10:58:45', '2020-10-01 08:17:24', '2020-10-02 10:58:45'),
(76, 'App\\User', 10, 'authToken', 'b4049ab24902040a10cf7fe58e5e6f6c237371b35128b43fc3b4223dcca47b62', '[\"*\"]', '2020-10-01 08:36:19', '2020-10-01 08:18:27', '2020-10-01 08:36:19'),
(78, 'App\\User', 10, 'authToken', '0c4b5b3c413dd6bdd4522e7526cd44a2d5f043e5d2006793c37edf9be8c477af', '[\"*\"]', '2020-10-06 13:52:57', '2020-10-03 09:46:10', '2020-10-06 13:52:57'),
(79, 'App\\User', 10, 'authToken', '61274ee6a168b48093588906ce1c8e7000e4c0f5d62c85fa0ecc32dc78e9bee8', '[\"*\"]', '2020-10-06 14:00:34', '2020-10-03 11:58:01', '2020-10-06 14:00:34'),
(80, 'App\\User', 9, 'authToken', '2b78a366f1ac3dc73231272a0cfbc5b35442890845fa234a03325ba2b5c21d13', '[\"*\"]', '2020-10-03 14:14:02', '2020-10-03 14:12:43', '2020-10-03 14:14:02'),
(81, 'App\\User', 10, 'authToken', '49dd5fac0e4a838362473dbe27c993024f2c0638cda88f8b41691813944acb8c', '[\"*\"]', '2020-10-07 09:00:41', '2020-10-07 07:37:48', '2020-10-07 09:00:41'),
(85, 'App\\User', 13, 'authToken', 'a6ea9c7f34643d82afd4255fedaac88edf095be8fe77d23090d00c860015e90e', '[\"*\"]', NULL, '2020-10-07 11:22:28', '2020-10-07 11:22:28'),
(86, 'App\\User', 13, 'authToken', '6e4c027be3fffbf03dece3d5ab8b3abbc41a46fa62ba22d03d7287f065d75149', '[\"*\"]', NULL, '2020-10-07 11:22:59', '2020-10-07 11:22:59'),
(87, 'App\\User', 13, 'authToken', 'f442ddf4733fb8dc0a6b50afa91d914c8622430cf4173cf5ed365f08fedcc3a6', '[\"*\"]', NULL, '2020-10-07 11:25:01', '2020-10-07 11:25:01'),
(88, 'App\\User', 13, 'authToken', '88d6f6496355858b488d38e73d3f8deb112aeaaa0a0f179e9938e5a14072d0fa', '[\"*\"]', NULL, '2020-10-07 11:25:17', '2020-10-07 11:25:17'),
(89, 'App\\User', 13, 'authToken', '73937c8b5aa61d67c55a7ee1840c30b8cd9b6a0a83a60173bba7d0ddac9a72a6', '[\"*\"]', NULL, '2020-10-07 11:25:38', '2020-10-07 11:25:38'),
(90, 'App\\User', 13, 'authToken', '4a323922c0dc0e27a6494a3a712a8ada5481de13e3c8a5802d751c3c987a1089', '[\"*\"]', NULL, '2020-10-07 11:31:34', '2020-10-07 11:31:34'),
(91, 'App\\User', 13, 'authToken', '5124523a34030954af33132c082b8b2b64ab53acb382ebe34a665c9fa7eb9ce0', '[\"*\"]', NULL, '2020-10-07 11:32:02', '2020-10-07 11:32:02'),
(93, 'App\\User', 13, 'authToken', 'd5afb6c8695f74f079731f42c2f927c519d3252c25b3a04cf6d9f8f64d972faa', '[\"*\"]', NULL, '2020-10-07 11:33:08', '2020-10-07 11:33:08'),
(94, 'App\\User', 10, 'authToken', '29614714b7bf4be7b0b1b9efec904c3381616505b4eba8a700f817f011a6029f', '[\"*\"]', '2020-10-07 11:44:04', '2020-10-07 11:33:29', '2020-10-07 11:44:04'),
(95, 'App\\User', 13, 'authToken', '7afe1e01672b0117fbc641af2b1265755fc0c07bb96409b27dd6e961d28051c1', '[\"*\"]', NULL, '2020-10-07 11:50:38', '2020-10-07 11:50:38'),
(96, 'App\\User', 13, 'authToken', 'f877f7b1aebdb80026364a159610dd5a17ca441fd95442aac2b8fcff88582257', '[\"*\"]', NULL, '2020-10-07 11:57:50', '2020-10-07 11:57:50'),
(97, 'App\\User', 10, 'authToken', '76e3a7ccba07adf1eb0df15f6f8f2647dbc6a90df23f983dd7c0893fb2efa1c8', '[\"*\"]', NULL, '2020-10-07 06:51:32', '2020-10-07 06:51:32'),
(98, 'App\\User', 10, 'authToken', 'ba44aff5fa4c5f9627311e719991d27b9a3cd9de248babdd2966fd9c06bbf15d', '[\"*\"]', '2020-10-07 07:19:07', '2020-10-07 06:53:10', '2020-10-07 07:19:07'),
(99, 'App\\User', 10, 'authToken', 'cf47929f1b6b16984d37e09075f9221c946266ded2f0aa0c6f5cec0a460e3ac4', '[\"*\"]', '2020-10-07 12:01:20', '2020-10-07 11:45:39', '2020-10-07 12:01:20'),
(100, 'App\\User', 9, 'authToken', 'c0198ef368d92a8b0a1f630591c427b3bc3526be89f7d6c5dbd61206d6ca13b9', '[\"*\"]', '2020-10-09 00:52:49', '2020-10-09 00:51:42', '2020-10-09 00:52:49'),
(101, 'App\\User', 9, 'authToken', '21d36fd256abba1da690c9f5f1b232959963be4f7fec96a72e683bdaa1e33f68', '[\"*\"]', '2020-10-09 08:36:50', '2020-10-09 08:35:38', '2020-10-09 08:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verification_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verified` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` mediumtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ios',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `free_trade_count` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `country_code`, `mobile`, `mobile_verification_code`, `mobile_verified`, `address`, `image`, `account_type`, `device_token`, `device_type`, `remember_token`, `free_trade_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Admin', NULL, 'admin@admin.com', '2020-08-20 06:41:37', '$2y$10$hiVFGY6xvxTP8DWDLixTyuJvINGZ4bJ8PHj58v0KMLjEjiFQHjfMi', '+91', '1234567890', NULL, NULL, 'ipsum lorem ipsum lorem ipsum lorem ipsum  ipsum lorem ipsum', 'profile_1599252572.png', NULL, NULL, 'ios', 'qodNDQxBPBFStkV668MGixgwOUxWRnl0Cwc87OvHyiKWNpxF7jmhHYQyxynD', 1, '2020-08-10 08:45:31', '2020-09-04 20:49:32', NULL),
(2, 'tarun', 'Tarun U', 'Upadhyay', 'tarunupadhyay011@gmail.com', '2020-08-20 06:41:37', '$2y$10$bCudAFBKmKIPK/H2if5sReLIqUdetvU0ighdlEAxyu2xhDz/FfaBy', '+91', '7999747580', '39879', '1', NULL, '1599917218.jpg', 'Freemium', '81335A9855F060EB73412AD66367576368CFFDA041B25991B148E6A6FAC823C0', 'ios', NULL, 1, '2020-08-11 09:02:30', '2020-09-04 11:10:33', NULL),
(4, 'alechamilton4', 'Alec', 'Hamilton', 'ahammilton422@gmail.com', NULL, '$2y$10$LAaiZIFcA6oMxRNtClFbruadt0kiJK/FOwMuG1AbNrb6ueVwwnvpq', '+1', '(303)-886-1262', '41967', '1', NULL, '1599917218.jpg', 'Premium', 'CEAC80DF7FCCA4269B8A81684A530151CCFFC959472CD10AD93D273717710A41', 'ios', 'smrVMcj5VIIGAWwa3seyKhcgZHMDNkpuMepBW1lVUCBPdwXSjYkDTJGCTtLn', 1, '2020-09-09 19:11:02', '2020-09-23 13:38:51', NULL),
(7, 'saurabh', 's', 'ar', 'saurabh.a@valere.io', '2020-09-12 13:27:43', '$2y$10$NdvXupof.4fm.QrEnOVWHejX4lIo1VSKVwOeaMu5/Oh/Nfm2snp62', '+91', '(997)-700-1105', '15916', '1', NULL, '1599917218.jpg', 'Freemium', NULL, 'ios', 'X7R4MIRgfjsbXQ7xoOGX7lfnpIrlnmupzRqFlfp2GQDRfnMmF33RJFTIP0FR', 1, '2020-09-12 13:24:13', '2020-09-28 05:54:05', NULL),
(8, 'kundan', 'test', 'user', 'kundan930@yopmail.com', NULL, '$2y$10$qgY5AQiSYw3NuQofC2/ZK./C4XLtuTLJKp96YrTLKrOyAFcb4RBjC', '+91', '(930)-126-2064', NULL, '1', NULL, '1600337038.jpg', 'Freemium', NULL, 'ios', 'ckKHxRUkCPQi5fdE5nLwPFFjGaOOhfvMavm65ygaa5p8WgBpjOTggb7UePG3', 1, '2020-09-17 06:14:30', '2020-09-23 08:18:23', NULL),
(9, 'Kundan', 'Kundan', 'Singh', 'hdhdhs@hdhshs...hdhdh', NULL, '$2y$10$4CmQKCu8Nz7uAYXLZCMJ8.w5w8P66Z8QFbzHZTk/di7YGhLCtAyKi', '+91', '7747970068', '98456', '1', NULL, '1601468994.jpg', 'Premium', '81335A9855F060EB73412AD66367576368CFFDA041B25991B148E6A6FAC823C0', 'ios', 'M3GENTPPYhQoYvgSdoGLRznydwHo2jsx3KtbhXP697MO1J5Xsnbzf8jMyNUr', 1, '2020-09-23 11:11:22', '2020-10-09 00:51:42', NULL),
(10, 'm', 'k', 'kthus', 'mk@gmail.com', NULL, '$2y$10$DaReKrCjmZTl9rN5/fq9c.KKuv..4mj1bbyhmrxVaEecUJ5PCzngm', '+91', '9179370347', NULL, '1', NULL, '1601552677.jpg', 'Freemium', '81335A9855F060EB73412AD66367576368CFFDA041B25991B148E6A6FAC823C0', 'ios', 'v36gRU4D0nZK0cS5VtWw4CI1KNVSZUTKs8fRWxxETM6NlSo6EAYpowZX6vJb', 1, '2020-09-24 13:56:20', '2020-10-07 06:51:32', NULL),
(12, '@SAR81', 'Saurabh .', '.......... valere', 'saurabhvalereio@gmail.com', NULL, '$2y$10$xfkX5I2TB8ECsWVb7cz/4.F5hVaumVmqwnzT9zZbxVPOAdO1O7w8G', '+91', '9977001105', NULL, '1', NULL, NULL, 'Freemium', 'EA460DC3626BF544176F7862CB8E8D6EFF541CE732BD506422B62A7F7BD074C4', 'ios', 'OGqg7afku7HocFxCN3cT7u3cW9qm7ajt8VnPkgE1vIkDkoRf5aHpuaqeirJ0', 1, '2020-09-30 11:24:45', '2020-09-30 11:27:54', NULL),
(13, '', NULL, NULL, NULL, NULL, '$2y$10$WQd5tkqPoM6A/0Q5.PUoX.d.lLPPxyFbWAXeJkYQwIBPeabCe5LUW', '+91', '9993968327', '01860', '1', NULL, NULL, NULL, '491988E6FFE08D516090D0209D1910EA5A37B30F1B87868AB6D00B6249124A80', 'ios', 'wZNT1wNOoMPPieM1mYYVI7S6zXlkSy513HxLSipu5lnDG4JIOgkhgaUsqzZ6', 1, '2020-10-07 11:22:15', '2020-10-07 11:50:38', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apis`
--
ALTER TABLE `apis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_requests`
--
ALTER TABLE `api_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `free_trade_counts`
--
ALTER TABLE `free_trade_counts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `joined_leagues`
--
ALTER TABLE `joined_leagues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leagues`
--
ALTER TABLE `leagues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `league_invitations`
--
ALTER TABLE `league_invitations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

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
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `apis`
--
ALTER TABLE `apis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `free_trade_counts`
--
ALTER TABLE `free_trade_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `joined_leagues`
--
ALTER TABLE `joined_leagues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leagues`
--
ALTER TABLE `leagues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `league_invitations`
--
ALTER TABLE `league_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership`
--
ALTER TABLE `membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
