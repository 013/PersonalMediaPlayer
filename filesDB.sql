
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `files`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientip`
--

CREATE TABLE IF NOT EXISTS `clientip` (
  `ip` varchar(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `IMDbID` varchar(256) NOT NULL,
  `path` varchar(512) NOT NULL,
  `date` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IMDbID` (`IMDbID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=315 ;

-- --------------------------------------------------------

--
-- Table structure for table `imdb`
--

CREATE TABLE IF NOT EXISTS `imdb` (
  `imdbid` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `year` varchar(64) NOT NULL,
  `imdbRating` varchar(64) NOT NULL,
  `released` varchar(64) NOT NULL,
  `genre` varchar(128) NOT NULL,
  `plot` text NOT NULL,
  `posterurl` varchar(512) NOT NULL,
  PRIMARY KEY (`imdbid`),
  UNIQUE KEY `imdbid` (`imdbid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
