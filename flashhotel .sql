-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 12:44 PM
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
-- Database: `flashhotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','subadmin') NOT NULL,
  `is_first_login` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `role`, `is_first_login`, `reset_token`, `token_expiry`) VALUES
(0, 'superadmin', '$2y$10$NgurTiYkfCngUmkT9Jog9.yzbT5owmE1m83y25mtyl603XsG2jo2W', 'superadmin', 1, NULL, NULL),
(1221, 'Yadusa', '$2y$10$zZRQOXi8JACoZxPsxK9FVezSdsDzk099sc6qZTGLgQDDmudCvDHpq', 'subadmin', 1, NULL, NULL),
(1253, 'Yadu', '$2y$10$gvZAkwf0fYSEw5x5MCRqiebMSegNNno.jsKc.TUKL4pOM4XzT/EXq', 'subadmin', 1, NULL, NULL),
(1475, 'Yaru', '$2y$10$WkfKVOFIvZdWCglDbsTgEeCRavZ6wo33LOxyIg0kiCIDw1STf/H/K', 'subadmin', 1, NULL, NULL),
(14523, 'Yarusha', '$2y$10$H42tqMNwO1QFVj9ilEzvWugYW1CWrYYNVnM5QTRk5VXTS0sFmb1tC', 'subadmin', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_username` varchar(100) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `room_price` decimal(10,2) NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `adults` int(11) NOT NULL DEFAULT 1,
  `children` int(11) NOT NULL DEFAULT 0,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_username`, `room_name`, `room_price`, `checkin`, `checkout`, `adults`, `children`, `total_price`, `created_at`) VALUES
(1, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-16', 1, 2, 6650.00, '2026-01-08 06:44:52'),
(2, 'shen', 'Executive Suite', 1000.00, '2026-01-10', '2026-01-15', 1, 1, 5000.00, '2026-01-08 06:52:43'),
(3, 'shen', 'Deluxe King Room', 950.00, '2026-01-08', '2026-01-11', 1, 1, 2850.00, '2026-01-08 06:56:24'),
(4, 'shen', 'Deluxe King Room', 950.00, '2026-01-08', '2026-01-11', 1, 1, 2850.00, '2026-01-08 06:58:43'),
(5, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, '2026-01-08 06:58:59'),
(6, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, '2026-01-08 07:00:49'),
(7, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-11', 1, 4, 3000.00, '2026-01-08 07:01:35'),
(8, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, '2026-01-08 07:03:18'),
(9, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, '2026-01-08 07:04:59'),
(10, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, '2026-01-08 07:06:05'),
(11, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, '2026-01-08 07:12:41'),
(12, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, '2026-01-08 07:26:43'),
(13, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-13', 4, 1, 4000.00, '2026-01-08 07:38:02'),
(14, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-09', 1, 0, 1000.00, '2026-01-08 07:41:55'),
(15, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, '2026-01-08 07:52:17'),
(16, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, '2026-01-08 07:59:33'),
(17, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, '2026-01-08 08:04:32'),
(18, 'shen', 'Deluxe King Room', 950.00, '2026-01-13', '2026-01-16', 1, 0, 2850.00, '2026-01-08 08:07:02'),
(19, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-10', '2026-01-23', 1, 0, 13000.00, '2026-01-08 08:45:34'),
(20, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-14', '2026-01-29', 1, 0, 15000.00, '2026-01-08 08:55:12'),
(21, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-17', '2026-01-23', 2, 0, 6000.00, '2026-01-08 11:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `cust_name` varchar(100) NOT NULL,
  `cust_email` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `username`, `cust_name`, `cust_email`, `contact_number`, `password_hash`, `created_at`) VALUES
(6, 'Yadusha', 'Yadusha Balakrishnan', '', '0142336773', '$2y$10$QOF4o.jm1TAPMbmkRKriR.8wfqJ2GxtD1wPriSpxguUQC3I4vPBfy', '2025-12-27 09:32:58'),
(16, 'Yarusha', 'Yarusha Balakrishnan', 'Yarusha@2.com1', '0142558396', '$2y$10$0l1NLD2ynEYPnQXPCpiOOOe9Xm6kSi60Xlgy.kdDMv/13N9Tncgx2', '2026-01-03 17:49:43'),
(17, 'Jane', 'Jane Abbeys', 'Jane@21', '0142533365', '$2y$10$teVpb0ZkjRFG4saphVJnPeJvhSYTannOIiEyP33.IgRyfXq.eODjC', '2026-01-08 11:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `total_slots` int(11) DEFAULT 5,
  `available_slots` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `total_slots`, `available_slots`) VALUES
(1, 'Executive Suite', 5, 5),
(2, 'Deluxe King Room', 5, 5),
(3, 'Family Room', 5, 5),
(4, 'Executive Deluxe King', 5, 5),
(5, 'Standard Double Room', 5, 5),
(6, 'Budget Twin Room', 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `staff_id` varchar(20) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_dob` date NOT NULL,
  `staff_salary` decimal(10,2) NOT NULL,
  `staff_join_date` date NOT NULL,
  `staff_position` varchar(100) NOT NULL,
  `staff_email` varchar(100) DEFAULT NULL,
  `staff_phone` varchar(20) DEFAULT NULL,
  `staff_status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `staff_name`, `staff_dob`, `staff_salary`, `staff_join_date`, `staff_position`, `staff_email`, `staff_phone`, `staff_status`) VALUES
('1253', 'Yadusa', '2012-01-28', 500.00, '2025-12-02', 'Sales', 'yadusa@12', '07553', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `FK` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_name` (`room_name`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
