-- Alexandria Database Schema
-- Generated from alexandria_structure.json
-- Created on: August 4, 2025
-- Tables ordered by dependency requirements

-- ============================================
-- LEVEL 1: Independent tables (no foreign keys)
-- ============================================

-- Table: person
CREATE TABLE `person` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `surname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `birth` date DEFAULT NULL,
  `death` date DEFAULT NULL,
  `rpgdk_id` int DEFAULT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `picfile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `popularity` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `firstname` (`firstname`,`surname`),
  KEY `surname` (`surname`,`firstname`)
) ENGINE=InnoDB AUTO_INCREMENT=10278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;

-- Table: gamesystem
CREATE TABLE `gamesystem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: conset
CREATE TABLE `conset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `country` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: genre
CREATE TABLE `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `genre` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: tag
CREATE TABLE `tag` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tag` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`(8))
) ENGINE=InnoDB AUTO_INCREMENT=545 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: title
CREATE TABLE `title` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL DEFAULT '',
  `title_label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `priority` tinyint NOT NULL,
  `iconfile` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci DEFAULT NULL,
  `iconwidth` tinyint DEFAULT NULL,
  `iconheight` tinyint DEFAULT NULL,
  `textsymbol` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;

-- Table: presentation
CREATE TABLE `presentation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci DEFAULT NULL,
  `event_label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL DEFAULT '',
  `iconfile` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci DEFAULT NULL,
  `textsymbol` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: feeds
CREATE TABLE `feeds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `owner` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `person_id` int DEFAULT NULL,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `pageurl` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `lastchecked` datetime DEFAULT NULL,
  `podcast` tinyint NOT NULL DEFAULT '0',
  `pauseupdate` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: weblanguages
CREATE TABLE `weblanguages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `lastupdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7775 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: news
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `published` datetime NOT NULL,
  `online` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=320 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: achievements
CREATE TABLE `achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `icon` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `available` tinyint NOT NULL DEFAULT '0',
  `special` tinyint NOT NULL,
  `points` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: searches
CREATE TABLE `searches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `find` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci,
  `found` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci,
  `referer` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci,
  `searchtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1839015 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: updates
CREATE TABLE `updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL,
  `title` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `description` text CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `submittime` datetime DEFAULT NULL,
  `user_name` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `user_email` tinytext CHARACTER SET latin1 COLLATE latin1_danish_ci,
  `user_id` int NOT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `status` enum('open','in progress','closed') CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: installation
CREATE TABLE `installation` (
  `key` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `value` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: magazine
CREATE TABLE `magazine` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: rpgforum_posts
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

-- Table: filedata
CREATE TABLE `filedata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `files_id` int NOT NULL,
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `archivefile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=InnoDB AUTO_INCREMENT=186734 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: locations
CREATE TABLE `locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `city` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `geo` geometry /*!80003 SRID 4326 */ DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=847 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: log
CREATE TABLE `log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_id` int DEFAULT NULL,
  `category` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `time` datetime DEFAULT NULL,
  `user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `user` (`user`,`id`),
  KEY `time` (`time`),
  KEY `log_category_IDX` (`category`(8),`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=169823 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- ============================================
-- LEVEL 2: Tables with simple dependencies
-- ============================================

-- Table: game (depends on: gamesystem)
CREATE TABLE `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `gamesystem_id` int DEFAULT NULL,
  `gamesystem_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `person_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
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
  KEY `sys_id` (`gamesystem_id`),
  KEY `title` (`title`),
  KEY `players_min` (`players_min`,`players_max`),
  KEY `boardgame` (`boardgame`),
  CONSTRAINT `sce_FK` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;

-- Table: convention (depends on: conset)
CREATE TABLE `convention` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `place` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `confirmed` tinyint NOT NULL DEFAULT '0',
  `cancelled` tinyint NOT NULL DEFAULT '0',
  `country` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `conset_id` (`conset_id`),
  KEY `end` (`end`),
  KEY `year` (`year`),
  CONSTRAINT `convent_FK` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2732 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: users (depends on: person)
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci,
  `created` datetime NOT NULL,
  `log` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `person_id` int DEFAULT NULL,
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
  KEY `users_FK` (`person_id`),
  CONSTRAINT `users_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=817 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: issue (depends on: magazine)
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
) ENGINE=InnoDB AUTO_INCREMENT=1343 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- ============================================
-- LEVEL 3: Tables with multiple dependencies
-- ============================================

