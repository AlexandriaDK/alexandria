-- Large rename update
-- Remember new version of Smarty 4.1.1

RENAME TABLE aut TO person;
RENAME TABLE sce TO game;
RENAME TABLE scerun TO gamerun;
RENAME TABLE gen TO genre;
RENAME TABLE convent TO convention;
RENAME TABLE sys TO gamesystem;
RENAME TABLE pre TO presentation;

RENAME TABLE asrel TO pgrel;
RENAME TABLE acrel TO pcrel;
RENAME TABLE csrel TO cgrel;
RENAME TABLE gsrel TO ggrel;

ALTER TABLE cgrel CHANGE convent_id convention_id int DEFAULT 0 NOT NULL;
ALTER TABLE cgrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;
ALTER TABLE cgrel CHANGE pre_id presentation_id int DEFAULT 1 NOT NULL;

ALTER TABLE pgrel CHANGE aut_id person_id int DEFAULT 0 NOT NULL;
ALTER TABLE pgrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;
ALTER TABLE pgrel CHANGE tit_id title_id int DEFAULT 1 NOT NULL;

ALTER TABLE pcrel CHANGE aut_id person_id int NULL;
ALTER TABLE pcrel CHANGE convent_id convention_id int DEFAULT 0 NOT NULL;
ALTER TABLE pcrel CHANGE aut_extra person_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE ggrel CHANGE gen_id genre_id int DEFAULT 0 NOT NULL;
ALTER TABLE ggrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;

ALTER TABLE game CHANGE sys_id gamesystem_id int NULL;
ALTER TABLE game CHANGE sys_ext gamesystem_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE game CHANGE aut_extra person_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE gamerun CHANGE sce_id game_id int NOT NULL;

ALTER TABLE users CHANGE aut_id person_id int NULL;

ALTER TABLE tags CHANGE sce_id game_id int NOT NULL;

ALTER TABLE feeds CHANGE aut_id person_id int NULL;

ALTER TABLE article CHANGE sce_id game_id int NULL;

ALTER TABLE contributor CHANGE aut_id person_id int NULL;
ALTER TABLE contributor CHANGE aut_extra person_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE person CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE game CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE game_description CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE convention CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE conset CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE updates CHANGE intern internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE award_nominees CHANGE sce_id game_id int NULL;
ALTER TABLE award_categories CHANGE convent_id convention_id int NULL;

-- Change `category`, `data_id` to explicit fields for every data type for the purpose of foreign keys
-- Done: Links, Trivia, Files, Alias, Article_reference, award_nominee_entities
-- Todo: Filedownloads, log, review, reviews, updates, userlog, ...

-- Cleanup; orphans
DELETE FROM trivia WHERE id = 99;
DELETE FROM links WHERE id IN (174, 519);
DELETE FROM article_reference WHERE id = 1749;
DELETE FROM award_nominee_entities where id = 249; -- Nickolaj Storgaard Oksen, Forum 2020

-- Trivia
ALTER TABLE trivia DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci;
ALTER TABLE trivia CHANGE hidden internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE trivia MODIFY COLUMN internal text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE trivia MODIFY COLUMN fact text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE trivia ADD person_id int NULL;
ALTER TABLE trivia ADD game_id int NULL;
ALTER TABLE trivia ADD convention_id int NULL;
ALTER TABLE trivia ADD conset_id int NULL;
ALTER TABLE trivia ADD gamesystem_id int NULL;
ALTER TABLE trivia ADD tag_id int unsigned NULL;

UPDATE trivia SET
person_id = IF(category = 'aut', data_id, NULL),
game_id = IF(category = 'sce', data_id, NULL),
convention_id = IF(category = 'convent', data_id, NULL),
conset_id = IF(category = 'conset', data_id, NULL),
gamesystem_id = IF(category = 'sys', data_id, NULL),
tag_id = IF(category = 'tag', data_id, NULL);

