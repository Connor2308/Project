-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 05:39 AM
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
  `branch_email` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_address`, `branch_phone`, `branch_email`, `active`) VALUES
(5, 'Rotherham Branch', 'Miller Street', '79824698454', 'rotherham@sap.com', 1),
(6, 'Sheffield City Centre', 'City Street', '87939534785', 'citycentre@sap.com', 0),
(7, 'Barnsley Branch', 'Barnsley Road', '809382098', 'barns@sap.com', 1),
(8, 'Sheffield Branch', 'Sheffield Street', '0974504794', 'sheffield@sap.com', 1);

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
(5000006, 6000011, '2025-01-06', '09:29:00', 0.00, 0, 0.00),
(5000007, 6000012, '2025-01-06', '09:30:00', 0.00, 0, 0.00),
(5000008, 6000013, '2025-01-06', '04:32:00', 0.00, 0, 0.00);

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
(6000011, '2025-01-06', '09:29:00', 'Pending', 'Harry', 330.50, 1000026),
(6000012, '2025-01-06', '09:30:00', 'Pending', 'Halifax', 343.50, 1000026),
(6000013, '2025-01-06', '04:32:00', 'Pending', 'Johnsons', 200.00, 1000027);

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
(7000022, 6000011, 3000021, 2, 80.50),
(7000023, 6000011, 3000019, 3, 20.75),
(7000024, 6000011, 3000012, 3, 35.75),
(7000025, 6000012, 3000010, 2, 95.50),
(7000026, 6000012, 3000029, 4, 12.50),
(7000027, 6000012, 3000027, 5, 20.50),
(7000028, 6000013, 3000031, 2, 100.00);

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
(3000002, 'Spark Plug', 'High-performance spark plug', 'Engine', 'SparkWorks', 5.76, 29, 30, 8000004, 5),
(3000003, 'Oil Filter', 'Premium oil filter', 'Engine', 'FilterMax', 8.25, 14, 15, 8000002, 5),
(3000005, 'Fuel Pump', 'Reliable fuel pump for various car models', 'Fuel System', 'Auto Parts Co.', 45.75, 2, 5, 8000001, 5),
(3000007, 'Headlight Bulb', 'Long-lasting headlight bulb, compatible with most cars', 'Electrical', 'Engine Works', 9.95, 49, 50, 8000002, 5),
(3000008, 'Windshield Wiper', 'Premium wiper for clear visibility', 'Accessories', 'Auto Parts Co.', 7.25, 29, 30, 8000001, 5),
(3000009, 'Thermostat', 'Efficient thermostat for temperature regulation', 'Cooling System', 'Engine Works', 15.75, 60, 20, 8000004, 5),
(3000010, 'Radiator', 'Efficient radiator for engine cooling', 'Cooling System', 'Engine Works', 95.50, 3, 5, 8000004, 5),
(3000011, 'Battery', '12V car battery with long lifespan', 'Electrical', 'Auto Parts Co.', 120.00, 4, 3, 8000001, 5),
(3000012, 'Timing Belt', 'Durable timing belt for precise engine performance', 'Engine', 'SparkWorks', 35.75, 49, 10, 8000003, 5),
(3000013, 'Clutch Plate', 'High-performance clutch plate for smooth driving', 'Transmission', 'Brakes Inc.', 89.99, 7, 5, 8000001, 5),
(3000014, 'Exhaust Muffler', 'Premium muffler for noise reduction and performance', 'Exhaust', 'Fastenings Ltd.', 45.00, 30, 8, 8000002, 5),
(3000015, 'Fuel Injector', 'High-efficiency fuel injector for optimal combustion', 'Fuel System', 'Engine Works', 75.25, 40, 10, 8000003, 5),
(3000017, 'Starter Motor', 'Durable starter motor for consistent engine ignition', 'Electrical', 'SparkWorks', 110.00, 0, 2, 8000003, 5),
(3000018, 'CV Joint', 'High-quality CV joint for drive shaft durability', 'Transmission', 'Brakes Inc.', 72.50, 9, 10, 8000004, 5),
(3000019, 'Drive Belt', 'Flexible drive belt for various car models', 'Engine', 'Fastenings Ltd.', 20.75, 50, 12, 8000002, 5),
(3000020, 'Alternator', 'High-performance alternator for consistent charging', 'Electrical', 'PowerDrive Inc.', 150.00, 3, 5, 8000001, 5),
(3000021, 'Water Pump', 'Reliable water pump for efficient cooling', 'Cooling System', 'CoolFlow Ltd.', 80.50, 9, 10, 8000002, 5),
(3000022, 'Wheel Bearing', 'Durable wheel bearing for smooth rotation', 'Suspension', 'Auto Parts Co.', 35.00, 40, 12, 8000001, 5),
(3000023, 'Control Arm', 'High-strength control arm for steering stability', 'Suspension', 'Fastenings Ltd.', 60.00, 20, 8, 8000002, 5),
(3000024, 'Engine Mount', 'Sturdy engine mount to minimize vibrations', 'Engine', 'Engine Works', 45.75, 2, 3, 8000001, 5),
(3000025, 'Exhaust Pipe', 'High-grade exhaust pipe for durability', 'Exhaust', 'Fastenings Ltd.', 55.00, 10, 2, 8000002, 5),
(3000026, 'Timing Chain', 'Long-lasting timing chain for precise synchronization', 'Engine', 'SparkWorks', 70.00, 18, 6, 8000003, 5),
(3000027, 'Battery Cable', 'Durable cable for secure battery connections', 'Electrical', 'Engine Works', 20.50, 50, 15, 8000003, 5),
(3000028, 'Radiator Hose', 'Flexible radiator hose for efficient coolant flow', 'Cooling System', 'CoolFlow Ltd.', 18.99, 65, 20, 8000002, 5),
(3000029, 'Brake Fluid', 'High-performance brake fluid for safety', 'Brakes', 'Brakes Inc.', 12.50, 100, 25, 8000001, 5),
(3000031, 'Bonet', 'Very good bonets', 'Accessories', 'BonetsRUs', 100.00, 5, 1, 8000004, 6);

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
(8000001, 'Auto Parts Co.', 'John Smith', '07123456789', 'jsmith@autopartsco.com', '123 Auto Lane', 1),
(8000002, 'Fastenings Ltd.', 'Jane Doe', '07987654321', 'jdoe@fasteningsltd.com', '45 Bolt St.', 1),
(8000003, 'Engine Works', 'Richard Roe', '07012345678', 'rroe@engineworks.com', '789 Engine Rd.', 0),
(8000004, 'Car Parts Company', 'Bob', '03877348753', 'Richard@gmail.com', 'Car Part Street', 1),
(8000005, 'We Love Car Parts', 'Jimmy', '07892437932', 'weluvcarparts@email.com', 'Blackburn Road, Blackburn', 1);

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
(339, '2025-01-06 04:29:31', 1000026, 'Admin1', 'CREATE', 'Added an order'),
(340, '2025-01-06 04:29:41', 1000026, 'Admin1', 'ADD', 'Added an item with part ID: 3000021 to order ID: 6000011'),
(341, '2025-01-06 04:29:46', 1000026, 'Admin1', 'ADD', 'Added an item with part ID: 3000019 to order ID: 6000011'),
(342, '2025-01-06 04:29:52', 1000026, 'Admin1', 'ADD', 'Added an item with part ID: 3000012 to order ID: 6000011'),
(343, '2025-01-06 04:30:15', 1000026, 'Admin1', 'LOGOUT', 'Logged Out'),
(344, '2025-01-06 04:30:29', 1000027, 'User1', 'LOGIN', 'Logged in at this time'),
(345, '2025-01-06 04:31:11', 1000027, 'User1', 'CREATE', 'Added an order'),
(346, '2025-01-06 04:31:24', 1000027, 'User1', 'ADD', 'Added an item with part ID: 3000010 to order ID: 6000012'),
(347, '2025-01-06 04:31:31', 1000027, 'User1', 'ADD', 'Added an item with part ID: 3000029 to order ID: 6000012'),
(348, '2025-01-06 04:31:37', 1000027, 'User1', 'ADD', 'Added an item with part ID: 3000027 to order ID: 6000012'),
(349, '2025-01-06 04:31:53', 1000027, 'User1', 'LOGOUT', 'Logged Out'),
(350, '2025-01-06 04:32:05', 1000026, 'Admin1', 'LOGIN', 'Logged in at this time'),
(351, '2025-01-06 04:32:32', 1000026, 'Admin1', 'LOGOUT', 'Logged Out'),
(352, '2025-01-06 04:32:45', 1000027, 'User1', 'LOGIN', 'Logged in at this time'),
(353, '2025-01-06 04:33:00', 1000027, 'User1', 'CREATE', 'Added an order'),
(354, '2025-01-06 04:33:13', 1000027, 'User1', 'ADD', 'Added an item with part ID: 3000031 to order ID: 6000013'),
(355, '2025-01-06 04:34:01', 1000027, 'User1', 'LOGIN', 'Logged in at this time'),
(356, '2025-01-06 04:35:08', 1000027, 'User1', 'LOGOUT', 'Logged Out'),
(357, '2025-01-06 04:35:21', 1000026, 'Admin1', 'LOGIN', 'Logged in at this time'),
(358, '2025-01-06 04:35:40', 1000026, 'Admin1', 'UPDATE', 'Updated Part ID: 3000028'),
(359, '2025-01-06 04:35:58', 1000026, 'Admin1', 'UPDATE', 'Reactivated a supplier'),
(360, '2025-01-06 04:37:53', 1000026, 'Admin1', 'DELETE', 'Deactivated a supplier');

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
(1000026, 'Admin1', '$2y$10$RY53Jsh/7ubJ3pMEZvlWCu3HJEkoqpCr4TzUENc7hVOQLK51Mho7m', 'admin@sap.com', 'Admin', 1),
(1000027, 'User1', '$2y$10$bnP2gI8h3lMXiUBgL78VS.YLBnWBSsQqHihcAtFhj4sV41es99KZu', 'johnsmith@sap.com', 'User', 1);

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
(2000026, 1000026, 'James', 'Hunt', '07476492942'),
(2000027, 1000027, 'John', 'Smith', '023099327');

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
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5000009;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6000014;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7000029;

--
-- AUTO_INCREMENT for table `parts`
--
ALTER TABLE `parts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3000032;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8000006;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000028;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2000028;

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
