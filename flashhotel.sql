-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2026 at 10:32 AM
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
  `token_expiry` datetime DEFAULT NULL,
  `status` enum('active','inactive','suspended','terminated') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `role`, `is_first_login`, `reset_token`, `token_expiry`, `status`) VALUES
(0, 'superadmin', '$2y$10$NgurTiYkfCngUmkT9Jog9.yzbT5owmE1m83y25mtyl603XsG2jo2W', 'superadmin', 1, NULL, NULL, 'active'),
(129, 'Vannesia', '$2y$10$h4sfog5c/lLDWp5HAoBZaOsUIkNbQoIIpqXhAOTdeFPhJDVCt6sPG', 'subadmin', 1, NULL, NULL, 'active'),
(1221, 'Yadusa', '$2y$10$zZRQOXi8JACoZxPsxK9FVezSdsDzk099sc6qZTGLgQDDmudCvDHpq', 'subadmin', 1, NULL, NULL, 'active'),
(1253, 'Yadu', '$2y$10$gvZAkwf0fYSEw5x5MCRqiebMSegNNno.jsKc.TUKL4pOM4XzT/EXq', 'subadmin', 1, NULL, NULL, 'active'),
(1589, 'Shey', '$2y$10$CgFVcyIFWoLWrpqJaQ.27u2jXCIP3IAQFZ9fx1WtFL5UnCS9xNzue', 'subadmin', 0, NULL, NULL, 'active'),
(5889, 'Yarusha', '$2y$10$ADS.4sCVMV6IdERPKiw3ru825H0SFExPr5XQyHQSv8YF4hNBUvhFO', 'subadmin', 0, NULL, NULL, 'active');

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
  `payment_status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_username`, `room_name`, `room_price`, `checkin`, `checkout`, `adults`, `children`, `total_price`, `payment_status`, `created_at`) VALUES
