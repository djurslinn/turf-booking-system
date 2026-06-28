-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2025 at 06:56 PM
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
-- Database: `turf`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$xleclS72BqfQ.XyiLXsv1ONH8LzE0OVFWVC7.S2sWd4A/JzahrWx.');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `turf_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `customer_id`, `turf_id`, `booking_date`, `status_id`, `is_deleted`) VALUES
('BKG019A5D370C9DA32B', 1, 1, '2025-09-21', 1, 0),
('BKG4A7B36BC74B3815E', 1, 1, '2025-09-21', 1, 0),
('BKG859EE986118DF3FB', 1, 1, '2025-09-21', 1, 0),
('BKGA80ADB5DB04FE6D0', 1, 1, '2025-09-21', 1, 0),
('BKGAAF12AFFEE41D0BB', 1, 1, '2025-09-21', 1, 0),
('BKGC285EA2F52CF46E1', 1, 1, '2025-09-21', 1, 1),
('BKGD6764581884B85F0', 1, 1, '2025-09-20', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `booking_status`
--

CREATE TABLE `booking_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_status`
--

INSERT INTO `booking_status` (`status_id`, `status_name`) VALUES
(1, 'Confirmed'),
(2, 'Pending'),
(3, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `password`, `phone`, `email`, `created_at`, `is_deleted`) VALUES
(1, 'djurslinn james', '$2y$10$qIpBwvLSVvYkC6hfEobqqe6L9uHKkRecWLq4QXwPk4K/iZJJ/CaiG', '9526117716', 'dj@gmail.com', '2025-09-21 08:50:55', 0),
(3, 'James K M', '$2y$10$xZOgsYKRmBPtCiZ51LfkWutlDLHe1E3/.PLtYBpXhAF3nin89CUWe', '5676543565', 'djurslinnjameskm@gmail.com', '2025-09-21 15:19:38', 0),
(4, 'derli', '$2y$10$RjEvSZF5hdg4acZQdIZoHuzIrWYG1QTG3K4K5p1JOQiyxdaJzFfwm', '8978898763', 'jameskm9526@gmail.com', '2025-09-21 15:25:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('admin','owner','customer') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_type` enum('admin','owner','customer') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `sender_id`, `sender_type`, `receiver_id`, `receiver_type`, `message`, `created_at`) VALUES
(2, 1, 'owner', 1, 'customer', 'thank you for booking in our turf', '2025-09-21 16:01:40'),
(4, 1, 'owner', 1, 'customer', 'Hello! Your booking for \'ethiad turf\' on 2025-09-25 has been confirmed.', '2025-09-21 21:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `owner_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`owner_id`, `name`, `password`, `phone`, `email`, `address_id`, `is_deleted`) VALUES
(1, 'Djurslinn James K M', '$2y$10$c75rud3DjdHBrkze8AgFrOTZKV3M1NkYbH24Xo/NBhKxRvz7dCh2O', '7559947412', 'djurslinn@gmail.com', NULL, 0),
(2, 'derlinn', '$2y$10$eZwq3e89IJaG1xhtNw52We716pieS2NCTmf/KH.orYSVNrP0G0a8a', '09526117716', 'djurslinnjameskm@gmail.com', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `slot_id` int(11) NOT NULL,
  `booking_id` varchar(50) DEFAULT NULL,
  `turf_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `slot_time` time NOT NULL,
  `is_booked` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`slot_id`, `booking_id`, `turf_id`, `customer_id`, `slot_date`, `slot_time`, `is_booked`) VALUES
(5, 'BKG859EE986118DF3FB', 1, 1, '2025-09-22', '12:00:00', 1),
(6, 'BKG859EE986118DF3FB', 1, 1, '2025-09-22', '13:00:00', 1),
(7, 'BKGA80ADB5DB04FE6D0', 1, 1, '2025-09-20', '20:00:00', 1),
(8, 'BKGAAF12AFFEE41D0BB', 1, 1, '2025-09-22', '20:00:00', 1),
(9, 'BKGAAF12AFFEE41D0BB', 1, 1, '2025-09-22', '19:00:00', 1),
(10, 'BKGAAF12AFFEE41D0BB', 1, 1, '2025-09-22', '21:00:00', 1),
(11, 'BKG4A7B36BC74B3815E', 1, 1, '2025-09-25', '16:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_address`
--

CREATE TABLE `tbl_address` (
  `address_id` int(11) NOT NULL,
  `landmark` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pin_code` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_address`
--

INSERT INTO `tbl_address` (`address_id`, `landmark`, `street`, `city`, `district`, `state`, `pin_code`) VALUES
(1, 'city', 'ethiad campus', 'm11', 'manchester', 'united kingdom', '456789'),
(2, 'city', 'ethiad campus', 'm11', 'manchester', 'united kingdom', '456789'),
(3, 'spotify campus', 'les corts', 'Barcelona', 'Madrid', 'spain', '08082'),
(4, 'spotify campus', 'les corts', 'Barcelona', 'Madrid', 'spain', '08082'),
(5, ' near Magic Planet', 'Karyavattom', 'Kazhakootam', 'Thiruvanathapuram', 'kerala', '695581');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reviews`
--

CREATE TABLE `tbl_reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `turf_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reviews`
--

INSERT INTO `tbl_reviews` (`review_id`, `booking_id`, `turf_id`, `customer_id`, `rating`, `review_text`, `created_at`) VALUES
(2, 'BKGA80ADB5DB04FE6D0', 1, 1, 5, 'high quality grass\r\n', '2025-09-21 10:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `turf`
--

CREATE TABLE `turf` (
  `turf_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL,
  `grass_type` varchar(50) DEFAULT NULL,
  `description` text NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by_admin_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `price_day` decimal(10,2) DEFAULT NULL,
  `price_night` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turf`
--

INSERT INTO `turf` (`turf_id`, `name`, `category`, `size`, `image_path`, `map_url`, `grass_type`, `description`, `owner_id`, `address_id`, `is_approved`, `approved_by_admin_id`, `is_deleted`, `price_day`, `price_night`) VALUES
(1, 'ethiad turf', 'FOOTBALL', '7', 'uploads/1758445233_mc.avif', 'https://maps.app.goo.gl/trT1s2tKPcM5VmqX9', 'Hybrid Grass', ' Etihad City Football Academy - The LandTek GroupThe turf at the Etihad Stadium is a hybrid natural grass system, featuring Desso GrassMaster technology, which involves stitching natural grass with artificial fibres for enhanced durability and playing hours, providing a stable, homogeneous surface that feels like natural grass and can withstand heavy use. It has UEFA standard dimensions of 105 by 68 meters and is illuminated by powerful floodlights. ', 1, 2, 1, NULL, 0, 1700.00, 2000.00),
(2, 'Camp Nou ', 'MULTI_SPORT', '5', 'uploads/1758472644_Camp-Nou-3.webp', 'https://maps.app.goo.gl/LLJGmzF25tk9r8L99', 'Natural Grass', ' Camp Nou | Visit The Stadium of FC Barcelona in 2025Camp Nou, meaning \"New Field,\" is a football stadium in Barcelona, Spain, serving as the home for FC Barcelona since 1957. Currently undergoing a significant reconstruction, the stadium is being modernized and will feature a new, state-of-the-art design by 2027, increasing its capacity to over 105,000 and making it a premier European and world-class venue. The project aims to create a self-sufficient, technologically advanced stadium that blends its historic legacy with 21st-century innovation.  Key characteristics and features: Home to FC Barcelona: The stadium is the official and symbolic home of the La Liga club FC Barcelona.  Iconic and historic: Opened in 1957, it is considered a \"cathedral of football,\" rich with history and passionate nights.  Undergoing Reconstruction: A major reconstruction project started in 2023, partially demolishing the stadium to make way for a futuristic facility.  Future Capacity: The planned capacity is over 105,000, which will make it the largest stadium by seating capacity in Spain and Europe.  Modern Design: The new stadium will feature a circular, lightweight steel roof with solar panels, providing energy efficiency and carbon neutrality.  Technological Advancements: It will include a new audiovisual experience with a 160-meter wraparound screen.  Preserving Tradition: The design aims to maintain the stadium\'s iconic shape while embracing innovation for modern football.  Purpose and Significance: Symbolic Landmark: Camp Nou is not just a stadium but a symbol of Barcelona and Catalan sporting achievement.  21st Century Venue: The reconstruction is designed to serve the needs and aspirations of 21st-century audiences and provide a legacy for future generations.  Sustainability: The new stadium is designed to be energy self-sufficient and carbon-neutral.  This video shows the transformation of Camp Nou from its early days to the current reconstruction: Related video thumbnailFavicon YouTube • Build It Big 08:47 Inside FC Barcelona\'s €1.6 Billion New Camp Nou Stadium ... Jun 5, 2025 — imagine tearing down a cathedral while people are still praying. inside. that\'s what it feels like when you mess with Camp. New it\'s not just a st...   YouTube·Build It Big 8:47 The Evolution of Camp Nou 🏟️In 1957, FC Barcelona opened the ... Apr 22, 2025 — The Evolution of Camp Nou 🏟️In 1957, FC Barcelona opened the gates to what would become one of the most iconic football stadiums in the world: Camp Nou. A cath... favicon Facebook  Spotify Camp Nou | FC Barcelona Official Channel Camp Nou is the home stadium of FC Barcelona. It has a capacity of 99,354, making it the largest stadium in Europe. The stadium was designed by Francesc Mitjans... favicon FC Barcelona Website  Show all', 1, 3, 1, NULL, 0, 1500.00, 2100.00),
(4, 'Green Field', 'CRICKET', '7', 'uploads/1758473444_crik.jpg', 'https://maps.app.goo.gl/tso4DEDMA1u5o37x9', 'Artificial Grass', 'Greenfield International Stadium Trivandrum and Trivandrum International Stadium, is a multi-purpose stadium in capital city Trivandrum in the state Kerala, India. It is primarily used for international cricket and also has been used football.[6] The stadium has a seating capacity of 50,000. It was built on 36 acres of land leased by the University of Kerala for ₹94 lakh (US$146,527.23) per year for a period of 15 years.[7] The first international football tournament hosted by the stadium was the 2015 SAFF Championship. India were crowned the champions, beating Afghanistan 2–1 in the final. On 1 November 2018, the venue hosted its first cricket ODI.[8] It is the home ground of the Kerala Cricket Association (KCA)', 1, 5, 1, NULL, 0, 1000.00, 1600.00);

-- --------------------------------------------------------

--
-- Table structure for table `turf_images`
--

CREATE TABLE `turf_images` (
  `image_id` int(11) NOT NULL,
  `turf_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turf_images`
--

INSERT INTO `turf_images` (`image_id`, `turf_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/turf_images/1758445292_68cfbeecd5c30.webp', '2025-09-21 09:01:32'),
(2, 2, 'uploads/turf_images/1758472796_68d02a5ce4f24.webp', '2025-09-21 16:39:56');

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
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `turf_id` (`turf_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `booking_status`
--
ALTER TABLE `booking_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`slot_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `turf_id` (`turf_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `turf_id` (`turf_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `turf`
--
ALTER TABLE `turf`
  ADD PRIMARY KEY (`turf_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `approved_by_admin_id` (`approved_by_admin_id`);

--
-- Indexes for table `turf_images`
--
ALTER TABLE `turf_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `turf_id` (`turf_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_status`
--
ALTER TABLE `booking_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `owner`
--
ALTER TABLE `owner`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_address`
--
ALTER TABLE `tbl_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `turf`
--
ALTER TABLE `turf`
  MODIFY `turf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `turf_images`
--
ALTER TABLE `turf_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`turf_id`) REFERENCES `turf` (`turf_id`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `booking_status` (`status_id`);

--
-- Constraints for table `owner`
--
ALTER TABLE `owner`
  ADD CONSTRAINT `owner_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `tbl_address` (`address_id`);

--
-- Constraints for table `slots`
--
ALTER TABLE `slots`
  ADD CONSTRAINT `slots_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `slots_ibfk_2` FOREIGN KEY (`turf_id`) REFERENCES `turf` (`turf_id`),
  ADD CONSTRAINT `slots_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  ADD CONSTRAINT `tbl_reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reviews_ibfk_2` FOREIGN KEY (`turf_id`) REFERENCES `turf` (`turf_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reviews_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `turf`
--
ALTER TABLE `turf`
  ADD CONSTRAINT `turf_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owner` (`owner_id`),
  ADD CONSTRAINT `turf_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `tbl_address` (`address_id`),
  ADD CONSTRAINT `turf_ibfk_3` FOREIGN KEY (`approved_by_admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `turf_images`
--
ALTER TABLE `turf_images`
  ADD CONSTRAINT `turf_images_ibfk_1` FOREIGN KEY (`turf_id`) REFERENCES `turf` (`turf_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
