-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 08:11 AM
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
-- Database: `e_vaccination`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `child_id`, `parent_id`, `hospital_id`, `vaccine_id`, `appointment_date`, `time_slot`, `status`, `created_at`) VALUES
(39, 29, 16, 16, 7, '2025-07-24', '10:00 AM', 'Approved', '2025-07-23 18:50:18'),
(40, 29, 16, 17, 11, '2025-07-24', '10:00 AM', 'Approved', '2025-07-23 19:54:01');

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `name`, `dob`, `gender`, `parent_id`, `age`, `image_url`) VALUES
(20, 'Sana Khan', '2023-07-19', 'Female', 50, 2, 'uploads/1752942094_687bc60e64474.jpg'),
(21, 'Subhan Khan', '2022-03-19', 'Male', 50, 3, 'uploads/1752942136_687bc63869bff.jpg'),
(22, 'Fatima Ali', '2023-01-19', 'Female', 51, 2, 'uploads/1752944075_687bcdcbf2922.jpg'),
(24, 'Yasir Khas', '2024-01-20', 'Male', 50, 1, 'uploads/1752954215_687bf5671e29f.jpg'),
(25, 'Yasir Khan', '2023-04-20', 'Male', 50, 2, 'uploads/1752958236_687c051c9e51b.jpg'),
(27, 'Junaid Khan', '2023-08-20', 'Male', 52, 2, 'uploads/1753010399_687cd0df85ede.jpg'),
(28, 'saleem', '2018-07-18', 'Male', 44, 7, 'uploads/1753161466_687f1efa844e4.jpg'),
(29, 'Maria', '2023-12-23', 'Female', 44, 2, 'uploads/1753294182_68812566ec8d9.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `hospital_name`, `location`, `contact_number`, `email`, `address`, `created_at`, `user_id`, `image`) VALUES
(16, 'kharadar Hospital', 'Karachi', '03453243231', 'abc@gmail.com', 'Kharadar', '2025-07-20 19:21:08', 53, 'uploads/1753296519_doctor.jpg'),
(17, 'Kharadar General Hospital', 'Karachi', '03453243222', 'kharadar@gmail.com', 'Kharadar Karachi ', '2025-07-20 19:54:50', 54, 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `user_id`, `name`, `phone`, `address`, `image`) VALUES
(16, 44, 'Mr. Mark', '03460968011', 'north Nazimabad\r\nBlock H, D-63', 'parent_6881253bc65d4_images.jpg'),
(44, NULL, NULL, NULL, NULL, NULL),
(45, 48, 'Zaid Khan', '03460968032', 'north Nazimabad\r\nBlock H, D-63', NULL),
(46, 50, 'ZAID Ahmad', '03460968034', 'north Nazimabad\r\nBlock H, D-41', 'parent_687b8548cde31_facebook.jpg'),
(50, NULL, NULL, NULL, NULL, NULL),
(51, 51, 'Uzair Khan', '03460968021', 'north Nazimabad\r\nBlock H, D-63', 'parent_687cbf300f71e_baby boy.jpg'),
(52, 52, 'Azmat Khan', '03460968076', 'Bolten Market Karachi', 'parent_687cd15fe449b_WhatsApp Image 2025-07-20 at 3.29.35 PM.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('admin','parent','hospital') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `email`, `password`, `name`, `image`) VALUES
(43, 'admin', 'admin@example.com', '$2y$10$L3lh90RRqHHb4QpS8VJrSeIG.azHWMDNrHB0k6pgRUPIjPOMcFTyu', 'Admin User', 'uploads/1753294595_image.jpg'),
(44, 'parent', 'azmatkhan@gmail.com', '$2y$10$6C7chcJdVAaBp6tXRuBGx.15bDvHXYveh9.iA9a.r/W6pARnhZHca', 'Azmat Khan', 'uploads/1753161780_baby boy.jpg'),
(45, 'hospital', 'az@gmail.com', '$2y$10$xtW6v4AvQ/1dzh/DH8A3hemZkp2bCc8/XmCTmMlYyx9MhBlz.nFUm', 'Azmat Khan ALI', NULL),
(47, 'hospital', 'xyz@gmail.com', '$2y$10$rnerE.2s/lDTynVra2Mlr.6vO/5m35GV3.KbfevjMxWZgNkuhL36C', 'Azmat Khan', 'uploads/1753123078_baby boy.jpg'),
(48, 'parent', 'zaid@gmail.com', '$2y$10$7ez/wJRolvuEFRyx4XAG1uB.7zQCm68fsCOFZFLPFlIhIKSOUguya', 'Zaid Khan', NULL),
(50, 'parent', 'zaid1@gmail.com', '$2y$10$Kbmbwd9EzGPNVQEHg.ZC5u5AdN8JQD8LiD.bJjjvc.1nAvH3VnUhy', 'ZAID KHAN', NULL),
(51, 'parent', 'uzair@gmail.com', '$2y$10$SqNAmzb4bOhWdEETIu9O6uoiEhonh.NVRZmK7BGcA8km8H8x8TDHC', 'Uzair Khan', NULL),
(52, 'parent', 'azmatkhan8045@gmail.com', '$2y$10$vCEv4Ph1NObMjdOfC4AqOOAAfnIM0PRgJG5.Z1p/3aVNe88/Qhdhq', 'Azmat Ullah', NULL),
(53, 'hospital', 'abc@gmail.com', '$2y$10$9iJiaoenQRDm6Cn1.l4d1Or51qLB5DwT16DUmddMr0uw4O9uL1yTG', 'Ahmad Khan', NULL),
(54, 'hospital', 'kharadar@gmail.com', '$2y$10$63zhSePbUKxlMsgv2Sr2LebjGUlNIhSIk5TmY/w4dPPldyMAA.bgK', 'Kharadar General Hospital', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_history`
--

CREATE TABLE `vaccination_history` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `child_name` varchar(100) DEFAULT NULL,
  `vaccine_name` varchar(100) DEFAULT NULL,
  `date_given` date DEFAULT NULL,
  `hospital_name` varchar(100) DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(55) DEFAULT NULL,
  `disease_prevented` varchar(100) DEFAULT NULL,
  `recommended_age` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `vaccine_name`, `disease_prevented`, `recommended_age`, `quantity`, `hospital_id`) VALUES
(5, 'BCG', 'Tuberculosis	', 0, 87, NULL),
(6, 'Hepatitis  B', 'Hepatitis  B', 0, 145, NULL),
(7, 'OPV (Oral Polio)', 'Polio', 0, 137, NULL),
(11, 'MMMR', 'something', 2, 165, 47),
(12, 'PVC', 'FOR BETTER', 3, 289, 47),
(13, 'Polio', 'Polio', 5, 20, NULL),
(14, 'Polio', 'Polio', 5, 98, 53);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccination_history`
--
ALTER TABLE `vaccination_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `vaccination_history`
--
ALTER TABLE `vaccination_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
