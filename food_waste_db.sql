
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


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

-- Query 2 | Category: INSERT
-- Purpose: Insert a new sample donation to demonstrate adding food into the system.
INSERT INTO donations (donor_id, food_name, description, quantity, unit, expiry_date, status)
VALUES (1, 'Beans', 'Dry beans pack', 7.50, 'kg', '2030-12-31', 'available');

-- Query 3 | Category: SELECT-Filter
-- Purpose: List donations that are currently available and have not expired.
SELECT donation_id, food_name, quantity, unit, expiry_date
FROM donations
WHERE status = 'available' AND expiry_date >= CURDATE();

-- Query 4 | Category: ORDER BY
-- Purpose: Sort donations by soonest expiry date first.
SELECT food_name, quantity, unit, expiry_date
FROM donations
ORDER BY expiry_date ASC, food_name ASC;

-- Query 5 | Category: LIKE
-- Purpose: Find food names containing the word 'ri' (case sensitivity depends on collation).
SELECT donation_id, food_name
FROM donations
WHERE food_name LIKE '%ri%';

-- Query 6 | Category: IN-BETWEEN
-- Purpose: Find donations in selected statuses with quantities between 5 and 20 inclusive.
SELECT food_name, quantity, status
FROM donations
WHERE status IN ('available', 'accepted')
  AND quantity BETWEEN 5 AND 20
ORDER BY quantity DESC;

-- Query 7 | Category: TOP-LIMIT
-- Purpose: Return only the three most recently created donations. MySQL uses LIMIT for this purpose.
SELECT donation_id, food_name, created_at
FROM donations
ORDER BY created_at DESC
LIMIT 3;

-- Query 8 | Category: JOIN
-- Purpose: Show each donation with the donor's name by joining donations to users.
SELECT d.donation_id, d.food_name, u.name AS donor_name, d.quantity, d.unit
FROM donations AS d
INNER JOIN users AS u ON u.user_id = d.donor_id
ORDER BY d.donation_id;

-- Query 9 | Category: JOIN
-- Purpose: Show NGO requests with food name and NGO name using two joins.
SELECT r.request_id, d.food_name, u.name AS ngo_name,
       r.requested_quantity, r.status
FROM ngo_requests AS r
JOIN donations AS d ON d.donation_id = r.donation_id
JOIN users AS u ON u.user_id = r.ngo_id
ORDER BY r.request_date DESC;

-- Query 10 | Category: Aggregate-GROUP BY
-- Purpose: Count donations by status for a quick inventory summary.
SELECT status, COUNT(*) AS donation_count
FROM donations
GROUP BY status
ORDER BY donation_count DESC, status;

-- Query 11 | Category: Subquery
-- Purpose: Show each donation and how many NGO requests it has received.
SELECT d.donation_id, d.food_name,
       (SELECT COUNT(*)
        FROM ngo_requests AS r
        WHERE r.donation_id = d.donation_id) AS request_count
FROM donations AS d
ORDER BY d.donation_id;

-- Query 12 | Category: UPDATE
-- Purpose: Change a donation's status after an NGO request is submitted.
UPDATE donations
SET status = 'requested'
WHERE donation_id = 2 AND status = 'available';

-- Query 13 | Category: DELETE
-- Purpose: Delete a demonstration donation only if it has no related requests.
DELETE FROM donations
WHERE donation_id = 2
  AND NOT EXISTS (
    SELECT 1 FROM ngo_requests AS r
    WHERE r.donation_id = donations.donation_id
  );

-- Query 14 | Category: Aggregate-GROUP BY
-- Purpose: Calculate total collected quantity for each NGO request.
SELECT request_id, SUM(collected_quantity) AS total_collected
FROM collections
WHERE status = 'collected'
GROUP BY request_id
ORDER BY request_id;