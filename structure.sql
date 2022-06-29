-- MySQL dump 10.13  Distrib 8.0.29, for Linux (x86_64)
--
-- Host: localhost    Database: alexandria
-- ------------------------------------------------------
-- Server version	8.0.29-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `icon` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `available` tinyint NOT NULL DEFAULT '0',
  `special` tinyint NOT NULL,
  `points` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acrel`
--

DROP TABLE IF EXISTS `acrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acrel` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `aut_id` int DEFAULT NULL,
  `convent_id` int NOT NULL DEFAULT '0',
  `aut_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `role` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `added_by_user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aut_id` (`aut_id`),
  KEY `convent_id` (`convent_id`),
  CONSTRAINT `acrel_FK` FOREIGN KEY (`convent_id`) REFERENCES `convent` (`id`),
  CONSTRAINT `acrel_FK_1` FOREIGN KEY (`aut_id`) REFERENCES `aut` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6312 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `actions_log`
--

DROP TABLE IF EXISTS `actions_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actions_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `responseid` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `session` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `intent` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `incoming_raw` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `outgoing_raw` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `logtime` datetime DEFAULT NULL,
  `logip` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alias`
--

DROP TABLE IF EXISTS `alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT 'aut',
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `visible` tinyint NOT NULL DEFAULT '0',
  `language` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`),
  KEY `label` (`label`(20))
) ENGINE=InnoDB AUTO_INCREMENT=1682 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `page` int DEFAULT NULL,
  `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `articletype` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `sce_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `convent_id` (`issue_id`) USING BTREE,
  KEY `airel_FK_2` (`sce_id`),
  CONSTRAINT `airel_FK_1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `airel_FK_2` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6034 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article_reference`
--

DROP TABLE IF EXISTS `article_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_reference` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int unsigned NOT NULL,
  `category` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `data_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_relation_FK` (`article_id`),
  KEY `article_reference_category_IDX` (`category`(8),`data_id`) USING BTREE,
  CONSTRAINT `article_relation_FK` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=12079 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asrel`
--

DROP TABLE IF EXISTS `asrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `aut_id` int NOT NULL DEFAULT '0',
  `sce_id` int NOT NULL DEFAULT '0',
  `tit_id` int NOT NULL DEFAULT '1',
  `note` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aut_id` (`aut_id`),
  KEY `sce_id` (`sce_id`),
  KEY `tit_id` (`tit_id`),
  CONSTRAINT `asrel_FK` FOREIGN KEY (`aut_id`) REFERENCES `aut` (`id`),
  CONSTRAINT `asrel_FK_1` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`),
  CONSTRAINT `asrel_FK_2` FOREIGN KEY (`tit_id`) REFERENCES `title` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61435 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `atl`
--

DROP TABLE IF EXISTS `atl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titel` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `forfatter1` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `forfatter2` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `genre` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `system` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `skrevettil` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `filnavn` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `bemark` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `journal` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `sider` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `synopsis` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `view` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut`
--

DROP TABLE IF EXISTS `aut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aut` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `surname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `birth` date DEFAULT NULL,
  `death` date DEFAULT NULL,
  `rpgdk_id` int DEFAULT NULL,
  `intern` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `picfile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `popularity` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `firstname` (`firstname`,`surname`),
  KEY `surname` (`surname`,`firstname`)
) ENGINE=InnoDB AUTO_INCREMENT=7311 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_categories`
--

