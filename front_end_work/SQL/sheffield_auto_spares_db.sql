-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2025 at 01:59 AM
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
-- Database: `sheffield_auto_spares_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `branch_phone` varchar(20) DEFAULT NULL,
  `branch_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_address`, `branch_phone`, `branch_email`) VALUES
(5, 'Test Branch Test', 'bsvjfdbfvdil', 'uigolhfvdohvfd', 'bkvfjdbhvfd@gmailc.om');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_time` time NOT NULL,
  `total_due` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `order_id`, `invoice_date`, `invoice_time`, `total_due`, `status`, `total_paid`) VALUES
(5000002, 6000002, '2024-11-02', '15:00:00', 87.75, 0, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_time` time NOT NULL,
  `order_status` varchar(20) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `order_time`, `order_status`, `recipient_name`, `total_cost`, `user_id`) VALUES
(6000002, '2024-11-02', '14:30:00', 'Completed', 'John', 208.75, 1000003),
(6000003, '2024-12-02', '15:47:00', 'Pending', '0', 0.00, 1000004),
(6000006, '2024-12-04', '11:46:00', 'Pending', '0', 1264.45, 1000003),
(6000007, '2024-12-09', '01:47:00', 'Pending', 'John Dale', 0.00, 1000004);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `order_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `part_id`, `order_quantity`, `order_price`) VALUES
(7000003, 6000002, 3000002, 15, 86.25),
(7000008, 6000002, 3000004, 5, 12.50),
(7000011, 6000006, 3000006, 55, 22.99);

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--

CREATE TABLE `parts` (
  `part_id` int(11) NOT NULL,
  `part_name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `genre` varchar(30) NOT NULL,
  `manufacturer` varchar(100) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity_in_stock` int(11) NOT NULL,
  `reorder_level` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parts`
--

INSERT INTO `parts` (`part_id`, `part_name`, `description`, `genre`, `manufacturer`, `unit_price`, `quantity_in_stock`, `reorder_level`, `supplier_id`, `branch_id`) VALUES
(3000001, 'Brake Pad', 'Standard brake pad for various models', 'Brakes', 'Brakes Inc.', 15.99, 14, 10, 8000004, 5),
(3000002, 'Spark Plugs', 'High-performance spark plug', 'Engine', 'SparkWorks', 5.75, 120, 30, 8000004, 5),
(3000003, 'Oil Filter', 'Premium oil filter', 'Engine', 'FilterMax', 8.25, 60, 15, 8000002, NULL),
(3000004, 'Air Filter', 'Durable air filter for improved engine performance', 'Engine', 'FilterMax', 12.50, 75, 20, 8000002, NULL),
(3000005, 'Fuel Pump', 'Reliable fuel pump for various car models', 'Fuel System', 'Auto Parts Co.', 45.75, 30, 5, 8000001, NULL),
(3000006, 'Brake Disc', 'High-quality brake disc for enhanced braking', 'Brakes', 'Brakes Inc.', 22.99, 40, 10, 8000001, NULL),
(3000007, 'Headlight Bulb', 'Long-lasting headlight bulb, compatible with most cars', 'Electrical', 'Engine Works', 9.95, 195, 50, 8000003, NULL),
(3000008, 'Windshield Wiper', 'Premium wiper for clear visibility', 'Accessories', 'Auto Parts Co.', 7.25, 150, 30, 8000001, NULL),
(3000009, 'Thermostat', 'Efficient thermostat for temperature regulation', 'Cooling System', 'Engine Works', 15.75, 60, 20, 8000003, NULL),
(3000010, 'Radiator', 'Efficient radiator for engine cooling', 'Cooling System', 'Engine Works', 95.50, 20, 5, 8000003, NULL),
(3000011, 'Battery', '12V car battery with long lifespan', 'Electrical', 'Auto Parts Co.', 120.00, 15, 3, 8000001, NULL),
(3000012, 'Timing Belt', 'Durable timing belt for precise engine performance', 'Engine', 'SparkWorks', 35.75, 49, 10, 8000003, NULL),
(3000013, 'Clutch Plate', 'High-performance clutch plate for smooth driving', 'Transmission', 'Brakes Inc.', 89.99, 25, 5, 8000001, NULL),
(3000014, 'Exhaust Muffler', 'Premium muffler for noise reduction and performance', 'Exhaust', 'Fastenings Ltd.', 45.00, 30, 8, 8000002, NULL),
(3000015, 'Fuel Injector', 'High-efficiency fuel injector for optimal combustion', 'Fuel System', 'Engine Works', 75.25, 40, 10, 8000003, NULL),
(3000017, 'Starter Motor', 'Durable starter motor for consistent engine ignition', 'Electrical', 'SparkWorks', 110.00, 12, 2, 8000003, NULL),
(3000018, 'CV Joint', 'High-quality CV joint for drive shaft durability', 'Transmission', 'Brakes Inc.', 72.50, 28, 10, 8000004, NULL),
(3000019, 'Drive Belt', 'Flexible drive belt for various car models', 'Engine', 'Fastenings Ltd.', 20.75, 50, 12, 8000002, NULL),
(3000020, 'Alternator', 'High-performance alternator for consistent charging', 'Electrical', 'PowerDrive Inc.', 150.00, 25, 5, 8000001, NULL),
(3000021, 'Water Pump', 'Reliable water pump for efficient cooling', 'Cooling System', 'CoolFlow Ltd.', 80.50, 30, 10, 8000003, NULL),
(3000022, 'Wheel Bearing', 'Durable wheel bearing for smooth rotation', 'Suspension', 'Auto Parts Co.', 35.00, 40, 12, 8000001, NULL),
(3000023, 'Control Arm', 'High-strength control arm for steering stability', 'Suspension', 'Fastenings Ltd.', 60.00, 20, 8, 8000002, NULL),
(3000024, 'Engine Mount', 'Sturdy engine mount to minimize vibrations', 'Engine', 'Engine Works', 45.75, 15, 3, 8000003, NULL),
(3000025, 'Exhaust Pipe', 'High-grade exhaust pipe for durability', 'Exhaust', 'Fastenings Ltd.', 55.00, 10, 2, 8000002, NULL),
(3000026, 'Timing Chain', 'Long-lasting timing chain for precise synchronization', 'Engine', 'SparkWorks', 70.00, 18, 6, 8000003, NULL),
(3000027, 'Battery Cable', 'Durable cable for secure battery connections', 'Electrical', 'Engine Works', 20.50, 50, 15, 8000003, NULL),
(3000028, 'Radiator Hose', 'Flexible radiator hose for efficient coolant flow', 'Cooling System', 'CoolFlow Ltd.', 18.99, 60, 20, 8000003, NULL),
(3000029, 'Brake Fluid', 'High-performance brake fluid for safety', 'Brakes', 'Brakes Inc.', 12.50, 100, 25, 8000001, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_phone` varchar(11) NOT NULL,
  `contact_email` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_name`, `contact_phone`, `contact_email`, `address`, `active`) VALUES
(8000001, 'Auto Parts Co.', 'John Smith', '07123456789', 'jsmith@autopartsco.com', '123 Auto Lane', 0),
(8000002, 'Fastenings Ltd.', 'Jane Doe', '07987654321', 'jdoe@fasteningsltd.com', '45 Bolt St.', 0),
(8000003, 'Engine Works', 'Richard Roe', '07012345678', 'rroe@engineworks.com', '789 Engine Rd.', 0),
(8000004, 'Car Parts Company', 'Bob', '38773487534', 'fufbiibc@hfufbish.com', 'giuwugfi ryegbfrie uigjer', 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `log_timestamp` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `action_type` enum('CREATE','UPDATE','DELETE','LOGIN','LOGOUT','ADD','REMOVE','OTHER') NOT NULL,
  `log_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `log_timestamp`, `user_id`, `user_name`, `action_type`, `log_description`) VALUES
