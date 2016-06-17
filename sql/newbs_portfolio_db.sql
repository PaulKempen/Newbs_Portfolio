-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2016 at 11:28 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newbs_portfolio`
--
CREATE DATABASE IF NOT EXISTS `newbs_portfolio` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `newbs_portfolio`;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `faq_id` varchar(50) NOT NULL,
  `question` varchar(1000) NOT NULL,
  `answer` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`faq_id`, `question`, `answer`) VALUES
('3E0F6E25-84C2-45B9-82AC-07CD823E7534', 'What is bacon ipsum?', 'Bacon ipsum dolor amet shank strip steak jerky turducken capicola ham. Cupim turkey ham jowl cow. Leberkas pork chop shoulder spare ribs bacon, filet mignon strip steak doner ham pastrami tri-tip jerky cupim pig shankle. Pancetta ham hock pork pork belly biltong short ribs venison kevin.\n\nFlank picanha turkey capicola, pancetta meatloaf bacon tail shankle ground round chuck brisket bresaola. Landjaeger filet mignon pork sausage. Pork leberkas sirloin prosciutto chicken bresaola, bacon doner tongue tenderloin beef alcatra jerky tail frankfurter. Bacon kevin tri-tip jowl frankfurter pancetta ball tip, drumstick tail salami fatback jerky tongue t-bone chuck. Bresaola cow pork sausage chicken drumstick chuck meatball, tri-tip landjaeger pancetta salami capicola. Kevin alcatra salami meatloaf beef ribs shoulder ribeye cow ground round t-bone frankfurter ham. Leberkas landjaeger shoulder venison.\n\nBeef pork flank pork belly tri-tip. Pancetta alcatra hamburger ribeye boudin ground round swine t-bone fatback. Cupim swine capicola ground round tongue corned beef shoulder, frankfurter pig fatback pork belly. Pork chop brisket spare ribs, ham hock salami biltong flank.\n\nPork loin spare ribs bacon t-bone pig, pastrami picanha doner pork belly tongue meatloaf. Fatback kielbasa leberkas spare ribs bacon ribeye. Ribeye bresaola swine ground round hamburger doner pork loin shankle frankfurter shoulder cupim pork. Hamburger ball tip filet mignon pork chop boudin, cupim tongue fatback.'),
('B017DD9A-39EA-4AB4-9', 'Why do we need FAQ', 'To answer all the questions that ever need to be asked about anything and everything'),
('F3DB6AF4-3419-4138-988D-143D2D8F5B0F', 'The question that needs not to be asked', 'the answer that needs not the be answered about the question that needs not to be asked');

-- --------------------------------------------------------

--
-- Table structure for table `focus_areas`
--

CREATE TABLE IF NOT EXISTS `focus_areas` (
  `focus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `focus_areas`
--

INSERT INTO `focus_areas` (`focus`) VALUES
('Leadership'),
('Network Security'),
('Networking'),
('Programming'),
('Project Management'),
('Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `user_id` varchar(50) NOT NULL,
  `user_name` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(15) NOT NULL,
  `change_pw` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`user_id`, `user_name`, `password`, `role`, `change_pw`) VALUES
