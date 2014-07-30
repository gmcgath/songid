-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2014 at 07:43 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=73 ;

--
-- Dumping data for table `INSTRUMENTS`
--

INSERT INTO `INSTRUMENTS` (`ID`, `NAME`, `CATEGORY_ID`) VALUES
(14, 'Autoharp', 1),
(15, 'Banjo', 1),
(16, 'Banjola', 1),
(66, 'Baritone', 2),
(17, 'Bass (acoustic)', 1),
(18, 'Bass (electric)', 1),
(19, 'Bass (upright)', 1),
(20, 'Bass (washtub)', 1),
(62, 'Bassoon', 2),
(21, 'Bazouki', 1),
(36, 'Bones', 3),
(55, 'Clarinet', 2),
(56, 'Clarinet (alto)', 2),
(57, 'Clarinet (bass)', 2),
(65, 'Cornet', 2),
(37, 'Cow bell', 3),
(38, 'Cymbals', 3),
(69, 'Didgeridoo', 2),
(22, 'Dobro', 1),
(39, 'Drums (bongos)', 3),
(40, 'Drums (djembe)', 3),
(8, 'Drums (frame or bodhran)', 3),
(7, 'Drums (kit)', 3),
(41, 'Drums (snare)', 3),
(42, 'Drums (steel)', 3),
(43, 'Drums (tom tom)', 3),
(23, 'Dulcimer (hammer)', 1),
(24, 'Dulcimer (mountain)', 1),
(11, 'Electronic keyboard', 4),
(61, 'English horn', 2),
(25, 'Fiddle (acoustic)', 1),
(70, 'Fife', 2),
(44, 'Finger cymbals', 3),
(4, 'Flute', 2),
(58, 'Flute (alto)', 2),
(59, 'Flute (bass)', 2),
(63, 'French horn', 2),
(45, 'Gong', 3),
(1, 'Guitar (acoustic)', 1),
(26, 'Guitar (electric)', 1),
(29, 'Guitar (harp guitar)', 1),
(27, 'Guitar (lap steel)', 1),
(28, 'Guitar (resonator)', 1),
(30, 'Harp (floor)', 1),
(2, 'Harp (lap)', 1),
(71, 'Harpsichord', 4),
(46, 'Jew''s harp', 3),
(31, 'Mandolin', 1),
(72, 'Melodica', 4),
(60, 'Oboe', 2),
(12, 'Organ', 4),
(10, 'Piano', 4),
(54, 'Piccolo', 2),
(47, 'Rainstick', 3),
(5, 'Recorder', 2),
(9, 'Shaker egg/Maracas', 3),
(48, 'Spoons', 3),
(49, 'Tambourine', 3),
(13, 'Theremin', 5),
(6, 'Tin whistle', 2),
(50, 'Triangle', 3),
(68, 'Trombone', 2),
(64, 'Trumpet', 2),
(67, 'Tuba', 2),
(32, 'Ukelele (alto)', 1),
(33, 'Ukelele (bass)', 1),
(34, 'Ukelele (soprano)', 1),
(35, 'Ukelele (tenor)', 1),
(51, 'Vibraphone', 3),
(3, 'Violin', 1),
(52, 'Wooden block', 3),
(53, 'Xylophone', 3);

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
  `MASTER_ID` int(11) DEFAULT NULL COMMENT 'ID of master report',
  `SEQ_NUM` smallint(6) NOT NULL DEFAULT '0',
  `CLIP_ID` int(11) NOT NULL COMMENT 'FK to CLIPS',
  `USER_ID` int(11) NOT NULL COMMENT 'FK to USERS',
  `SOUND_TYPE` int(11) NOT NULL,
  `SOUND_SUBTYPE` int(11) NOT NULL DEFAULT '0',
  `PERFORMER_TYPE` int(11) NOT NULL DEFAULT '0',
  `SONG_ID` int(11) DEFAULT NULL COMMENT 'FK to SONGS',
  `SINGALONG` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `CLIP_ID` (`CLIP_ID`,`USER_ID`,`SONG_ID`),
  KEY `MASTER_ID` (`MASTER_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=70 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;


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