-- Table: ggrel (depends on: game, genre)
CREATE TABLE `ggrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `genre_id` int NOT NULL DEFAULT '0',
  `game_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sce_id` (`game_id`,`genre_id`),
  KEY `gen_id` (`genre_id`),
  CONSTRAINT `gsrel_FK` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `gsrel_FK_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4720 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: tags (depends on: game)
CREATE TABLE `tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `tag` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `added_by_user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`game_id`),
  KEY `tag` (`tag`(8)),
  CONSTRAINT `tags_FK` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7432 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: gamerun (depends on: game)
CREATE TABLE `gamerun` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `location` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `cancelled` tinyint NOT NULL DEFAULT '0',
  `country` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`game_id`),
  KEY `begin` (`begin`),
  CONSTRAINT `scerun_FK` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: game_description (depends on: game)
CREATE TABLE `game_description` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `note` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `priority` tinyint NOT NULL DEFAULT '1',
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `sce_id` (`game_id`),
  FULLTEXT KEY `description` (`description`),
  CONSTRAINT `game_description_FK` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40699 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: feedcontent (depends on: feeds)
CREATE TABLE `feedcontent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feed_id` int NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `guid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `comments` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pubdate` (`pubdate`),
  KEY `guid` (`guid`(85)),
  KEY `rssfeed_id` (`feed_id`,`guid`(85)),
  CONSTRAINT `feedcontent_FK` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: userlog (depends on: users, game, convention)
CREATE TABLE `userlog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `type` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userlog_FK` (`user_id`),
  KEY `userlog_FK_1` (`game_id`),
  KEY `userlog_FK_2` (`convention_id`),
  CONSTRAINT `userlog_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `userlog_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `userlog_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=32703 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: loginmap
CREATE TABLE `loginmap` (
  `site` enum('rpgforum','liveforum','digisign','alexandria','steam','spotify','google','discord') CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `siteuserid` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `user_id` int NOT NULL,
  `name` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `logintime` int unsigned DEFAULT NULL,
  PRIMARY KEY (`site`,`siteuserid`(16)),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: user_achievements (depends on: users, achievements)
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
) ENGINE=InnoDB AUTO_INCREMENT=5403 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: article (depends on: issue, game)
CREATE TABLE `article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `page` int DEFAULT NULL,
  `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `articletype` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `game_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `convent_id` (`issue_id`) USING BTREE,
  KEY `airel_FK_2` (`game_id`),
  CONSTRAINT `airel_FK_1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `airel_FK_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- ============================================
-- LEVEL 4: Tables with complex dependencies
-- ============================================