ALTER TABLE trivia ADD CONSTRAINT trivia_FK FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE trivia ADD CONSTRAINT trivia_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE trivia ADD CONSTRAINT trivia_FK_2 FOREIGN KEY (convention_id) REFERENCES convention(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE trivia ADD CONSTRAINT trivia_FK_3 FOREIGN KEY (conset_id) REFERENCES conset(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE trivia ADD CONSTRAINT trivia_FK_4 FOREIGN KEY (gamesystem_id) REFERENCES gamesystem(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE trivia ADD CONSTRAINT trivia_FK_5 FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE trivia DROP COLUMN data_id;
ALTER TABLE trivia DROP COLUMN category;

-- Links
ALTER TABLE links ADD person_id int NULL;
ALTER TABLE links ADD game_id int NULL;
ALTER TABLE links ADD convention_id int NULL;
ALTER TABLE links ADD conset_id int NULL;
ALTER TABLE links ADD gamesystem_id int NULL;
ALTER TABLE links ADD tag_id int unsigned NULL;

UPDATE links SET
person_id = IF(category = 'aut', data_id, NULL),
game_id = IF(category = 'sce', data_id, NULL),
convention_id = IF(category = 'convent', data_id, NULL),
conset_id = IF(category = 'conset', data_id, NULL),
gamesystem_id = IF(category = 'sys', data_id, NULL),
tag_id = IF(category = 'tag', data_id, NULL);

ALTER TABLE links ADD CONSTRAINT links_FK FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE links ADD CONSTRAINT links_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE links ADD CONSTRAINT links_FK_2 FOREIGN KEY (convention_id) REFERENCES convention(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE links ADD CONSTRAINT links_FK_3 FOREIGN KEY (conset_id) REFERENCES conset(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE links ADD CONSTRAINT links_FK_4 FOREIGN KEY (gamesystem_id) REFERENCES gamesystem(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE links ADD CONSTRAINT links_FK_5 FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE links DROP COLUMN data_id;
ALTER TABLE links DROP COLUMN category;

-- Files
ALTER TABLE files ADD game_id int NULL;
ALTER TABLE files ADD convention_id int NULL;
ALTER TABLE files ADD conset_id int NULL;
ALTER TABLE files ADD gamesystem_id int NULL;
ALTER TABLE files ADD tag_id int unsigned NULL;
ALTER TABLE files ADD issue_id int NULL;

UPDATE files SET
game_id = IF(category = 'sce', data_id, NULL),
convention_id = IF(category = 'convent', data_id, NULL),
conset_id = IF(category = 'conset', data_id, NULL),
gamesystem_id = IF(category = 'sys', data_id, NULL),
tag_id = IF(category = 'tag', data_id, NULL),
issue_id = IF(category = 'issue', data_id, NULL);

ALTER TABLE files ADD CONSTRAINT files_FK FOREIGN KEY (issue_id) REFERENCES issue(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE files ADD CONSTRAINT files_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE files ADD CONSTRAINT files_FK_2 FOREIGN KEY (convention_id) REFERENCES convention(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE files ADD CONSTRAINT files_FK_3 FOREIGN KEY (conset_id) REFERENCES conset(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE files ADD CONSTRAINT files_FK_4 FOREIGN KEY (gamesystem_id) REFERENCES gamesystem(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE files ADD CONSTRAINT files_FK_5 FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE files DROP COLUMN data_id;
ALTER TABLE files DROP COLUMN category;

-- Alias
ALTER TABLE alias ADD person_id int NULL;
ALTER TABLE alias ADD game_id int NULL;
ALTER TABLE alias ADD convention_id int NULL;
ALTER TABLE alias ADD conset_id int NULL;
ALTER TABLE alias ADD gamesystem_id int NULL;

UPDATE alias SET
person_id = IF(category = 'aut', data_id, NULL),
game_id = IF(category = 'sce', data_id, NULL),
convention_id = IF(category = 'convent', data_id, NULL),
conset_id = IF(category = 'conset', data_id, NULL),
gamesystem_id = IF(category = 'sys', data_id, NULL);

ALTER TABLE alias ADD CONSTRAINT alias_FK FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE alias ADD CONSTRAINT alias_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE alias ADD CONSTRAINT alias_FK_2 FOREIGN KEY (convention_id) REFERENCES convention(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE alias ADD CONSTRAINT alias_FK_3 FOREIGN KEY (conset_id) REFERENCES conset(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE alias ADD CONSTRAINT alias_FK_4 FOREIGN KEY (gamesystem_id) REFERENCES gamesystem(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE alias DROP COLUMN data_id;
ALTER TABLE alias DROP COLUMN category;

-- Article_reference
ALTER TABLE article_reference ADD person_id int NULL;
ALTER TABLE article_reference ADD game_id int NULL;
ALTER TABLE article_reference ADD convention_id int NULL;
ALTER TABLE article_reference ADD conset_id int NULL;
ALTER TABLE article_reference ADD gamesystem_id int NULL;
ALTER TABLE article_reference ADD tag_id int unsigned NULL;
ALTER TABLE article_reference ADD magazine_id int NULL;
ALTER TABLE article_reference ADD issue_id int NULL;

UPDATE article_reference SET
person_id = IF(category = 'person', data_id, NULL),
game_id = IF(category = 'game', data_id, NULL),
convention_id = IF(category = 'convention', data_id, NULL),
conset_id = IF(category = 'conset', data_id, NULL),
gamesystem_id = IF(category = 'system', data_id, NULL),
tag_id = IF(category = 'tag', data_id, NULL),
magazine_id = IF(category = 'magazine', data_id, NULL),
issue_id = IF(category = 'issue', data_id, NULL);

ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_2 FOREIGN KEY (convention_id) REFERENCES convention(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_3 FOREIGN KEY (conset_id) REFERENCES conset(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_4 FOREIGN KEY (gamesystem_id) REFERENCES gamesystem(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_5 FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_6 FOREIGN KEY (magazine_id) REFERENCES magazine(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE article_reference ADD CONSTRAINT article_reference_FK_7 FOREIGN KEY (issue_id) REFERENCES issue(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE article_reference DROP COLUMN data_id;
ALTER TABLE article_reference DROP COLUMN category;

-- award_nominee_entities
ALTER TABLE award_nominee_entities ADD person_id int NULL;
ALTER TABLE award_nominee_entities ADD game_id int NULL;

UPDATE award_nominee_entities SET
person_id = IF(category = 'aut', data_id, NULL),
game_id = IF(category = 'sce', data_id, NULL);

ALTER TABLE award_nominee_entities ADD CONSTRAINT award_nominee_entities_FK FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE award_nominee_entities ADD CONSTRAINT award_nominee_entities_FK_1 FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE award_nominee_entities DROP COLUMN data_id;
ALTER TABLE award_nominee_entities DROP COLUMN category;

-- SELECT * FROM links WHERE convention_id NOT IN (SELECT id FROM convention)

-- Fix export.php !!!