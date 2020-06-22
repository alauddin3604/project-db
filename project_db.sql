-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2020 at 03:02 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(4) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_password` varchar(128) NOT NULL,
  `log_status` tinyint(1) NOT NULL COMMENT 'false means the user doesn''t login for the first time.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_password`, `log_status`) VALUES
(1001, 'MUHAMMAD ALAUDDIN', '$2y$10$sOf34WkADDLWTwFbEsBg2OwXPVnarUmZdUBX6L/r7muW9487nBHvK', 1),
(1322, 'FARIDAH SUPANJI', '$2y$10$hSiU1bds7ImDXHhz7oA8weR3v.qGZhN6RNVEkgKeOtBKS5kDtKVy6', 1),
(2039, 'UMMU ISMAIL', '$2y$10$c5Zp/KsfKpvpmXutvIXCLeLddNxHiPQG6Tx51H93XZ5GsDa7zd/Me', 1);

-- --------------------------------------------------------

--
-- Table structure for table `adm_lect`
--

CREATE TABLE `adm_lect` (
  `admin_id` int(4) NOT NULL,
  `lecturer_id` int(4) NOT NULL,
  `modified_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `adm_lect`
--

INSERT INTO `adm_lect` (`admin_id`, `lecturer_id`, `modified_on`) VALUES
(1001, 783, '2020-04-07 18:19:21'),
(1322, 1822, '2020-06-21 22:15:46'),
(1322, 2266, '2020-06-21 22:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `adm_stud`
--

CREATE TABLE `adm_stud` (
  `admin_id` int(4) NOT NULL,
  `student_id` varchar(8) NOT NULL,
  `modified_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `adm_stud`
--

INSERT INTO `adm_stud` (`admin_id`, `student_id`, `modified_on`) VALUES
(1001, 'AI160085', '2020-06-20 11:56:47'),
(1001, 'AI160168', '2020-06-20 11:57:33'),
(1001, 'AI160171', '2020-06-10 18:36:17'),
(1001, 'AI160190', '2020-06-20 11:58:16'),
(1001, 'AI160191', '2020-06-20 11:57:53'),
(1001, 'CI190043', '2020-06-19 14:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `adm_sub`
--

CREATE TABLE `adm_sub` (
  `admin_id` int(4) NOT NULL,
  `subject_code` varchar(8) NOT NULL,
  `modified_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `adm_sub`
--

INSERT INTO `adm_sub` (`admin_id`, `subject_code`, `modified_on`) VALUES
(1001, 'BIC10303', '2020-04-14 00:02:35'),
(1001, 'BIS20404', '2020-06-19 19:15:22'),
(1001, 'BIS33404', '2020-04-12 09:23:44'),
(1322, 'BIS20904', '2020-06-21 22:55:16');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_id` int(4) NOT NULL,
  `lecturer_name` varchar(100) NOT NULL,
  `lecturer_password` varchar(128) NOT NULL,
  `log_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `lecturer_name`, `lecturer_password`, `log_status`) VALUES
(783, 'NURHANIFAH MURLI', '$2y$10$EnvjrF7O1PVYwURlvO4xoeSSDPqwL73smfPfqOv/pr6cto6vwF6xy', 1),
(1822, 'CHUAH CHAI WEN', '$2y$10$UancKFtMLQAtbeu6szYVUe055iq8g/jgDiJB3VnyNf9fvUh48aQpG', 1),
(2266, 'SHAHREEN KASSIM', '$2y$10$h.FDycYHyzS.d5HVL..1WuZdZmcj/aVcO5LtE1omMlmQspwQgGpAu', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mark_objective`
--

CREATE TABLE `mark_objective` (
  `id` int(11) NOT NULL,
  `student_id` varchar(8) NOT NULL,
  `workload_id` int(11) NOT NULL,
  `mark` int(3) NOT NULL,
  `date_submit` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mark_objective`
--

INSERT INTO `mark_objective` (`id`, `student_id`, `workload_id`, `mark`, `date_submit`) VALUES
(1, 'CI190043', 10, 4, '2020-06-20 03:11:43');

-- --------------------------------------------------------

--
-- Table structure for table `mark_truefalse`
--

CREATE TABLE `mark_truefalse` (
  `id` int(11) NOT NULL,
  `student_id` varchar(8) NOT NULL,
  `workload_id` int(11) NOT NULL,
  `mark` int(3) NOT NULL,
  `date_submit` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mark_truefalse`
--

INSERT INTO `mark_truefalse` (`id`, `student_id`, `workload_id`, `mark`, `date_submit`) VALUES
(5, 'CI190043', 5, 2, '2020-06-19 14:07:53'),
(6, 'CI190043', 6, 4, '2020-06-19 17:21:37'),
(7, 'CI190043', 7, 2, '2020-06-19 17:25:10'),
(8, 'CI190043', 10, 8, '2020-06-20 12:16:57'),
(9, 'AI160171', 5, 6, '2020-06-21 17:21:10'),
(10, 'AI160171', 10, 0, '2020-06-21 17:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_objective`
--

CREATE TABLE `quiz_objective` (
  `id` int(11) NOT NULL,
  `question` varchar(300) NOT NULL,
  `option_a` varchar(256) NOT NULL,
  `option_b` varchar(256) NOT NULL,
  `option_c` varchar(256) NOT NULL,
  `option_d` varchar(256) NOT NULL,
  `answer` varchar(1) NOT NULL,
  `workload_id` int(11) NOT NULL,
  `modified_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz_objective`
--

INSERT INTO `quiz_objective` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `answer`, `workload_id`, `modified_on`) VALUES
(1, 'The equation a equivalent to b (mod n) means', 'a + kb = n for some integer k', 'a - kb = n for some integer k', 'a + b = kn for some integer k', 'a - b = kn for some integer k', 'D', 10, '2020-06-19 20:12:18'),
(2, 'If a = 7, then an inverse mod 17 is', '20', '7', '1', '5', 'D', 10, '2020-06-20 07:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_truefalse`
--

CREATE TABLE `quiz_truefalse` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` tinyint(1) NOT NULL,
  `modified_on` datetime NOT NULL,
  `workload_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz_truefalse`
--

INSERT INTO `quiz_truefalse` (`id`, `question`, `answer`, `modified_on`, `workload_id`) VALUES
(5, 'DFD', 1, '2020-06-18 11:13:14', 5),
(6, 'kok', 0, '2020-06-18 11:13:22', 5),
(7, 'sssaww', 1, '2020-06-19 01:01:53', 5),
(8, 'Mak kau hijau?', 0, '2020-06-19 19:07:45', 6),
(9, 'Hhuhe', 1, '2020-06-19 11:21:14', 6),
(10, 'Huhheeee', 1, '2020-06-19 11:24:53', 7),
(11, 'xcascascascacas', 0, '2020-06-19 20:10:12', 6),
(12, 'Based on current technology, it is possible to apply quantum cryptography between UTHM Parit Raja and UTHM at Pagoh.', 0, '2020-06-20 12:12:08', 10),
(13, 'Caesar cipher does not satisfy Kerchhoff\'s principle', 1, '2020-06-20 12:14:31', 10),
(14, 'Data compression can decreased the redundancy of the language', 1, '2020-06-20 12:15:18', 10),
(15, 'DES has 128-key bit and 128-key block', 0, '2020-06-20 12:16:31', 10);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(8) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_email` varchar(254) NOT NULL,
  `student_password` varchar(128) NOT NULL,
  `log_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `student_email`, `student_password`, `log_status`) VALUES
('AI160085', 'ABDUL RAHMAN BIN AMZAL', 'AI160085@siswa.uthm.edu.my', '$2y$10$YFh6pUrjjCg1HQAnw71wne5Lpcm5YtmahJWBs146ZtLycXAo/7sQq', 0),
('AI160168', 'AHMAD KAMIL BIN KAMARUDDIN MALIK', 'AI160168@siswa.uthm.edu.my', '$2y$10$EDuPkpB/Xa3EziwiZ2BKx.azbFnAeqr87K5k.xm2Enba/dhBHqSKy', 0),
('AI160171', 'MARPUAH', 'AI160171@siswa.uthm.edu.my', '$2y$10$iJOo/Uk8ZVcxSnKY6c0st.B3wL5NRUasGNZH6ohDshdmxrOO3AKam', 1),
('AI160190', 'CHAN WEI LIANG', 'AI160190@siswa.uthm.edu.my', '$2y$10$Keq8UCTVERROXF5NZCUyRehm3QJ9Ifl1TPMnbI8g2uvC/PXqDkgnG', 0),
('AI160191', 'CHAI CHEEN SHUN', 'AI160191@siswa.uthm.edu.my', '$2y$10$y8Gu4gitW2NSVN8.UxMn5.e/MbC9XH7J4nSlVLHCy1t9TIFVZ5gX.', 0),
('CI190043', 'MUHAMMAD ALAUDDIN SHAH', 'CI190043@siswa.uthm.edu.my', '$2y$10$zqVBS0gd92p9zRp0cOqAj.i6SIB.AqdEM7wmZMApsDFdT.wEspgOS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stud_sub`
--

CREATE TABLE `stud_sub` (
  `stud_sub_id` int(11) NOT NULL,
  `student_id` varchar(8) NOT NULL,
  `workload_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stud_sub`
--

INSERT INTO `stud_sub` (`stud_sub_id`, `student_id`, `workload_id`) VALUES
(1, 'AI160171', 5),
(3, 'AI160171', 6),
(4, 'CI190043', 5),
(5, 'CI190043', 6),
(6, 'CI190043', 7),
(7, 'CI190043', 10),
(8, 'AI160171', 10);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_code` varchar(8) NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_code`, `subject_name`) VALUES
('BIC10303', 'ALGEBRA'),
('BIS20404', 'CRYPTOGRAPHY'),
('BIS20904', 'OBJECT ORIENTED PROGRAMMING'),
('BIS33404', 'SPECIAL TOPIC OF INFORMATION SECURITY');

-- --------------------------------------------------------

--
-- Table structure for table `workloads`
--

CREATE TABLE `workloads` (
  `workload_id` int(11) NOT NULL,
  `lecturer_id` int(4) NOT NULL,
  `subject_code` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `workloads`
--

INSERT INTO `workloads` (`workload_id`, `lecturer_id`, `subject_code`) VALUES
(5, 1822, 'BIS20904'),
(6, 1822, 'BIC10303'),
(7, 783, 'BIS33404'),
(9, 783, 'BIC10303'),
(10, 1822, 'BIS20404');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `adm_lect`
--
ALTER TABLE `adm_lect`
  ADD PRIMARY KEY (`admin_id`,`lecturer_id`),
  ADD KEY `Adm_ID` (`admin_id`,`lecturer_id`),
  ADD KEY `Lect_FK` (`lecturer_id`);

--
-- Indexes for table `adm_stud`
--
ALTER TABLE `adm_stud`
  ADD PRIMARY KEY (`admin_id`,`student_id`),
  ADD KEY `admin_id` (`admin_id`) USING BTREE,
  ADD KEY `student_id` (`student_id`) USING BTREE;

--
-- Indexes for table `adm_sub`
--
ALTER TABLE `adm_sub`
  ADD PRIMARY KEY (`admin_id`,`subject_code`),
  ADD KEY `Adm_ID` (`admin_id`,`subject_code`),
  ADD KEY `adm_sub_ibfk_2` (`subject_code`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`);

--
-- Indexes for table `mark_objective`
--
ALTER TABLE `mark_objective`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `workload_id` (`workload_id`);

--
-- Indexes for table `mark_truefalse`
--
ALTER TABLE `mark_truefalse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `workload_id` (`workload_id`);

--
-- Indexes for table `quiz_objective`
--
ALTER TABLE `quiz_objective`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workload_id` (`workload_id`);

--
-- Indexes for table `quiz_truefalse`
--
ALTER TABLE `quiz_truefalse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workload_id` (`workload_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `stud_sub`
--
ALTER TABLE `stud_sub`
  ADD PRIMARY KEY (`stud_sub_id`),
  ADD KEY `workload_id` (`workload_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_code`) USING BTREE;

--
-- Indexes for table `workloads`
--
ALTER TABLE `workloads`
  ADD PRIMARY KEY (`workload_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `subject_code` (`subject_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mark_objective`
--
ALTER TABLE `mark_objective`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mark_truefalse`
--
ALTER TABLE `mark_truefalse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_objective`
--
ALTER TABLE `quiz_objective`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quiz_truefalse`
--
ALTER TABLE `quiz_truefalse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stud_sub`
--
ALTER TABLE `stud_sub`
  MODIFY `stud_sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `workloads`
--
ALTER TABLE `workloads`
  MODIFY `workload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adm_lect`
--
ALTER TABLE `adm_lect`
  ADD CONSTRAINT `Admin_Lect_FK` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Lect_FK` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adm_stud`
--
ALTER TABLE `adm_stud`
  ADD CONSTRAINT `adm_stud_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adm_stud_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adm_sub`
--
ALTER TABLE `adm_sub`
  ADD CONSTRAINT `adm_sub_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adm_sub_ibfk_2` FOREIGN KEY (`subject_code`) REFERENCES `subjects` (`subject_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mark_objective`
--
ALTER TABLE `mark_objective`
  ADD CONSTRAINT `mark_objective_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mark_objective_ibfk_2` FOREIGN KEY (`workload_id`) REFERENCES `workloads` (`workload_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mark_truefalse`
--
ALTER TABLE `mark_truefalse`
  ADD CONSTRAINT `mark_truefalse_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mark_truefalse_ibfk_2` FOREIGN KEY (`workload_id`) REFERENCES `workloads` (`workload_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz_objective`
--
ALTER TABLE `quiz_objective`
  ADD CONSTRAINT `quiz_objective_ibfk_1` FOREIGN KEY (`workload_id`) REFERENCES `workloads` (`workload_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz_truefalse`
--
ALTER TABLE `quiz_truefalse`
  ADD CONSTRAINT `quiz_truefalse_ibfk_2` FOREIGN KEY (`workload_id`) REFERENCES `workloads` (`workload_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stud_sub`
--
ALTER TABLE `stud_sub`
  ADD CONSTRAINT `stud_sub_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stud_sub_ibfk_2` FOREIGN KEY (`workload_id`) REFERENCES `workloads` (`workload_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workloads`
--
ALTER TABLE `workloads`
  ADD CONSTRAINT `workloads_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workloads_ibfk_2` FOREIGN KEY (`subject_code`) REFERENCES `subjects` (`subject_code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
