SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `exceptions`;
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
DROP TABLE IF EXISTS `lots`;
CREATE TABLE `lots` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'New Lot' COMMENT 'The name of the area.',
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Description of the lot, helpful for users.',
  `coords` longtext CHARACTER SET utf8 COMMENT 'Lat/Long pairs to mark the boundaries of the lot.',
  `timed` int(10) NOT NULL DEFAULT '0' COMMENT '-1 for metered lots.\r\n0 for non-timed lots.\r\n>0 specifies the time limit.',
  `scheme` int(10) NOT NULL DEFAULT '1' COMMENT 'Which color scheme to apply to the lot.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `scheme` (`scheme`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `passTypes`;
CREATE TABLE `passTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'Name of the pass type.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `rules`;
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
DROP TABLE IF EXISTS `schemes`;
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
INSERT INTO `schemes` VALUES ('1', 'Default', '#336699', '10', '1.00', '#305989', '0.30');
INSERT INTO `schemes` VALUES ('2', 'Black', '#000000', '10', '1.00', '#000000', '0.30');
INSERT INTO `schemes` VALUES ('3', 'White', '#FFFFFF', '10', '1.00', '#FFFFFF', '0.30');
INSERT INTO `schemes` VALUES ('4', 'Red', '#FF0000', '10', '1.00', '#E50000', '0.30');
INSERT INTO `schemes` VALUES ('5', 'Blue', '#0000FF', '10', '1.00', '#0000FF', '0.30');
INSERT INTO `schemes` VALUES ('6', 'Green', '#00B400', '10', '1.00', '#00A200', '0.30');
INSERT INTO `schemes` VALUES ('7', 'Orange', '#FF9933', '10', '1.00', '#E5892D', '0.30');
INSERT INTO `schemes` VALUES ('8', 'Yellow', '#FFFF00', '10', '1.00', '#E5E500', '0.30');
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
INSERT INTO `settings` VALUES ('1', '0', 'mapCenter', '34.6766, -82.8343');
INSERT INTO `settings` VALUES ('2', '0', 'mapTypeId', 'google.maps.MapTypeId.ROADMAP');
INSERT INTO `settings` VALUES ('3', '0', 'markerImage', './images/clemsonPaw.png');
INSERT INTO `settings` VALUES ('4', '0', 'markerShadow', 'http://www.google.com/mapfiles/shadow50.png');
INSERT INTO `settings` VALUES ('5', '0', 'lotHTML', '<b>{lotName}</b><br><i>{lotDescription}</i><br><br><u>Who Can Park Here?</u><br>{currentPassTypes}');
INSERT INTO `settings` VALUES ('6', '0', 'mapZoom', '16');
DROP TABLE IF EXISTS `users`;
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
