-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 05:44 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_reservation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `room_capacity` varchar(50) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `number_of_days` int(11) DEFAULT NULL,
  `total_bill` decimal(10,2) DEFAULT NULL,
  `reservation_timestamp` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `customer_name`, `contact_number`, `from_date`, `to_date`, `room_type`, `room_capacity`, `payment_type`, `number_of_days`, `total_bill`, `reservation_timestamp`) VALUES
(2, 'Richard Meow', '09178051962', '2025-04-04', '2025-04-12', 'Suite', 'Family', 'Cash', 8, 6800.00, '2025-04-03 15:34:10'),
(3, 'Richard Traballo', '09178051962', '2025-04-04', '2025-04-09', 'Regular', 'Family', 'Credit-Card', 5, 2750.00, '2025-04-03 15:36:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
