-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: rpg
-- ------------------------------------------------------
-- Server version	5.7.23

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
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achievements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` tinytext COLLATE utf8_danish_ci NOT NULL,
  `description` tinytext COLLATE utf8_danish_ci NOT NULL,
  `icon` tinytext COLLATE utf8_danish_ci NOT NULL,
  `available` tinyint(4) NOT NULL DEFAULT '0',
  `special` tinyint(4) NOT NULL,
  `points` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acrel`
--

DROP TABLE IF EXISTS `acrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acrel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aut_id` int(11) DEFAULT NULL,
  `convent_id` int(11) NOT NULL DEFAULT '0',
  `aut_extra` tinytext COLLATE utf8mb4_danish_ci,
  `role` tinytext COLLATE utf8mb4_danish_ci,
  `added_by_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aut_id` (`aut_id`),
  KEY `convent_id` (`convent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alias`
--

DROP TABLE IF EXISTS `alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci NOT NULL DEFAULT 'aut',
  `label` tinytext COLLATE utf8_danish_ci NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`),
  KEY `label` (`label`(20))
) ENGINE=MyISAM AUTO_INCREMENT=815 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asrel`
--

DROP TABLE IF EXISTS `asrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asrel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aut_id` int(11) NOT NULL DEFAULT '0',
  `sce_id` int(11) NOT NULL DEFAULT '0',
  `tit_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `aut_id` (`aut_id`),
  KEY `sce_id` (`sce_id`),
  KEY `tit_id` (`tit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21801 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `atl`
--

DROP TABLE IF EXISTS `atl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` tinytext COLLATE utf8_danish_ci,
  `forfatter1` tinytext COLLATE utf8_danish_ci,
  `forfatter2` tinytext COLLATE utf8_danish_ci,
  `genre` mediumtext COLLATE utf8_danish_ci,
  `system` tinytext COLLATE utf8_danish_ci,
  `skrevettil` tinytext COLLATE utf8_danish_ci,
  `filnavn` tinytext COLLATE utf8_danish_ci,
  `bemark` mediumtext COLLATE utf8_danish_ci,
  `journal` tinytext COLLATE utf8_danish_ci,
  `sider` tinytext COLLATE utf8_danish_ci,
  `synopsis` mediumtext COLLATE utf8_danish_ci,
  `view` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=277 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut`
--

DROP TABLE IF EXISTS `aut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `surname` varchar(100) COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `birth` date DEFAULT NULL,
  `death` date DEFAULT NULL,
  `rpgdk_id` int(11) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `picfile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `firstname` (`firstname`,`surname`),
  KEY `surname` (`surname`,`firstname`)
) ENGINE=MyISAM AUTO_INCREMENT=2559 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_categories`
--

DROP TABLE IF EXISTS `award_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `award_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_danish_ci NOT NULL,
  `convent_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_danish_ci,
  `award_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `convent_id` (`convent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=434 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_nominee_entities`
--

DROP TABLE IF EXISTS `award_nominee_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `award_nominee_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `award_nominee_id` int(11) NOT NULL,
  `data_id` int(11) DEFAULT NULL,
  `category` enum('aut','sce') COLLATE utf8_danish_ci DEFAULT NULL,
  `label` text COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `award_nominee` (`award_nominee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=223 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_nominees`
--

DROP TABLE IF EXISTS `award_nominees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `award_nominees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `award_category_id` int(11) NOT NULL,
  `sce_id` int(11) DEFAULT NULL,
  `name` tinytext COLLATE utf8_danish_ci,
  `nominationtext` text COLLATE utf8_danish_ci NOT NULL,
  `winner` tinyint(4) DEFAULT NULL,
  `ranking` tinytext COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1187 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_danish_ci NOT NULL,
  `conset_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_danish_ci,
  `label` text COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conset`
--

DROP TABLE IF EXISTS `conset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_danish_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8_danish_ci,
  `intern` mediumtext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `convent`
--

