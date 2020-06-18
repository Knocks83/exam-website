-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2020 at 02:51 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esame`
--

-- --------------------------------------------------------

--
-- Table structure for table `air_cities`
--

CREATE TABLE `air_cities` (
  `city` varchar(30) CHARACTER SET latin1 NOT NULL,
  `province` varchar(30) CHARACTER SET latin1 NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air_measurements`
--

CREATE TABLE `air_measurements` (
  `Measurement_ID` int(10) UNSIGNED NOT NULL,
  `sensor_ID` int(10) UNSIGNED NOT NULL,
  `Date` datetime NOT NULL,
  `Value` int(10) NOT NULL,
  `Status` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air_sensors`
--

CREATE TABLE `air_sensors` (
  `ID` int(10) UNSIGNED NOT NULL,
  `type` varchar(30) CHARACTER SET latin1 NOT NULL,
  `station_ID` int(10) UNSIGNED NOT NULL,
  `data_start` date DEFAULT NULL,
  `data_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air_sensortypes`
--

CREATE TABLE `air_sensortypes` (
  `type_name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `unit_of_measure` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air_stations`
--

CREATE TABLE `air_stations` (
  `ID` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `city` varchar(30) CHARACTER SET latin1 NOT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `air_cities`
--
ALTER TABLE `air_cities`
  ADD PRIMARY KEY (`city`);

--
-- Indexes for table `air_measurements`
--
ALTER TABLE `air_measurements`
  ADD PRIMARY KEY (`Measurement_ID`),
  ADD KEY `sensor_ID` (`sensor_ID`);

--
-- Indexes for table `air_sensors`
--
ALTER TABLE `air_sensors`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `type` (`type`),
  ADD KEY `station_ID` (`station_ID`);

--
-- Indexes for table `air_sensortypes`
--
ALTER TABLE `air_sensortypes`
  ADD PRIMARY KEY (`type_name`);

--
-- Indexes for table `air_stations`
--
ALTER TABLE `air_stations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `city` (`city`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `air_measurements`
--
ALTER TABLE `air_measurements`
  MODIFY `Measurement_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `air_sensors`
--
ALTER TABLE `air_sensors`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `air_stations`
--
ALTER TABLE `air_stations`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `air_measurements`
--
ALTER TABLE `air_measurements`
  ADD CONSTRAINT `air_measurements_ibfk_1` FOREIGN KEY (`sensor_ID`) REFERENCES `air_sensors` (`ID`);

--
-- Constraints for table `air_sensors`
--
ALTER TABLE `air_sensors`
  ADD CONSTRAINT `air_sensors_ibfk_1` FOREIGN KEY (`type`) REFERENCES `air_sensortypes` (`type_name`),
  ADD CONSTRAINT `air_sensors_ibfk_2` FOREIGN KEY (`station_ID`) REFERENCES `air_stations` (`ID`);

--
-- Constraints for table `air_stations`
--
ALTER TABLE `air_stations`
  ADD CONSTRAINT `air_stations_ibfk_1` FOREIGN KEY (`city`) REFERENCES `air_cities` (`city`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
