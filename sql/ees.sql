-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 11:16 AM
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
(1, 'admin', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_num` varchar(50) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `date_started` date NOT NULL,
  `date_of_exam` date NOT NULL,
  `score` int(11) NOT NULL,
  `average` decimal(5,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_num`, `id`, `full_name`, `branch`, `position`, `date_started`, `date_of_exam`, `score`, `average`, `status`, `submitted_at`) VALUES
('250007', 59, 'Guillermo Mercado', 'NP Arnolds', 'Store Manager', '2025-01-06', '2025-06-04', 100, NULL, 'Passed', '2025-06-04 16:19:10'),
('sa', 66, 'a', 'fs', 'Store Manager', '2025-01-06', '2025-06-04', 0, NULL, NULL, '2025-06-04 16:19:19'),
('afa', 67, 'fadf', 'adfa', 'Store Manager', '1111-01-06', '2025-06-04', 0, NULL, NULL, '2025-06-04 16:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

CREATE TABLE `examinations` (
  `exam_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `passing_score` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinations`
--

INSERT INTO `examinations` (`exam_id`, `title`, `position`, `duration`, `description`, `passing_score`, `status`, `created`) VALUES
(1, 'TSD EXAM', 'All', 30, 'TSD EXAMINATION', 75, 'Active', '2025-06-04 06:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` varchar(20) NOT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `option_d` text DEFAULT NULL,
  `correct_option` varchar(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `exam_id`, `question_text`, `question_type`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `created_at`) VALUES
(51, 1, '1', 'Multiple Choice', '1', '2', '3', '4', 'A', '2025-06-04 04:57:36'),
(52, 1, '2', 'Multiple Choice', '1', '2', '3', '4', 'B', '2025-06-04 04:57:36'),
(53, 1, '3', 'Multiple Choice', '1', '2', '3', '4', 'C', '2025-06-04 04:57:36'),
(54, 1, '4', 'Multiple Choice', '1', '23', '3', '4', 'D', '2025-06-04 04:57:36'),
(55, 1, '5', 'Multiple Choice', '12', '23', '23', '43', 'C', '2025-06-04 04:57:36'),
(56, 1, '6', 'Multiple Choice', '14', '1', '14', '1', 'B', '2025-06-04 04:57:36'),
(57, 1, '7', 'Multiple Choice', '13', '65', '665', '65', 'A', '2025-06-04 04:57:36'),
(58, 1, '8', 'Multiple Choice', '65', '56', '65', '54', 'B', '2025-06-04 04:57:36'),
(59, 1, '96', 'Multiple Choice', '56', '43', '5345', '345', 'B', '2025-06-04 04:57:36'),
(60, 1, '10', 'Multiple Choice', '345', '345', '345', '34', 'C', '2025-06-04 04:57:36'),
(61, 1, '11', 'Multiple Choice', '3345', '345', '435345', '534', 'D', '2025-06-04 04:57:36'),
(62, 1, '12', 'Multiple Choice', '345', '34543', '345', '345', 'B', '2025-06-04 04:57:36'),
(63, 1, '13', 'Multiple Choice', '435', '34543', '45', '543', 'B', '2025-06-04 04:57:36'),
(64, 1, '14', 'Multiple Choice', '345', '453', '345', '3435', 'B', '2025-06-04 04:57:36'),
(65, 1, '15', 'Multiple Choice', '45', '54', '35', '543', 'A', '2025-06-04 04:57:36'),
(66, 1, '16', 'True/False', 'True', 'False', '213', '123', 'A', '2025-06-04 04:57:36'),
(67, 1, '17', 'True/False', 'True', 'False', '213', '123', 'B', '2025-06-04 04:57:36'),
(68, 1, '18', 'True/False', 'True', 'False', '123', '213', 'A', '2025-06-04 04:57:36'),
(69, 1, '19', 'True/False', 'True', 'False', '123', '123', 'B', '2025-06-04 04:57:36'),
(70, 1, '20', 'True/False', 'True', 'False', '123', '123', 'A', '2025-06-04 04:57:36'),
(71, 1, '21', 'True/False', 'True', 'False', '132', '123', 'B', '2025-06-04 04:57:36'),
(72, 1, '22', 'True/False', 'True', 'False', '123', '123', 'B', '2025-06-04 04:57:36'),
(73, 1, '23', 'True/False', 'True', 'False', '123', '132', 'A', '2025-06-04 04:57:36'),
(74, 1, '24', 'True/False', 'True', 'False', '123', '123', 'B', '2025-06-04 04:57:36'),
(75, 1, '25', 'True/False', 'True', 'False', '2', '1', 'D', '2025-06-04 04:57:36');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_num` (`employee_num`);

--
-- Indexes for table `examinations`
--
ALTER TABLE `examinations`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `examinations`
--
ALTER TABLE `examinations`
  MODIFY `exam_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
