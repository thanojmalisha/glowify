-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 07:42 PM
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
-- Database: `glowify`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','manager','editor') DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Chathuka', 'chathukapriyankaraedirisinghe@gmail.com', '$2y$10$6DE1KYVnhWeKEPY2qonJDubOnxEhWhHn00hz2W8RULXGGx3Upy1TS', 'super_admin', '2025-09-06 22:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `description`) VALUES
(1, 'Hair Care', 'Products for healthy, shiny, and nourished hair.'),
(2, 'Skin Care', 'Products for glowing and healthy skin.'),
(3, 'Nail Care', 'Products for strong and well-groomed nails.'),
(4, 'Hair Care', 'Products for healthy, shiny, and nourished hair.'),
(5, 'Skin Care', 'Products for glowing and healthy skin.'),
(6, 'Nail Care', 'Products for strong and well-groomed nails.');

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `full_name`, `email`, `phone`, `address`, `password`, `created_at`) VALUES
(1, 'Dasun', 'Dasun@gmail.com', '0760587419', 'no 43/21 homagama', '$2y$10$xpEwjIkr9YC70uDbdJKXC.1YBkHDEtsudIrPPVdJHwqV6wVmHaINW', '2025-09-06 22:06:09'),
(2, 'nimal', 'nimal@gmail.com', '0770644489', 'no10/24 north colombo', '$2y$10$7aLM5X/rnOuYMPmPc8/BDO9tZ68yOPImHmGDssiGTGkWdFx7V5aZK', '2025-09-07 06:42:29'),
(3, 'Kavishka', 'user@gmail.com', '123456789', 'backer street,England', '$2y$10$G/vgxfIPLX0hcpP21cdYguFVac8Mgi/t1HjjcGULk45dQe5TWIIfa', '2025-09-07 06:47:50'),
(4, 'sasindu', 'user123@gmail.com', '123456789', 'backer street,England', '$2y$10$aamWQzBMffsmQX2AbfI/L.hZuTWqOqXWLXQ4NTGZOpIaM5/hMzRL2', '2025-09-07 07:11:41'),
(5, 'kamal', 'kamal@gmail.com', '0781548963', 'New town,kurunegala', '$2y$10$XXitHasW65ZKFtU9ZhXTIOApGZM24A9MPOUKmY.AOLWP5BmS/C7DW', '2025-09-07 15:40:10');

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `discount_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `description`, `price`, `stock_quantity`, `category_id`, `image_url`, `created_at`) VALUES
(16, 'Glowify Special Hair Cream', 'Infused with aloe vera and coconut oil. Keeps hair smooth, shiny, and frizz-free.', 1800.00, 12, 1, 'uploads/SpecialHairCream.jpg', '2025-09-06 21:47:29'),
(17, 'Padwer Bryunn Hair Serum', 'Enriched with natural oils and vitamins. Nourishes dry and frizzy hair, reduces split ends, adds shine.', 3200.00, 12, 1, 'uploads/padwer.jpg', '2025-09-06 21:47:29'),
(18, 'Dandruff Control Shampoo', 'Made from lime and fenugreek extracts. Helps eliminate dandruff and refresh the scalp.', 2100.00, 10, 1, 'uploads/DandurfShampoo.jpg', '2025-09-06 21:47:29'),
(19, 'Glowify Ayurvedic Hair Masks', 'Packed with Bhringraj and Shikakai. Deeply conditions hair and restores natural shine.', 2700.00, 9, 1, 'uploads/AyurvedicHairMask.jpg', '2025-09-06 21:47:29'),
(20, 'Long & Thick Hair Oil', 'A blend of castor oil and hibiscus. Promotes strong hair growth and prevents breakage.', 2300.00, 15, 1, 'uploads/Long&thickOil.jpg', '2025-09-06 21:47:29'),
(21, 'Intensive Care Ayurvedic Shampoo', 'Enriched with neem and amla. Nourishes roots and prevents damage.', 2500.00, 8, 1, 'uploads/IntensiveCareAyurvedic.jpg', '2025-09-06 21:47:29'),
(22, 'Hair Fall Rescue Shampoo', 'Contains onion extract and biotin. Reduces hair fall and strengthens weak strands.', 2200.00, 14, 1, 'uploads/HairFallRescue.jpg', '2025-09-06 21:47:29'),
(23, 'Emerald Men’s Face Mask', 'Packed with natural minerals. Detoxifies skin, removes impurities, and restores freshness.', 3000.00, 10, 2, 'uploads/MensFaceMask.jpg', '2025-09-06 21:47:29'),
(24, 'Glowify Men’s Face Serum', 'Moisturizes and protects skin from dryness. Provides a natural healthy look.', 2800.00, 14, 2, 'uploads/NailgrowthOil.jpg', '2025-09-06 21:47:29'),
(25, 'Glowify Men’s Face Cream', 'Moisturizes and protects skin from dryness. Provides a natural healthy look.', 2800.00, 14, 2, 'uploads/FaceMenCream.jpg', '2025-09-06 21:47:29'),
(26, 'Glowify Beauty Soap', 'Enriched with aloe vera and rose extracts. Gently cleanses while keeping skin soft and glowing.', 650.00, 16, 2, 'uploads/soap.jpg', '2025-09-06 21:47:29'),
(27, 'Glowify Men’s Face Wash', 'Cleans deep and refreshes skin, designed for men’s daily skincare.', 950.00, 12, 2, 'uploads/MensFaceWash.jpg', '2025-09-06 21:47:29'),
(28, 'Glowify Special Tablet for Strong Nails', 'Rich in biotin and keratin. Strengthens brittle nails from within.', 1800.00, 7, 3, 'uploads/NailGrowthTablet.jpg', '2025-09-06 21:47:29'),
(29, 'Nail Buffer', 'Smoothens nail surface and adds natural shine without polish.', 400.00, 11, 3, 'uploads/NailBuffer.jpg', '2025-09-06 21:47:29'),
(30, 'Nail Clippers', 'Sharp stainless steel blades for precise and clean nail trimming.', 500.00, 8, 3, 'uploads/NailCutter.jpg', '2025-09-06 21:47:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `discount`
--
ALTER TABLE `discount`
  ADD CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
