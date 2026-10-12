-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2026 at 05:34 PM
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
-- Database: `food_waste_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `beneficiary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `household_size` int(11) DEFAULT 1,
  `needs` text DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `collection_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `ngo_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `collection_date` datetime DEFAULT current_timestamp(),
  `collected_quantity` decimal(10,2) NOT NULL,
  `status` enum('pending','collected') DEFAULT 'collected'
) ;

--
-- Dumping data for table `collections`
--

INSERT INTO `collections` (`collection_id`, `donation_id`, `ngo_id`, `request_id`, `collection_date`, `collected_quantity`, `status`) VALUES
(1, 1, 1, 1, '2026-09-30 00:02:04', 2.00, 'collected'),
(2, 1, 1, 1, '2026-09-30 00:02:54', 0.50, 'collected'),
(3, 1, 1, 1, '2026-09-30 00:05:35', 2.50, 'collected');

-- --------------------------------------------------------

--
-- Table structure for table `distributions`
--

CREATE TABLE `distributions` (
  `distribution_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `distribution_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `food_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(30) NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('available','requested','accepted','distributed','expired') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donation_id`, `donor_id`, `food_name`, `description`, `quantity`, `unit`, `expiry_date`, `status`, `created_at`) VALUES
(1, 2, 'Rice', 'Fresh cooked rice', 20.00, 'kg', '2027-02-02', 'accepted', '2026-09-29 17:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `ngo_requests`
--

CREATE TABLE `ngo_requests` (
  `request_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `ngo_id` int(11) NOT NULL,
  `requested_quantity` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','rejected','completed') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `ngo_requests`
--

INSERT INTO `ngo_requests` (`request_id`, `donation_id`, `ngo_id`, `requested_quantity`, `status`, `request_date`) VALUES
(1, 1, 1, 5.00, 'accepted', '2026-09-29 17:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('donor','ngo','beneficiary') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Test NGO', 'ngo@test.com', '$2y$10$dte3M3qQNUQz.UcQI6PX9OdPjuJL5DFCBa2Pi3wgx3ooRFkcQd/CG', '', '', 'ngo', '2026-09-29 17:50:26'),
(2, 'Test Donor', 'donor@test.com', '$2y$10$e0zd8NA7eGb3o4ZvZBXdHecF20zk/kmXYlArO1DlK0OGy9zbkoWQq', '', '', 'donor', '2026-09-29 17:52:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`beneficiary_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`collection_id`),
  ADD KEY `donation_id` (`donation_id`),
  ADD KEY `ngo_id` (`ngo_id`),
  ADD KEY `idx_collection_request` (`request_id`);

--
-- Indexes for table `distributions`
--
ALTER TABLE `distributions`
  ADD PRIMARY KEY (`distribution_id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `idx_distribution_request` (`request_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `fk_donation_donor` (`donor_id`),
  ADD KEY `idx_donation_status` (`status`),
  ADD KEY `idx_donation_expiry` (`expiry_date`);

--
-- Indexes for table `ngo_requests`
--
ALTER TABLE `ngo_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `donation_id` (`donation_id`),
  ADD KEY `ngo_id` (`ngo_id`),
  ADD KEY `idx_request_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `beneficiary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distributions`
--
ALTER TABLE `distributions`
  MODIFY `distribution_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ngo_requests`
--
ALTER TABLE `ngo_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `collections_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`),
  ADD CONSTRAINT `collections_ibfk_2` FOREIGN KEY (`ngo_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `collections_ibfk_3` FOREIGN KEY (`request_id`) REFERENCES `ngo_requests` (`request_id`);

--
-- Constraints for table `distributions`
--
ALTER TABLE `distributions`
  ADD CONSTRAINT `distributions_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `ngo_requests` (`request_id`),
  ADD CONSTRAINT `distributions_ibfk_2` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`beneficiary_id`);

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `fk_donation_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ngo_requests`
--
ALTER TABLE `ngo_requests`
  ADD CONSTRAINT `ngo_requests_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`),
  ADD CONSTRAINT `ngo_requests_ibfk_2` FOREIGN KEY (`ngo_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
