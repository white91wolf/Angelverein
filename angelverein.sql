-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Apr 2014 um 21:44
-- Server Version: 5.6.16
-- PHP-Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `angelverein`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `arbeitsdienst`
--

CREATE TABLE IF NOT EXISTS `arbeitsdienst` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stunden` int(11) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `bestaetigt` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bilder`
--

CREATE TABLE IF NOT EXISTS `bilder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateiname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `text` text NOT NULL,
  `headline` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `lastupdateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`type_id`),
  KEY `user_id_2` (`user_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `content_type`
--

CREATE TABLE IF NOT EXISTS `content_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fangliste`
--

CREATE TABLE IF NOT EXISTS `fangliste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gewaesser_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datum` (`datum`),
  KEY `user_id_2` (`user_id`),
  KEY `gewaesser_id` (`gewaesser_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `fangliste`
--

INSERT INTO `fangliste` (`id`, `user_id`, `datum`, `gewaesser_id`) VALUES
(4, 1, '2014-04-03 19:47:15', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fangliste_eintrag`
--

CREATE TABLE IF NOT EXISTS `fangliste_eintrag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fisch_id` int(11) NOT NULL,
  `anzahl` int(11) NOT NULL,
  `gewicht` int(11) NOT NULL,
  `fangliste_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fisch_id` (`fisch_id`),
  KEY `fangliste_id` (`fangliste_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fischarten`
--

CREATE TABLE IF NOT EXISTS `fischarten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `fischarten`
--

INSERT INTO `fischarten` (`id`, `name`) VALUES
(1, 'Forelle'),
(2, 'Karpfen'),
(3, 'Hai'),
(4, 'Blauwaal');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gewaesser`
--

CREATE TABLE IF NOT EXISTS `gewaesser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `gewaesser`
--

INSERT INTO `gewaesser` (`id`, `name`) VALUES
(1, 'Donau'),
(2, 'Brigach'),
(3, 'Nil'),
(4, 'Kötach');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rolle`
--

CREATE TABLE IF NOT EXISTS `rolle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `rolle`
--

INSERT INTO `rolle` (`id`, `name`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'hgf');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termin`
--

CREATE TABLE IF NOT EXISTS `termin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `erstelldatum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datum` date NOT NULL,
  `uhrzeit` time NOT NULL,
  `beschreibung` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `anmeldung` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termin_rolle`
--

CREATE TABLE IF NOT EXISTS `termin_rolle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rolle_id` int(11) NOT NULL,
  `termin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rolle_id` (`rolle_id`),
  KEY `termin_id` (`termin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termin_user`
--

CREATE TABLE IF NOT EXISTS `termin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dabei` tinyint(1) NOT NULL DEFAULT '0',
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `termin_id` (`termin_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rolle_id` int(11) NOT NULL DEFAULT '1',
  `email` varchar(255) NOT NULL,
  `gebutsdatum` date DEFAULT NULL,
  `userimage` int(11) DEFAULT NULL,
  `freigeschaltet` tinyint(1) NOT NULL DEFAULT '0',
  `aboutme` varchar(510) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rolle_id` (`rolle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `username`, `vorname`, `nachname`, `password`, `rolle_id`, `email`, `gebutsdatum`, `userimage`, `freigeschaltet`, `aboutme`) VALUES
(1, 'admin', 'admin', 'admin', '123456', 2, 'admin@admin.de', '2014-03-19', NULL, 1, NULL);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `arbeitsdienst`
--
ALTER TABLE `arbeitsdienst`
  ADD CONSTRAINT `arbeitsdienst_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bilder`
--
ALTER TABLE `bilder`
  ADD CONSTRAINT `bilder_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `content_contenttype` FOREIGN KEY (`type_id`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `fangliste`
--
ALTER TABLE `fangliste`
  ADD CONSTRAINT `fangliste_gewaesser` FOREIGN KEY (`gewaesser_id`) REFERENCES `gewaesser` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fangliste_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `fangliste_eintrag`
--
ALTER TABLE `fangliste_eintrag`
  ADD CONSTRAINT `fanglisteeintrag_fangliste` FOREIGN KEY (`fangliste_id`) REFERENCES `fangliste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fanglisteeintrag_fischarten` FOREIGN KEY (`fisch_id`) REFERENCES `fischarten` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `termin_rolle`
--
ALTER TABLE `termin_rolle`
  ADD CONSTRAINT `terminrolle_termin` FOREIGN KEY (`termin_id`) REFERENCES `termin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `terminrolle_rolle` FOREIGN KEY (`rolle_id`) REFERENCES `rolle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `termin_user`
--
ALTER TABLE `termin_user`
  ADD CONSTRAINT `terminuser_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `terminuser_termin` FOREIGN KEY (`termin_id`) REFERENCES `termin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_rolle` FOREIGN KEY (`rolle_id`) REFERENCES `rolle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
