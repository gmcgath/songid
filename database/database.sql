-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 23, 2014 at 04:48 PM
-- Server version: 5.5.37-35.1
-- PHP Version: 5.4.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gmcgath_songid`
--

-- --------------------------------------------------------

--
-- Table structure for table `ACTORS`
--

CREATE TABLE IF NOT EXISTS `ACTORS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Primary name',
  `TYPE_ID` int(11) NOT NULL DEFAULT '1' COMMENT 'FK TO ACTOR_TYPES',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME_2` (`NAME`),
  KEY `NAME` (`NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;


-- --------------------------------------------------------

--
-- Table structure for table `ACTOR_NAMES`
--

CREATE TABLE IF NOT EXISTS `ACTOR_NAMES` (
  `ACTOR_ID` int(11) NOT NULL COMMENT 'FK to ACTORS',
  `NAME` varchar(128) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  UNIQUE KEY `NAME` (`NAME`),
  KEY `ACTOR_ID` (`ACTOR_ID`),
  KEY `NAME_2` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `ACTOR_TYPES`
--

CREATE TABLE IF NOT EXISTS `ACTOR_TYPES` (
  `ID` int(11) NOT NULL,
  `DESCRIPTION` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ACTOR_TYPES`
--

INSERT INTO `ACTOR_TYPES` (`ID`, `DESCRIPTION`) VALUES
(1, 'INDIVIDUAL'),
(2, 'GROUP');

-- --------------------------------------------------------

--
-- Table structure for table `AUTHCODES`
--

CREATE TABLE IF NOT EXISTS `AUTHCODES` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODE_HASH` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- Table structure for table `CLIPS`
--

CREATE TABLE IF NOT EXISTS `CLIPS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPTION` varchar(256) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `URL` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;


-- --------------------------------------------------------

--
-- Table structure for table `INSTRUMENTS`
--

CREATE TABLE IF NOT EXISTS `INSTRUMENTS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `CATEGORY_ID` int(11) NOT NULL COMMENT 'FK to INSTRUMENT_CATEGORIES',
  PRIMARY KEY (`ID`),
  KEY `NAME` (`NAME`,`CATEGORY_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `INSTRUMENTS`
--

INSERT INTO `INSTRUMENTS` (`ID`, `NAME`, `CATEGORY_ID`) VALUES
(8, 'Bodhran', 3),
(7, 'Drums', 3),
(11, 'Electronic keyboard', 4),
(4, 'Flute', 2),
(1, 'Guitar', 1),
(2, 'Harp', 1),
(12, 'Organ', 4),
(10, 'Piano', 4),
(5, 'Recorder', 2),
(9, 'Shaker', 3),
(13, 'Theremin', 5),
(3, 'Violin', 1),
(6, 'Whistle', 2);

-- --------------------------------------------------------

--
-- Table structure for table `INSTRUMENT_CATEGORIES`
--

CREATE TABLE IF NOT EXISTS `INSTRUMENT_CATEGORIES` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `DISPLAY_SEQUENCE` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `DISPLAY_SEQUENCE` (`DISPLAY_SEQUENCE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `INSTRUMENT_CATEGORIES`
--

INSERT INTO `INSTRUMENT_CATEGORIES` (`ID`, `NAME`, `DISPLAY_SEQUENCE`) VALUES
(1, 'Strings', 1),
(2, 'Winds', 2),
(3, 'Percussion', 3),
(4, 'Keyboard', 4),
(5, 'Other', 5);

-- --------------------------------------------------------

--
-- Table structure for table `REPORTS`
--

CREATE TABLE IF NOT EXISTS `REPORTS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CLIP_ID` int(11) NOT NULL COMMENT 'FK to CLIPS',
  `USER_ID` int(11) NOT NULL COMMENT 'FK to USERS',
  `SOUND_TYPE` int(11) NOT NULL,
  `SOUND_SUBTYPE` int(11) NOT NULL DEFAULT '0',
  `PERFORMER_TYPE` int(11) NOT NULL DEFAULT '0',
  `SONG_ID` int(11) DEFAULT NULL COMMENT 'FK to SONGS',
  `SINGALONG` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `CLIP_ID` (`CLIP_ID`,`USER_ID`,`SONG_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;


-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_COMPOSERS`
--

CREATE TABLE IF NOT EXISTS `REPORTS_COMPOSERS` (
  `REPORT_ID` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `ACTOR_ID` int(11) NOT NULL COMMENT 'FK to ACTORS',
  KEY `REPORT_ID` (`REPORT_ID`,`ACTOR_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_INSTRUMENTS`
--

CREATE TABLE IF NOT EXISTS `REPORTS_INSTRUMENTS` (
  `REPORT_ID` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `INSTRUMENT_ID` int(11) NOT NULL COMMENT 'FK to INSTRUMENTS',
  KEY `REPORT_ID` (`REPORT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_PERFORMERS`
--

CREATE TABLE IF NOT EXISTS `REPORTS_PERFORMERS` (
  `REPORT_ID` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `ACTOR_ID` int(11) NOT NULL COMMENT 'FK to ACTORS',
  KEY `REPORT_ID` (`REPORT_ID`,`ACTOR_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `SONGS`
--

CREATE TABLE IF NOT EXISTS `SONGS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(128) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `NOTE` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'For disambiguation',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;


-- --------------------------------------------------------

--
-- Table structure for table `SONGS_TUNES`
--

CREATE TABLE IF NOT EXISTS `SONGS_TUNES` (
  `SONG_ID` int(11) NOT NULL COMMENT 'FK to SONGS',
  `TUNE_ID` int(11) NOT NULL COMMENT 'FK to SONGS',
  KEY `SONG_ID` (`SONG_ID`,`TUNE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE IF NOT EXISTS `USERS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LOGIN_ID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `PASSWORD_HASH` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `NAME` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DATE_REGISTERED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `LOGIN_ID` (`LOGIN_ID`),
  KEY `NAME` (`NAME`),
  KEY `LOGIN_ID_2` (`LOGIN_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;


-- --------------------------------------------------------

--
-- Table structure for table `USERS_ROLES`
--

CREATE TABLE IF NOT EXISTS `USERS_ROLES` (
  `USER_ID` int(11) NOT NULL COMMENT 'FK to USERS',
  `ROLE` int(11) NOT NULL,
  KEY `USER_ID` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
