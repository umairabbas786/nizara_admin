-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2022 at 12:11 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monkeycr_nizara_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(28) NOT NULL,
  `permissions` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `forgot_password_code` varchar(255) DEFAULT NULL,
  `fcm_id` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `role`, `permissions`, `created_by`, `forgot_password_code`, `fcm_id`, `date_created`) VALUES
(1, 'admin', 'd8578edf8458ce06fbc5bb76a58c5ca4', 'support@ekart.in', 'super admin', '{\"orders\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"categories\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"sellers\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"subcategories\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"products\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"products_order\":{\"read\":\"1\",\"update\":\"1\"},\"featured\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"customers\":{\"read\":\"1\"},\"payment\":{\"read\":\"1\",\"update\":\"1\"},\"notifications\":{\"create\":\"1\",\"read\":\"1\",\"delete\":\"1\"},\"transactions\":{\"read\":\"1\"},\"settings\":{\"read\":\"1\",\"update\":\"1\"},\"locations\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"reports\":{\"create\":\"1\",\"read\":\"1\"},\"faqs\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"home_sliders\":{\"create\":\"1\",\"read\":\"1\",\"delete\":\"1\"},\"new_offers\":{\"create\":\"1\",\"read\":\"1\",\"delete\":\"1\"},\"promo_codes\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"delivery_boys\":{\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"return_requests\":{\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"}}', 0, '', 'cv7qKEjDS-uhwHzhFxCiwq:APA91bFzCRjuBWXM3lEM5rlxVrkXRMfAgPM4E6PTa7Q79bFgP9yMj5OI6eIG2O4koStpX97hHE8GyTN-453Fd4s9cDKtb6TiVj5103ORdAQlwPgyAIazDiUcYu-FS6COuRF0YIzJWQu0', '2020-06-22 16:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT 0,
  `pincode_id` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `minimum_free_delivery_order_amount` int(20) NOT NULL DEFAULT 0,
  `delivery_charges` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`id`, `city_id`, `pincode_id`, `name`, `minimum_free_delivery_order_amount`, `delivery_charges`) VALUES
(1, 1, 1, 'Guwahati assam', 1000, 200);

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(11) NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `name` varchar(60) NOT NULL,
  `slug` varchar(256) DEFAULT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `product_rating` tinyint(2) NOT NULL DEFAULT 0,
  `web_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `row_order`, `name`, `slug`, `subtitle`, `image`, `status`, `product_rating`, `web_image`) VALUES
(1, 0, 'test final', 'test-final', 'testing final', 'upload/images/0308-2022-04-10.jpg', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_variant_id`, `qty`, `date_created`) VALUES
(7, 6, 5, 7, 4, '2022-04-08 15:44:11'),
(8, 6, 6, 8, 3, '2022-04-08 15:44:13'),
(9, 6, 3, 4, 3, '2022-04-08 15:44:14'),
(10, 6, 4, 5, 3, '2022-04-08 15:44:16'),
(12, 8, 3, 4, 1, '2022-04-08 18:12:06'),
(16, 9, 6, 10, 2, '2022-04-09 09:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `name` varchar(60) NOT NULL,
  `slug` varchar(256) DEFAULT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `product_rating` tinyint(2) NOT NULL DEFAULT 0,
  `web_image` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `row_order`, `name`, `slug`, `subtitle`, `image`, `status`, `product_rating`, `web_image`) VALUES