(1, '2024-12-10 00:17:34', 1000004, '1', 'LOGIN', 'Logged in at this time.'),
(2, '2024-12-10 00:25:56', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(3, '2024-12-10 00:29:26', 1000004, '1', 'DELETE', 'Removed a user'),
(4, '2024-12-10 01:14:18', 1000004, '1', 'ADD', 'Added an item with part ID: 3000016 to order ID: 6000002'),
(5, '2024-12-10 01:15:22', 1000004, '1', 'LOGOUT', 'Logged Out'),
(6, '2024-12-10 01:15:25', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(7, '2024-12-10 01:15:27', 1000004, '1', 'LOGOUT', 'Logged Out'),
(8, '2024-12-10 01:20:44', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(9, '2024-12-10 01:26:40', 1000004, '1', 'UPDATE', 'Increased stock for Part ID: 3000001 by 51'),
(10, '2024-12-10 01:26:41', 1000004, '1', 'UPDATE', 'Increased stock for Part ID: 3000001 by 52'),
(11, '2024-12-10 01:26:41', 1000004, '1', 'UPDATE', 'Increased stock for Part ID: 3000001 by 53'),
(12, '2024-12-10 01:26:43', 1000004, '1', 'UPDATE', 'Decreased stock for Part ID: 3000001 by 54'),
(13, '2024-12-10 01:26:43', 1000004, '1', 'UPDATE', 'Decreased stock for Part ID: 3000001 by 53'),
(14, '2024-12-10 01:26:43', 1000004, '1', 'UPDATE', 'Decreased stock for Part ID: 3000001 by 52'),
(15, '2024-12-10 01:30:01', 1000004, '1', 'DELETE', 'Deleted Part ID: 3000016'),
(16, '2024-12-10 01:30:28', 1000004, '1', 'UPDATE', 'Updated part'),
(17, '2024-12-10 01:33:45', 1000004, '1', 'CREATE', 'Created a user'),
(18, '2024-12-10 01:34:14', 1000004, '1', 'DELETE', 'Removed 1000024 '),
(19, '2024-12-11 11:41:53', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(20, '2024-12-11 11:43:12', 1000004, '1', 'UPDATE', 'Updated a user'),
(21, '2024-12-13 12:09:57', 1000004, '1', 'LOGOUT', 'Logged Out'),
(22, '2024-12-13 12:11:45', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(23, '2025-01-02 21:34:44', 1000004, '1', 'LOGIN', 'Logged in at this time'),
(24, '2025-01-03 12:46:26', 1000004, '1', 'CREATE', 'Added a branch'),
(25, '2025-01-03 12:51:32', 1000004, '1', 'CREATE', 'Added a branch'),
(99, '2025-01-03 12:57:34', 1000004, '1', 'DELETE', 'Deleted a branch'),
(127, '2025-01-03 13:01:00', 1000004, '1', 'CREATE', 'Added a branch'),
(128, '2025-01-03 13:01:09', 1000004, '1', 'DELETE', 'Deleted a branch'),
(129, '2025-01-03 13:03:17', 1000004, '1', 'DELETE', 'Deleted branch: '),
(130, '2025-01-03 13:04:18', 1000004, '1', 'CREATE', 'Added a branch'),
(131, '2025-01-03 13:04:21', 1000004, '1', 'DELETE', 'Deleted branch: City Centre Branch'),
(132, '2025-01-03 13:25:48', 1000004, '1', 'CREATE', 'Added a branch'),
(133, '2025-01-03 13:28:39', 1000004, '1', 'UPDATE', 'Updated branch: Test Branch Test'),
(134, '2025-01-03 13:46:21', 1000004, '1', 'UPDATE', 'Updated Part ID: 3000001'),
(135, '2025-01-03 13:46:33', 1000004, '1', 'UPDATE', 'Updated Part ID: 3000002');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(99) NOT NULL,
  `role` varchar(20) NOT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `active`) VALUES
(1000001, 'a', '$2y$10$VklExK7CdoqS3.qLuro3mOmZVT2SEhQIjyUG/II683o0dt3Zzpb2.', 'admin@autospare.com', 'Admin', 0),
(1000002, 'staff_jane', 'hashed_password', 'jane@autospare.com', 'Admin', 1),
(1000003, 'user', '$2y$10$JaP4ZkhHRAfcnL5xcSCuM.2O45OO/UBiumpbrnwaDyZAoA1yi8SF2', 'john@autospare.com', 'User', 1),
(1000004, '1', '$2y$10$pCExSMCgHSqc8ARSqfTdIerOBPiA/GifuV6gGbxXuCHrw1RIAHkye', '1@gmail.com', 'Admin', 1),
(1000020, 'test', '$2y$10$xwPfaTrtGFHas7PkCG9w5e2AfWyV8IF5Wir1UZTv1Q/s8LcGh80sS', 'standenfe@gmail.com', 'Admin', 1),
(1000023, 'i am a test user', '$2y$10$VrsY0dZBfEU6IBlu3KAZVuvl3cxcPL9RxodW9p558ZuU/CKGaQ3t6', 'aiufugifah@ufbflb.com', 'User', 0),
(1000024, 'EXamPLEUSER', '$2y$10$SBQIZyO6SvRUHkqK5OShDe/SO.TdzCcW7r62sS4K80Qfx6w04o0Uq', 'bob@gmail.com', 'admin', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_detail_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_detail_id`, `user_id`, `first_name`, `last_name`, `phone_number`) VALUES
(2000001, 1000001, 'Alice', 'Adminson', '07011223344'),
(2000002, 1000002, 'Jane', 'Doe', '07022334455'),
(2000003, 1000003, 'John', 'Smith', '07033445566'),
(2000005, 1000004, 'bobadmin', 'admin', '848484994'),
(2000020, 1000020, 'ddddddd', 'dddddd', '07927110610'),
(2000023, 1000023, 'felix', 'standen', '83549538'),
(2000024, 1000024, 'example', 'bobb', '79348543');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Indexes for table `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `fk_branch` (`branch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_detail_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5000003;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6000008;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7000014;

--
-- AUTO_INCREMENT for table `parts`
--
ALTER TABLE `parts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3000031;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8000005;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000025;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2000025;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `parts` (`part_id`);

--
-- Constraints for table `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `fk_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `parts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