DROP TABLE IF EXISTS `award_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `award_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `convent_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `award_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `convent_id` (`convent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_nominee_entities`
--

DROP TABLE IF EXISTS `award_nominee_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `award_nominee_entities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `award_nominee_id` int NOT NULL,
  `data_id` int DEFAULT NULL,
  `category` enum('aut','sce') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `label` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `award_nominee` (`award_nominee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `award_nominees`
--

DROP TABLE IF EXISTS `award_nominees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `award_nominees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `award_category_id` int NOT NULL,
  `sce_id` int DEFAULT NULL,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `nominationtext` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `winner` tinyint DEFAULT NULL,
  `ranking` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1549 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `awards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `conset_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `label` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conset`
--

DROP TABLE IF EXISTS `conset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `intern` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `country` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contributor`
--

DROP TABLE IF EXISTS `contributor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contributor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `aut_id` int DEFAULT NULL,
  `aut_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `role` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `article_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contributor_FK` (`article_id`),
  KEY `contributor_FK_1` (`aut_id`),
  CONSTRAINT `contributor_FK` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `contributor_FK_1` FOREIGN KEY (`aut_id`) REFERENCES `aut` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=26650 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci COMMENT='Contributor to article';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `convent`
--

DROP TABLE IF EXISTS `convent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `convent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `place` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `intern` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `confirmed` tinyint NOT NULL DEFAULT '0',
  `cancelled` tinyint NOT NULL DEFAULT '0',
  `country` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `conset_id` (`conset_id`),
  KEY `end` (`end`),
  KEY `year` (`year`),
  CONSTRAINT `convent_FK` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1698 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `csrel`
--

DROP TABLE IF EXISTS `csrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `csrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `convent_id` int NOT NULL DEFAULT '0',
  `sce_id` int NOT NULL DEFAULT '0',
  `pre_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `con_id` (`convent_id`),
  KEY `pre_id` (`pre_id`),
  KEY `sce_id` (`sce_id`),
  CONSTRAINT `csrel_FK` FOREIGN KEY (`convent_id`) REFERENCES `convent` (`id`),
  CONSTRAINT `csrel_FK_1` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`),
  CONSTRAINT `csrel_FK_2` FOREIGN KEY (`pre_id`) REFERENCES `pre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49032 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedcontent`
--

DROP TABLE IF EXISTS `feedcontent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedcontent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feed_id` int NOT NULL,
  `title` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `guid` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `link` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `comments` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pubdate` (`pubdate`),
  KEY `guid` (`guid`(85)),
  KEY `rssfeed_id` (`feed_id`,`guid`(85)),
  CONSTRAINT `feedcontent_FK` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9298 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feeds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `owner` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `aut_id` int DEFAULT NULL,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `pageurl` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `lastchecked` datetime DEFAULT NULL,
  `podcast` tinyint NOT NULL DEFAULT '0',
  `pauseupdate` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filedata`
--

DROP TABLE IF EXISTS `filedata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `filedata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `files_id` int NOT NULL,
  `label` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `archivefile` text COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=InnoDB AUTO_INCREMENT=143903 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filedownloads`
--

DROP TABLE IF EXISTS `filedownloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `filedownloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `files_id` int NOT NULL,
  `data_id` int NOT NULL,
  `category` enum('aut','sce','convent','conset','sys') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `accesstime` datetime DEFAULT NULL,
  `browser` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `referer` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1243028 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int NOT NULL,
  `category` enum('aut','sce','convent','conset','sys','tag','issue') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `filename` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `downloadable` tinyint NOT NULL,
  `inserted` datetime DEFAULT NULL,
  `language` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `indexed` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`),
  KEY `indexed` (`indexed`,`downloadable`)
) ENGINE=InnoDB AUTO_INCREMENT=4646 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_description`
--

DROP TABLE IF EXISTS `game_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_description` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `note` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `priority` tinyint NOT NULL DEFAULT '1',
  `intern` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`game_id`),
  FULLTEXT KEY `description` (`description`),
  CONSTRAINT `game_description_FK` FOREIGN KEY (`game_id`) REFERENCES `sce` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gen`
--

DROP TABLE IF EXISTS `gen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(24) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `genre` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gsrel`
--

