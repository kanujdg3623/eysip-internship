-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 21, 2020 at 11:42 AM
-- Server version: 5.7.30-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: project2
--

--
-- Dumping data for table initiatives_years
--

INSERT INTO initiatives_years (id, `year`, initiative, theme) VALUES
(1, 2018, 'eYRC', 'Jungle Safari'),
(2, 2017, 'eYRC', 'Agriculture'),
(3, 2016, 'eYRC', 'Space Exploration'),
(4, 2015, 'eYRC', 'Smart Services'),
(5, 2014, 'eYRC', 'Urban Services'),
(6, 2013, 'eYRC', 'Agriculture'),
(7, 2012, 'eYRC', 'Automation'),
(8, 2020, 'eYIC', NULL),
(9, 2019, 'eYIC', NULL),
(10, 2018, 'eYIC', NULL),
(11, 2019, 'eYSIP', NULL),
(12, 2018, 'eYSIP', NULL),
(13, 2017, 'eYSIP', NULL),
(14, 2015, 'eYSIP', NULL),
(15, 2020, 'Hackathon', 'COVID-19'),
(16, 2018, 'eYS', NULL),
(17, 2019, 'eYS', NULL),
(18, 2020, 'Talk', NULL),
(19, 2019, 'eYRC', 'Disaster Management'),
(20, 2016, 'eYSIP', NULL),
(21, 2018, 'Workshop', 'I&E Workshop'),
(22, 2019, 'Workshop', 'DM Workshop');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