DROP TABLE IF EXISTS `convent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `convent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_danish_ci DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `place` varchar(200) COLLATE utf8_danish_ci DEFAULT NULL,
  `conset_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_danish_ci,
  `intern` text COLLATE utf8_danish_ci,
  `confirmed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conset_id` (`conset_id`),
  KEY `end` (`end`),
  KEY `year` (`year`)
) ENGINE=MyISAM AUTO_INCREMENT=624 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `csrel`
--

DROP TABLE IF EXISTS `csrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csrel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `convent_id` int(11) NOT NULL DEFAULT '0',
  `sce_id` int(11) NOT NULL DEFAULT '0',
  `pre_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `con_id` (`convent_id`),
  KEY `pre_id` (`pre_id`),
  KEY `sce_id` (`sce_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19432 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedcontent`
--

DROP TABLE IF EXISTS `feedcontent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedcontent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `title` tinytext COLLATE utf8_danish_ci NOT NULL,
  `guid` tinytext COLLATE utf8_danish_ci NOT NULL,
  `link` tinytext COLLATE utf8_danish_ci NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text COLLATE utf8_danish_ci NOT NULL,
  `comments` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pubdate` (`pubdate`),
  KEY `guid` (`guid`(85)),
  KEY `rssfeed_id` (`feed_id`,`guid`(85))
) ENGINE=MyISAM AUTO_INCREMENT=7613 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` tinytext COLLATE utf8_danish_ci NOT NULL,
  `owner` tinytext COLLATE utf8_danish_ci NOT NULL,
  `aut_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_danish_ci NOT NULL,
  `pageurl` tinytext COLLATE utf8_danish_ci NOT NULL,
  `lastchecked` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filedata`
--

DROP TABLE IF EXISTS `filedata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filedata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files_id` int(11) NOT NULL,
  `label` tinytext COLLATE utf8_danish_ci NOT NULL,
  `content` longtext COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=35504 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filedownloads`
--

DROP TABLE IF EXISTS `filedownloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filedownloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files_id` int(11) NOT NULL,
  `data_id` int(11) NOT NULL,
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci NOT NULL,
  `accesstime` datetime DEFAULT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `browser` tinytext COLLATE utf8_danish_ci NOT NULL,
  `referer` tinytext COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`)
) ENGINE=MyISAM AUTO_INCREMENT=609469 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) NOT NULL,
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci NOT NULL,
  `filename` tinytext COLLATE utf8_danish_ci NOT NULL,
  `description` tinytext COLLATE utf8_danish_ci NOT NULL,
  `downloadable` tinyint(4) NOT NULL,
  `inserted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1828 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gen`
--

DROP TABLE IF EXISTS `gen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `genre` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gsrel`
--