('6FA78A97-33A5-47EB-8D6E-6868C981DEA5', 'siteAdmin', '$2y$10$kv69i4Shlg8bJe3azETKIOZSOw.H05x8ZceZfNhln5wJO8i/s6TRW', 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `member_info`
--

CREATE TABLE IF NOT EXISTS `member_info` (
  `user_id` varchar(50) NOT NULL,
  `first` varchar(25) DEFAULT NULL,
  `mi` char(1) DEFAULT NULL,
  `last` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_info`
--

INSERT INTO `member_info` (`user_id`, `first`, `mi`, `last`, `email`, `phone`) VALUES
('6FA78A97-33A5-47EB-8D6E-6868C981DEA5', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `member_pics`
--

CREATE TABLE IF NOT EXISTS `member_pics` (
  `user_id` varchar(50) NOT NULL,
  `saved_name` varchar(50) DEFAULT NULL,
  `uploaded_name` varchar(50) DEFAULT NULL,
  `extension` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `past_work`
--

CREATE TABLE IF NOT EXISTS `past_work` (
  `work_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `link` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_data`
--

CREATE TABLE IF NOT EXISTS `portfolio_data` (
  `user_id` varchar(50) NOT NULL,
  `short_desc` varchar(75) DEFAULT NULL,
  `focus` varchar(300) DEFAULT NULL,
  `about_me` varchar(10000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE IF NOT EXISTS `resumes` (
  `user_id` varchar(50) NOT NULL,
  `education` varchar(1000) DEFAULT NULL,
  `employment` varchar(1000) DEFAULT NULL,
  `awards` varchar(1000) DEFAULT NULL,
  `interests` varchar(1000) DEFAULT NULL,
  `skills` varchar(1000) DEFAULT NULL,
  `certs` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role`) VALUES
('Admin'),
('Member');

-- --------------------------------------------------------

--
-- Table structure for table `site_colors`
--

CREATE TABLE IF NOT EXISTS `site_colors` (
  `profile` varchar(30) NOT NULL,
  `bg1` varchar(6) NOT NULL COMMENT 'hex value',
  `bg2` varchar(6) NOT NULL COMMENT 'hex value',
  `bg3` varchar(6) NOT NULL COMMENT 'hex value',
  `bg4` varchar(6) NOT NULL COMMENT 'hex value',
  `bg5` varchar(6) NOT NULL COMMENT 'hex value',
  `bg6` varchar(6) NOT NULL COMMENT 'hex value',
  `bg7` varchar(6) NOT NULL COMMENT 'hex value',
  `bg8` varchar(6) NOT NULL COMMENT 'hex value',
  `bg9` varchar(6) NOT NULL COMMENT 'hex value',
  `bg10` varchar(6) NOT NULL COMMENT 'hex value',
  `bg11` varchar(6) NOT NULL COMMENT 'hex value',
  `bg12` varchar(6) NOT NULL COMMENT 'hex value',
  `bg13` varchar(6) NOT NULL COMMENT 'hex value',
  `tx1` varchar(6) NOT NULL COMMENT 'hex value',
  `tx2` varchar(6) NOT NULL COMMENT 'hex value',
  `tx3` varchar(6) NOT NULL COMMENT 'hex value',
  `tx4` varchar(6) NOT NULL COMMENT 'hex value',
  `tx5` varchar(6) NOT NULL COMMENT 'hex value',
  `tx6` varchar(6) NOT NULL COMMENT 'hex value',
  `tx7` varchar(6) NOT NULL COMMENT 'hex value',
  `tx8` varchar(6) NOT NULL COMMENT 'hex value',
  `tx9` varchar(6) NOT NULL COMMENT 'hex value',
  `tx10` varchar(6) NOT NULL COMMENT 'hex value',
  `tx11` varchar(6) NOT NULL COMMENT 'hex value',
  `tx12` varchar(6) NOT NULL COMMENT 'hex value',
  `tx13` varchar(6) NOT NULL COMMENT 'hex value',
  `tx14` varchar(6) NOT NULL COMMENT 'hex value',
  `tx15` varchar(6) NOT NULL COMMENT 'hex value',
  `tx16` varchar(6) NOT NULL COMMENT 'hex value',
  `tx17` varchar(6) NOT NULL COMMENT 'hex value',
  `tx18` varchar(6) NOT NULL COMMENT 'hex value',
  `tx19` varchar(6) NOT NULL COMMENT 'hex value',
  `sh1` varchar(6) NOT NULL COMMENT 'hex value',
  `sh2` varchar(6) NOT NULL COMMENT 'hex value',
  `bd1` varchar(6) NOT NULL COMMENT 'hex value',
  `bd2` varchar(6) NOT NULL COMMENT 'hex value'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site_colors`
--

INSERT INTO `site_colors` (`profile`, `bg1`, `bg2`, `bg3`, `bg4`, `bg5`, `bg6`, `bg7`, `bg8`, `bg9`, `bg10`, `bg11`, `bg12`, `bg13`, `tx1`, `tx2`, `tx3`, `tx4`, `tx5`, `tx6`, `tx7`, `tx8`, `tx9`, `tx10`, `tx11`, `tx12`, `tx13`, `tx14`, `tx15`, `tx16`, `tx17`, `tx18`, `tx19`, `sh1`, `sh2`, `bd1`, `bd2`) VALUES
('Blue Gray', 'BFDCEC', '087EA7', '087EA7', '3C3733', 'BFDCEC', 'F5F5F5', '087EA7', '345C82', '2989D8', 'FFFFFF', '1E5799', '2989D8', '3C3733', '202020', '202020', 'FFFFFF', 'FFFFFF', '087EA7', '345C82', '505050', 'FFFFFF', 'FFFFFF', '000000', '087EA7', '345C82', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', '087EA7', '345C82', '202020', '202020', '345C82', '000000'),
('BlueMinimal', '595959', '087EA7', '087EA7', 'BFDCEC', 'BFDCEC', 'F5F5F5', '595959', '087EA7', '2989D8', 'FFFFFF', '1E5799', '2989D8', '595959', '202020', '202020', 'FFFFFF', 'FFFFFF', '087EA7', '345C82', '303030', 'FFFFFF', 'FFFFFF', '000000', '087EA7', '345C82', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', '087EA7', '345C82', '202020', '202020', 'BFDCEC', '000000'),
('Gray Scale', 'C8C8C8', '000000', '000000', '000000', 'C8C8C8', 'FFFFFF', 'C8C8C8', '000000', 'C8C8C8', 'FFFFFF', '646464', 'C8C8C8', '000000', '282828', '282828', 'FFFFFF', 'FFFFFF', '646464', '000000', '000000', 'FFFFFF', 'FFFFFF', '000000', '646464', '000000', '000000', 'FFFFFF', '000000', '000000', 'FFFFFF', '646464', 'C8C8C8', 'FFFFFF', '000000', '646464', 'FFFFFF'),
('Plum Crazy', 'DAC9AE', '4E0159', '4E0159', '4E0159', 'DAC9AE', 'F8E4E1', '912E91', '4E0159', '912E91', 'F8E4E1', '4E0159', '912E91', '4E0159', '300E2D', '300E2D', 'FFFFFF', 'FFFFFF', 'FF00DC', '912E91', '505050', 'FFFFFF', 'FFFFFF', '000000', 'FF00DC', '912E91', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FFFFFF', 'FF00DC', '912E91', '300E2D', '300E2D', 'B69D9D', '000000');

-- --------------------------------------------------------

--
-- Table structure for table `site_index`
--

CREATE TABLE IF NOT EXISTS `site_index` (
  `index_id` varchar(100) NOT NULL,
  `entry` varchar(10000) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `category` varchar(30) NOT NULL,
  `extra` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_text`
--

CREATE TABLE IF NOT EXISTS `site_text` (
  `site_id` int(10) NOT NULL,
  `about` varchar(10000) NOT NULL,
  `footer` varchar(500) NOT NULL,
  `copyright` varchar(100) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `current_profile` varchar(30) NOT NULL,
  `contact` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site_text`
--

INSERT INTO `site_text` (`site_id`, `about`, `footer`, `copyright`, `group_name`, `title`, `current_profile`, `contact`) VALUES
(1, 'Your "about" text here', 'Created by Newbs Unit''dÂ Â <span class="glyphicon glyphicon-star-empty"></span> Your footer text hereÂ <span class="glyphicon glyphicon-star-empty"></span>', 'Copyright &copy; 2016 Newbs Unit&#39;d. All rights reserved.', 'Your Group Name Here', 'Portfolio Website', 'Blue Gray', 'Your (admin/group leader)<br/>\nContact<br/>\nInformation<br/>\nHere<br/>\n');

-- --------------------------------------------------------

--
-- Table structure for table `work_pics`
--

CREATE TABLE IF NOT EXISTS `work_pics` (
  `work_id` varchar(50) NOT NULL,
  `saved_name` varchar(50) DEFAULT NULL,
  `uploaded_name` varchar(50) DEFAULT NULL,
  `extension` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `focus_areas`
--
ALTER TABLE `focus_areas`
  ADD PRIMARY KEY (`focus`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD KEY `role_index` (`role`) USING BTREE;

--
-- Indexes for table `member_info`
--
ALTER TABLE `member_info`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `member_pics`
--
ALTER TABLE `member_pics`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `pic_user_index` (`user_id`) USING BTREE;

--
-- Indexes for table `past_work`
--
ALTER TABLE `past_work`
  ADD PRIMARY KEY (`work_id`),
  ADD KEY `skill_user_index` (`user_id`) USING BTREE;

--
-- Indexes for table `portfolio_data`
--
ALTER TABLE `portfolio_data`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role`);

--
-- Indexes for table `site_colors`
--
ALTER TABLE `site_colors`
  ADD PRIMARY KEY (`profile`);

--
-- Indexes for table `site_index`
--
ALTER TABLE `site_index`
  ADD PRIMARY KEY (`index_id`),
  ADD KEY `user_site_index` (`user_id`);

--
-- Indexes for table `site_text`
--
ALTER TABLE `site_text`
  ADD PRIMARY KEY (`site_id`),
  ADD KEY `color_set_index` (`current_profile`);

--
-- Indexes for table `work_pics`
--
ALTER TABLE `work_pics`
  ADD PRIMARY KEY (`work_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_role_fk` FOREIGN KEY (`role`) REFERENCES `roles` (`role`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `member_info`
--
ALTER TABLE `member_info`
  ADD CONSTRAINT `fk_mem_inf_user_id` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `member_pics`
--
ALTER TABLE `member_pics`
  ADD CONSTRAINT `fk_mem_pic_user_id` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `past_work`
--
ALTER TABLE `past_work`
  ADD CONSTRAINT `fk_skill_user_id` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `portfolio_data`
--
ALTER TABLE `portfolio_data`
  ADD CONSTRAINT `fk_mem_dat_user_id` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `fk_resume_user_id` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `site_index`
--
ALTER TABLE `site_index`
  ADD CONSTRAINT `fk_user_site_index` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `work_pics`
--
ALTER TABLE `work_pics`
  ADD CONSTRAINT `fk_skill_pic_id` FOREIGN KEY (`work_id`) REFERENCES `past_work` (`work_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