-- Table: trivia (depends on: person, game, convention, conset, gamesystem, tag)
CREATE TABLE `trivia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `internal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `person_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `gamesystem_id` int DEFAULT NULL,
  `tag_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trivia_FK` (`person_id`),
  KEY `trivia_FK_1` (`game_id`),
  KEY `trivia_FK_2` (`convention_id`),
  KEY `trivia_FK_3` (`conset_id`),
  KEY `trivia_FK_4` (`gamesystem_id`),
  KEY `trivia_FK_5` (`tag_id`),
  CONSTRAINT `trivia_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trivia_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trivia_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trivia_FK_3` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trivia_FK_4` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trivia_FK_5` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3422 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: links (depends on: person, game, convention, conset, gamesystem, tag)
CREATE TABLE `links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `person_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `gamesystem_id` int DEFAULT NULL,
  `tag_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `links_FK` (`person_id`),
  KEY `links_FK_1` (`game_id`),
  KEY `links_FK_2` (`convention_id`),
  KEY `links_FK_3` (`conset_id`),
  KEY `links_FK_4` (`gamesystem_id`),
  KEY `links_FK_5` (`tag_id`),
  CONSTRAINT `links_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `links_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `links_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `links_FK_3` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `links_FK_4` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `links_FK_5` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4599 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: alias (depends on: person, game, convention, conset, gamesystem, locations)
CREATE TABLE `alias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `visible` tinyint NOT NULL DEFAULT '0',
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `person_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `gamesystem_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `label` (`label`(20)),
  KEY `alias_FK` (`person_id`),
  KEY `alias_FK_1` (`game_id`),
  KEY `alias_FK_2` (`convention_id`),
  KEY `alias_FK_3` (`conset_id`),
  KEY `alias_FK_4` (`gamesystem_id`),
  KEY `alias_locations_FK` (`location_id`),
  CONSTRAINT `alias_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `alias_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `alias_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `alias_FK_3` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `alias_FK_4` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `alias_locations_FK` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2522 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: pgrel (depends on: person, game, title, convention, gamerun)
CREATE TABLE `pgrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL DEFAULT '0',
  `game_id` int NOT NULL DEFAULT '0',
  `title_id` int NOT NULL DEFAULT '1',
  `convention_id` int DEFAULT NULL,
  `gamerun_id` int DEFAULT NULL,
  `note` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aut_id` (`person_id`),
  KEY `sce_id` (`game_id`),
  KEY `tit_id` (`title_id`),
  KEY `pgrel_FK` (`gamerun_id`),
  KEY `pgrel_FK_1` (`convention_id`),
  CONSTRAINT `asrel_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `asrel_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `asrel_FK_2` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`),
  CONSTRAINT `pgrel_FK` FOREIGN KEY (`gamerun_id`) REFERENCES `gamerun` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `pgrel_FK_1` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=92215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci PACK_KEYS=1;

-- Table: cgrel (depends on: convention, game, presentation)
CREATE TABLE `cgrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `convention_id` int NOT NULL DEFAULT '0',
  `game_id` int NOT NULL DEFAULT '0',
  `presentation_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `con_id` (`convention_id`),
  KEY `pre_id` (`presentation_id`),
  KEY `sce_id` (`game_id`),
  CONSTRAINT `csrel_FK` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`),
  CONSTRAINT `csrel_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `csrel_FK_2` FOREIGN KEY (`presentation_id`) REFERENCES `presentation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70313 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: pcrel (depends on: person, convention)
CREATE TABLE `pcrel` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `convention_id` int NOT NULL DEFAULT '0',
  `person_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `role` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `added_by_user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aut_id` (`person_id`),
  KEY `convent_id` (`convention_id`),
  CONSTRAINT `acrel_FK` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`),
  CONSTRAINT `acrel_FK_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9324 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: awards (depends on: conset, tag)
CREATE TABLE `awards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `conset_id` int DEFAULT NULL,
  `tag_id` int unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  KEY `awards_FK` (`conset_id`),
  KEY `awards_FK_1` (`tag_id`),
  CONSTRAINT `awards_FK` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `awards_FK_1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: award_categories (depends on: awards)