DROP TABLE IF EXISTS `gsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gsrel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gen_id` int(11) NOT NULL DEFAULT '0',
  `sce_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sce_id` (`sce_id`,`gen_id`),
  KEY `gen_id` (`gen_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3329 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext COLLATE utf8_danish_ci,
  `info` mediumtext COLLATE utf8_danish_ci,
  `open` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci NOT NULL DEFAULT 'aut',
  `url` tinytext COLLATE utf8_danish_ci,
  `description` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1270 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_arrangoer`
--

DROP TABLE IF EXISTS `live_arrangoer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_arrangoer` (
  `titel` tinytext COLLATE utf8_danish_ci,
  `arrangoer` tinytext COLLATE utf8_danish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys','links','trivia','alias') COLLATE utf8_danish_ci DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `user` varchar(100) COLLATE utf8_danish_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(32) COLLATE utf8_danish_ci DEFAULT NULL,
  `ip_forward` varchar(64) COLLATE utf8_danish_ci DEFAULT NULL,
  `note` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`),
  KEY `user` (`user`,`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=35777 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loginmap`
--

DROP TABLE IF EXISTS `loginmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loginmap` (
  `site` enum('rpgforum','liveforum','digisign','facebook','alexandria','twitter','steam','twitch','spotify') COLLATE utf8_danish_ci NOT NULL,
  `siteuserid` bigint(20) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_danish_ci NOT NULL,
  `logintime` int(10) unsigned DEFAULT NULL,
  `ip` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`site`,`siteuserid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `published` datetime NOT NULL,
  `online` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pre`
--

DROP TABLE IF EXISTS `pre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(100) CHARACTER SET utf8 COLLATE utf8_danish_ci DEFAULT NULL,
  `iconfile` varchar(64) CHARACTER SET utf8 COLLATE utf8_danish_ci DEFAULT NULL,
  `textsymbol` tinytext COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remotelogin`
--

DROP TABLE IF EXISTS `remotelogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `remotelogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site` varchar(16) COLLATE utf8_danish_ci NOT NULL,
  `salt` tinytext COLLATE utf8_danish_ci NOT NULL,
  `loginurl` tinytext COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci DEFAULT NULL,
  `title` tinytext COLLATE utf8_danish_ci,
  `description` mediumtext COLLATE utf8_danish_ci,
  `relation` set('gm','read','played') COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `spoiler` tinyint(4) NOT NULL DEFAULT '0',
  `aut_id` int(11) DEFAULT NULL,
  `reviewer` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sce`
--

DROP TABLE IF EXISTS `sce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_danish_ci NOT NULL,
  `description` text COLLATE utf8_danish_ci,
  `intern` text COLLATE utf8_danish_ci,
  `sys_id` int(11) DEFAULT NULL,
  `sys_ext` tinytext COLLATE utf8_danish_ci,
  `aut_extra` tinytext COLLATE utf8_danish_ci,
  `participants` tinytext COLLATE utf8_danish_ci NOT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT '1',
  `ottowinner` tinyint(4) NOT NULL DEFAULT '0',
  `rlyeh_id` tinyint(4) NOT NULL,
  `gms_min` int(11) DEFAULT NULL,
  `gms_max` int(11) DEFAULT NULL,
  `players_min` int(11) DEFAULT NULL,
  `players_max` int(11) DEFAULT NULL,
  `participants_extra` text COLLATE utf8_danish_ci,
  `boardgame` tinyint(4) NOT NULL,
  `popularity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hidden` (`hidden`),
  KEY `sys_id` (`sys_id`),
  KEY `title` (`title`),
  KEY `players_min` (`players_min`,`players_max`)
) ENGINE=MyISAM AUTO_INCREMENT=5706 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scerun`
--

DROP TABLE IF EXISTS `scerun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scerun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sce_id` int(11) NOT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `location` tinytext COLLATE utf8_danish_ci,
  `description` text COLLATE utf8_danish_ci,
  `cancelled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sce_id` (`sce_id`),
  KEY `begin` (`begin`)
) ENGINE=MyISAM AUTO_INCREMENT=386 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searches`
--

DROP TABLE IF EXISTS `searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `find` tinytext COLLATE utf8_danish_ci,
  `found` tinytext COLLATE utf8_danish_ci,
  `referer` tinytext COLLATE utf8_danish_ci,
  `searchtime` datetime DEFAULT NULL,
  `ip` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=352677 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys`
--

DROP TABLE IF EXISTS `sys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_danish_ci DEFAULT NULL,
  `description` text COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `priority` tinyint(4) NOT NULL,
  `iconfile` varchar(64) CHARACTER SET utf8 COLLATE utf8_danish_ci DEFAULT NULL,
  `iconwidth` tinyint(4) DEFAULT NULL,
  `iconheight` tinyint(4) DEFAULT NULL,
  `textsymbol` tinytext COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trivia`
--

DROP TABLE IF EXISTS `trivia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trivia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys') COLLATE utf8_danish_ci NOT NULL DEFAULT 'aut',
  `fact` text COLLATE utf8_danish_ci,
  `hidden` text COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1761 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys','links','trivia') COLLATE latin1_danish_ci DEFAULT NULL,
  `title` tinytext COLLATE latin1_danish_ci,
  `description` text COLLATE latin1_danish_ci,
  `submittime` datetime DEFAULT NULL,
  `user_name` tinytext COLLATE latin1_danish_ci,
  `user_email` tinytext COLLATE latin1_danish_ci,
  `user_id` int(11) NOT NULL,
  `intern` text COLLATE latin1_danish_ci NOT NULL,
  `status` enum('åben','i gang','lukket') COLLATE latin1_danish_ci NOT NULL DEFAULT 'åben',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3297 DEFAULT CHARSET=latin1 COLLATE=latin1_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_achievements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `completed` datetime DEFAULT NULL,
  `shown` tinyint(4) NOT NULL,
  `ip` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`achievement_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3116 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userlog`
--

DROP TABLE IF EXISTS `userlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `category` enum('sce','convent') COLLATE utf8_danish_ci NOT NULL DEFAULT 'sce',
  `data_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(8) COLLATE utf8_danish_ci DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`category`,`data_id`,`type`),
  KEY `user_id` (`user_id`,`category`,`data_id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27339 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_danish_ci,
  `created` datetime NOT NULL,
  `log` mediumtext COLLATE utf8_danish_ci NOT NULL,
  `aut_id` int(11) DEFAULT NULL,
  `editor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(3) unsigned NOT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `login_days_in_row` int(11) DEFAULT NULL,
  `login_count` int(11) DEFAULT NULL,
  `active_days_in_row` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `last_login` (`last_login`),
  KEY `last_active` (`last_active`)
) ENGINE=MyISAM AUTO_INCREMENT=442 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_acrel_blank`
--

DROP TABLE IF EXISTS `v_acrel_blank`;
/*!50001 DROP VIEW IF EXISTS `v_acrel_blank`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_acrel_blank` AS SELECT 
 1 AS `COUNT(*)`,
 1 AS `aut_extra`,
 1 AS `GROUP_CONCAT(convent_id)`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_aut`
--

DROP TABLE IF EXISTS `v_aut`;
/*!50001 DROP VIEW IF EXISTS `v_aut`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_aut` AS SELECT 
 1 AS `id`,
 1 AS `navn`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_sce`
--

DROP TABLE IF EXISTS `v_sce`;
/*!50001 DROP VIEW IF EXISTS `v_sce`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_sce` AS SELECT 
 1 AS `id`,
 1 AS `title`,
 1 AS `system`,
 1 AS `forfattere`,
 1 AS `cons`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vc29_rpg`
--

DROP TABLE IF EXISTS `vc29_rpg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vc29_rpg` (
  `titel` text COLLATE utf8_danish_ci,
  `forkortelse` text COLLATE utf8_danish_ci,
  `forfatter` text COLLATE utf8_danish_ci,
  `system` text COLLATE utf8_danish_ci,
  `genre` text COLLATE utf8_danish_ci,
  `foromtale` text COLLATE utf8_danish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `v_acrel_blank`
--

/*!50001 DROP VIEW IF EXISTS `v_acrel_blank`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`rpg`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_acrel_blank` AS select count(0) AS `COUNT(*)`,`acrel`.`aut_extra` AS `aut_extra`,group_concat(`acrel`.`convent_id` separator ',') AS `GROUP_CONCAT(convent_id)` from `acrel` where isnull(`acrel`.`aut_id`) group by `acrel`.`aut_extra` order by count(0) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_aut`
--

/*!50001 DROP VIEW IF EXISTS `v_aut`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`rpg`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_aut` AS select `aut`.`id` AS `id`,concat(`aut`.`firstname`,_utf8' ',`aut`.`surname`) AS `navn` from `aut` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_sce`
--

/*!50001 DROP VIEW IF EXISTS `v_sce`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`rpg`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sce` AS select `a`.`id` AS `id`,`a`.`title` AS `title`,coalesce(`b`.`name`,`a`.`sys_ext`) AS `system`,group_concat(distinct concat(`d`.`firstname`,_utf8' ',`d`.`surname`) separator ',') AS `forfattere`,group_concat(concat(`f`.`name`,' (',`f`.`year`,')') separator ',') AS `cons` from (((((`sce` `a` left join `sys` `b` on((`a`.`sys_id` = `b`.`id`))) left join `asrel` `c` on(((`a`.`id` = `c`.`sce_id`) and (`c`.`tit_id` = 1)))) left join `aut` `d` on((`c`.`aut_id` = `d`.`id`))) left join `csrel` `e` on((`a`.`id` = `e`.`sce_id`))) left join `convent` `f` on((`f`.`id` = `e`.`convent_id`))) group by `a`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-10-18  0:13:18
