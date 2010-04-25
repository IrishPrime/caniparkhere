-- MySQL dump 10.13  Distrib 5.1.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ciph
-- ------------------------------------------------------
-- Server version	5.1.37-1ubuntu5.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `exceptions`
--

DROP TABLE IF EXISTS `exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exceptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lot` int(10) NOT NULL COMMENT 'Which lots the exception will apply to.',
  `passType` int(10) NOT NULL COMMENT 'Which pass types the exception will apply to.',
  `start` datetime NOT NULL COMMENT 'Beginning of the exception.',
  `end` datetime NOT NULL COMMENT 'End of the exception.',
  `allowed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the exception allows or disallows users to park.',
  PRIMARY KEY (`id`),
  KEY `lot` (`lot`) USING BTREE,
  KEY `pass` (`passType`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lots`
--

DROP TABLE IF EXISTS `lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lots` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'New Lot' COMMENT 'The name of the area.',
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Description of the lot, helpful for users.',
  `coords` longtext CHARACTER SET utf8 COMMENT 'Lat/Long pairs to mark the boundaries of the lot.',
  `timed` int(10) NOT NULL DEFAULT '0' COMMENT '-1 for metered lots.\r\n0 for non-timed lots.\r\n>0 specifies the time limit in minutes.',
  `scheme` int(10) NOT NULL DEFAULT '1' COMMENT 'Which color scheme to apply to the lot.',
  PRIMARY KEY (`id`),
  KEY `scheme` (`scheme`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `passTypes`
--

DROP TABLE IF EXISTS `passTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `passTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'Name of the pass type.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lot` int(10) NOT NULL COMMENT 'Which lot the rule applies to.',
  `passType` int(10) NOT NULL COMMENT 'Which pass type the rule applies to.',
  `startDate` date NOT NULL COMMENT 'When the rule goes into effect.',
  `endDate` date NOT NULL COMMENT 'When the rule will no longer be needed. 00-00-0000 to prevent expiration.',
  `startTime` time NOT NULL COMMENT 'When users may start parking.',
  `endTime` time NOT NULL COMMENT 'When users may no longer be parked in the area.',
  `days` varchar(15) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT 'Which days the rule applies. 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday.',
  PRIMARY KEY (`id`),
  KEY `lot` (`lot`),
  KEY `pass` (`passType`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schemes`
--

DROP TABLE IF EXISTS `schemes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of the color scheme.',
  `lineColor` varchar(7) NOT NULL DEFAULT '#336699' COMMENT 'Color of the line applied to the border of areas in hex.',
  `lineWidth` int(5) NOT NULL DEFAULT '10' COMMENT 'Width of the line applied to borders in pixels.',
  `lineOpacity` decimal(3,2) NOT NULL DEFAULT '0.80' COMMENT 'Opacity of the line applied to borders.',
  `fillColor` varchar(7) NOT NULL DEFAULT '#305989' COMMENT 'Color applied to areas.',
  `fillOpacity` decimal(3,2) NOT NULL DEFAULT '0.30' COMMENT 'Opacity of color applied to areas.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Color schemes to make lots more recognizable at a glance.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `lastName` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `email` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'Password hash.',
  `passType` int(10) unsigned DEFAULT NULL,
  `lastLoc` text CHARACTER SET utf8 COMMENT 'Latitude, Longitude',
  `admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the user is an administrator.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-24 19:05:35