(1, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-16', 1, 2, 6650.00, 'Pending', '2026-01-08 06:44:52'),
(2, 'shen', 'Executive Suite', 1000.00, '2026-01-10', '2026-01-15', 1, 1, 5000.00, 'Pending', '2026-01-08 06:52:43'),
(3, 'shen', 'Deluxe King Room', 950.00, '2026-01-08', '2026-01-11', 1, 1, 2850.00, 'Pending', '2026-01-08 06:56:24'),
(4, 'shen', 'Deluxe King Room', 950.00, '2026-01-08', '2026-01-11', 1, 1, 2850.00, 'Pending', '2026-01-08 06:58:43'),
(5, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, 'Pending', '2026-01-08 06:58:59'),
(6, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, 'Pending', '2026-01-08 07:00:49'),
(7, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-11', 1, 4, 3000.00, 'Pending', '2026-01-08 07:01:35'),
(8, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, 'Pending', '2026-01-08 07:03:18'),
(9, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, 'Pending', '2026-01-08 07:04:59'),
(10, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-10', 1, 0, 2000.00, 'Pending', '2026-01-08 07:06:05'),
(11, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, 'Pending', '2026-01-08 07:12:41'),
(12, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-10', 1, 0, 1000.00, 'Pending', '2026-01-08 07:26:43'),
(13, 'shen', 'Executive Suite', 1000.00, '2026-01-09', '2026-01-13', 4, 1, 4000.00, 'Pending', '2026-01-08 07:38:02'),
(14, 'shen', 'Executive Suite', 1000.00, '2026-01-08', '2026-01-09', 1, 0, 1000.00, 'Pending', '2026-01-08 07:41:55'),
(15, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, 'Pending', '2026-01-08 07:52:17'),
(16, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, 'Pending', '2026-01-08 07:59:33'),
(17, 'shen', 'Deluxe King Room', 950.00, '2026-01-09', '2026-01-10', 1, 0, 950.00, 'Pending', '2026-01-08 08:04:32'),
(18, 'shen', 'Deluxe King Room', 950.00, '2026-01-13', '2026-01-16', 1, 0, 2850.00, 'Pending', '2026-01-08 08:07:02'),
(19, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-10', '2026-01-23', 1, 0, 13000.00, 'Pending', '2026-01-08 08:45:34'),
(20, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-14', '2026-01-29', 1, 0, 15000.00, 'Pending', '2026-01-08 08:55:12'),
(21, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-17', '2026-01-23', 2, 0, 6000.00, 'Pending', '2026-01-08 11:07:55'),
(22, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-22', '2026-02-05', 1, 0, 14000.00, 'Pending', '2026-01-09 09:17:06'),
(23, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-21', '2026-01-29', 2, 1, 8000.00, 'Pending', '2026-01-09 09:31:09'),
(24, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-21', '2026-01-29', 1, 0, 8000.00, 'Pending', '2026-01-09 09:31:58'),
(25, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-24', '2026-02-04', 1, 0, 11000.00, 'Pending', '2026-01-09 09:32:52'),
(26, 'Yarusha', 'Budget Twin Room', 120.00, '2026-01-27', '2026-02-06', 1, 0, 1200.00, 'Pending', '2026-01-09 09:35:14'),
(27, 'Yarusha', 'Budget Twin Room', 120.00, '2026-01-23', '2026-02-14', 3, 2, 2640.00, 'Pending', '2026-01-09 09:35:38'),
(28, 'Yarusha', 'Deluxe King Room', 950.00, '2026-01-15', '2026-01-31', 3, 2, 15200.00, 'Pending', '2026-01-09 09:36:40'),
(29, 'Yarusha', 'Family Room', 500.00, '2026-01-21', '2026-02-05', 1, 0, 7500.00, 'Pending', '2026-01-09 09:37:09'),
(30, 'Yarusha', 'Executive Deluxe King', 420.00, '2026-01-13', '2026-02-05', 1, 0, 9660.00, 'Pending', '2026-01-09 09:37:30'),
(31, 'Yarusha', 'Budget Twin Room', 120.00, '2026-01-20', '2026-02-07', 1, 0, 2160.00, 'Pending', '2026-01-09 09:38:26'),
(32, 'Yarusha', 'Budget Twin Room', 120.00, '2026-01-23', '2026-01-30', 1, 0, 840.00, 'Pending', '2026-01-09 09:40:31'),
(53, 'Yadusa3', 'Executive Suite', 1000.00, '2026-01-29', '2026-01-30', 1, 0, 1000.00, 'Pending', '2026-01-28 02:27:21'),
(61, 'Yarusha', 'Standard Double Room', 150.00, '2026-01-30', '2026-01-31', 1, 0, 150.00, 'Confirmed', '2026-01-30 13:37:27'),
(62, 'Yarusha', 'Deluxe King Room', 950.00, '2026-01-30', '2026-01-31', 1, 0, 950.00, 'Confirmed', '2026-01-30 13:37:42'),
(63, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-30', '2026-01-31', 1, 0, 1000.00, 'Confirmed', '2026-01-30 13:46:33'),
(64, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-30', '2026-02-07', 1, 0, 8000.00, 'Pending', '2026-01-30 13:58:56'),
(65, 'Yarusha', 'Family Room', 500.00, '2026-02-09', '2026-02-10', 1, 0, 500.00, 'Confirmed', '2026-01-30 14:15:38'),
(66, 'Yarusha', 'Executive Suite', 1000.00, '2026-01-30', '2026-01-31', 1, 0, 1000.00, 'Confirmed', '2026-01-30 14:36:59'),
(69, 'Yarusha', 'Budget Twin Room', 120.00, '2026-02-02', '2026-02-04', 1, 0, 240.00, 'Confirmed', '2026-01-31 11:26:57'),
(76, 'Yarusha', 'Budget Twin Room', 120.00, '2026-02-06', '2026-02-07', 1, 0, 120.00, 'Confirmed', '2026-01-31 11:56:41'),
(88, 'Yarusha', 'Budget Twin Room', 120.00, '2026-02-04', '2026-02-05', 1, 0, 120.00, 'Confirmed', '2026-01-31 14:06:35'),
(90, 'Yarusha', 'Budget Twin Room', 120.00, '2026-01-31', '2026-02-01', 1, 0, 120.00, 'Confirmed', '2026-01-31 14:14:27'),
(91, 'Yarusha', 'Budget Twin Room', 120.00, '2026-02-01', '2026-02-02', 1, 0, 120.00, 'Confirmed', '2026-01-31 14:15:22'),
(93, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-07', '2026-02-08', 1, 0, 150.00, 'Confirmed', '2026-01-31 14:40:59'),
(94, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-06', '2026-02-08', 1, 0, 300.00, 'Confirmed', '2026-01-31 14:41:19'),
(95, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-06', '2026-02-07', 1, 0, 150.00, 'Confirmed', '2026-01-31 14:41:33'),
(96, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-06', '2026-02-06', 1, 0, 150.00, 'Confirmed', '2026-01-31 14:41:39'),
(97, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-06', '2026-02-07', 1, 0, 150.00, 'Confirmed', '2026-01-31 14:42:19'),
(99, 'Yarusha', 'Budget Twin Room', 120.00, '2026-02-05', '2026-02-06', 1, 0, 120.00, 'Confirmed', '2026-01-31 14:45:17'),
(101, 'Yarusha', 'Standard Double Room', 150.00, '2026-02-06', '2026-02-07', 1, 0, 150.00, 'Confirmed', '2026-01-31 14:52:57'),
(103, 'SYSTEM_BLOCK', 'Budget Twin Room', 0.00, '2026-02-03', '2026-02-05', 1, 0, 0.00, 'Blocked', '2026-02-01 09:25:10');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `username`, `cust_name`, `cust_email`, `contact_number`, `password_hash`, `created_at`, `address`, `status`) VALUES
(6, 'Yadusha', 'Yadusha Balakrishnan', '', '0142336773', '$2y$10$QOF4o.jm1TAPMbmkRKriR.8wfqJ2GxtD1wPriSpxguUQC3I4vPBfy', '2025-12-27 09:32:58', NULL, 'active'),
(16, 'Yarusha', 'Yarusha Balakrishnan', 'Yarusha@2.com1', '0142558396', '$2y$10$0l1NLD2ynEYPnQXPCpiOOOe9Xm6kSi60Xlgy.kdDMv/13N9Tncgx2', '2026-01-03 17:49:43', 'wsrhzetdj', 'active'),
(17, 'Jane', 'Jane Abbeys', 'Jane@21', '0142533365', '$2y$10$teVpb0ZkjRFG4saphVJnPeJvhSYTannOIiEyP33.IgRyfXq.eODjC', '2026-01-08 11:42:44', NULL, 'active'),
(18, 'Jason', 'Jin', 'JAson@21.com', '0142536', '$2y$10$Gl2rZbyx4iND0NoiDoHqbuxu683pKL2/VOh6E5rNeh6QAV0W.diEq', '2026-01-15 05:58:21', NULL, 'active');

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
  `available_slots` int(11) DEFAULT 5,
  `room_status` varchar(20) NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `total_slots`, `available_slots`, `room_status`) VALUES
(1, 'Executive Suite', 5, 5, 'Available'),
(2, 'Deluxe King Room', 5, 5, 'Available'),
(3, 'Family Room', 5, 5, 'Available'),
(4, 'Executive Deluxe King', 5, 5, 'Available'),
(5, 'Standard Double Room', 5, 5, 'Available'),
(6, 'Budget Twin Room', 5, 5, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `room_blocked_dates`
--

CREATE TABLE `room_blocked_dates` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `blocked_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_blocked_dates`
--

INSERT INTO `room_blocked_dates` (`id`, `room_name`, `blocked_date`) VALUES
(1, 'Budget Twin Room', '0000-00-00');

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
  `staff_role` varchar(50) NOT NULL DEFAULT 'Staff',
  `staff_email` varchar(100) DEFAULT NULL,
  `staff_phone` varchar(20) DEFAULT NULL,
  `staff_status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `staff_name`, `staff_dob`, `staff_salary`, `staff_join_date`, `staff_position`, `staff_role`, `staff_email`, `staff_phone`, `staff_status`) VALUES
('1253', 'Yadusa', '2003-02-14', 500.00, '2025-12-02', 'Sales', 'Staff', 'yadusa@12', '0142336773', 'Active');

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
-- Indexes for table `room_blocked_dates`
--
ALTER TABLE `room_blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_name` (`room_name`,`blocked_date`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_blocked_dates`
--
ALTER TABLE `room_blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