(2, 2, 'School Items', 'school-items', 'School Items', 'upload/images/1704-2022-03-19.jpg', 1, 0, ''),
(3, 0, ' Kitchen & Tableware', 'kitchen-tableware', 'Kitchen & Tableware', 'upload/images/4057-2022-03-23.png', 1, 0, ''),
(4, 63, 'Spare Parts', 'spare-parts', 'Spare Parts', 'upload/images/8771-2022-03-19.jpg', 1, 0, ''),
(5, 62, 'Glassware', 'glassware', 'GlassWare', 'upload/images/1865-2022-03-19.jpg', 1, 0, ''),
(6, 1, ' Storage & Decor', 'storage-decor', 'Storage & Decor', 'upload/images/0303-2022-03-23.png', 1, 0, ''),
(7, 61, ' HouseHold', 'household', 'Household', 'upload/images/8581-2022-03-19.jpg', 1, 0, ''),
(8, 64, ' Appliance', 'appliance', 'Appliance', 'upload/images/7973-2022-03-19.png', 1, 0, ''),
(11, 5, ' Personal Care', 'personal-care', 'Personal Care', 'upload/images/0433-2022-03-19.png', 1, 0, ''),
(12, 4, 'Prestige', 'prestige-1', 'Prestige', 'upload/images/9601-2022-03-20.png', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `status`, `date_created`) VALUES
(1, 'Guwahati', 1, '2022-04-08 17:53:39'),
(2, 'Manipur', 1, '2022-04-08 17:53:50'),
(3, 'Lamding', 1, '2022-04-08 17:53:57'),
(4, 'Manipur', 1, '2022-04-08 17:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boys`
--

CREATE TABLE `delivery_boys` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `order_note` text DEFAULT NULL,
  `password` text NOT NULL,
  `address` text NOT NULL,
  `bonus` int(11) NOT NULL,
  `balance` double DEFAULT 0,
  `driving_license` text DEFAULT NULL,
  `national_identity_card` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bank_account_number` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `account_name` text DEFAULT NULL,
  `ifsc_code` text DEFAULT NULL,
  `other_payment_information` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `fcm_id` varchar(256) DEFAULT NULL,
  `pincode_id` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boy_notifications`
--

CREATE TABLE `delivery_boy_notifications` (
  `id` int(11) NOT NULL,
  `delivery_boy_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(56) COLLATE utf8_unicode_ci NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fcm_id` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `user_id`, `fcm_id`, `seller_id`) VALUES
(2, 0, 'dxp8EFoRSnC75kQHqWMVRk:APA91bGx5cK3mRKetoO3CZtefLVAvyHl-Frx2G9kLzCPIYwl6P1aFx2dHYvTL1izYcRVa5T3N7aynC4oL-ndOYlKX7PX-7G874eNwIx4Yot_282dimByVoJowkdxd5-VuMAoXNZPtO37', NULL),
(3, 0, 'fyjYTGWdQt298_uJQ250z-:APA91bHona0dj369OKcWDZm4EGP07kX1ENPEJSCb3Zif5VhBrQin3sPbCI5vpAh3iZiPPIZjB9XqR1KimqvhMKZXEwmj7klZSI6ThBhOMn7DH6os_1xsClfFfDm6bB7E_9wgOg_DRuc0', NULL),
(4, 0, 'c_Rk27s5RqOpOAme7KHXIO:APA91bFlL5pux28n2vvMnRAw98VBGbqNprHdzOiHKxDnht6H8kTn1mEL_EbT9v19wh0WZ5bXqDekPanGbPXXC1Doho5Uvvr3WS-lMNNOK82hncVZLhZaBpvGDxTDqI5BvFTfhJ4fjo9F', NULL),
(5, 0, 'cVa4lOy1RcawdrtVLatWra:APA91bEmIrnRUmCt-nACv8S1KE2rQ2sIkQSPkB6Rx4aB8PDkW6cfy08ue2qXu_U618Tif9zR3daRwdO1WhxCfK6-fsa9swzJfrY4V0iM6_yOwvMyxN4belyuASHsV7EVr1FfrK3FMAjk', NULL),
(6, 0, 'dpEXK_VgQI-ovjz507p0qZ:APA91bEDvgK_2aqjc6ttHHXaYmgXk69QQgPlIBobUmumjPrIq9-EaeJG-OE1CLcYnLbFW727L8SqVLwtd3dsVvGUI75LJAma_6mOoCZB8Umhetxc_8NOh9DFwZgzQiaoLS8-14R2Lorm', NULL),
(7, 0, 'csPHGXYXTSm1SpPuYO9k0r:APA91bGnG3UAUUNf5sWuca07uWAZqcLp0xRJJUaItadOk_NX-Bbnza8yNF2BC9VE42ESmgMv1vryK8krAeHdz6leCsDSAjRDO_9YVR9NeuiHwN7elXRIjWAaYeAQSRAZKzx0VpaKh-Hx', NULL),
(8, 0, 'fN8EJzRFS_SX23N079f2b9:APA91bFZcblqJVP42Zjh1gvEUuy_6_OdD7vwtyoOGKrkC41C_glSjjJz-MpeABxQwcFty7R0R-Hdb5mh10uvAWKPmuvBpWlzL6lF3GnAUbuXgf_wvj1b9GoZ6zSCVaVnkkcah2YyMcAX', NULL),
(9, 0, 'dCX0Wg1cTlOO7XIroAB5JA:APA91bEEGMzE27dSnMpOJE92cdkdAf9kRm6hA39YPKCQwqmKxVbYgyV221g-_GVynlIAKCHAkUWfjjrnO9jNGS6hOwwfREaP8HCY4WxUfLCsPZ7IbcSwtcRYv8Gpwhg4RPHVIxN-fU2M', NULL),
(10, 0, 'dw_zRwvlTD2qm8NJY5Nyc2:APA91bHuIfa3N9asCb3BtnO2b_RUBEuA3MogizeNRRS-NC80TjIBDrDE8xMrVf_iDXrg9q3DDzpGGSD_q9mX5dx1fkwQvYJI81H5j48FDMgH1u1O_SSv5r1fU5IlpAZrJa77pwbP5cIg', NULL),
(11, 0, 'fiCfFe_yTMutGY6Bna53Py:APA91bE_5CbCoGc7JErZa76pPHTnlp5jvC2uCyOSmOLLHntiiqxAo6jgEjGS3PcyrW_ppK9FMWhMRwYGyE_lT4Quw_Kt6nBfVFnU3XJK9tPJbeDenhebgoFBkyhMAe1gGFIS7YN_YjCK', NULL),
(12, 0, 'd8xyRT19TI6FxarKoIvz8q:APA91bHsbGd7Osvs1GziaPC4dUijOyIbmWbtAfve-u1hg5W-xsq5uchfVeext-JfPzy5NKUrPZmN1xqpqZn6iFofr05xEu_fSb3qvmt38RGQvwn3dwIHXETmyxO4mOIRodFo4TrZnJfI', NULL),
(13, 0, 'c-tYdlaBQLaNddxMV37SYK:APA91bGUux9iRXw5IpMk653rU4TVRlWXx0cUMIZiqMDdh6ljNP79t63zpxXBp6L_CyMoL2V-8lr6J3KRnySVcicfjbzulPITD9riHhueYnLH6EY4paoMMBl97JhztpADZwImwoiSXo_7', NULL),
(14, 0, 'er59Y7wrQVOsDSnzTgN3UM:APA91bGJvNvkwogbV0ayzU4wHs6DXikR4btq-OUjQ81FaHHC99UHy_LElVqBoNT447iMW--kjRzh-_vJE6ps9ABI9RVkiWuXWt2DVjLzwZBBqguhMMyxAO-2P6RO4dIqShQscr0oHoCb', NULL),
(18, 0, 'dJoQIWwGSnKdDrGC4vwCrC:APA91bG6Y-LpEKdnaeapB7U4Kxo9VQDcPtjkKXtpfnzRTynMwHFTuqtVD4IbrTgcdgUQfgE1ptNxeK25qeIEmY-u_A8BoMg-n2JDkwwXj8TghvLotrYloZ37z73Q0R5R4ob1C3AUmXGF', NULL),
(19, 0, 'emuNgWmcT1KlbCtkT5GGye:APA91bGCLt4h1Xv8B-ZlTaA7SL7fm8mC068MyF52HNOf_yhwuz1cdpmTXpjKqZ3lZL9lhfMUUdjSPBUg9ouqGobIeFuA3cuBGMwm7Q_K2eIgWKW8dpwnHhtKARWwRGA7onAvf2V7rSAx', NULL),
(20, 0, 'erPqMVogRvWMbV_qVK_fUb:APA91bFz_8CA7UIaAQTexybVK6VMPktskirK7kXcK5Zbrlxtz6HU6XtWPR2WVZzfybuG9pgC--yFZavRbbE95x-n-MAsAU318byvwSmyuta11ZJDe_wp895K9e-ZPz6n6T027XGiDGOq', NULL),
(21, 0, 'dJQ3xQ_RQCyNW9ANNALweR:APA91bEv73fnCnkhpqJfeIyCj7c-ks26JlEYKgQRvrRsj9_xvYYFRcEXBWAKmEBV0M2H-sU48DVHJMLxE4C8XC1NaekZOCzG0UiqGQf17G_dV4EYLhAzUicJwcWGxFdMkGZDZh9VytpG', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `status` char(1) DEFAULT '1',
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(264) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `date_created`) VALUES
(1, 8, 3, '2022-04-08 18:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `fund_transfers`
--

CREATE TABLE `fund_transfers` (
  `id` int(11) NOT NULL,
  `delivery_boy_id` int(11) NOT NULL,
  `type` varchar(8) NOT NULL COMMENT 'credit | debit',
  `opening_balance` double NOT NULL,
  `closing_balance` double NOT NULL,
  `amount` double NOT NULL,
  `status` varchar(28) NOT NULL,
  `message` varchar(512) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `order_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `order_date` datetime NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `order_list` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `discount` varchar(6) NOT NULL,
  `total_sale` varchar(10) NOT NULL,
  `shipping_charge` varchar(100) NOT NULL,
  `payment` text NOT NULL,
  `order_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `extension` varchar(100) CHARACTER SET utf8 NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sub_directory` text CHARACTER SET utf8 NOT NULL,
  `size` text CHARACTER SET utf8 NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `message` varchar(512) NOT NULL,
  `type` varchar(12) NOT NULL,
  `type_id` int(11) NOT NULL,
  `image` varchar(128) DEFAULT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `type_id`, `image`, `date_sent`) VALUES
(1, 'Hlo', 'This is demo notification', 'default', 0, NULL, '2022-03-19 14:23:45'),
(2, 'This is a product notification', 'This is notification', 'product', 3, 'upload/notifications/1647699892.0131.jpg', '2022-03-19 14:24:52');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `image` varchar(256) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp` int(10) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL,
  `order_note` text DEFAULT NULL,
  `total` float NOT NULL,
  `delivery_charge` float NOT NULL,
  `tax_amount` float NOT NULL DEFAULT 0,
  `tax_percentage` float NOT NULL DEFAULT 0,
  `wallet_balance` float NOT NULL,
  `discount` float NOT NULL DEFAULT 0,
  `promo_code` varchar(28) DEFAULT NULL,
  `promo_discount` float NOT NULL DEFAULT 0,
  `final_total` float DEFAULT NULL,
  `payment_method` varchar(16) NOT NULL,
  `address` text NOT NULL,
  `latitude` varchar(256) NOT NULL,
  `longitude` varchar(256) NOT NULL,
  `delivery_time` varchar(128) NOT NULL,
  `status` varchar(1024) NOT NULL,
  `active_status` varchar(16) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_from` int(11) DEFAULT 0,
  `pincode_id` int(11) DEFAULT 0,
  `area_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `otp`, `mobile`, `order_note`, `total`, `delivery_charge`, `tax_amount`, `tax_percentage`, `wallet_balance`, `discount`, `promo_code`, `promo_discount`, `final_total`, `payment_method`, `address`, `latitude`, `longitude`, `delivery_time`, `status`, `active_status`, `date_added`, `order_from`, `pincode_id`, `area_id`) VALUES
(1, 8, 0, '+919957762925', '', 5250, 0, 0, 0, 0, 0, '', 0, 5250, 'cod', 'hhd,us,Guwahati,Guwahati assam,hj,ii,Pincode:781016', '0', '0', 'Date : N/A - Time : N/A', '[[\"received\",\"08-04-2022 11:33:26pm\"]]', 'received', '2022-04-08 18:03:26', 0, 1, 1),
(2, 9, 0, '6913831187', 'please deliver faster', 3600, 0, 0, 0, 0, 0, '', 0, 3600, 'cod', 'sarrabhatti,nandkunj,Guwahati,Guwahati assam,assam,india,Pincode:781016', '0', '0', 'Date : N/A - Time : N/A', '[[\"received\",\"08-04-2022 11:34:29pm\"]]', 'received', '2022-04-08 18:04:29', 0, 1, 1),
(3, 7, 0, '+923045227329', '', 4550, 0, 0, 0, 0, 0, '', 0, 4550, 'cod', 'Islamabad,FAISAL MOSQUE,Guwahati,Guwahati assam,Punjab,Pakistan,Pincode:781016', '33.64400166666667', '73.05242166666667', 'Date : N/A - Time : N/A', '[[\"received\",\"08-04-2022 11:47:35pm\"]]', 'received', '2022-04-08 18:17:35', 0, 1, 1),
(4, 9, 0, '6913831187', 'thanks', 2000, 0, 0, 0, 0, 0, '', 0, 2000, 'cod', 'sarrabhatti,nandkunj,Guwahati,Guwahati assam,assam,india,Pincode:781016', '0', '0', 'Date : N/A - Time : N/A', '[[\"received\",\"09-04-2022 12:08:08am\"]]', 'received', '2022-04-08 18:38:08', 0, 1, 1),
(5, 9, 0, '6913831187', 'hlo', 2000, 0, 0, 0, 0, 0, '', 0, 2000, 'cod', 'sarrabhatti,nandkunj,Guwahati,Guwahati assam,assam,india,Pincode:781016', '0', '0', 'Date : N/A - Time : N/A', '[[\"received\",\"09-04-2022 12:18:16am\"]]', 'received', '2022-04-08 18:48:16', 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` text CHARACTER SET utf8 DEFAULT NULL,
  `variant_name` text CHARACTER SET utf8 DEFAULT NULL,
  `product_variant_id` int(11) NOT NULL,
  `delivery_boy_id` int(11) DEFAULT 0,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL,
  `discounted_price` double NOT NULL,
  `tax_amount` float DEFAULT 0,
  `tax_percentage` float DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `sub_total` float NOT NULL,
  `status` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `active_status` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `seller_id` int(11) DEFAULT NULL,
  `is_credited` tinyint(2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `user_id`, `order_id`, `product_name`, `variant_name`, `product_variant_id`, `delivery_boy_id`, `quantity`, `price`, `discounted_price`, `tax_amount`, `tax_percentage`, `discount`, `sub_total`, `status`, `active_status`, `date_added`, `seller_id`, `is_credited`) VALUES
(1, 8, 1, 'Prestige PIC 20 1600 Watt Induction Cooktop with Push button (Black)', '1pc', 4, 0, 1, 3000, 2000, 0, 0, 0, 2000, '[[\"received\",\"08-04-2022 11:33:26pm\"]]', 'received', '2022-04-08 18:03:26', 4, 0),
(2, 8, 1, 'Shreya 3 ltr Handi cooker', '1pc', 5, 0, 5, 1500, 650, 0, 0, 0, 3250, '[[\"received\",\"08-04-2022 11:33:26pm\"]]', 'received', '2022-04-08 18:03:26', 4, 0),
(3, 9, 2, 'Shreya 3 ltr Handi cooker', '6pc', 6, 0, 1, 9000, 3600, 0, 0, 0, 3600, '[[\"received\",\"08-04-2022 11:34:29pm\"]]', 'received', '2022-04-08 18:04:29', 4, 0),
(4, 7, 3, 'Shreya 3 ltr Handi cooker', '1pc', 5, 0, 7, 1500, 650, 0, 0, 0, 4550, '[[\"received\",\"08-04-2022 11:47:35pm\"]]', 'received', '2022-04-08 18:17:35', 4, 0),
(5, 9, 4, 'Prestige PIC 20 1600 Watt Induction Cooktop with Push button (Black)', '1pc', 4, 0, 1, 3000, 2000, 0, 0, 0, 2000, '[[\"received\",\"09-04-2022 12:08:08am\"]]', 'received', '2022-04-08 18:38:08', 4, 0),
(6, 9, 5, 'Prestige PIC 20 1600 Watt Induction Cooktop with Push button (Black)', '1pc', 4, 0, 1, 3000, 2000, 0, 0, 0, 2000, '[[\"received\",\"09-04-2022 12:18:16am\"]]', 'received', '2022-04-08 18:48:16', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(6) NOT NULL,
  `txnid` varchar(20) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `itemid` varchar(25) NOT NULL,
  `createdtime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_type` varchar(56) NOT NULL,
  `payment_address` varchar(1024) NOT NULL,
  `amount_requested` int(11) NOT NULL,
  `remarks` varchar(512) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pincodes`
--

CREATE TABLE `pincodes` (
  `id` int(11) NOT NULL,
  `pincode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pincodes`
--

INSERT INTO `pincodes` (`id`, `pincode`, `status`, `date_created`) VALUES
(1, '781016', 1, '2022-04-08 17:54:34'),
(2, '781001', 1, '2022-04-08 17:54:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `name` varchar(500) NOT NULL,
  `tax_id` tinyint(4) DEFAULT 0,
  `slug` varchar(120) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `subbrand_id` int(11) NOT NULL,
  `indicator` tinyint(4) DEFAULT 0 COMMENT '0 - none | 1 - veg | 2 - non-veg',
  `manufacturer` varchar(512) DEFAULT NULL,
  `made_in` varchar(512) DEFAULT NULL,
  `return_status` tinyint(4) DEFAULT NULL,
  `cancelable_status` tinyint(4) DEFAULT NULL,
  `till_status` varchar(28) DEFAULT NULL,
  `image` text NOT NULL,
  `other_images` varchar(512) DEFAULT NULL,
  `description` text NOT NULL,
  `status` int(2) DEFAULT 1,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_approved` int(11) DEFAULT NULL,
  `return_days` int(11) DEFAULT 0,
  `type` text DEFAULT NULL,
  `pincodes` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `row_order`, `name`, `tax_id`, `slug`, `category_id`, `brand_id`, `subcategory_id`, `subbrand_id`, `indicator`, `manufacturer`, `made_in`, `return_status`, `cancelable_status`, `till_status`, `image`, `other_images`, `description`, `status`, `date_added`, `is_approved`, `return_days`, `type`, `pincodes`) VALUES
(3, 4, 0, 'Prestige PIC 20 1600 Watt Induction Cooktop with Push button (Black)', 0, 'prestige-pic-20-1600-watt-induction-cooktop-with-push-button-black-1', 8, 0, 53, 0, 0, 'Prestige', 'India with oride', 1, 1, 'processed', 'upload/images/0809-2022-03-19.jpg', '[\"upload/other_images/1647698436.6885-373.jpg\",\"upload/other_images/1647698436.6888-422.jpg\"]', 'BrandPrestige\r\nMaterial\\\"Other\\\"\r\nColourBlack\r\nItem Dimensions LxWxH39 x 32 x 10 Centimeters\r\nItem Weight4.14 Pounds\r\nHeating Elements1\r\n\r\nContent: Prestige Induction Cooktop-Pic 20.0\r\nNet Quantity: 1 Unit. Extended Cooling Sysytem\r\nVoltage: 230V, Wattage: 1600Watt\r\nNote: Kindly refer to 6th and 7th Image for error codes and their respective solutions\r\nTroubleshooting guidelines: Works only with Induction base cookware- bottom diameter between 12cm-26cm\r\nType of Control Panel - Push button\r\nGreat Features - i)Indian Menu Options ii) Aerodynamic cooling system iii) Automatic voltage regulator ,saves power\r\nIncludes: Main Unit, User Manual, Warranty Card\r\nProduct Dimensions: 38cm(Length)x26cm(Width)x6cm(Height) Weight: 2.2kg\r\n\r\n\r\nAbout The Brand\r\nPrestige is one of India’s largest kitchen appliances brands.\r\n\r\nIt is built on the pillars of safety, innovation, durability, & trust. Continuous market research & analysis is done to modify product offerings, introduce brand extensions and innovative new models that dictate the needs of an evolving consumer.\r\n\r\nPrestige operates nationally in both the outer and inner lid pressure cooker and the clip-on cooker market. The company’s state-of-the-art manufacturing capabilities and strong RPD facilities have helped the brand deliver more technologically advanced products with the highest safety standards. Prestige has developed the widest network of service centers in the country. It also has a full range of cooking and grinding appliances, kitchen tools and cleaning solutions products.', 1, '2022-03-19 14:00:36', 1, 7, 'all', ''),
(4, 4, 0, 'Shreya 3 ltr Handi cooker', 0, 'shreya-3-ltr-handi-cooker-1', 8, 0, 47, 0, 0, 'Shreya', 'India', 0, 1, 'processed', 'upload/images/1884-2022-03-22.jpg', '[\"upload/other_images/1647943828.8042-938.jpg\"]', '<p>Ree</p>', 1, '2022-03-22 10:10:28', 1, 0, 'all', ''),
(5, 3, 0, 'testing1234567890', 0, 'testing1234567890', 3, 0, 46, 0, 0, 'qwerty', 'qwerty', 0, 0, '', 'upload/images/3776-2022-03-22.jpg', '', '<p>cvbnm</p>', 1, '2022-03-22 13:32:07', 1, 0, 'all', ''),
(6, 5, 0, 'Borosil - Stainless Steel Hydra Trek - Vacuum Insulated Flask Water bottle, Black, 700ML', 0, 'borosil-stainless-steel-hydra-trek-vacuum-insulated-flask-water-bottle-black-700ml', 3, 0, 42, 0, 0, 'Borosil', 'India', 1, 1, 'processed', 'upload/images/7735-2022-03-23.jpg', '[\"upload/other_images/1648037672.4111-943.jpg\",\"upload/other_images/1648037672.4118-207.jpg\",\"upload/other_images/1648037672.4121-489.jpg\",\"upload/other_images/1648037672.4124-714.jpg\"]', 'Material type: Stainless Steel; No. of pieces: 1; Capacity: 850; Colour: Black\r\nInsulation type: Double wall; Temperature retention: Yes (Keeps liquid hot/cold upto 22 hrs hot n 24 hrs cold); Leak proof: Yes. Clean with mild detergents only\r\nBest Usage: Office/School/College/Gym/Picnic\r\nWarranty: 1 Year; Covered in warranty: This product is under one year warranty from the date of retail purchase against any manufacturing defects in material and workmanship. Warranty valid against producing original bill endorsed by the seller.1 year warranty on manufacturing defect; Not covered in warranty: The warranty does not cover damages resulting from accidents, mishandling or tampering with mechanism.\r\nInsulated double wall vacuum with copper coating for maximum temperature retention, non leaching and toxin free for healthy drinking\r\nInner wall: SS 304 Grade; Outer wall: SS 201 Grade\r\nEasy to carry in bagpacks/gym bags\r\nEasy to Pour & Easy to hold', 1, '2022-03-23 12:14:32', 1, 3, 'all', ''),
(7, 1, 0, 'umair abbas', 0, 'umair-abbas', 6, 2, 12, 0, 1, 'faf', 'fadsf', 0, 0, '', 'upload/images/8269-2022-04-10.jpg', '', '<p>asljflkasjglkjasdlkgklasdjglkasg</p>', 1, '2022-04-09 21:45:10', 1, 0, 'included', '1');

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

CREATE TABLE `product_variant` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `measurement` float NOT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `discounted_price` float NOT NULL,
  `serve_for` varchar(16) NOT NULL,
  `stock` float NOT NULL,
  `stock_unit_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_variant`
--

INSERT INTO `product_variant` (`id`, `product_id`, `type`, `measurement`, `measurement_unit_id`, `price`, `discounted_price`, `serve_for`, `stock`, `stock_unit_id`) VALUES
(1, 1, 'packet', 76, 1, 6, 0, 'Available', 6, 1),
(5, 4, 'packet', 1, 6, 1500, 650, 'Available', 88, 1),
(4, 3, 'packet', 1, 6, 3000, 2000, 'Available', 17, 6),
(6, 4, 'packet', 6, 6, 9000, 3600, 'Available', 9, 1),
(7, 5, 'packet', 1, 1, 8, 5, 'Available', 7, 1),
(8, 6, 'packet', 750, 4, 975, 750, 'Available', 50, 6),
(9, 6, 'packet', 500, 4, 835, 600, 'Available', 50, 6),
(10, 6, 'packet', 850, 4, 1150, 820, 'Available', 50, 6),
(11, 7, 'packet', 4213, 1, 4123, 1342, 'Available', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL,
  `promo_code` varchar(28) NOT NULL,
  `message` varchar(512) NOT NULL,
  `start_date` varchar(28) NOT NULL,
  `end_date` varchar(28) NOT NULL,
  `no_of_users` int(11) NOT NULL,
  `minimum_order_amount` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `discount_type` varchar(28) NOT NULL,
  `max_discount_amount` int(11) NOT NULL,
  `repeat_usage` tinyint(4) NOT NULL,
  `no_of_repeat_usage` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `remarks` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 NOT NULL,
  `short_description` varchar(64) CHARACTER SET utf8 NOT NULL,
  `style` varchar(16) NOT NULL,
  `product_ids` varchar(1024) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `title`, `short_description`, `style`, `product_ids`, `date_added`) VALUES
(1, 'Recent Products', 'qwerty', 'style_1', '6,5,4,3', '2022-03-19 11:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 DEFAULT NULL,
  `store_name` text CHARACTER SET utf8 DEFAULT NULL,
  `slug` varchar(256) DEFAULT NULL,
  `email` text CHARACTER SET utf8 DEFAULT NULL,
  `mobile` text DEFAULT NULL,
  `password` text CHARACTER SET utf8 NOT NULL,
  `balance` int(50) NOT NULL DEFAULT 0,
  `store_url` text CHARACTER SET utf8 DEFAULT NULL,
  `logo` text CHARACTER SET utf8 DEFAULT NULL,
  `store_description` text CHARACTER SET utf8 DEFAULT NULL,
  `street` text CHARACTER SET utf8 DEFAULT NULL,
  `pincode_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `state` text CHARACTER SET utf8 DEFAULT NULL,
  `categories` text CHARACTER SET utf8 DEFAULT NULL,
  `account_number` text CHARACTER SET utf8 DEFAULT NULL,
  `bank_ifsc_code` text CHARACTER SET utf8 DEFAULT NULL,
  `account_name` text CHARACTER SET utf8 DEFAULT NULL,
  `bank_name` text CHARACTER SET utf8 DEFAULT NULL,
  `commission` int(11) DEFAULT 0,
  `status` tinyint(2) NOT NULL DEFAULT 0,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `require_products_approval` tinyint(2) NOT NULL DEFAULT 0,
  `fcm_id` text CHARACTER SET utf8 DEFAULT NULL,
  `national_identity_card` text CHARACTER SET utf8 DEFAULT NULL,
  `address_proof` text CHARACTER SET utf8 DEFAULT NULL,
  `pan_number` text CHARACTER SET utf8 DEFAULT NULL,
  `tax_name` text CHARACTER SET utf8 DEFAULT NULL,
  `tax_number` text CHARACTER SET utf8 DEFAULT NULL,
  `customer_privacy` tinyint(4) DEFAULT 0,
  `latitude` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `longitude` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `forgot_password_code` varchar(256) DEFAULT NULL,
  `view_order_otp` tinyint(2) DEFAULT 0,
  `assign_delivery_boy` tinyint(2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `seller`
--

INSERT INTO `seller` (`id`, `name`, `store_name`, `slug`, `email`, `mobile`, `password`, `balance`, `store_url`, `logo`, `store_description`, `street`, `pincode_id`, `city_id`, `state`, `categories`, `account_number`, `bank_ifsc_code`, `account_name`, `bank_name`, `commission`, `status`, `last_updated`, `date_created`, `require_products_approval`, `fcm_id`, `national_identity_card`, `address_proof`, `pan_number`, `tax_name`, `tax_number`, `customer_privacy`, `latitude`, `longitude`, `forgot_password_code`, `view_order_otp`, `assign_delivery_boy`) VALUES
(1, 'Futura', 'Futura', 'futura', 'fg@ggf.gg', '9876543210', 'd8578edf8458ce06fbc5bb76a58c5ca4', 0, '', '1645562650.4337.png', '<p>ghj</p>', 'dfdgh', 0, 0, 'gfdb', '6', '7', 'gf', 'admin', 'gfdf', 6, 1, NULL, '2022-02-22 20:44:10', 1, NULL, '1645562650.4507.png', '1645562650.451.png', 'tytjy', 'gghj', 'fhj', 0, '0', 'hmg', NULL, 0, 0),
(2, 'Milton', 'Milton', 'milton', 'fdb@dt.yf', '9876543', 'd8578edf8458ce06fbc5bb76a58c5ca4', 0, '', '1645562745.4734.png', '', 'dfdgh', 0, 0, 'gfdb', '5', '5', 'gf', 'admin', 'gfdf', 1, 1, '2022-02-22 20:48:59', '2022-02-22 20:45:45', 0, NULL, '1645562745.4738.png', '1645562745.4741.png', 'tytjy', 'gghj', 'fhj', 1, '0', 'hmg', NULL, 0, 0),
(3, 'Pigeon', 'Pigeon', 'pigeon', 'fdb@dt.yf', '4567', 'd8578edf8458ce06fbc5bb76a58c5ca4', 0, '', '1645562809.2871.png', '', 'dfdgh', 0, 0, 'gfdb', '3', '6', 'gf', 'admin', 'gfdf', 2, 1, NULL, '2022-02-22 20:46:49', 1, NULL, '1645562809.2877.png', '1645562809.288.png', 'tytjy', 'gghj', 'fhj', 0, '0', 'hmg', NULL, 0, 0),
(4, 'Prestige', 'Prestige', 'prestige', 'fdb@dt.yf', '56', 'd8578edf8458ce06fbc5bb76a58c5ca4', 0, '', '1645562915.3292.png', '', 'dfdgh', 0, 0, 'gfdb', '10,8', '67', 'gf', 'admin', 'gfdf', 6, 1, '2022-03-19 13:40:29', '2022-02-22 20:48:35', 1, NULL, '1645562915.3297.png', '1645562915.33.png', 'tytjy', 'gghj', 'fhj', 1, '0', 'hmg', NULL, 0, 0),
(5, 'Borosil', 'Borosil', 'borosil', 'borosil@gmail.com', '9706123879', '25d55ad283aa400af464c76d713c07ad', 0, '', '1648037312.2926.jpeg', 'Borisil', '', 0, 0, '', '11,8,7,6,5,3,2', '', '', '', '', 5, 1, NULL, '2022-03-23 12:08:32', 0, NULL, '1648037312.2939.jpeg', '1648037312.2941.jpeg', '1245678', 'Borosil', '12356', 0, '0', '0', NULL, 0, 0),
(6, 'Jdhdj', 'Baltra', 'baltra', 'shaifzee6264@gmail.com', '9577494269', '202cb962ac59075b964b07152d234b70', 0, '94629539936', '1649222469.9693.jpeg', '<p>Bcx</p>', '', 0, 0, '', '8', '', '', 'admin', '', 56, 1, '2022-04-06 05:37:51', '2022-04-06 05:21:09', 0, NULL, '1649222469.9806.jpeg', '1649222469.981.jpeg', 'Ggf', 'Nhf', 'Kfd', 0, '0', '0', NULL, 0, 0),
(7, 'Lara', 'Lara', 'lara', '13134@gmail.com', '6913831187', '827ccb0eea8a706c4c34a16891f84e7b', 0, '', '1649223811.1401.jpg', 'Wfguj', 'Afg', 0, 0, 'Assam', '11,8,7', '88', '24', 'Sam', '134', 5, 1, NULL, '2022-04-06 05:43:31', 0, NULL, '1649223811.1406.jpg', '1649223811.1409.jpg', '135', 'Sam', '1356', 0, '15', '24', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `seller_transactions`
--

CREATE TABLE `seller_transactions` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `type` text CHARACTER SET utf8 DEFAULT NULL,
  `txn_id` text CHARACTER SET utf8 DEFAULT NULL,
  `amount` double(10,2) NOT NULL DEFAULT 0.00,
  `status` text CHARACTER SET utf8 DEFAULT NULL,
  `message` text CHARACTER SET utf8 DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `seller_wallet_transactions`
--

CREATE TABLE `seller_wallet_transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `type` varchar(256) DEFAULT NULL,
  `amount` double(8,2) NOT NULL DEFAULT 0.00,
  `message` text CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `variable` text NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `variable`, `value`) VALUES
(6, 'logo', 'logo.jpg'),
(9, 'privacy_policy', '<p><strong>Privacy Policy</strong></p>\r\n\r\n<p> \r\n<p>built the eCart app as a Free app. This SERVICE is provided by at no cost and is intended for use as is.</p>\r\n</p>\r\n\r\n<p>This page is used to inform visitors regarding my policies with the collection, use, and disclosure of Personal Information if anyone decided to use my Service.</p>\r\n\r\n<p>If you choose to use my Service, then you agree to the collection and use of information in relation to this policy. The Personal Information that I collect is used for providing and improving the Service. I will not use or share your information with anyone except as described in this Privacy Policy.</p>\r\n\r\n<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at eCart unless otherwise defined in this Privacy Policy.</p>\r\n\r\n<p><strong>Information Collection and Use</strong></p>\r\n\r\n<p>For a better experience, while using our Service, I may require you to provide us with certain personally identifiable information. The information that I request will be retained on your device and is not collected by me in any way.</p>\r\n\r\n<p>The app does use third party services that may collect information used to identify you.</p>\r\n\r\n<p>Link to privacy policy of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://www.google.com/policies/privacy/\" target=\"_blank\">Google Play Services</a></li>\r\n	<li><a href=\"https://firebase.google.com/policies/analytics\" target=\"_blank\">Google Analytics for Firebase</a></li>\r\n	<li><a href=\"https://firebase.google.com/support/privacy/\" target=\"_blank\">Firebase Crashlytics</a></li>\r\n</ul>\r\n\r\n<p><strong>Log Data</strong></p>\r\n\r\n<p>I want to inform you that whenever you use my Service, in a case of an error in the app I collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (“IP”) address, device name, operating system version, the configuration of the app when utilizing my Service, the time and date of your use of the Service, and other statistics.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your device&#39;s internal memory.</p>\r\n\r\n<p>This Service does not use these “cookies” explicitly. However, the app may use third party code and libraries that use “cookies” to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>\r\n\r\n<p><strong>Service Providers</strong></p>\r\n\r\n<p>I may employ third-party companies and individuals due to the following reasons:</p>\r\n\r\n<ul>\r\n	<li>To facilitate our Service;</li>\r\n	<li>To provide the Service on our behalf;</li>\r\n	<li>To perform Service-related services; or</li>\r\n	<li>To assist us in analyzing how our Service is used.</li>\r\n</ul>\r\n\r\n<p>I want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>\r\n\r\n<p><strong>Security</strong></p>\r\n\r\n<p>I value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and I cannot guarantee its absolute security.</p>\r\n\r\n<p><strong>Links to Other Sites</strong></p>\r\n\r\n<p>This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by me. Therefore, I strongly advise you to review the Privacy Policy of these websites. I have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>\r\n\r\n<p><strong>Children’s Privacy</strong></p>\r\n\r\n<p>These Services do not address anyone under the age of 13. I do not knowingly collect personally identifiable information from children under 13. In the case I discover that a child under 13 has provided me with personal information, I immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact me so that I will be able to do necessary actions.</p>\r\n\r\n<p><strong>Changes to This Privacy Policy</strong></p>\r\n\r\n<p>I may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p>This policy is effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Privacy Policy, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This privacy policy page was created at <a href=\"https://privacypolicytemplate.net\" target=\"_blank\">privacypolicytemplate.net </a>and modified/generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(10, 'terms_conditions', '<p><strong>Terms &amp; Conditions</strong></p>\r\n\r\n<p> \r\n<p>By downloading or using the app, these terms will automatically apply to you – you should make sure therefore that you read them carefully before using the app. You’re not allowed to copy, or modify the app, any part of the app, or our trademarks in any way. You’re not allowed to attempt to extract the source code of the app, and you also shouldn’t try to translate the app into other languages, or make derivative versions. The app itself, and all the trade marks, copyright, database rights and other intellectual property rights related to it, still belong to .</p>\r\n</p>\r\n\r\n<p>is committed to ensuring that the app is as useful and efficient as possible. For that reason, we reserve the right to make changes to the app or to charge for its services, at any time and for any reason. We will never charge you for the app or its services without making it very clear to you exactly what you’re paying for.</p>\r\n\r\n<p>The eCart app stores and processes personal data that you have provided to us, in order to provide my Service. It’s your responsibility to keep your phone and access to the app secure. We therefore recommend that you do not jailbreak or root your phone, which is the process of removing software restrictions and limitations imposed by the official operating system of your device. It could make your phone vulnerable to malware/viruses/malicious programs, compromise your phone’s security features and it could mean that the eCart app won’t work properly or at all.</p>\r\n\r\n<p>The app does use third party services that declare their own Terms and Conditions.</p>\r\n\r\n<p>Link to Terms and Conditions of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://policies.google.com/terms\" target=\"_blank\">Google Play Services</a></li>\r\n	<li><a href=\"https://firebase.google.com/terms/analytics\" target=\"_blank\">Google Analytics for Firebase</a></li>\r\n	<li><a href=\"https://firebase.google.com/terms/crashlytics\" target=\"_blank\">Firebase Crashlytics</a></li>\r\n</ul>\r\n\r\n<p>You should be aware that there are certain things that will not take responsibility for. Certain functions of the app will require the app to have an active internet connection. The connection can be Wi-Fi, or provided by your mobile network provider, but cannot take responsibility for the app not working at full functionality if you don’t have access to Wi-Fi, and you don’t have any of your data allowance left.</p>\r\n\r\n<p> </p>\r\n\r\n<p>If you’re using the app outside of an area with Wi-Fi, you should remember that your terms of the agreement with your mobile network provider will still apply. As a result, you may be charged by your mobile provider for the cost of data for the duration of the connection while accessing the app, or other third party charges. In using the app, you’re accepting responsibility for any such charges, including roaming data charges if you use the app outside of your home territory (i.e. region or country) without turning off data roaming. If you are not the bill payer for the device on which you’re using the app, please be aware that we assume that you have received permission from the bill payer for using the app.</p>\r\n\r\n<p>Along the same lines, cannot always take responsibility for the way you use the app i.e. You need to make sure that your device stays charged – if it runs out of battery and you can’t turn it on to avail the Service, cannot accept responsibility.</p>\r\n\r\n<p>With respect to ’s responsibility for your use of the app, when you’re using the app, it’s important to bear in mind that although we endeavour to ensure that it is updated and correct at all times, we do rely on third parties to provide information to us so that we can make it available to you. accepts no liability for any loss, direct or indirect, you experience as a result of relying wholly on this functionality of the app.</p>\r\n\r\n<p>At some point, we may wish to update the app. The app is currently available on Android – the requirements for system(and for any additional systems we decide to extend the availability of the app to) may change, and you’ll need to download the updates if you want to keep using the app. does not promise that it will always update the app so that it is relevant to you and/or works with the Android version that you have installed on your device. However, you promise to always accept updates to the application when offered to you, We may also wish to stop providing the app, and may terminate use of it at any time without giving notice of termination to you. Unless we tell you otherwise, upon any termination, (a) the rights and licenses granted to you in these terms will end; (b) you must stop using the app, and (if needed) delete it from your device.</p>\r\n\r\n<p><strong>Changes to This Terms and Conditions</strong></p>\r\n\r\n<p>I may update our Terms and Conditions from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Terms and Conditions on this page.</p>\r\n\r\n<p>These terms and conditions are effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Terms and Conditions, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This Terms and Conditions page was generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(11, 'fcm_server_key', 'AAAAUnWSmKU:APA91bG3zJNHAsuSsSkVxaCkEe9NPTPV2byx6XEms3q9DUTnYKHIG9OjzckGs53iR-Naqo_sSRofqYE5mflpCIQjDCPYnaBQjbq5LuvN8AI83QVk9II5z0HPk7s2A-CHXbpapess7sdf'),
(12, 'contact_us', '<h2>OAD E-commerce </h2>\r\n\r\n<p>For any kind of queries related to products, orders or services feel free to contact us on our official email address or phone number as given below :</p>\r\n\r\n<p><strong>Areas we deliver : </strong></p>\r\n\r\n<p><strong>Delivery Timings :</strong></p>\r\n\r\n<ol>\r\n	<li><strong>  8:00 AM To 10:30 AM</strong></li>\r\n	<li><strong> 10:30 AM To 12:30 PM</strong></li>\r\n	<li><strong>  4:00 PM To  7:00 PM</strong></li>\r\n</ol>\r\n\r\n<p><strong>Note : </strong>You can order for maximum 2days in advance. i.e., Today &amp; Tomorrow only.</p>\r\n\r\n<h3> </h3>'),
(13, 'system_timezone', '{\"system_configurations\":\"1\",\"system_timezone_gmt\":\"+05:30\",\"system_configurations_id\":\"13\",\"app_name\":\"Nizara\",\"support_number\":\"+91 9876543210\",\"support_email\":\"support@nizara.in\",\"current_version\":\"1.0.0\",\"minimum_version_required\":\"1.0.0\",\"is-version-system-on\":\"0\",\"store_address\":\"qwerty\",\"map_latitude\":\"23.23305215147397\",\"map_longitude\":\"69.64400665873588\",\"currency\":\"u20b9\",\"system_timezone\":\"Asia/Kolkata\",\"max_cart_items_count\":\"10\",\"min_order_amount\":\"10\",\"area-wise-delivery-charge\":\"0\",\"min_amount\":\"0\",\"delivery_charge\":\"100\",\"is-refer-earn-on\":\"0\",\"min-refer-earn-order-amount\":\"100\",\"refer-earn-bonus\":\"2\",\"refer-earn-method\":\"percentage\",\"max-refer-earn-amount\":\"5000\",\"minimum-withdrawal-amount\":\"100\",\"max-product-return-days\":\"10\",\"delivery-boy-bonus-percentage\":\"10\",\"low-stock-limit\":\"10\",\"user-wallet-refill-limit\":\"10000\",\"from_mail\":\"support@nizara.in\",\"reply_to\":\"support@nizara.in\",\"generate-otp\":\"0\",\"smtp-from-mail\":\"support@nizara.in\",\"smtp-reply-to\":\"support@nizara.in\",\"smtp-email-password\":\"\",\"smtp-host\":\"support@nizara.in\",\"smtp-port\":\"465\",\"smtp-content-type\":\"html\",\"smtp-encryption-type\":\"ssl\"}'),
(15, 'about_us', '<h2>About Us</h2>\r\n\r\n<p>eCart is one of the most selling and trending  Grocery, Food Delivery, Fruits &amp; Vegetable store, Full Android eCommerce &amp; Website. which is helps to create your own app and web with your brand name.</p>\r\n\r\n<p>eCart has creative and dedicated group of developers who are mastered in Apps Developments and Web Development with a nice in delivering quality solutions to customers across the globe.</p>\r\n\r\n<p>Everything there including code, doc, amazing support, and most important developed by WRTeam.</p>'),
(80, 'currency', '₹'),
(81, 'delivery_boy_privacy_policy', '<p><strong>Privacy Policy</strong></p>\r\n\r\n<p> \r\n<p>built the Delivery Boy - eCart app as a Free app. This SERVICE is provided by at no cost and is intended for use as is.</p>\r\n</p>\r\n\r\n<p>This page is used to inform visitors regarding my policies with the collection, use, and disclosure of Personal Information if anyone decided to use my Service.</p>\r\n\r\n<p>If you choose to use my Service, then you agree to the collection and use of information in relation to this policy. The Personal Information that I collect is used for providing and improving the Service. I will not use or share your information with anyone except as described in this Privacy Policy.</p>\r\n\r\n<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at Delivery Boy - eCart unless otherwise defined in this Privacy Policy.</p>\r\n\r\n<p><strong>Information Collection and Use</strong></p>\r\n\r\n<p>For a better experience, while using our Service, I may require you to provide us with certain personally identifiable information. The information that I request will be retained on your device and is not collected by me in any way.</p>\r\n\r\n<p>The app does use third party services that may collect information used to identify you.</p>\r\n\r\n<p>Link to privacy policy of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://www.google.com/policies/privacy/\" target=\"_blank\">Google Play Services</a></li>\r\n	<li><a href=\"https://firebase.google.com/policies/analytics\" target=\"_blank\">Google Analytics for Firebase</a></li>\r\n	<li><a href=\"https://firebase.google.com/support/privacy/\" target=\"_blank\">Firebase Crashlytics</a></li>\r\n</ul>\r\n\r\n<p><strong>Log Data</strong></p>\r\n\r\n<p>I want to inform you that whenever you use my Service, in a case of an error in the app I collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (“IP”) address, device name, operating system version, the configuration of the app when utilizing my Service, the time and date of your use of the Service, and other statistics.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your device&#39;s internal memory.</p>\r\n\r\n<p>This Service does not use these “cookies” explicitly. However, the app may use third party code and libraries that use “cookies” to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>\r\n\r\n<p><strong>Service Providers</strong></p>\r\n\r\n<p>I may employ third-party companies and individuals due to the following reasons:</p>\r\n\r\n<ul>\r\n	<li>To facilitate our Service;</li>\r\n	<li>To provide the Service on our behalf;</li>\r\n	<li>To perform Service-related services; or</li>\r\n	<li>To assist us in analyzing how our Service is used.</li>\r\n</ul>\r\n\r\n<p>I want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>\r\n\r\n<p><strong>Security</strong></p>\r\n\r\n<p>I value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and I cannot guarantee its absolute security.</p>\r\n\r\n<p><strong>Links to Other Sites</strong></p>\r\n\r\n<p>This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by me. Therefore, I strongly advise you to review the Privacy Policy of these websites. I have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>\r\n\r\n<p><strong>Children’s Privacy</strong></p>\r\n\r\n<p>These Services do not address anyone under the age of 13. I do not knowingly collect personally identifiable information from children under 13. In the case I discover that a child under 13 has provided me with personal information, I immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact me so that I will be able to do necessary actions.</p>\r\n\r\n<p><strong>Changes to This Privacy Policy</strong></p>\r\n\r\n<p>I may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p>This policy is effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Privacy Policy, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This privacy policy page was created at <a href=\"https://privacypolicytemplate.net\" target=\"_blank\">privacypolicytemplate.net </a>and modified/generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(82, 'delivery_boy_terms_conditions', '<p><strong>Terms &amp; Conditions</strong></p>\r\n\r\n<p> \r\n<p>By downloading or using the app, these terms will automatically apply to you – you should make sure therefore that you read them carefully before using the app. You’re not allowed to copy, or modify the app, any part of the app, or our trademarks in any way. You’re not allowed to attempt to extract the source code of the app, and you also shouldn’t try to translate the app into other languages, or make derivative versions. The app itself, and all the trade marks, copyright, database rights and other intellectual property rights related to it, still belong to .</p>\r\n</p>\r\n\r\n<p>is committed to ensuring that the app is as useful and efficient as possible. For that reason, we reserve the right to make changes to the app or to charge for its services, at any time and for any reason. We will never charge you for the app or its services without making it very clear to you exactly what you’re paying for.</p>\r\n\r\n<p>The Delivery Boy - eCart app stores and processes personal data that you have provided to us, in order to provide my Service. It’s your responsibility to keep your phone and access to the app secure. We therefore recommend that you do not jailbreak or root your phone, which is the process of removing software restrictions and limitations imposed by the official operating system of your device. It could make your phone vulnerable to malware/viruses/malicious programs, compromise your phone’s security features and it could mean that the Delivery Boy - eCart app won’t work properly or at all.</p>\r\n\r\n<p>The app does use third party services that declare their own Terms and Conditions.</p>\r\n\r\n<p>Link to Terms and Conditions of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://policies.google.com/terms\" target=\"_blank\">Google Play Services</a></li>\r\n	<li><a href=\"https://firebase.google.com/terms/analytics\" target=\"_blank\">Google Analytics for Firebase</a></li>\r\n	<li><a href=\"https://firebase.google.com/terms/crashlytics\" target=\"_blank\">Firebase Crashlytics</a></li>\r\n</ul>\r\n\r\n<p>You should be aware that there are certain things that will not take responsibility for. Certain functions of the app will require the app to have an active internet connection. The connection can be Wi-Fi, or provided by your mobile network provider, but cannot take responsibility for the app not working at full functionality if you don’t have access to Wi-Fi, and you don’t have any of your data allowance left.</p>\r\n\r\n<p> </p>\r\n\r\n<p>If you’re using the app outside of an area with Wi-Fi, you should remember that your terms of the agreement with your mobile network provider will still apply. As a result, you may be charged by your mobile provider for the cost of data for the duration of the connection while accessing the app, or other third party charges. In using the app, you’re accepting responsibility for any such charges, including roaming data charges if you use the app outside of your home territory (i.e. region or country) without turning off data roaming. If you are not the bill payer for the device on which you’re using the app, please be aware that we assume that you have received permission from the bill payer for using the app.</p>\r\n\r\n<p>Along the same lines, cannot always take responsibility for the way you use the app i.e. You need to make sure that your device stays charged – if it runs out of battery and you can’t turn it on to avail the Service, cannot accept responsibility.</p>\r\n\r\n<p>With respect to ’s responsibility for your use of the app, when you’re using the app, it’s important to bear in mind that although we endeavour to ensure that it is updated and correct at all times, we do rely on third parties to provide information to us so that we can make it available to you. accepts no liability for any loss, direct or indirect, you experience as a result of relying wholly on this functionality of the app.</p>\r\n\r\n<p>At some point, we may wish to update the app. The app is currently available on Android – the requirements for system(and for any additional systems we decide to extend the availability of the app to) may change, and you’ll need to download the updates if you want to keep using the app. does not promise that it will always update the app so that it is relevant to you and/or works with the Android version that you have installed on your device. However, you promise to always accept updates to the application when offered to you, We may also wish to stop providing the app, and may terminate use of it at any time without giving notice of termination to you. Unless we tell you otherwise, upon any termination, (a) the rights and licenses granted to you in these terms will end; (b) you must stop using the app, and (if needed) delete it from your device.</p>\r\n\r\n<p><strong>Changes to This Terms and Conditions</strong></p>\r\n\r\n<p>I may update our Terms and Conditions from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Terms and Conditions on this page.</p>\r\n\r\n<p>These terms and conditions are effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Terms and Conditions, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This Terms and Conditions page was generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(92, 'payment_methods', '{\"payment_method_settings\":\"1\",\"cod_payment_method\":\"1\",\"paypal_payment_method\":\"0\",\"paypal_mode\":\"sandbox\",\"paypal_currency_code\":\"USD\",\"paypal_business_email\":\"seller@somedomain.com\",\"payumoney_payment_method\":\"1\",\"payumoney_mode\":\"sandbox\",\"payumoney_merchant_key\":\"FGCWtd8L\",\"payumoney_merchant_id\":\"6934786\",\"payumoney_salt\":\"40QIgAPghj\",\"razorpay_payment_method\":\"1\",\"razorpay_key\":\"rzp_test_PeH2Z44Chsfg5h\",\"razorpay_secret_key\":\"JlFiUHYoRKZc5LwR6GGc5t6y\",\"paystack_payment_method\":\"0\",\"paystack_public_key\":\"pk_test_fd8f8d9c548cbd143c78a4bdf6cee5c11f8e6c12\",\"paystack_secret_key\":\"sk_test_dcc02e93456783bb933b6d4c0dec928f1f7e0118\",\"flutterwave_payment_method\":\"0\",\"flutterwave_public_key\":\"FLWPUBK_TEST-1ffbaed6ee3788cd2bcbb234d3b90c59-X\",\"flutterwave_secret_key\":\"FLWSECK_TEST-c659ffd76304hhh67fc4b67ae735b126-X\",\"flutterwave_encryption_key\":\"FLWSECK_TEST25c36edcfvbb\",\"flutterwave_currency_code\":\"KES\",\"midtrans_payment_method\":\"0\",\"is_production\":\"0\",\"midtrans_merchant_id\":\"G213016789\",\"midtrans_client_key\":\"SB-Mid-client-gv4vPZ5utTTClO7u\",\"midtrans_server_key\":\"SB-Mid-server-PHtT70awwC_GsfIR_8TzIVyh\",\"stripe_payment_method\":\"0\",\"stripe_publishable_key\":\"pk_test_51Hh90WLYfObhNTTwooBHwynrlfiPo2uwxyCVqGNNCWGmpdOHuaW4rYS9cDldKJ1hxV5ik52UXUDSYgEM66OX45534565US7tRX\",\"stripe_secret_key\":\"sk_test_51Hh90WLYfObhNTTwO8kCsbdnMdmLxiGHEpiQPGBkYlafghjQ3RnXPIKGn3YsGIEMoIQ5bNfxye4kzE6wfLiINzNk00xOYprnZt\",\"stripe_webhook_secret_key\":\"whsec_mPs10vgbh0QDZPiH3drKBe7fOpMSRppX\",\"stripe_currency_code\":\"INR\",\"paytm_payment_method\":\"1\",\"paytm_mode\":\"sandbox\",\"paytm_merchant_key\":\"eIcrB!DTfgth5DN8\",\"paytm_merchant_id\":\"PpGeMd36789525540215\"}'),
(83, 'time_slot_config', '{\"time_slot_config\":\"1\",\"is_time_slots_enabled\":\"0\",\"delivery_starts_from\":\"1\",\"allowed_days\":\"1\"}'),
(95, 'manager_app_privacy_policy', '<p><strong>Privacy Policy</strong></p>\r\n\r\n<p> \r\n<p>built the eCart Manager App app as a Free app. This SERVICE is provided by at no cost and is intended for use as is.</p>\r\n</p>\r\n\r\n<p>This page is used to inform visitors regarding my policies with the collection, use, and disclosure of Personal Information if anyone decided to use my Service.</p>\r\n\r\n<p>If you choose to use my Service, then you agree to the collection and use of information in relation to this policy. The Personal Information that I collect is used for providing and improving the Service. I will not use or share your information with anyone except as described in this Privacy Policy.</p>\r\n\r\n<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at eCart Manager App unless otherwise defined in this Privacy Policy.</p>\r\n\r\n<p><strong>Information Collection and Use</strong></p>\r\n\r\n<p>For a better experience, while using our Service, I may require you to provide us with certain personally identifiable information. The information that I request will be retained on your device and is not collected by me in any way.</p>\r\n\r\n<p>The app does use third party services that may collect information used to identify you.</p>\r\n\r\n<p>Link to privacy policy of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://www.google.com/policies/privacy/\" target=\"_blank\">Google Play Services</a></li>\r\n</ul>\r\n\r\n<p><strong>Log Data</strong></p>\r\n\r\n<p>I want to inform you that whenever you use my Service, in a case of an error in the app I collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (“IP”) address, device name, operating system version, the configuration of the app when utilizing my Service, the time and date of your use of the Service, and other statistics.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your device&#39;s internal memory.</p>\r\n\r\n<p>This Service does not use these “cookies” explicitly. However, the app may use third party code and libraries that use “cookies” to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>\r\n\r\n<p><strong>Service Providers</strong></p>\r\n\r\n<p>I may employ third-party companies and individuals due to the following reasons:</p>\r\n\r\n<ul>\r\n	<li>To facilitate our Service;</li>\r\n	<li>To provide the Service on our behalf;</li>\r\n	<li>To perform Service-related services; or</li>\r\n	<li>To assist us in analyzing how our Service is used.</li>\r\n</ul>\r\n\r\n<p>I want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>\r\n\r\n<p><strong>Security</strong></p>\r\n\r\n<p>I value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and I cannot guarantee its absolute security.</p>\r\n\r\n<p><strong>Links to Other Sites</strong></p>\r\n\r\n<p>This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by me. Therefore, I strongly advise you to review the Privacy Policy of these websites. I have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>\r\n\r\n<p><strong>Children’s Privacy</strong></p>\r\n\r\n<p>These Services do not address anyone under the age of 13. I do not knowingly collect personally identifiable information from children under 13. In the case I discover that a child under 13 has provided me with personal information, I immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact me so that I will be able to do necessary actions.</p>\r\n\r\n<p><strong>Changes to This Privacy Policy</strong></p>\r\n\r\n<p>I may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p>This policy is effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Privacy Policy, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This privacy policy page was created at <a href=\"https://privacypolicytemplate.net\" target=\"_blank\">privacypolicytemplate.net </a>and modified/generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(96, 'manager_app_terms_conditions', '<p><strong>Terms &amp; Conditions</strong></p>\r\n\r\n<p> \r\n<p>By downloading or using the app, these terms will automatically apply to you – you should make sure therefore that you read them carefully before using the app. You’re not allowed to copy, or modify the app, any part of the app, or our trademarks in any way. You’re not allowed to attempt to extract the source code of the app, and you also shouldn’t try to translate the app into other languages, or make derivative versions. The app itself, and all the trade marks, copyright, database rights and other intellectual property rights related to it, still belong to .</p>\r\n</p>\r\n\r\n<p>is committed to ensuring that the app is as useful and efficient as possible. For that reason, we reserve the right to make changes to the app or to charge for its services, at any time and for any reason. We will never charge you for the app or its services without making it very clear to you exactly what you’re paying for.</p>\r\n\r\n<p>The eCart Manager App app stores and processes personal data that you have provided to us, in order to provide my Service. It’s your responsibility to keep your phone and access to the app secure. We therefore recommend that you do not jailbreak or root your phone, which is the process of removing software restrictions and limitations imposed by the official operating system of your device. It could make your phone vulnerable to malware/viruses/malicious programs, compromise your phone’s security features and it could mean that the eCart Manager App app won’t work properly or at all.</p>\r\n\r\n<p>The app does use third party services that declare their own Terms and Conditions.</p>\r\n\r\n<p>Link to Terms and Conditions of third party service providers used by the app</p>\r\n\r\n<ul>\r\n	<li><a href=\"https://policies.google.com/terms\" target=\"_blank\">Google Play Services</a></li>\r\n</ul>\r\n\r\n<p>You should be aware that there are certain things that will not take responsibility for. Certain functions of the app will require the app to have an active internet connection. The connection can be Wi-Fi, or provided by your mobile network provider, but cannot take responsibility for the app not working at full functionality if you don’t have access to Wi-Fi, and you don’t have any of your data allowance left.</p>\r\n\r\n<p> </p>\r\n\r\n<p>If you’re using the app outside of an area with Wi-Fi, you should remember that your terms of the agreement with your mobile network provider will still apply. As a result, you may be charged by your mobile provider for the cost of data for the duration of the connection while accessing the app, or other third party charges. In using the app, you’re accepting responsibility for any such charges, including roaming data charges if you use the app outside of your home territory (i.e. region or country) without turning off data roaming. If you are not the bill payer for the device on which you’re using the app, please be aware that we assume that you have received permission from the bill payer for using the app.</p>\r\n\r\n<p>Along the same lines, cannot always take responsibility for the way you use the app i.e. You need to make sure that your device stays charged – if it runs out of battery and you can’t turn it on to avail the Service, cannot accept responsibility.</p>\r\n\r\n<p>With respect to ’s responsibility for your use of the app, when you’re using the app, it’s important to bear in mind that although we endeavour to ensure that it is updated and correct at all times, we do rely on third parties to provide information to us so that we can make it available to you. accepts no liability for any loss, direct or indirect, you experience as a result of relying wholly on this functionality of the app.</p>\r\n\r\n<p>At some point, we may wish to update the app. The app is currently available on Android – the requirements for system(and for any additional systems we decide to extend the availability of the app to) may change, and you’ll need to download the updates if you want to keep using the app. does not promise that it will always update the app so that it is relevant to you and/or works with the Android version that you have installed on your device. However, you promise to always accept updates to the application when offered to you, We may also wish to stop providing the app, and may terminate use of it at any time without giving notice of termination to you. Unless we tell you otherwise, upon any termination, (a) the rights and licenses granted to you in these terms will end; (b) you must stop using the app, and (if needed) delete it from your device.</p>\r\n\r\n<p><strong>Changes to This Terms and Conditions</strong></p>\r\n\r\n<p>I may update our Terms and Conditions from time to time. Thus, you are advised to review this page periodically for any changes. I will notify you of any changes by posting the new Terms and Conditions on this page.</p>\r\n\r\n<p>These terms and conditions are effective as of 2021-01-04</p>\r\n\r\n<p><strong>Contact Us</strong></p>\r\n\r\n<p>If you have any questions or suggestions about my Terms and Conditions, do not hesitate to contact me at info@wrteam.in.</p>\r\n\r\n<p>This Terms and Conditions page was generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\">App Privacy Policy Generator</a></p>'),
(99, 'categories_settings', '{\"add_category_settings\":\"1\",\"cat_style\":\"style_2\",\"max_visible_categories\":\"6\",\"max_col_in_single_row\":\"6\"}'),
(97, 'front_end_settings', '{\"front_end_settings\":\"1\",\"android_app_url\":\"https://play.google.com\",\"call_back_url\":\"http://ekart.local:8000/\",\"common_meta_keywords\":\"eCart,WebeCart,eCart Front,eCart Web,eCart Front End\",\"common_meta_description\":\"eCart Front End is Web Version of eCart - Grocery, Food Delivery, Fruits & Vegetable store, Web Version.\",\"favicon\":\"1609822161.5542.png\",\"web_logo\":\"1610961661.239.png\",\"screenshots\":\"1608552564.1753.png\",\"google_play\":\"1608552564.1758.png\"}'),
(100, 'seller_privacy_policy', '<p>seller privacy &amp; policy</p>'),
(101, 'seller_terms_conditions', '<p>seller terms &amp; condition</p>');

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `type_id` varchar(16) NOT NULL,
  `image` varchar(256) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `slider`
--

INSERT INTO `slider` (`id`, `type`, `type_id`, `image`, `date_added`) VALUES
(1, 'default', '0', 'upload/slider/1645558287690.PNG', '2022-02-22 19:31:27'),
(4, 'default', '0', 'upload/slider/1647697781806.jpg', '2022-03-19 13:49:41'),
(5, 'default', '0', 'upload/slider/1647697953191.png', '2022-03-19 13:52:33'),
(6, 'default', '0', 'upload/slider/1647698107004.jpg', '2022-03-19 13:55:07');

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

CREATE TABLE `social_media` (
  `id` int(11) NOT NULL,
  `icon` text COLLATE utf8_unicode_ci NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subbrand`
--

CREATE TABLE `subbrand` (
  `id` int(11) NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `brand_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subbrand`
--

INSERT INTO `subbrand` (`id`, `row_order`, `brand_id`, `name`, `slug`, `subtitle`, `image`) VALUES
(2, 0, 2, 'hello', 'hello', 'hello', 'upload/images/1882-2022-04-10.jpg'),
(3, 0, 1, ';fklajsklfj', 'fklajsklfj', 'f;adsjf', 'upload/images/2694-2022-04-10.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`id`, `row_order`, `category_id`, `name`, `slug`, `subtitle`, `image`) VALUES
(4, 52, 8, 'Chimney', 'chimney', 'Chimney', 'upload/images/9267-2022-03-19.jpeg'),
(8, 53, 8, 'Exhaust Fan', 'exhaust-fan-1', 'Exhaust Fan', 'upload/images/6908-2022-03-19.jpeg'),
(5, 51, 8, 'Geyser', 'geyser', 'Geyser', 'upload/images/0006-2022-03-19.jpeg'),
(7, 50, 8, 'Wall Fan', 'wall-fan', 'Wall Fan', 'upload/images/6917-2022-03-19.jpeg'),
(9, 49, 6, 'Wardrobe', 'wardrobe-1', 'Wardrobe', 'upload/images/9986-2022-03-19.jpeg'),
(10, 48, 6, 'Show Rack', 'show-rack', 'Show Rack', 'upload/images/8253-2022-03-19.jpeg'),
(11, 47, 6, 'Cloth Stand', 'cloth-stand', 'Cloth Stand', 'upload/images/4339-2022-03-19.jpeg'),
(12, 46, 6, 'Artifical Flower', 'artifical-flower', 'Artificial Flower', 'upload/images/0259-2022-03-19.jpeg'),
(13, 45, 6, 'Clock', 'clock', 'Clock', 'upload/images/6396-2022-03-19.jpeg'),
(14, 44, 6, 'Lamp', 'lamp', 'Lamp', 'upload/images/9213-2022-03-19.jpeg'),
(15, 43, 6, 'Photo Frame', 'photo-frame', 'Photo Frame', 'upload/images/2546-2022-03-19.png'),
(16, 42, 6, 'Wall Scenery', 'wall-scenery', 'Wall Scenery', 'upload/images/2898-2022-03-19.jpeg'),
(17, 41, 8, 'Table Fan', 'table-fan', 'Table Fan', 'upload/images/6147-2022-03-19.jpg'),
(18, 40, 8, 'Stand Fan', 'stand-fan-1', 'Stand Fan', 'upload/images/1004-2022-03-19.jpeg'),
(19, 39, 8, 'Calling Fan', 'calling-fan', 'Calling Fan', 'upload/images/2081-2022-03-19.jpeg'),
(20, 38, 8, 'Kettle', 'kettle-1', 'Kettle', 'upload/images/2623-2022-03-19.jpeg'),
(21, 37, 8, 'Roti Maker', 'roti-maker-1', 'Roti Maker', 'upload/images/5972-2022-03-19.jpeg'),
(22, 36, 8, 'Bread Toaster', 'bread-toaster', 'Bread Toaster', 'upload/images/3824-2022-03-19.png'),
(23, 35, 8, 'OTG & Oven', 'otg-oven', 'OTG & Oven', 'upload/images/5514-2022-03-19.jpeg'),
(24, 34, 8, 'Coffee Maker', 'coffee-maker', 'Coffee Maker', 'upload/images/3318-2022-03-19.png'),
(25, 33, 8, 'JMG', 'jmg', 'JMG', 'upload/images/3308-2022-03-19.jpeg'),
(26, 32, 8, 'Hand Blender', 'hand-blender', 'Hand Blender', 'upload/images/6870-2022-03-19.jpeg'),
(27, 31, 8, 'Iron', 'iron', 'Iron', 'upload/images/0050-2022-03-19.jpeg'),
(28, 30, 8, 'Room Heater', 'room-heater', 'Room Heater', 'upload/images/0586-2022-03-19.jpeg'),
(29, 29, 8, 'Water Filter', 'water-filter', 'Water Filter', 'upload/images/1118-2022-03-19.jpeg'),
(30, 28, 8, 'Electric Airpot', 'electric-airpot-1', 'Electric Airpot', 'upload/images/6426-2022-03-19.jpg'),
(31, 27, 7, 'Cleaning', 'cleaning', 'Cleaning', 'upload/images/4899-2022-03-19.jpeg'),
(32, 26, 7, 'Basket', 'basket', 'Basket', 'upload/images/8984-2022-03-19.jpeg'),
(33, 25, 8, 'Heating Cup', 'heating-cup', 'Heating Cup', 'upload/images/8049-2022-03-19.jpg'),
(34, 24, 7, 'Jug', 'jug', 'Jug', 'upload/images/1298-2022-03-19.jpeg'),
(35, 23, 7, 'Mug', 'mug', 'Mug', 'upload/images/2139-2022-03-19.jpeg'),
(36, 22, 7, 'Loundry Basket', 'loundry-basket', 'Loundry Basket', 'upload/images/7768-2022-03-19.jpeg'),
(37, 21, 8, 'Multy Cooker', 'multy-cooker', 'Multy Cooker', 'upload/images/3123-2022-03-19.jpg'),
(38, 20, 7, 'Brush Holder', 'brush-holder', 'Brush Holder', 'upload/images/3219-2022-03-19.jpeg'),
(39, 19, 7, 'Water Bottle', 'water-bottle', 'Water Bottle', 'upload/images/8735-2022-03-19.jpeg'),
(41, 18, 8, 'Gas Stove', 'gas-stove', 'Gas Stove', 'upload/images/2639-2022-03-19.jpeg'),
(42, 17, 3, 'Water Flask & Thermo', 'water-flask-thermo', 'Water Flask & Thermo', 'upload/images/7035-2022-03-21.jpeg'),
(43, 16, 3, 'Single Mill Mug', 'single-mill-mug', 'Single Mill Mug', 'upload/images/5705-2022-03-26.jpeg'),
(44, 15, 3, 'Cup & Saucer', 'cup-saucer-1', 'Cup & Saucer', 'upload/images/3024-2022-03-21.jpeg'),
(45, 14, 8, 'Induction', 'induction', 'Induction', 'upload/images/4440-2022-03-19.jpeg'),
(46, 13, 3, 'Cup Set', 'cup-set-1', 'Cup Set', 'upload/images/5992-2022-03-26.jpeg'),
(47, 12, 8, 'Pressure Cooker', 'pressure-cooker', 'Pressure Cooker', 'upload/images/2834-2022-03-19.png'),
(48, 11, 3, 'Tea Set', 'tea-set-1', 'Tea Set', 'upload/images/7610-2022-03-26.jpeg'),
(49, 10, 3, 'Coffee Set', 'coffee-set-1', 'Coffee Set', 'upload/images/2487-2022-03-26.jpeg'),
(50, 9, 3, 'Dinner Set', 'dinner-set', 'Dinner Set', 'upload/images/6236-2022-03-26.jpeg'),
(51, 8, 8, 'Rice Cooker', 'rice-cooker-1', 'Rice Cooker', 'upload/images/3419-2022-03-19.jpeg'),
(52, 7, 8, 'Mixer Grinder', 'mixer-grinder-1', 'Mixer Grinder', 'upload/images/7834-2022-03-19.jpg'),
(53, 6, 12, 'Induction', 'induction-1', 'Induction', 'upload/images/3081-2022-03-20.jpg'),
(54, 5, 8, 'Immersion Rod', 'immersion-rod', 'Immersion Rod', 'upload/images/9718-2022-03-22.jpeg'),
(55, 4, 8, 'Cooler', 'cooler-1', 'Cooler', 'upload/images/0143-2022-03-22.jpeg'),
(56, 3, 8, 'Blower Heater', 'blower-heater', 'Blower Heater', 'upload/images/7633-2022-03-22.jpeg'),
(57, 2, 8, 'Washing Machine', 'washing-machine-1', 'Washing Machine', 'upload/images/3522-2022-03-22.jpeg'),
(58, 0, 8, 'Refrigerator', 'refrigerator-1', 'Refrigerator', 'upload/images/0524-2022-03-22.png'),
(59, 1, 8, 'TV', 'tv', 'TV', 'upload/images/2380-2022-03-22.png'),
(63, 0, 3, 'Cookware Set', 'cookware-set', 'Cookware Set', 'upload/images/4093-2022-03-26.jpeg'),
(62, 0, 3, 'Casserole', 'casserole-1', 'Casserole', 'upload/images/9313-2022-03-26.jpeg'),
(64, 0, 3, 'Frypan', 'frypan-1', 'Frypan', 'upload/images/6073-2022-03-26.jpeg'),
(65, 0, 3, 'Kadhai', 'kadhai', 'Kadhai', 'upload/images/3333-2022-03-26.jpeg'),
(66, 0, 3, 'Saucepan', 'saucepan-1', 'Saucepan', 'upload/images/4226-2022-03-26.jpeg'),
(67, 0, 3, 'Tawa', 'tawa', 'Tawa', 'upload/images/9926-2022-03-26.jpeg'),
(68, 0, 3, 'Cutlery Set', 'cutlery-set', 'Cutlery Set', 'upload/images/6825-2022-03-26.jpeg'),
(69, 0, 3, 'Spoon', 'spoon-1', 'Spoon', 'upload/images/4186-2022-03-26.jpeg'),
(70, 0, 3, 'Kitchen Scissors', 'kitchen-scissors-1', 'Kitchen Scissors', 'upload/images/0424-2022-03-26.jpeg'),
(71, 0, 3, 'Kitchen Knife', 'kitchen-knife', 'Kitchen Knife', 'upload/images/1756-2022-03-26.jpeg'),
(72, 0, 3, 'Chopping Board', 'chopping-board', 'Chopping Board', 'upload/images/5225-2022-03-27.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 DEFAULT NULL,
  `percentage` double(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `last_order_time` time NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(128) NOT NULL,
  `type` varchar(12) NOT NULL,
  `txn_id` varchar(256) NOT NULL,
  `payu_txn_id` varchar(512) DEFAULT NULL,
  `amount` double NOT NULL,
  `status` varchar(8) NOT NULL,
  `message` varchar(128) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `short_code` varchar(8) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`id`, `name`, `short_code`, `parent_id`, `conversion`) VALUES
(1, 'Kilo Gram', 'kg', NULL, NULL),
(2, 'Gram', 'gm', 1, 1000),
(3, 'Liter', 'ltr', NULL, NULL),
(4, 'Milliliter', 'ml', 3, 1000),
(5, 'Pack', 'pack', NULL, NULL),
(6, 'Piece', 'pc', NULL, NULL),
(7, 'Meter', 'm', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `version` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`id`, `version`) VALUES
(1, 'v1.0.0'),
(2, 'v1.0.1'),
(3, 'v1.0.2'),
(4, 'v1.0.2.1'),
(5, 'v1.0.2.2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(250) CHARACTER SET utf8 NOT NULL,
  `profile` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_code` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '91',
  `mobile` varchar(14) CHARACTER SET utf8 NOT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `referral_code` varchar(28) COLLATE utf8_unicode_ci NOT NULL,
  `friends_code` varchar(28) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fcm_id` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(256) CHARACTER SET utf8 NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile`, `country_code`, `mobile`, `balance`, `referral_code`, `friends_code`, `fcm_id`, `password`, `status`, `created_at`) VALUES
(1, 'admin', 'admin@admin.com', 'default_user_profile.png', '91', '9999999999', 0, 'OCDNIPX78H', '', 'e4eKERLzQ56aLm47ARtssk:APA91bEYZVOflsb62pjAddNJPIfOiawMF-w93FRf6zST-TcGxHBK32WPvBD99c_wsk-aRbvWAD7fPk8UfKUQ5MyFqP3-G5QP-yo1Z8BcOnY0aIKdVsKCaOzN1VKLIgzf2kB2bEkyAsds', 'd8578edf8458ce06fbc5bb76a58c5ca4', 1, '2021-08-03 13:04:03'),
(4, 'dev', 'test@gmail.com', 'default_user_profile.png', '91', '9876543210', 10, '', NULL, '', 'qwerty', 1, '2022-04-06 05:16:00'),
(6, 'sabir', 'iamabboydontpanic@gmail.com', 'default_user_profile.png', '', '+916913831187', 0, 'B6RAT0KTF1', '', 'cSKvmmbLTviY14HSaj8k4r:APA91bFk8iIxT8JnkNQDx3IpiaQfsdXbo1Tifb6n6dUIIOOFTaqZM31dmXS-9ZsOhk-W5hPnXHWAfLAEc9-i1Dx6AX95x8c4a3_tQwr65ZQjknniwAnb9sefrCLlc7qrKavNT7Uj6_En', '56fafa8964024efa410773781a5f9e93', 1, '2022-04-06 05:23:03'),
(7, 'Nabeel Babar', 'nabeelbabar2@gmail.com', '1649419326.0666.jpg', '', '+923045227329', 0, 'K43XP8ERAY', '', 'dG2b6h4ATr-AXuGhnHqT-O:APA91bEzmDR4TUVbrMCYzlRaGRxetWgOnDqJD2K-f1FqB6ReRMwK7wZfG1UzWuJg2sUpjPPUl0fRYAYuNaqR_gllFutob-I1iCOV3EflImgkEe7Zv-QaPn8BElOiY2nqiIs3UnV4vFdQ', 'd8578edf8458ce06fbc5bb76a58c5ca4', 1, '2022-04-06 05:24:21'),
(8, 'Dev2', 'dmdevdutt6@gmail.com', 'default_user_profile.png', '', '+919957762925', 0, 'R5DGADR5WS', '', 'cE6sqEC5RoOvkBPH5hjnQ4:APA91bFOTFY4fkijygFyp2SehzlMaFycYutPkGkwBIUzhgwucmjpCdeiseUdbA-0PbSw-qCo2WmE-0JVKzjMop4Z_qD6zqByBTbdi9psTzbIS3U8Psj0iFb_37FEm3yA-E3j-v80KP3M', 'd8578edf8458ce06fbc5bb76a58c5ca4', 1, '2022-04-06 06:17:11'),
(9, 'sabir', 'iamabboydontpanic@gmail.com', '1649439079.5605.jpg', '91', '6913831187', 0, 'GSDBJI6K2S', '', 'cSKvmmbLTviY14HSaj8k4r:APA91bFk8iIxT8JnkNQDx3IpiaQfsdXbo1Tifb6n6dUIIOOFTaqZM31dmXS-9ZsOhk-W5hPnXHWAfLAEc9-i1Dx6AX95x8c4a3_tQwr65ZQjknniwAnb9sefrCLlc7qrKavNT7Uj6_En', '6bee8bef49259cc41f9bdc90f491ca3c', 1, '2022-04-08 17:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(28) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `alternate_mobile` varchar(28) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `landmark` text COLLATE utf8_unicode_ci NOT NULL,
  `area_id` int(11) NOT NULL,
  `pincode_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `state` varchar(56) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(56) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `type`, `name`, `mobile`, `alternate_mobile`, `address`, `landmark`, `area_id`, `pincode_id`, `city_id`, `state`, `country`, `latitude`, `longitude`, `is_default`, `date_created`) VALUES
(1, 9, 'Home', 'sabir', '6913831187', '', 'sarrabhatti', 'nandkunj', 1, 1, 1, 'assam', 'india', '0', '0', 0, '2022-04-08 17:56:17'),
(2, 8, 'Home', 'Dev2', '+919957762925', '33', 'hhd', 'us', 1, 1, 1, 'hj', 'ii', '0', '0', 0, '2022-04-08 18:00:36'),
(3, 7, 'Home', 'Nabeel Babar', '+923045227329', '', 'Islamabad', 'FAISAL MOSQUE', 1, 1, 1, 'Punjab', 'Pakistan', '33.64400166666667', '73.05242166666667', 1, '2022-04-08 18:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `order_id` varchar(32) NOT NULL DEFAULT '0',
  `order_item_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(8) NOT NULL COMMENT 'credit | debit',
  `amount` double NOT NULL,
  `message` varchar(512) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` int(11) NOT NULL,
  `type` varchar(28) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `message` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_boy_notifications`
--
ALTER TABLE `delivery_boy_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pincodes`
--
ALTER TABLE `pincodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_transactions`
--
ALTER TABLE `seller_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_wallet_transactions`
--
ALTER TABLE `seller_wallet_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_media`
--
ALTER TABLE `social_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subbrand`
--
ALTER TABLE `subbrand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_boy_notifications`
--
ALTER TABLE `delivery_boy_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pincodes`
--
ALTER TABLE `pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seller`
--
ALTER TABLE `seller`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seller_transactions`
--
ALTER TABLE `seller_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_wallet_transactions`
--
ALTER TABLE `seller_wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `social_media`
--
ALTER TABLE `social_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subbrand`
--
ALTER TABLE `subbrand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
