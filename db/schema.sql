-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 27-07-2011 a las 10:37:59
-- Versión del servidor: 5.1.30
-- Versión de PHP: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `football`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matches`
--

CREATE TABLE IF NOT EXISTS `matches` (
  `id_match` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_season` int(11) NOT NULL,
  `journey` int(11) NOT NULL,
  `type` enum('league','cup','friendly') NOT NULL,
  `day` date NOT NULL,
  `hour` time NOT NULL,
  `rival` varchar(255) NOT NULL,
  `status` enum('open','closed','hidden') NOT NULL,
  PRIMARY KEY (`id_match`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matches_player`
--

CREATE TABLE IF NOT EXISTS `matches_player` (
  `id_match` int(11) unsigned NOT NULL,
  `id_player` int(11) unsigned NOT NULL,
  `available` enum('available','called','unavailable','injuried','if_necessary') NOT NULL DEFAULT 'unavailable',
  `score` float(4,2) DEFAULT NULL,
  PRIMARY KEY (`id_match`,`id_player`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matches_player_score`
--

CREATE TABLE IF NOT EXISTS `matches_player_score` (
  `id_match` int(10) unsigned NOT NULL,
  `id_player` int(10) unsigned NOT NULL,
  `id_player_scorer` int(10) unsigned NOT NULL,
  `score` float(4,2) DEFAULT NULL,
  `best` varchar(500) CHARACTER SET ucs2 NOT NULL,
  `worst` varchar(500) NOT NULL,
  PRIMARY KEY (`id_match`,`id_player`,`id_player_scorer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player`
--

CREATE TABLE IF NOT EXISTS `player` (
  `id_player` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `number` tinyint(3) unsigned DEFAULT NULL,
  `position` enum('goalkeeper','defender','middle','forwarder') DEFAULT NULL,
  `type` enum('waiting','player','sold','friendly','hidden') NOT NULL,
  `phone` varchar(9) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` set('admin','guest','player') NOT NULL DEFAULT 'player',
  `date_add` date NOT NULL,
  `date_sold` date DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `sanitized_name` varchar(255) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id_player`),
  UNIQUE KEY `uq-sanitized_name` (`sanitized_name`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `role` (`role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `season`
--

CREATE TABLE IF NOT EXISTS `season` (
  `id_season` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `year` year(4) NOT NULL,
  PRIMARY KEY (`id_season`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


-- Example inserts --
INSERT INTO season VALUES ( 1, 'Example season', '2013');
INSERT INTO player VALUES ( 1, 'vader', '827ccb0eea8a706c4c34a16891f84e7b', 'Anakin', 'Skywalker', '', 1, 'goalkeeper', 'player', '', 'vader@example.com', 'admin,player', NOW(), NULL, 'http://aux.iconpedia.net/uploads/14253482371070846553.png', 'vader', NOW() );
INSERT INTO matches VALUES ( 1, 1, 1, 'league', '2013-01-01', '21:00:00', 'The Old Republic', 'closed' );
INSERT INTO matches VALUES ( 2, 1, 2, 'league', DATE_ADD( NOW(), INTERVAL 7 DAY), '21:00:00', 'The Old Republic', 'open' );
