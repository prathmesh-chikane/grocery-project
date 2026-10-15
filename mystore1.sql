-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 03:54 AM
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
-- Database: `mystore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'prathmesh', '$2y$10$wKRbyLcX0uWdiPR8Aq3b8O2dYVtidaal8v8LARIYjCelSd4LC/GNS');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `product_id` int(100) NOT NULL,
  `ip_address` varchar(200) NOT NULL,
  `quantity` int(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`product_id`, `ip_address`, `quantity`) VALUES
(1, '::1', 10);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_at`) VALUES
(1, 'CHIPS & SNACKS', '2024-12-16 14:31:42'),
(2, 'DAIRY PRODUCTS', '2025-05-01 19:21:42'),
(3, 'RICE and FLOURS', '2025-05-01 19:21:42'),
(4, 'PULSES/DALS', '2025-05-01 19:21:42'),
(5, 'OILS', '2025-05-01 19:21:42'),
(6, 'SPICES AND NUTS', '2025-03-01 19:21:42'),
(7, 'TOILETRIES', '2025-05-01 19:21:42'),
(8, 'POOJA ITEMS', '2025-05-01 19:21:42'),
(9, 'COLD DRINKS', '2025-05-01 19:21:42'),
(10, 'BUSCUITS', '2025-05-01 19:21:42'),
(11, 'OTHER INGREDIENTS', '2025-05-01 19:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contact_id`, `user_id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 2, 'Prathmesh', 'prathmeshchikane@gmail.com', 'Do you provide home delivery for grocery items in Satara city?', '2025-01-02 07:44:43'),
(2, 1, 'vedant dareker', 'vedantdarekar123@gmail.com', '\"Thank you for contacting us! We’ll get back to you shortly.\"', '2025-01-15 01:02:41'),
(3, 4, 'pranit shinde', 'pranit09@gmail.com', 'I received damaged packaging for my last order. Can I request a replacement?', '2025-01-22 05:20:19'),
(4, 6, 'anjali mardhekar', 'anjali321@gmail.com', 'Can you tell me if the Aashirvaad Atta is currently in stock?', '2025-01-23 09:15:41'),
(16, 0, 'prathmesh chikane', 'prathmeshchikane@gmail.com', 'hi hello', '2025-11-03 10:47:12'),
(17, 2, 'prathmesh chikane', 'prathmeshchikane@gmail.com', 'very good', '2025-11-03 12:03:02'),
(18, 20, 'mayuri shedge', 'mayuri@gmail.com', 'hi', '2025-11-03 12:21:35'),
(19, 3, 'kiran chorage', 'kiranchorage@gmail.com', 'hi', '2025-11-06 08:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `username`, `message`, `created_at`) VALUES
(1, 2, 'prathmesh chikane', 'this is very useful website\r\n', '2025-05-03 06:06:24'),
(2, 1, 'vedant darekar', 'the best collection i\'ve ever see..!', '2025-02-05 06:15:13'),
(3, 11, 'vipul gurav', 'thanks you', '2025-02-03 06:23:16'),
(4, 3, 'kiran chorge', 'this is very cool website', '2025-05-03 06:35:01'),
(5, 4, 'pranit shinde ', 'i like this website\r\n', '2025-05-03 06:40:58'),
(6, 9, 'omkar chikane', 'i like this website', '2025-03-14 06:52:23'),
(7, 12, 'Shubhangi shedge', 'hi', '2025-08-13 16:23:58'),
(8, 2, 'prathmesh chikane', 'hi', '2025-08-17 05:03:37'),
(24, 0, 'prathmesh chikane', 'this is very good', '2025-11-03 05:24:12'),
(25, 0, 'mayuri shedge', 'very good website', '2025-11-03 06:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_ip` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_ip`, `first_name`, `last_name`, `address1`, `address2`, `city`, `state`, `postcode`, `phone`, `email`, `product_id`, `quantity`, `price`, `payment_mode`, `order_status`, `order_date`) VALUES
(1, '::1', 'vedant', 'dareker', 'satara', 'ramnager', 'satara', 'maharashatra', '343532', '1234567689', 'vedantdarekar123@gmail.com', NULL, NULL, NULL, 'upi', 'Cancelled', '2025-05-01 21:40:00'),
(2, '::1', 'prathmesh', 'chikane', 'at-post ganje', 'varye', 'satara', 'maharashatra', '343532', '1234567689', 'prathmeshchikane123@gmail.com', NULL, NULL, NULL, 'upi', 'Delivered', '2025-05-01 21:40:00'),
(3, '::1', 'vedant', 'dareker', 'satara', 'ramnager', 'satara', 'maharashatra', '343532', '1234567689', 'vedantdarekar123@gmail.com', NULL, NULL, NULL, 'cod', 'Pending', '2025-05-01 21:40:00'),
(4, '::1', 'anjali', 'mardhekar', 'satara', 'medha', 'satara', 'maharashatra', '343532', '5353535434', 'anjali09@gmail.com', NULL, NULL, NULL, 'upi', 'Delivered', '2025-05-01 21:40:00'),
(5, '::1', 'vipul', 'gurav', 'kusumbi', 'ram coleny', 'satara', 'maharashatra', '343532', '5353535434', 'vipulgurav33@gmail.com', NULL, NULL, NULL, 'upi', 'Pending', '2025-05-01 21:40:00'),
(6, '::1', 'vedant', 'dareker', 'satara', 'ramnager', 'satara', 'maharashatra', '343532', '1234567689', 'vedantdarekar123@gmail.com', NULL, NULL, NULL, 'upi', 'Delivered', '2025-05-01 21:40:00'),
(7, '::1', 'vedant', 'dareker', 'satara', 'ramnager', 'satara', 'maharashatra', '343532', '1234567689', 'vedantdarekar123@gmail.com', NULL, NULL, NULL, 'Online', 'Pending', '2025-05-02 05:57:39'),
(8, '::1', 'pratik', 'bhosale', 'medha', 'kalushi', 'satara', 'maharashatra', '415014', '7777547632', 'pratik08@gmail.com', NULL, NULL, NULL, 'COD', 'Cancelled', '2025-05-02 09:27:24'),
(9, '::1', 'pratik', 'bhosale', 'medha', 'kalushi', 'satara', 'maharashatra', '415014', '7777547632', 'pratik08@gmail.com', NULL, NULL, NULL, 'Online', 'Delivered', '2025-05-02 09:30:47'),
(10, '::1', 'kiran', 'chorge', 'satara', 'varye', 'satara', 'maharashatra', '343532', '6754296754', 'kiranchorge@gmail.com', NULL, NULL, NULL, 'Online', 'Delivered', '2025-05-02 10:56:02'),
(11, '::1', 'omkar', 'chikane', 'at-post ganje', 'ganesh coleny', 'satara', 'maharashatra', '415012', ' 8888549828', 'omkar2000@gmail.com', NULL, NULL, NULL, 'Online', 'Delivered', '2025-05-03 04:41:39'),
(12, '::1', 'vedant', 'mane', 'satara', 'ramnager', 'satara', 'maharashatra', '343545', '5353535434', 'vedant123@gmail.com', NULL, NULL, NULL, 'Online', 'Delivered', '2025-05-04 18:28:17'),
(13, '::1', 'baliram ', 'chiakane', 'satara', 'medha', 'satara', 'maharashatra', '415012', '5643985674', 'baliramchiakne3@gmail.com', NULL, NULL, NULL, 'COD', 'Delivered', '2025-05-24 07:33:17'),
(14, '::1', 'shubhangi', 'shedge', 'nerul', 'navi mumbai', 'mumbai', 'Maharashtra', '415012', '9552462959', 'shubhangishedge13@gmail.com', NULL, NULL, NULL, 'Online', 'Delivered', '2025-08-13 18:27:33'),
(15, '::1', 'Prathmesh ', 'Chikane', 'nerul', '', 'navi mumbai', 'maharashtra', '415679', '9535362445', 'prathmesh1234@gmail.com', NULL, NULL, NULL, 'Online', 'Pending', '2025-09-24 08:31:36'),
(16, '::1', 'Prathmesh', 'Chikane', 'AT POST GANJE, TAL-JAOLI, DIST-SATARA 415012', '', 'SATARA', 'Maharashtra', '415012', '9552462959', 'prathmeshchikane@gmail.com', NULL, NULL, NULL, 'Online', 'Pending', '2025-11-03 07:34:18'),
(17, '::1', 'mayuri ', 'shedge', 'nerul,sec4 navi mumbai', '', 'mumbai', 'Maharashtra', '415012', '9552462967', 'mayuri@gmail.com', NULL, NULL, NULL, 'Online', 'Pending', '2025-11-03 07:54:00'),
(18, '::1', 'prathmesh', 'chikane', 'nerul,sec4 navi mumbai', '', 'navi mumbai', 'Maharashtra', '415012', '9552462959', 'prathmeshchikane@gmail.com', NULL, NULL, NULL, 'COD', 'Delivered', '2025-11-03 08:05:56'),
(19, '::1', 'प्रथमेश', 'चिकणे', 'AT POST GANJE, TAL-JAOLI, DIST-SATARA 415012', '', 'SATARA', 'Maharashtra', '415012', '9552462959', 'prathmeshchikane@gmail.com', NULL, NULL, NULL, 'COD', 'Cancelled', '2025-11-03 08:10:46'),
(20, '::1', 'प्रथमेश', 'चिकणे', 'AT POST GANJE, TAL-JAOLI, DIST-SATARA 415012', '', 'SATARA', 'Maharashtra', '415012', '9552462959', 'prathmeshchikane@gmail.com', NULL, NULL, NULL, 'COD', 'Cancelled', '2025-11-03 08:31:14'),
(21, '::1', 'kiran', 'chorage', 'satara', '', 'SATARA', 'Maharashtra', '415012', '9552462945', 'kiranchorage@gmail.com', NULL, NULL, NULL, 'COD', 'Delivered', '2025-11-06 04:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 40.00),
(2, 1, 11, 5, 10.00),
(3, 2, 1, 2, 40.00),
(4, 2, 4, 2, 20.00),
(5, 2, 6, 2, 20.00),
(6, 3, 7, 1, 20.00),
(7, 3, 15, 2, 30.00),
(8, 4, 2, 1, 30.00),
(9, 4, 9, 2, 10.00),
(10, 4, 15, 1, 30.00),
(11, 5, 1, 1, 40.00),
(12, 5, 7, 1, 20.00),
(13, 6, 6, 3, 20.00),
(14, 6, 9, 1, 10.00),
(15, 6, 11, 1, 10.00),
(16, 6, 14, 3, 40.00),
(17, 7, 1, 1, 40.00),
(18, 7, 3, 1, 80.00),
(19, 7, 4, 1, 20.00),
(20, 8, 9, 2, 10.00),
(21, 8, 23, 1, 30.00),
(22, 9, 10, 2, 10.00),
(23, 9, 18, 2, 30.00),
(24, 10, 18, 1, 30.00),
(25, 10, 20, 1, 40.00),
(26, 11, 9, 1, 10.00),
(27, 11, 23, 2, 30.00),
(28, 12, 3, 1, 80.00),
(29, 12, 7, 5, 20.00),
(30, 12, 10, 6, 10.00),
(31, 12, 16, 1, 50.00),
(32, 12, 17, 1, 40.00),
(33, 13, 20, 1, 40.00),
(34, 13, 23, 2, 30.00),
(35, 13, 24, 2, 10.00),
(36, 14, 3, 1, 80.00),
(37, 14, 9, 2, 10.00),
(38, 15, 14, 1, 40.00),
(39, 15, 26, 1, 20.00),
(40, 16, 26, 1, 20.00),
(41, 17, 3, 1, 80.00),
(42, 17, 4, 1, 20.00),
(43, 17, 8, 1, 10.00),
(44, 17, 17, 1, 40.00),
(45, 18, 1, 3, 40.00),
(46, 19, 1, 2, 40.00),
(47, 20, 2, 1, 30.00),
(48, 21, 1, 5, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `ID` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_names` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`ID`, `order_id`, `payment_id`, `payment_status`, `amount`, `payment_date`, `product_names`) VALUES
(1, 7, 'pay_QPv6KlP0NFNMPm', 'Successful', 131, '2025-01-02 03:57:39', ''),
(2, 9, 'pay_QPyjRCArqgEcUn', 'Successful', 70, '2025-02-18 07:30:47', ''),
(3, 10, 'pay_QQ0BVh1AKPXQv4', 'Successful', 60, '2025-05-02 08:56:02', ''),
(4, 11, 'pay_QQILBTEESdxQMf', 'Successful', 60, '2025-05-03 02:41:39', ''),
(5, 12, 'pay_QQuxSPeOgSYxM1', 'Successful', 323, '2025-05-04 16:28:17', ''),
(6, 14, 'pay_R4stlfuWrkibEC', 'Successful', 90, '2025-08-13 16:27:33', ''),
(7, 15, 'pay_RLLBRUeMwJzX2E', 'Successful', 50, '2025-09-24 06:31:36', ''),
(8, 16, 'pay_RbAb5517UsJZXL', 'Successful', 10, '2025-11-03 06:34:18', ''),
(9, 17, 'pay_RbAvqo0keFwFt9', 'Successful', 141, '2025-11-03 06:54:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `ID` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(25) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `product_keywords` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_image` varchar(200) NOT NULL,
  `Product_price` varchar(200) NOT NULL,
  `Product_unit` varchar(200) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(100) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `product_keywords`, `category_id`, `product_image`, `Product_price`, `Product_unit`, `date`, `status`, `stock_quantity`, `created_at`) VALUES
(1, 'Sugar', 'Sugar is a sweet, crystalline substance derived mainly from sugarcane or sugar beets. It\'s commonly used to sweeten foods and beverages and also plays a role in food preservation and texture.', 'Sugar', 3, 'Storing-Sugar.jpg', '40$', '1', '2025-04-16 14:17:51', 'true', 4, '2025-05-01 19:33:48'),
(2, 'Tea', 'Discover the perfect blend of tradition and taste with our premium teas, offering bold black, calming green, and soothing herbal flavors—carefully sourced to bring warmth and wellness to every cup.', 'Premium tea  Organic tea leaves  Black tea  Green tea  Herbal tea  Loose leaf tea  Natural tea blends  Caffeine-free tea  Relaxing tea  Wellness tea  Gourmet tea', 6, 'parivar-family-blend-tea', '30$', '1', '2025-04-16 14:56:32', 'true', 10, '2025-05-01 19:33:48'),
(3, 'Basmati rice', 'Fragrant and long-grained, our premium Basmati rice is naturally aged and perfect for biryanis, pilafs, and everyday meals', 'Basmati rice', 3, 'Basmati-rice.jpg', '80$', '1kg', '2025-04-16 19:01:18', 'true', 8, '2025-05-01 19:33:48'),
(4, 'Coca-cola ', 'Coca-Cola is a classic, refreshing soft drink with a bold, bubbly taste loved worldwide', 'coca-cola ', 9, 'coca cola drink.jpg', '20$', '1pcs', '2025-04-17 03:44:57', 'true', 0, '2025-05-01 19:33:48'),
(5, 'sting cold drinks', 'Sting is a bold, electrifying energy drink with a sharp, tangy bite that instantly powers you up', 'sting ', 9, 'sting cold drink.png', '20$', '1pcs', '2025-04-17 03:51:46', 'true', 0, '2025-05-01 19:33:48'),
(6, 'Sprite', 'Sprite is a crisp, lemon-lime flavored soda that delivers a refreshing burst of cool, citrusy fizz.', 'sprite', 9, 'sprite-cold-drink.jpg', '20$', '1pcs', '2025-04-17 03:54:07', 'true', 0, '2025-05-01 19:33:48'),
(7, 'Mazza', 'Maaza is a rich, smooth mango drink that captures the sweet, tropical taste of ripe mangoes in every sip.', 'Mazza', 9, 'maaza-cold drink.jpg', '20$', '1pcs', '2025-04-17 03:56:17', 'true', 0, '2025-05-01 19:33:48'),
(8, 'bourbon', 'Bourbon is a classic chocolate cream-filled biscuit with a crunchy bite and a rich, indulgent cocoa flavor', 'bourbon', 10, 'Bourbon.jpeg', '10$', '1pcs', '2025-04-17 04:07:58', 'true', 0, '2025-05-01 19:33:48'),
(9, 'Parle-g', 'Parle-G is a nostalgic, mildly sweet biscuit with a crisp texture and wholesome wheat flavor loved across generations.', ' parle-g', 10, 'Parle-g.jpg', '10$', '1 pcs', '2025-04-17 04:10:55', 'true', 0, '2025-05-01 19:33:48'),
(10, 'Good Day', 'Good Day is a delightfully crunchy biscuit loaded with nuts and a buttery sweetness that brightens every bite.', 'good day', 10, 'Good Day.jpeg', '10$', '1pcs', '2025-04-17 04:20:19', 'true', 0, '2025-05-01 19:33:48'),
(11, 'Kurkure', 'Kurkure is a crunchy, spicy snack with a burst of masaledar flavor that delivers a punch in every bite.', 'kurkure', 1, 'kurkure.jpg', '10$', '1pcs', '2025-04-17 04:25:55', 'true', 0, '2025-05-01 19:33:48'),
(14, ' Kissan-mixed-fruit-jam', 'A sweet, thick spread made from fruit and sugar, typically used on bread or pastries.', 'jam', 11, 'kissan-mixed-fruit-jam.jpg', '40', '1 kg', '2025-04-18 11:52:27', 'true', 0, '2025-05-01 19:33:48'),
(15, 'Clove', 'Clove is a warm, aromatic spice made from dried flower buds, known for its intense flavor and medicinal properties.', 'clove', 6, 'clove-(loung).webp', '30', '1 kg', '2025-04-18 11:56:23', 'true', 0, '2025-05-01 19:33:48'),
(16, 'sunny sunflower oil', 'sunny sun flower oil is the best for the health', 'sunny sunflower', 5, 'oil.jpg', '50$', '1kg', '2025-05-01 19:45:15', 'true', 0, '2025-05-01 19:45:15'),
(17, 'chana dal', 'chana dal is good for health', 'chana dal', 4, 'Chana-Dal.jpg', '40$', '1kg', '2025-05-01 19:49:49', 'true', 0, '2025-05-01 19:49:49'),
(18, 'Udid dal', ' Udit Dal is a person spotted at the grocery store.', 'udid dal', 4, 'udid dal 2.jpeg', '30$', '1kg', '2025-05-01 19:53:35', 'true', 0, '2025-05-01 19:53:35'),
(19, 'masoor dal', 'Masur Dal is a red lentil commonly used in Indian cuisine, known for its quick cooking time and rich protein content', 'massor dal', 4, 'masoor-dal.jpg', '30$', '1kg', '2025-05-01 19:55:11', 'true', 0, '2025-05-01 19:55:11'),
(20, 'milk', 'ChatGPT said: Milk is a nutritious white liquid produced by mammals, rich in calcium, protein, and essential vitamins.', 'milk', 2, 'Milk.jpg', '40$', '1 liters', '2025-05-01 19:59:26', 'true', 0, '2025-05-01 19:59:26'),
(21, 'vivel soap', 'Vivel soap is a skin-nourishing bath soap enriched with aloe vera, vitamin E, and milk cream for soft, healthy skin.', 'vivel soap', 7, 'Bathing soap.jpg', '20$', '1 pcs', '2025-05-01 20:04:05', 'true', 0, '2025-05-01 20:04:05'),
(22, 'Tooth brush', 'A toothbrush is a dental hygiene tool with bristles used to clean teeth, gums, and tongue for oral health.', 'Tooth brush', 7, 'Toothbrush.jpg', '20$', '1 pcs', '2025-05-01 20:06:21', 'true', 0, '2025-05-01 20:06:21'),
(23, 'Toothpaste', 'Toothpaste is a gel or paste used with a toothbrush to clean teeth, prevent cavities, and freshen breath.', 'toothpaste', 7, 'Toothpaste.jpg', '30$', '250gm', '2025-05-01 20:08:41', 'true', 0, '2025-05-01 20:08:41'),
(24, 'lays-chips', 'Lays chips are crispy, flavored potato snacks made from thinly sliced potatoes and seasoned with a variety of tastes.', 'lays', 1, 'Lays-Chips.jpg', '10$', '1 pcs', '2025-05-01 20:15:02', 'true', 0, '2025-05-01 20:15:02'),
(25, 'Amul Butter', 'Amul Butter is a delicious, creamy, and smooth table butter made from pure milk fat, perfect for spreading, cooking, and baking.', 'Butter', 2, 'Butter100grams - Copy.jpg', '40$', '1 kg', '2025-05-04 13:03:10', 'true', 0, '2025-05-04 13:03:10'),
(26, 'semolina', 'semoli is the best for the health', 'semolina', 3, 'semolina(Suji).jpg', '20 $', 'kg', '2025-06-11 16:59:44', 'true', 0, '2025-06-11 16:59:44');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  `mobile_number` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `image`, `user_ip`, `mobile_number`, `address`, `created_at`) VALUES
(1, 'vedant darekar', 'vedant123@gmail.com', '123456', 'customer1.png', '::1', '7649350944', 'vai,somrdi', '2025-01-02 12:47:06'),
(2, 'prathmesh chikane', 'prathmeshchikane@gmail.com', 'prath1234', 'customer4.png', '::1', '5432457689', 'satara', '2025-01-04 13:47:06'),
(3, 'kiran chorage', 'kiranchorge@gmail.com', '123456', 'customer3.png', '::1', '6754296754', 'pune', '2025-05-02 13:47:06'),
(4, 'pranit shinde', 'pranit09@gmail.com', 'pranit09', 'customer4.png', '::1', '9846294683', 'satara,molacha wada', '2025-05-02 13:47:06'),
(5, 'pranav hole', 'india645@gmail.com', 'india645', 'customer4.png', '::1', '4365338650', 'pune', '2025-05-02 13:47:06'),
(6, 'Anjali mardhekar', 'anjali312@gmail.com', 'anjali03', 'customer1.png', '::1', '8308856844', 'medha, ritkawali', '2025-05-02 13:47:06'),
(7, 'pratik bhosale', 'pratik08@gmail.com', 'pratik2000', 'customer4.png', '::1', '7777547632', 'medha, kalushi', '2025-05-02 13:47:06'),
(8, 'omkar chikane', 'omkar2000@gmail.com', 'omkar2000', 'customer3.png', '::1', '8888549828', 'Ganje', '2025-05-03 07:31:07'),
(9, 'omkar mane', 'omkarmane56@gmail.com', 'omkar2004', 'customer3.png', '::1', '7954781903', 'Satara, ahilya nagar', '2025-03-22 13:47:06'),
(10, 'sonali chikane', 'sonali98@gmail.com', 'sonali98', 'customer1.png', '::1', '7647352277', 'at post Ganje', '2025-02-13 11:18:47'),
(11, 'vipul gurav', 'vipul2000@gmail.com', '123456', 'customer3.png', '::1', '7648530965', 'pundharpur', '2025-01-25 12:17:27'),
(12, 'Shubhangi shedge', 'shubhangishedge13@gmail.com', '12345', 'customer1.png', '::1', '543458698', 'nerul', '2025-08-13 20:51:19'),
(20, 'mayuri shedge', 'mayuri@gmail.com', 'mayuri1234', 'customer1.png', '::1', '5465463487', 'nerul', '2025-11-03 12:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`id`, `user_id`, `username`, `login_time`) VALUES
(1, 2, 'prathmesh chikane', '2025-08-13 20:47:09'),
(2, 19, 'Shubhangi shedge', '2025-08-13 20:51:37'),
(3, 2, 'prathmesh chikane', '2025-08-13 21:02:45'),
(4, 2, 'prathmesh chikane', '2025-08-13 21:48:03'),
(5, 2, 'prathmesh chikane', '2025-08-17 10:13:19'),
(6, 2, 'prathmesh chikane', '2025-09-24 11:59:24'),
(7, 2, 'prathmesh chikane', '2025-11-02 10:11:59'),
(8, 2, 'prathmesh chikane', '2025-11-02 10:25:15'),
(9, 2, 'prathmesh chikane', '2025-11-02 10:37:01'),
(10, 2, 'prathmesh chikane', '2025-11-02 10:42:24'),
(11, 4, 'pranit shinde', '2025-11-02 11:09:25'),
(12, 4, 'pranit shinde', '2025-11-02 11:15:42'),
(13, 4, 'pranit shinde', '2025-11-02 11:24:02'),
(14, 2, 'prathmesh chikane', '2025-11-02 11:27:01'),
(15, 2, 'prathmesh chikane', '2025-11-02 11:28:03'),
(16, 2, 'prathmesh chikane', '2025-11-02 11:35:59'),
(17, 4, 'pranit shinde', '2025-11-03 10:48:08'),
(18, 2, 'prathmesh chikane', '2025-11-03 11:23:56'),
(19, 2, 'prathmesh chikane', '2025-11-03 11:46:56'),
(20, 2, 'prathmesh chikane', '2025-11-03 11:59:48'),
(21, 2, 'prathmesh chikane', '2025-11-03 12:01:47'),
(22, 20, 'mayuri shedge', '2025-11-03 12:21:02'),
(23, 2, 'prathmesh chikane', '2025-11-03 12:34:14'),
(24, 2, 'prathmesh chikane', '2025-11-03 12:40:08'),
(25, 2, 'prathmesh chikane', '2025-11-04 10:23:29'),
(26, 2, 'prathmesh chikane', '2025-11-04 10:24:38'),
(27, 2, 'prathmesh chikane', '2025-11-04 10:24:59'),
(28, 2, 'prathmesh chikane', '2025-11-04 11:08:31'),
(29, 4, 'pranit shinde', '2025-11-04 11:31:57'),
(30, 4, 'pranit shinde', '2025-11-04 11:55:36'),
(31, 2, 'prathmesh chikane', '2025-11-04 11:59:55'),
(32, 2, 'prathmesh chikane', '2025-11-04 12:09:35'),
(33, 3, 'kiran chorage', '2025-11-06 08:58:02'),
(34, 4, 'pranit shinde', '2025-11-08 19:57:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
