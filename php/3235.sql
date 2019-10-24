-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2019 at 10:07 AM
-- Server version: 10.3.18-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `privatepar_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `hibp_data`
--

CREATE TABLE `hibp_data` (
  `hibp_data_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `email` varchar(96) NOT NULL,
  `data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `instagram_data`
--

CREATE TABLE `instagram_data` (
  `instagram_data_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `instagram` varchar(32) NOT NULL,
  `facebook` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `web_data`
--

CREATE TABLE `web_data` (
  `web_data_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `html` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `web_url`
--

CREATE TABLE `web_url` (
  `web_url_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `scraped` tinyint(1) NOT NULL,
  `date_scraped` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hibp_data`
--
ALTER TABLE `hibp_data`
  ADD PRIMARY KEY (`hibp_data_id`);

--
-- Indexes for table `instagram_data`
--
ALTER TABLE `instagram_data`
  ADD PRIMARY KEY (`instagram_data_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `web_data`
--
ALTER TABLE `web_data`
  ADD PRIMARY KEY (`web_data_id`);
ALTER TABLE `web_data` ADD FULLTEXT KEY `html` (`html`);

--
-- Indexes for table `web_url`
--
ALTER TABLE `web_url`
  ADD PRIMARY KEY (`web_url_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hibp_data`
--
ALTER TABLE `hibp_data`
  MODIFY `hibp_data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instagram_data`
--
ALTER TABLE `instagram_data`
  MODIFY `instagram_data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_data`
--
ALTER TABLE `web_data`
  MODIFY `web_data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_url`
--
ALTER TABLE `web_url`
  MODIFY `web_url_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
