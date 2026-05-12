-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 07:27 AM
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
-- Database: `getafe_grab_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','in_progress','completed','cancelled') DEFAULT 'pending',
  `pickup_address` text NOT NULL,
  `pickup_lat` decimal(10,8) NOT NULL,
  `pickup_lng` decimal(11,8) NOT NULL,
  `dropoff_address` text NOT NULL,
  `dropoff_lat` decimal(10,8) NOT NULL,
  `dropoff_lng` decimal(11,8) NOT NULL,
  `fare` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `rider_id`, `driver_id`, `status`, `pickup_address`, `pickup_lat`, `pickup_lng`, `dropoff_address`, `dropoff_lat`, `dropoff_lng`, `fare`, `created_at`, `accepted_at`, `completed_at`) VALUES
(1, 1, 2, 'completed', 'Current Location', 10.14006142, 124.19042324, 'Getafe Town Plaza', 10.14280000, 124.15240000, 46.73, '2026-03-21 04:21:53', '2026-03-21 04:28:32', '2026-03-21 04:28:50'),
(2, 1, 2, 'completed', 'Current Location', 10.14014007, 124.19051947, 'Getafe National High School', 10.14180000, 124.15340000, 45.67, '2026-03-21 04:30:07', '2026-03-21 04:30:24', '2026-03-21 04:31:59'),
(3, 1, 2, 'cancelled', 'Current Location', 10.14000495, 124.19034256, 'Getafe Public Market', 10.14580000, 124.15040000, 49.19, '2026-03-21 04:45:58', '2026-03-21 04:46:19', NULL),
(4, 1, 2, 'completed', 'Current Location', 10.14016147, 124.19053856, 'Municipal Hall', 10.14620000, 124.15120000, 48.58, '2026-03-21 05:01:20', '2026-03-21 05:01:50', '2026-03-21 05:02:30'),
(5, 1, 2, 'completed', 'Current Location', 10.14006142, 124.19042324, 'Getafe Port', 10.15040000, 124.15540000, 45.02, '2026-03-21 05:06:54', '2026-03-21 05:07:07', '2026-03-21 05:07:51'),
(6, 1, 2, 'completed', 'Current Location', 10.14006034, 124.19041466, 'Getafe Port', 10.15040000, 124.15540000, 45.01, '2026-03-21 05:12:02', '2026-03-21 05:12:17', '2026-03-21 05:12:51'),
(7, 1, 2, 'completed', 'Current Location', 10.14015499, 124.19053603, 'Getafe Port', 10.15040000, 124.15540000, 45.11, '2026-03-21 05:14:40', '2026-03-21 05:14:48', '2026-03-21 05:15:04'),
(8, 1, 2, 'completed', 'Current Location', 10.14015438, 124.19053550, 'Getafe Public Market', 10.14580000, 124.15040000, 49.38, '2026-03-21 05:18:53', '2026-03-21 05:19:00', '2026-03-21 05:19:09'),
(9, 1, 2, 'completed', 'Current Location', 10.14015499, 124.19053603, 'Getafe Public Market', 10.14580000, 124.15040000, 49.38, '2026-03-21 05:21:47', '2026-03-21 05:21:55', '2026-03-21 05:22:45'),
(10, 1, 2, 'completed', 'Current Location', 10.14016147, 124.19053856, 'Getafe Port', 10.15040000, 124.15540000, 45.11, '2026-03-21 05:29:26', '2026-03-21 05:40:09', '2026-03-21 05:40:15'),
(11, 1, 2, 'completed', 'Current Location', 10.14004783, 124.19040038, 'Getafe Port', 10.15040000, 124.15540000, 45.00, '2026-03-21 05:33:26', '2026-03-21 05:40:00', '2026-03-21 05:40:05'),
(12, 1, 3, 'completed', 'Current Location', 10.14000495, 124.19034256, 'Getafe Port', 10.15040000, 124.15540000, 44.96, '2026-03-21 05:52:30', '2026-03-21 05:52:49', '2026-03-21 05:53:27'),
(13, 1, 3, 'completed', 'Current Location', 10.14009423, 124.19045268, 'St. Paul\'s Academy', 10.14520000, 124.15080000, 48.77, '2026-03-21 06:07:35', '2026-03-21 06:07:41', '2026-03-21 06:07:50'),
(19, 5, 6, 'completed', '10.143579, 124.158211', 10.14357923, 124.15821075, '10.141171, 124.151688', 10.14117128, 124.15168762, 12.63, '2026-05-13 05:12:38', '2026-05-13 05:12:42', '2026-05-13 05:18:20');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `booking_id`, `sender_id`, `message`, `created_at`) VALUES
(1, 3, 1, 'huguuu', '2026-03-21 05:21:32'),
(2, 9, 2, 'where you at sir', '2026-03-21 05:22:06'),
(3, 9, 1, 'dapit menteryo sir', '2026-03-21 05:22:35'),
(4, 12, 3, 'where are u love', '2026-03-21 05:53:03'),
(5, 12, 1, 'wbhabhbhawbh', '2026-03-21 05:53:12'),
(6, 19, 6, 'ohayo', '2026-05-13 05:12:53'),
(7, 19, 5, 'good evening', '2026-05-13 05:13:32'),
(8, 19, 5, 'ipick-up ko sa eskina', '2026-05-13 05:14:08'),
(9, 19, 6, 'copy ma\'am', '2026-05-13 05:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rater_id` int(11) NOT NULL,
  `ratee_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('rider','driver','admin') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `profile_pic`, `created_at`, `is_online`, `lat`, `lng`, `last_seen`) VALUES
(1, 'John Paul Comoso Mamugay', 'comosop62@gmail.com', '09707765695', '$2y$10$cos09Mg2Em3.mmEDkA5N9OFJlDBIFC/FTOjbk5ELc4W3aBWkBenku', 'rider', NULL, '2026-03-21 04:13:54', 0, NULL, NULL, '2026-03-21 05:30:55'),
(2, 'Nathaniel Arevalo', 'nathaniel@gmail.com', '12345678', '$2y$10$cq/qNMWrauQ70Orsz8Qt6efU7BZB7gJxjhTIczTczWuG/q0VWgNsC', 'driver', NULL, '2026-03-21 04:28:14', 1, 10.14000495, 124.19034256, '2026-03-21 05:52:40'),
(3, 'Marie Gold Garcia', 'goldy@gmail.com', '1243q', '$2y$10$YdHtp8cFnajKPnR4v3TyOeSEmoCHLMe.hCML0JrhWitRvPur2tJJa', 'driver', NULL, '2026-03-21 05:28:35', 1, 10.14009304, 124.19045080, '2026-03-21 05:53:32'),
(4, 'Juan', 'sample@gmail.com', '', '1234', 'driver', NULL, '2026-05-13 03:22:54', 0, NULL, NULL, '2026-05-13 03:22:54'),
(5, 'Jess ', 'sample25@gmail.com', '09123456789', '$2y$10$Pj6EM0JnV8uKuSARHWRvze15UG0FQMLfQfL7KqTHE/QVrD2U2.V2K', 'rider', NULL, '2026-05-13 03:51:09', 0, NULL, NULL, '2026-05-13 05:27:15'),
(6, 'Juan', 'juan@gmail.com', '09121212121', '$2y$10$CZ9LJ/ZcscTnv9ObsIz0geM.IVfHf.DuRowkmqmpmDQ3YYdnp2aA6', 'driver', NULL, '2026-05-13 03:58:31', 1, 10.15271600, 124.25316400, '2026-05-13 05:07:41');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(50) NOT NULL,
  `type` enum('car','motorcycle','taxi') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `driver_id`, `model`, `plate_number`, `type`) VALUES
(1, 2, 'Honda', 'LOVE143', 'motorcycle'),
(2, 3, 'Honda', '143Goldy', 'motorcycle'),
(3, 6, 'Toyota', 'ABC 1234', 'motorcycle');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rider_id` (`rider_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `rater_id` (`rater_id`),
  ADD KEY `ratee_id` (`ratee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `driver_id` (`driver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`rider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`rater_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`ratee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