CREATE TABLE `award_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `convention_id` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `award_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `convent_id` (`convention_id`),
  KEY `award_categories_tag_id_IDX` (`tag_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=937 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: award_nominees (depends on: award_categories)
CREATE TABLE `award_nominees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `award_category_id` int NOT NULL,
  `game_id` int DEFAULT NULL,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `nominationtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `winner` tinyint DEFAULT NULL,
  `ranking` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `award_nominees_FK` (`award_category_id`),
  CONSTRAINT `award_nominees_FK` FOREIGN KEY (`award_category_id`) REFERENCES `award_categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2220 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: award_nominee_entities (depends on: person, game, award_nominees)
CREATE TABLE `award_nominee_entities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `award_nominee_id` int NOT NULL,
  `label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `person_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `award_nominee` (`award_nominee_id`),
  KEY `award_nominee_entities_FK` (`person_id`),
  KEY `award_nominee_entities_FK_1` (`game_id`),
  CONSTRAINT `award_nominee_entities_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `award_nominee_entities_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `award_nominee_entities_FK_2` FOREIGN KEY (`award_nominee_id`) REFERENCES `award_nominees` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: contributor (depends on: person, article)
CREATE TABLE `contributor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `person_extra` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `role` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `article_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contributor_FK` (`article_id`),
  KEY `contributor_FK_1` (`person_id`),
  CONSTRAINT `contributor_FK` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `contributor_FK_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=41739 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci COMMENT='Contributor to article';

-- Table: article_reference (depends on: article, person, game, convention, conset, gamesystem, tag, magazine, issue)
CREATE TABLE `article_reference` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int unsigned NOT NULL,
  `person_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `gamesystem_id` int DEFAULT NULL,
  `tag_id` int unsigned DEFAULT NULL,
  `magazine_id` int DEFAULT NULL,
  `issue_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_relation_FK` (`article_id`),
  KEY `article_reference_FK` (`person_id`),
  KEY `article_reference_FK_1` (`game_id`),
  KEY `article_reference_FK_2` (`convention_id`),
  KEY `article_reference_FK_3` (`conset_id`),
  KEY `article_reference_FK_4` (`gamesystem_id`),
  KEY `article_reference_FK_5` (`tag_id`),
  KEY `article_reference_FK_6` (`magazine_id`),
  KEY `article_reference_FK_7` (`issue_id`),
  CONSTRAINT `article_reference_FK` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_3` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_4` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_5` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_6` FOREIGN KEY (`magazine_id`) REFERENCES `magazine` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_reference_FK_7` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `article_relation_FK` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=22829 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: files (depends on: game, convention, conset, gamesystem, tag, issue)
CREATE TABLE `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `downloadable` tinyint NOT NULL,
  `inserted` datetime DEFAULT NULL,
  `language` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `indexed` tinyint NOT NULL DEFAULT '0',
  `game_id` int DEFAULT NULL,
  `convention_id` int DEFAULT NULL,
  `conset_id` int DEFAULT NULL,
  `gamesystem_id` int DEFAULT NULL,
  `tag_id` int unsigned DEFAULT NULL,
  `issue_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indexed` (`indexed`,`downloadable`),
  KEY `files_FK` (`issue_id`),
  KEY `files_FK_1` (`game_id`),
  KEY `files_FK_2` (`convention_id`),
  KEY `files_FK_3` (`conset_id`),
  KEY `files_FK_4` (`gamesystem_id`),
  KEY `files_FK_5` (`tag_id`),
  CONSTRAINT `files_FK` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `files_FK_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `files_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `files_FK_3` FOREIGN KEY (`conset_id`) REFERENCES `conset` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `files_FK_4` FOREIGN KEY (`gamesystem_id`) REFERENCES `gamesystem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `files_FK_5` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6811 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: lrel (depends on: locations, gamerun, convention)
CREATE TABLE `lrel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `location_id` int NOT NULL,
  `convention_id` int DEFAULT NULL,
  `gamerun_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lrel_FK` (`location_id`),
  KEY `lrel_FK_1` (`gamerun_id`),
  KEY `lrel_FK_2` (`convention_id`),
  CONSTRAINT `lrel_FK` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `lrel_FK_1` FOREIGN KEY (`gamerun_id`) REFERENCES `gamerun` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `lrel_FK_2` FOREIGN KEY (`convention_id`) REFERENCES `convention` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;

-- Table: filedownloads
CREATE TABLE `filedownloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `files_id` int NOT NULL,
  `data_id` int NOT NULL,
  `category` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `accesstime` datetime DEFAULT NULL,
  `browser` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  `referer` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_id` (`files_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3074886 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
