-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2024 at 01:22 PM
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
-- Database: `gdpayments`
--

-- --------------------------------------------------------

--
-- Table structure for table `gdpays`
--

CREATE TABLE `gdpays` (
  `id` int(11) NOT NULL,
  `Gid` int(11) DEFAULT NULL,
  `total_paid` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gd_pay`
--

CREATE TABLE `gd_pay` (
  `id` int(11) NOT NULL,
  `Gd_bankDate` date DEFAULT NULL,
  `GD_number` Text DEFAULT NULL,
  `TotalAmount` varchar(255) DEFAULT NULL,
  `PaidAmount` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gdpays`
--
ALTER TABLE `gdpays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Gid` (`Gid`);

--
-- Indexes for table `gd_pay`
--
ALTER TABLE `gd_pay`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gdpays`
--
ALTER TABLE `gdpays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gd_pay`
--
ALTER TABLE `gd_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gdpays`
--
ALTER TABLE `gdpays`
  ADD CONSTRAINT `fk_Gid` FOREIGN KEY (`Gid`) REFERENCES `gd_pay` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