DROP TABLE IF EXISTS `gsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gsrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gen_id` int NOT NULL DEFAULT '0',
  `sce_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sce_id` (`sce_id`,`gen_id`),
  KEY `gen_id` (`gen_id`),
  CONSTRAINT `gsrel_FK` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`),
  CONSTRAINT `gsrel_FK_1` FOREIGN KEY (`gen_id`) REFERENCES `gen` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4249 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `installation`
--

DROP TABLE IF EXISTS `installation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `installation` (
  `key` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `value` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `issue`
--

DROP TABLE IF EXISTS `issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `issue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `magazine_id` int DEFAULT NULL,
  `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `releasedate` date DEFAULT NULL,
  `releasetext` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `issue_FK` (`magazine_id`),
  CONSTRAINT `issue_FK` FOREIGN KEY (`magazine_id`) REFERENCES `magazine` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=681 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys','tag') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `url` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `description` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3202 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_arrangoer`
--

DROP TABLE IF EXISTS `live_arrangoer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_arrangoer` (
  `titel` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `arrangoer` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys','links','trivia','alias','tag','language','review','issue','magazine') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `user` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`),
  KEY `user` (`user`,`id`),
  KEY `time` (`time`)
) ENGINE=InnoDB AUTO_INCREMENT=114086 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loginmap`
--

DROP TABLE IF EXISTS `loginmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loginmap` (
  `site` enum('rpgforum','liveforum','digisign','facebook','alexandria','twitter','steam','twitch','spotify','google','discord') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `siteuserid` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `user_id` int NOT NULL,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `logintime` int unsigned DEFAULT NULL,
  PRIMARY KEY (`site`,`siteuserid`(16)),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magazine`
--

DROP TABLE IF EXISTS `magazine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magazine` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `published` datetime NOT NULL,
  `online` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pre`
--

DROP TABLE IF EXISTS `pre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `event_label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `iconfile` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `textsymbol` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remotelogin`
--

DROP TABLE IF EXISTS `remotelogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remotelogin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `salt` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `loginurl` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `title` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `relation` set('gm','read','played') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `spoiler` tinyint NOT NULL DEFAULT '0',
  `aut_id` int DEFAULT NULL,
  `reviewer` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` enum('person','game','convent','conset','rpgsystem') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `title` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `spoilertext` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `relation` set('gm','read','played') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `spoiler` tinyint NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `reviewer` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `syndicatedurl` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `visible` tinyint NOT NULL DEFAULT '0',
  `language` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpgforum_posts`
--

DROP TABLE IF EXISTS `rpgforum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpgforum_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `author` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `timestamp` datetime DEFAULT NULL,
  `views` int DEFAULT NULL,
  `post` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `timestamp_idx` (`timestamp`),
  FULLTEXT KEY `post_idx` (`title`,`post`),
  FULLTEXT KEY `post_aut_idx` (`author`)
) ENGINE=InnoDB AUTO_INCREMENT=63867 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sce`
--

DROP TABLE IF EXISTS `sce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sce` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `intern` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `sys_id` int DEFAULT NULL,
  `sys_ext` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `aut_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `hidden` tinyint NOT NULL DEFAULT '1',
  `ottowinner` tinyint NOT NULL DEFAULT '0',
  `rlyeh_id` tinyint NOT NULL DEFAULT '0',
  `gms_min` int DEFAULT NULL,
  `gms_max` int DEFAULT NULL,
  `players_min` int DEFAULT NULL,
  `players_max` int DEFAULT NULL,
  `participants_extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `boardgame` tinyint NOT NULL,
  `popularity` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hidden` (`hidden`),
  KEY `sys_id` (`sys_id`),
  KEY `title` (`title`),
  KEY `players_min` (`players_min`,`players_max`),
  KEY `boardgame` (`boardgame`),
  CONSTRAINT `sce_FK` FOREIGN KEY (`sys_id`) REFERENCES `sys` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13022 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scerun`
--

DROP TABLE IF EXISTS `scerun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scerun` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sce_id` int NOT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `location` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `cancelled` tinyint NOT NULL DEFAULT '0',
  `country` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`sce_id`),
  KEY `begin` (`begin`),
  CONSTRAINT `scerun_FK` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1163 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searches`
--

DROP TABLE IF EXISTS `searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `searches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `find` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `found` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `referer` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `searchtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=558508 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys`
--

DROP TABLE IF EXISTS `sys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tag` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`(8))
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sce_id` int NOT NULL,
  `tag` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `added_by_user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`sce_id`),
  KEY `tag` (`tag`(8)),
  CONSTRAINT `tags_FK` FOREIGN KEY (`sce_id`) REFERENCES `sce` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5657 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `title` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `title_label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `priority` tinyint NOT NULL,
  `iconfile` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `iconwidth` tinyint DEFAULT NULL,
  `iconheight` tinyint DEFAULT NULL,
  `textsymbol` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trivia`
--

DROP TABLE IF EXISTS `trivia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trivia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int NOT NULL DEFAULT '0',
  `category` enum('aut','sce','convent','conset','sys','tag') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `fact` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `hidden` text CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2805 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` enum('aut','sce','convent','conset','sys','links','trivia','issue','magazine') CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT NULL,
  `title` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `description` text CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `submittime` datetime DEFAULT NULL,
  `user_name` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `user_email` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `user_id` int NOT NULL,
  `intern` text CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL,
  `status` enum('open','in progress','closed') COLLATE latin1_danish_ci NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3578 DEFAULT CHARSET=latin1 COLLATE=latin1_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `achievement_id` int unsigned NOT NULL,
  `completed` datetime DEFAULT NULL,
  `shown` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`achievement_id`),
  KEY `user_achievements_FK_1` (`achievement_id`),
  CONSTRAINT `user_achievements_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_achievements_FK_1` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4312 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userlog`
--

DROP TABLE IF EXISTS `userlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userlog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `category` enum('sce','convent') CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL DEFAULT 'sce',
  `data_id` int NOT NULL DEFAULT '0',
  `type` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8_danish_ci DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`category`,`data_id`,`type`),
  KEY `user_id` (`user_id`,`category`,`data_id`),
  KEY `category` (`category`,`data_id`),
  CONSTRAINT `userlog_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30615 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci,
  `created` datetime NOT NULL,
  `log` mediumtext CHARACTER SET utf8mb3 COLLATE utf8_danish_ci NOT NULL,
  `aut_id` int DEFAULT NULL,
  `editor` tinyint unsigned NOT NULL DEFAULT '0',
  `admin` tinyint unsigned NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `login_days_in_row` int DEFAULT NULL,
  `login_count` int DEFAULT NULL,
  `active_days_in_row` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `last_login` (`last_login`),
  KEY `last_active` (`last_active`),
  KEY `users_FK` (`aut_id`),
  CONSTRAINT `users_FK` FOREIGN KEY (`aut_id`) REFERENCES `aut` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=619 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weblanguages`
--

DROP TABLE IF EXISTS `weblanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weblanguages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `lastupdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7346 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-29 16:35:20
