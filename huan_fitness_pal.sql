-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 09:18 AM
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
-- Database: `huan_fitness_pal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminId` varchar(5) NOT NULL,
  `adminEmail` varchar(50) DEFAULT NULL,
  `adminPsw` varchar(8) NOT NULL,
  `adminName` varchar(30) NOT NULL,
  `adminPic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminId`, `adminEmail`, `adminPsw`, `adminName`, `adminPic`) VALUES
('A0001', 'a0001@gmail.com', '12345678', 'Lenna', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSm74qVEqNxdDlAiLTnWJJu_cT8slCafOYwoA&s'),
('A0002', 'a0002@gmail.com', '00000000', 'Brosnan', 'https://goldenglobes.com/wp-content/uploads/2023/10/pierce-brosnan.jpg?w=600?w=600'),
('A0003', 'a0003@gmail.com', 'ABC12345', 'Bella', 'https://img.freepik.com/free-photo/portrait-young-businesswoman-holding-eyeglasses-hand-against-gray-backdrop_23-2148029483.jpg'),
('A0004', 'a0004@gmail.com', '87654321', 'Johnny', 'https://t4.ftcdn.net/jpg/02/24/86/95/360_F_224869519_aRaeLneqALfPNBzg0xxMZXghtvBXkfIA.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bodyweight`
--

CREATE TABLE `bodyweight` (
  `id` int(11) NOT NULL,
  `custId` varchar(15) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `recordDate` date DEFAULT current_timestamp(),
  `recordTime` time DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bodyweight`
--

INSERT INTO `bodyweight` (`id`, `custId`, `weight`, `recordDate`, `recordTime`) VALUES
(21, 'Yy123', 60.00, '2024-10-17', '09:00:00'),
(23, 'Yy123', 50.00, '2024-10-09', '02:00:00'),
(24, 'Yy123', 20.00, '2024-10-16', '00:03:00'),
(25, 'Yy123', 20.00, '2024-10-09', '04:00:00'),
(28, 'Yy123', 40.00, '2024-10-22', '10:00:00'),
(32, 'Yy123', 20.00, '2024-10-17', '11:50:00'),
(33, 'Yy123', 20.00, '2024-10-25', '12:00:00'),
(34, 'Yy123', 50.00, '2024-10-27', '03:00:00'),
(35, 'Yy123', 50.00, '2024-10-27', '18:04:00'),
(36, 'Yy123', 20.00, '2024-10-27', '18:13:00'),
(37, 'Cwhui', 40.00, '2024-10-23', '18:58:00'),
(38, 'Cwhui', 42.00, '2024-10-22', '18:58:00'),
(39, 'Cwhui', 43.00, '2024-10-24', '18:58:00'),
(40, 'Cwhui', 45.00, '2024-10-13', '22:58:00'),
(41, 'Esther123', 40.00, '2024-10-04', '19:06:00'),
(42, 'Esther123', 42.00, '2024-10-23', '19:20:00'),
(43, 'Esther123', 44.30, '2024-10-11', '22:06:00'),
(44, 'LXJ123', 45.00, '2024-10-28', '19:18:00'),
(45, 'LXJ123', 46.00, '2024-10-23', '14:00:00'),
(46, 'Jarett123', 45.00, '2024-10-28', '19:28:00'),
(47, 'Yy123', 40.00, '2024-09-20', '00:59:00'),
(48, 'Yy123', 42.00, '2024-09-28', '00:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `consultants`
--

CREATE TABLE `consultants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `background` text DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `philosophy` text DEFAULT NULL,
  `achievements` text DEFAULT NULL,
  `profile_image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultants`
--

INSERT INTO `consultants` (`id`, `name`, `position`, `rating`, `background`, `specialization`, `philosophy`, `achievements`, `profile_image_url`) VALUES
(1, 'John Martinez', 'Strength and Conditioning Coach', 4.80, 'John is an NSCA-certified strength coach with 12 years of experience working with athletes and individuals seeking to build muscle and increase strength.', 'Strength training, muscle hypertrophy, and sports performance.', 'John believes in pushing limits through well-structured, progressive resistance training while maintaining proper form and injury prevention.', 'He has worked with several professional athletes and helped them improve their performance across various sports.', 'https://bloximages.chicago2.vip.townnews.com/thesunchronicle.com/content/tncms/assets/v3/editorial/d/4e/d4e72599-3794-5920-90fd-089c038cf08e/55dd330173141.image.jpg?resize=367%2C500'),
(2, 'Emily Wong', 'Registered Dietitian Nutritionist', 4.90, 'Emily has a Master’s degree in Nutrition Science and is a registered dietitian specializing in weight management and sports nutrition.', 'Tailored meal planning, nutrition for fitness enthusiasts, and healthy weight loss.', 'Emily advocates for balanced, nutrient-dense eating to fuel both the body and mind, focusing on long-term wellness instead of restrictive diets.', 'She has helped hundreds of clients transition to healthier lifestyles through personalized nutrition plans.', 'https://github.com/cwhui1001/huan_image/blob/main/emilywong.png?raw=true'),
(3, 'Nurul Amira', 'Certified Yoga Instructor', 4.80, 'Nurul is a certified yoga instructor with 8 years of experience in teaching yoga for flexibility, stress management, and mindfulness.', 'Hatha Yoga, mindfulness, and stress relief through movement and breathing techniques.', 'Nurul believes in using yoga as a holistic tool to not only improve physical fitness but also cultivate mental well-being and balance.', 'She has conducted over 200 workshops worldwide focusing on yoga for mental health.', 'https://github.com/cwhui1001/huan_image/blob/main/nurul.png?raw=true'),
(4, 'Jessica Chen', 'Wellness and Mindset Coach', 4.90, 'Jessica is a certified life and wellness coach, specializing in helping clients achieve a balanced approach to fitness by focusing on both physical health and mental well-being.', 'Mindset coaching, holistic wellness, and mental health support through fitness.', 'Jessica believes that true health comes from a balanced lifestyle, which integrates fitness, mental resilience, and emotional wellness.', 'She has helped over 400 clients overcome mental barriers to fitness and build sustainable, healthy habits.', 'https://github.com/cwhui1001/huan_image/blob/main/jessica2.png?raw=true');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `custId` varchar(15) NOT NULL,
  `custName` varchar(70) DEFAULT NULL,
  `custBdate` date DEFAULT NULL,
  `custEmail` varchar(50) DEFAULT NULL,
  `custPsw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`custId`, `custName`, `custBdate`, `custEmail`, `custPsw`) VALUES
('Cwhui', 'Wenhui', '2024-10-28', '123@gmail.com', '$2y$10$cBnLjmU6fbwX46kJWF1ATuisCIu4y8q6aHWxUh1dBnEWC007g6VX2'),
('Esther123', 'EstherChan', '2000-05-20', '456@gmail.com', '$2y$10$qEr.HAppg.Qpvp9hDsSFDOh6u7iJO2BTkaTsbF2dfxPVCni7s2pk.'),
('Jarett123', 'Jarett ', '2002-10-01', 'jl123@gmail.com', '$2y$10$hmaV4Kyi60ZYA9G54W.QoOxqkO/hWtMgwMRjWvR0NH5SEsz2qEQ0e'),
('LXJ123', 'Xinjie', '2000-02-20', 'ghd@gmail.com', '$2y$10$Ld5ubNTe8ifKpl5NqxLXveKzjuG/Viuh9/n/s9aXN2p4E.q9WE2iS'),
('Yy123', 'Teoh Yan Ying', '2024-07-31', 'tyy0534@gmail.com', '$2y$10$4p3tJ8aQpDYSRRAUSPYrjejcfMjjLLGeCJtpLSaySd2Q1I/k8zf6G');

-- --------------------------------------------------------

--
-- Table structure for table `exerciseroutine`
--

CREATE TABLE `exerciseroutine` (
  `id` int(11) NOT NULL,
  `custId` varchar(15) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `recordDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exerciseroutine`
--

INSERT INTO `exerciseroutine` (`id`, `custId`, `category`, `duration`, `recordDate`) VALUES
(12, 'Yy123', 'Jogging', 50, '2024-10-03'),
(13, 'Yy123', 'Strength Training', 10, '2024-10-01'),
(20, 'Yy123', 'Yoga', 45, '2024-10-26'),
(21, 'Yy123', 'Dancing', 20, '2024-10-26'),
(22, 'Yy123', 'Swimming', 20, '2024-10-31'),
(23, 'Yy123', 'Swimming', 20, '2024-10-17'),
(24, 'Yy123', 'Running', 60, '2024-10-17'),
(26, 'Yy123', 'Strength Training', 50, '2024-10-31'),
(27, 'Yy123', 'Pilates', 60, '2024-10-30'),
(28, 'Yy123', 'Running', 30, '2024-10-27'),
(30, 'Yy123', 'Strength Training', 40, '2024-10-27'),
(31, 'Yy123', 'Running', 40, '2024-10-27'),
(32, 'Yy123', 'Running', 10, '2024-10-27'),
(33, 'Cwhui', 'Running', 50, '2024-10-28'),
(34, 'Cwhui', 'Running', 30, '2024-10-28'),
(35, 'Cwhui', 'Walking', 30, '2024-10-28'),
(36, 'Cwhui', 'Yoga', 30, '2024-10-02'),
(37, 'Esther123', 'Jogging', 40, '2024-10-28'),
(38, 'Esther123', 'Walking', 20, '2024-10-02'),
(39, 'Esther123', 'Cycling', 30, '2024-10-08'),
(40, 'Esther123', 'Dancing', 120, '2024-09-18'),
(41, 'LXJ123', 'Running', 40, '2024-10-28'),
(42, 'LXJ123', 'Walking', 50, '2024-09-02'),
(43, 'LXJ123', 'Jogging', 120, '2024-10-20'),
(44, 'Jarett123', 'Running', 50, '2024-10-28'),
(45, 'Yy123', 'Swimming', 50, '2024-10-28'),
(46, 'Yy123', 'Running', 40, '2024-09-19'),
(47, 'Yy123', 'Yoga', 30, '2024-09-28');

-- --------------------------------------------------------

--
-- Table structure for table `fitnessclass`
--

CREATE TABLE `fitnessclass` (
  `id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `spots_available` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitnessclass`
--

INSERT INTO `fitnessclass` (`id`, `class_name`, `image_url`, `benefits`, `description`, `schedule`, `location`, `spots_available`) VALUES
(1, 'Yoga Class', 'https://artimg.gympik.com/articles/wp-content/uploads/2019/10/Featured3.jpg', 'Improve flexibility and posture, Reduce stress and anxiety, Strengthen your core', 'This class focuses on flexibility and mindfulness, combining breath work with poses that will help you relax and strengthen your body.', 'Monday & Wednesday, 7:00 PM - 8:00 PM', 'Online via Zoom / In-person at XYZ Studio', 20),
(3, 'Gym Class', 'https://olympiafitnessri.com/wp-content/uploads/2021/03/People-doing-pushups-together-in-a-health-club-class-853407238_5976x3984-scaled-e1615396175580.jpeg', 'Build muscle strength and endurance, Enhance cardiovascular fitness, Boost energy and stamina', 'Join our gym class to boost your strength and endurance. This high-energy workout combines strength ', ' Tuesday & Thursday, 6:00 PM - 7:00 PM', 'Online via Zoom / In-person at ABC Gym', 15);

-- --------------------------------------------------------

--
-- Table structure for table `fitnessclassmember`
--

CREATE TABLE `fitnessclassmember` (
  `memberID` int(11) NOT NULL,
  `custID` varchar(15) NOT NULL,
  `paymentDuration` varchar(10) NOT NULL,
  `amountPaid` int(5) NOT NULL,
  `fitnessType` varchar(20) NOT NULL,
  `joinDate` date NOT NULL DEFAULT curdate(),
  `membershipExpiryDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitnessclassmember`
--

INSERT INTO `fitnessclassmember` (`memberID`, `custID`, `paymentDuration`, `amountPaid`, `fitnessType`, `joinDate`, `membershipExpiryDate`) VALUES
(19, 'Yy123', '6', 240, 'gym', '2024-10-27', '2025-04-27'),
(20, 'Yy123', '3', 135, 'gym', '2024-10-28', '2025-01-27'),
(21, 'Yy123', '6', 240, 'yoga', '2024-10-28', '2025-04-28');

-- --------------------------------------------------------

--
-- Table structure for table `userrequest`
--

CREATE TABLE `userrequest` (
  `requestId` int(4) NOT NULL,
  `custID` varchar(15) NOT NULL,
  `requestdate` date NOT NULL DEFAULT current_timestamp(),
  `predate` date NOT NULL,
  `pretime` time NOT NULL,
  `nutritionistName` varchar(255) NOT NULL,
  `remarks` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT '"Pending"',
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userrequest`
--

INSERT INTO `userrequest` (`requestId`, `custID`, `requestdate`, `predate`, `pretime`, `nutritionistName`, `remarks`, `status`, `reason`) VALUES
(32, 'Yy123', '2024-10-25', '2024-10-28', '08:30:00', 'Nurul Amira', 'I dont know', '\"Pending\"', ''),
(47, 'yy123', '2024-10-27', '2024-10-29', '04:03:00', 'Jessica Chen', 'none', '\"Pending\"', ''),
(48, 'yy123', '2024-10-27', '2024-10-31', '16:06:00', 'Emily Wong', '?', '\"Success\"', ''),
(49, 'Yy123', '2024-10-27', '2024-10-30', '11:30:00', 'John Martinez', 'hi', '\"Reject\"', 'nutritionist unavailable'),
(52, 'Cwhui', '2024-10-28', '2024-10-30', '13:00:00', 'Emily Wong', '', '\"Pending\"', ''),
(53, 'Esther123', '2024-10-28', '2024-10-31', '14:00:00', 'Emily Wong', 'no', '\"Pending\"', ''),
(54, 'LXJ123', '2024-10-28', '2024-11-27', '13:00:00', 'Nurul Amiral', '', '\"Pending\"', ''),
(55, 'Jarett123', '2024-10-28', '2024-10-31', '15:00:00', 'John Martinez', 'none', '\"Reject\"', 'payment unsuccess'),
(56, 'yy123', '2024-10-29', '2024-10-30', '15:42:00', 'Emily Wong', '', '\"Pending\"', '');

-- --------------------------------------------------------

--
-- Table structure for table `waterconsumption`
--

CREATE TABLE `waterconsumption` (
  `id` int(11) NOT NULL,
  `custId` varchar(15) DEFAULT NULL,
  `drinkingTime` time DEFAULT NULL,
  `drinkingDate` date DEFAULT NULL,
  `waterIntake` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waterconsumption`
--

INSERT INTO `waterconsumption` (`id`, `custId`, `drinkingTime`, `drinkingDate`, `waterIntake`) VALUES
(54, 'Yy123', '16:23:00', '2024-10-27', '1000'),
(55, 'Yy123', '18:41:00', '2024-10-28', '2000'),
(56, 'Yy123', '18:41:00', '2024-10-28', '1000'),
(57, 'Yy123', '18:41:00', '2024-10-22', '1000'),
(58, 'Cwhui', '18:57:00', '2024-10-28', '500'),
(59, 'Cwhui', '18:57:00', '2024-10-14', '1000'),
(60, 'Esther123', '19:05:00', '2024-10-28', '1000'),
(61, 'Esther123', '19:05:00', '2024-10-15', '2000'),
(62, 'LXJ123', '19:19:00', '2024-10-28', '1500'),
(63, 'LXJ123', '14:00:00', '2024-10-15', '1000'),
(64, 'Jarett123', '19:28:00', '2024-10-28', '1500');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminId`);

--
-- Indexes for table `bodyweight`
--
ALTER TABLE `bodyweight`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bodyWeight_cust` (`custId`) USING BTREE;

--
-- Indexes for table `consultants`
--
ALTER TABLE `consultants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`custId`);

--
-- Indexes for table `exerciseroutine`
--
ALTER TABLE `exerciseroutine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_cust` (`custId`) USING BTREE;

--
-- Indexes for table `fitnessclass`
--
ALTER TABLE `fitnessclass`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fitnessclassmember`
--
ALTER TABLE `fitnessclassmember`
  ADD PRIMARY KEY (`memberID`);

--
-- Indexes for table `userrequest`
--
ALTER TABLE `userrequest`
  ADD PRIMARY KEY (`requestId`),
  ADD KEY `userrequest_cust` (`custID`) USING BTREE;

--
-- Indexes for table `waterconsumption`
--
ALTER TABLE `waterconsumption`
  ADD PRIMARY KEY (`id`),
  ADD KEY `waterCons_cust` (`custId`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bodyweight`
--
ALTER TABLE `bodyweight`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `consultants`
--
ALTER TABLE `consultants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exerciseroutine`
--
ALTER TABLE `exerciseroutine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `fitnessclass`
--
ALTER TABLE `fitnessclass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fitnessclassmember`
--
ALTER TABLE `fitnessclassmember`
  MODIFY `memberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `userrequest`
--
ALTER TABLE `userrequest`
  MODIFY `requestId` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `waterconsumption`
--
ALTER TABLE `waterconsumption`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `userrequest`
--
ALTER TABLE `userrequest`
  ADD CONSTRAINT `userrequest_cust` FOREIGN KEY (`custID`) REFERENCES `customer` (`custId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
