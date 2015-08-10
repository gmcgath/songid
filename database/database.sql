-- phpMyAdmin SQL Dump
-- version 4.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 10, 2015 at 12:35 PM
-- Server version: 5.5.43-0+deb7u1
-- PHP Version: 5.6.9-1~dotdeb+7.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `songid`
--
CREATE DATABASE IF NOT EXISTS `songid` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `songid`;

-- --------------------------------------------------------

--
-- Table structure for table `ACTORS`
--

DROP TABLE IF EXISTS `ACTORS`;
CREATE TABLE IF NOT EXISTS `ACTORS` (
`id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Primary name',
  `type_id` int(11) NOT NULL DEFAULT '1' COMMENT 'FK TO ACTOR_TYPES'
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ACTORS`
--


-- --------------------------------------------------------

--
-- Table structure for table `ACTOR_NAMES`
--

DROP TABLE IF EXISTS `ACTOR_NAMES`;
CREATE TABLE IF NOT EXISTS `ACTOR_NAMES` (
`id` int(10) NOT NULL,
  `actor_id` int(11) NOT NULL COMMENT 'FK to ACTORS',
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ACTOR_TYPES`
--

DROP TABLE IF EXISTS `ACTOR_TYPES`;
CREATE TABLE IF NOT EXISTS `ACTOR_TYPES` (
  `ID` int(11) NOT NULL,
  `DESCRIPTION` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ACTOR_TYPES`
--

INSERT INTO `ACTOR_TYPES` (`ID`, `DESCRIPTION`) VALUES(1, 'INDIVIDUAL');
INSERT INTO `ACTOR_TYPES` (`ID`, `DESCRIPTION`) VALUES(2, 'GROUP');

-- --------------------------------------------------------

--
-- Table structure for table `AUTHCODES`
--

DROP TABLE IF EXISTS `AUTHCODES`;
CREATE TABLE IF NOT EXISTS `AUTHCODES` (
`id` int(11) NOT NULL,
  `code_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AUTHCODES`
--

INSERT INTO `AUTHCODES` (`id`, `code_hash`) VALUES(1, '$2y$10$vs2p.ooXsprDxdn7POrscu3HpVHv03k0mEF59N6ZK1yjcPmBAl4Qe');

-- --------------------------------------------------------

--
-- Table structure for table `CLIPS`
--

DROP TABLE IF EXISTS `CLIPS`;
CREATE TABLE IF NOT EXISTS `CLIPS` (
`id` int(11) NOT NULL,
  `description` varchar(256) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `performer` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `event` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recording_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `INSTRUMENTS`
--

DROP TABLE IF EXISTS `INSTRUMENTS`;
CREATE TABLE IF NOT EXISTS `INSTRUMENTS` (
`id` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL COMMENT 'FK to INSTRUMENT_CATEGORIES'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `INSTRUMENTS`
--

INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(14, 'Autoharp', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(15, 'Banjo', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(16, 'Banjola', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(66, 'Baritone', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(17, 'Bass (acoustic)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(18, 'Bass (electric)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(19, 'Bass (upright)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(20, 'Bass (washtub)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(62, 'Bassoon', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(21, 'Bazouki', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(36, 'Bones', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(55, 'Clarinet', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(56, 'Clarinet (alto)', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(57, 'Clarinet (bass)', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(65, 'Cornet', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(37, 'Cow bell', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(38, 'Cymbals', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(69, 'Didgeridoo', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(22, 'Dobro', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(39, 'Drums (bongos)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(40, 'Drums (djembe)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(8, 'Drums (frame or bodhran)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(7, 'Drums (kit)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(41, 'Drums (snare)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(42, 'Drums (steel)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(43, 'Drums (tom tom)', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(23, 'Dulcimer (hammer)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(24, 'Dulcimer (mountain)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(11, 'Electronic keyboard', 4);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(61, 'English horn', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(25, 'Fiddle (acoustic)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(70, 'Fife', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(44, 'Finger cymbals', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(4, 'Flute', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(58, 'Flute (alto)', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(59, 'Flute (bass)', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(63, 'French horn', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(45, 'Gong', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(1, 'Guitar (acoustic)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(26, 'Guitar (electric)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(29, 'Guitar (harp guitar)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(27, 'Guitar (lap steel)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(28, 'Guitar (resonator)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(30, 'Harp (floor)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(2, 'Harp (lap)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(71, 'Harpsichord', 4);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(46, 'Jew''s harp', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(31, 'Mandolin', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(72, 'Melodica', 4);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(60, 'Oboe', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(12, 'Organ', 4);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(10, 'Piano', 4);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(54, 'Piccolo', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(47, 'Rainstick', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(5, 'Recorder', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(9, 'Shaker egg/Maracas', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(48, 'Spoons', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(49, 'Tambourine', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(13, 'Theremin', 5);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(6, 'Tin whistle', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(50, 'Triangle', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(68, 'Trombone', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(64, 'Trumpet', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(67, 'Tuba', 2);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(32, 'Ukelele (alto)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(33, 'Ukelele (bass)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(34, 'Ukelele (soprano)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(35, 'Ukelele (tenor)', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(51, 'Vibraphone', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(3, 'Violin', 1);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(52, 'Wooden block', 3);
INSERT INTO `INSTRUMENTS` (`id`, `name`, `category_id`) VALUES(53, 'Xylophone', 3);

-- --------------------------------------------------------

--
-- Table structure for table `INSTRUMENT_CATEGORIES`
--

DROP TABLE IF EXISTS `INSTRUMENT_CATEGORIES`;
CREATE TABLE IF NOT EXISTS `INSTRUMENT_CATEGORIES` (
`id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `display_sequence` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `INSTRUMENT_CATEGORIES`
--

INSERT INTO `INSTRUMENT_CATEGORIES` (`id`, `name`, `display_sequence`) VALUES(1, 'Strings', 1);
INSERT INTO `INSTRUMENT_CATEGORIES` (`id`, `name`, `display_sequence`) VALUES(2, 'Winds', 2);
INSERT INTO `INSTRUMENT_CATEGORIES` (`id`, `name`, `display_sequence`) VALUES(3, 'Percussion', 3);
INSERT INTO `INSTRUMENT_CATEGORIES` (`id`, `name`, `display_sequence`) VALUES(4, 'Keyboard', 4);
INSERT INTO `INSTRUMENT_CATEGORIES` (`id`, `name`, `display_sequence`) VALUES(5, 'Other', 5);

-- --------------------------------------------------------

--
-- Table structure for table `RECORDINGS`
--

DROP TABLE IF EXISTS `RECORDINGS`;
CREATE TABLE IF NOT EXISTS `RECORDINGS` (
`id` int(11) NOT NULL,
  `path` varchar(192) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `REPORTS`
--

DROP TABLE IF EXISTS `REPORTS`;
CREATE TABLE IF NOT EXISTS `REPORTS` (
`id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `master_id` int(11) DEFAULT NULL COMMENT 'ID of master report',
  `seq_num` smallint(6) NOT NULL DEFAULT '0',
  `clip_id` int(11) NOT NULL COMMENT 'FK to CLIPS',
  `user_id` int(11) NOT NULL COMMENT 'FK to USERS',
  `sound_type` int(11) NOT NULL,
  `sound_subtype` int(11) NOT NULL DEFAULT '0',
  `performer_type` int(11) NOT NULL DEFAULT '0',
  `song_id` int(11) DEFAULT NULL COMMENT 'FK to SONGS',
  `singalong` tinyint(1) NOT NULL DEFAULT '0',
  `flagged` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_COMPOSERS`
--

DROP TABLE IF EXISTS `REPORTS_COMPOSERS`;
CREATE TABLE IF NOT EXISTS `REPORTS_COMPOSERS` (
`id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `actor_id` int(11) NOT NULL COMMENT 'FK to ACTORS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_INSTRUMENTS`
--

DROP TABLE IF EXISTS `REPORTS_INSTRUMENTS`;
CREATE TABLE IF NOT EXISTS `REPORTS_INSTRUMENTS` (
`id` int(11) NOT NULL,
  `REPORT_ID` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `INSTRUMENT_ID` int(11) NOT NULL COMMENT 'FK to INSTRUMENTS'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `REPORTS_PERFORMERS`
--

DROP TABLE IF EXISTS `REPORTS_PERFORMERS`;
CREATE TABLE IF NOT EXISTS `REPORTS_PERFORMERS` (
`id` int(11) NOT NULL,
  `REPORT_ID` int(11) NOT NULL COMMENT 'FK to REPORTS',
  `ACTOR_ID` int(11) NOT NULL COMMENT 'FK to ACTORS'
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `SONGS`
--

DROP TABLE IF EXISTS `SONGS`;
CREATE TABLE IF NOT EXISTS `SONGS` (
`id` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `note` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'For disambiguation'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `SONGS_TUNES`
--

DROP TABLE IF EXISTS `SONGS_TUNES`;
CREATE TABLE IF NOT EXISTS `SONGS_TUNES` (
`id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL COMMENT 'FK to SONGS',
  `tune_id` int(11) NOT NULL COMMENT 'FK to SONGS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
CREATE TABLE IF NOT EXISTS `USERS` (
`id` int(11) NOT NULL,
  `login_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `self_info` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `USERS_ROLES`
--

DROP TABLE IF EXISTS `USERS_ROLES`;
CREATE TABLE IF NOT EXISTS `USERS_ROLES` (
`id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'FK to USERS',
  `role` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Indexes for dumped tables
--

--
-- Indexes for table `ACTORS`
--
ALTER TABLE `ACTORS`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `NAME_2` (`name`), ADD KEY `NAME` (`name`);

--
-- Indexes for table `ACTOR_NAMES`
--
ALTER TABLE `ACTOR_NAMES`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `NAME` (`name`), ADD KEY `ACTOR_ID` (`actor_id`), ADD KEY `NAME_2` (`name`);

--
-- Indexes for table `ACTOR_TYPES`
--
ALTER TABLE `ACTOR_TYPES`
 ADD UNIQUE KEY `ID_2` (`ID`), ADD KEY `ID` (`ID`);

--
-- Indexes for table `AUTHCODES`
--
ALTER TABLE `AUTHCODES`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `CLIPS`
--
ALTER TABLE `CLIPS`
 ADD PRIMARY KEY (`id`), ADD KEY `recording_id` (`recording_id`);

--
-- Indexes for table `INSTRUMENTS`
--
ALTER TABLE `INSTRUMENTS`
 ADD PRIMARY KEY (`id`), ADD KEY `NAME` (`name`,`category_id`);

--
-- Indexes for table `INSTRUMENT_CATEGORIES`
--
ALTER TABLE `INSTRUMENT_CATEGORIES`
 ADD PRIMARY KEY (`id`), ADD KEY `DISPLAY_SEQUENCE` (`display_sequence`);

--
-- Indexes for table `RECORDINGS`
--
ALTER TABLE `RECORDINGS`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `REPORTS`
--
ALTER TABLE `REPORTS`
 ADD PRIMARY KEY (`id`), ADD KEY `CLIP_ID` (`clip_id`,`user_id`,`song_id`), ADD KEY `MASTER_ID` (`master_id`);

--
-- Indexes for table `REPORTS_COMPOSERS`
--
ALTER TABLE `REPORTS_COMPOSERS`
 ADD PRIMARY KEY (`id`), ADD KEY `REPORT_ID` (`report_id`,`actor_id`);

--
-- Indexes for table `REPORTS_INSTRUMENTS`
--
ALTER TABLE `REPORTS_INSTRUMENTS`
 ADD PRIMARY KEY (`id`), ADD KEY `REPORT_ID` (`REPORT_ID`);

--
-- Indexes for table `REPORTS_PERFORMERS`
--
ALTER TABLE `REPORTS_PERFORMERS`
 ADD PRIMARY KEY (`id`), ADD KEY `REPORT_ID` (`REPORT_ID`,`ACTOR_ID`);

--
-- Indexes for table `SONGS`
--
ALTER TABLE `SONGS`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `SONGS_TUNES`
--
ALTER TABLE `SONGS_TUNES`
 ADD PRIMARY KEY (`id`), ADD KEY `SONG_ID` (`song_id`,`tune_id`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
 ADD PRIMARY KEY (`id`), ADD KEY `LOGIN_ID` (`login_id`), ADD KEY `NAME` (`name`), ADD KEY `LOGIN_ID_2` (`login_id`);

--
-- Indexes for table `USERS_ROLES`
--
ALTER TABLE `USERS_ROLES`
 ADD PRIMARY KEY (`id`), ADD KEY `USER_ID` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ACTORS`
--
ALTER TABLE `ACTORS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `ACTOR_NAMES`
--
ALTER TABLE `ACTOR_NAMES`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AUTHCODES`
--
ALTER TABLE `AUTHCODES`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `CLIPS`
--
ALTER TABLE `CLIPS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `INSTRUMENTS`
--
ALTER TABLE `INSTRUMENTS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `INSTRUMENT_CATEGORIES`
--
ALTER TABLE `INSTRUMENT_CATEGORIES`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `RECORDINGS`
--
ALTER TABLE `RECORDINGS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `REPORTS`
--
ALTER TABLE `REPORTS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `REPORTS_COMPOSERS`
--
ALTER TABLE `REPORTS_COMPOSERS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `REPORTS_INSTRUMENTS`
--
ALTER TABLE `REPORTS_INSTRUMENTS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `REPORTS_PERFORMERS`
--
ALTER TABLE `REPORTS_PERFORMERS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SONGS`
--
ALTER TABLE `SONGS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SONGS_TUNES`
--
ALTER TABLE `SONGS_TUNES`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `USERS_ROLES`
--
ALTER TABLE `USERS_ROLES`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `CLIPS`
--
ALTER TABLE `CLIPS`
ADD CONSTRAINT `cliptorecording` FOREIGN KEY (`recording_id`) REFERENCES `RECORDINGS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
