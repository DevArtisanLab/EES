-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 08:32 AM
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
-- Database: `ees`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `username`, `password`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'hr', 'hr', 'hr'),
(3, 'Guillermo Mercado', 'guimomercado11', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `date_started` date NOT NULL,
  `date_of_exam` date NOT NULL,
  `exam_1` decimal(5,2) DEFAULT NULL,
  `average` decimal(5,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `full_name`, `branch`, `position`, `date_started`, `date_of_exam`, `exam_1`, `average`, `status`, `submitted_at`) VALUES
(46, 'adga', 'gadg', 'Dining Staff', '2025-01-06', '2025-05-29', 100.00, NULL, 'Passed', '2025-05-29 07:46:52'),
(47, 'Guillermo Mercado', 'NP Arnolds', 'Kitchen Supervisor', '2025-01-06', '2025-05-29', 0.00, NULL, 'Failed', '2025-05-29 13:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

CREATE TABLE `examinations` (
  `id` int(11) NOT NULL,
  `exam_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `question_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Draft','Active') NOT NULL DEFAULT 'Draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinations`
--

INSERT INTO `examinations` (`id`, `exam_id`, `title`, `position`, `duration`, `created`, `question_id`, `question_text`, `option_id`, `option_text`, `is_correct`, `status`) VALUES
(1, 'TSD01', 'TSD', 'All Employee', 60, '2025-05-29 06:19:31', 1, 'What is HTML?', 1, 'Hypertext Markup Language', 1, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `examinations`
--
ALTER TABLE `examinations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `examinations`
--
ALTER TABLE `examinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
