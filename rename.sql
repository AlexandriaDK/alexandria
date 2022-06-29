RENAME TABLE aut TO person;
RENAME TABLE sce TO game;
RENAME TABLE scerun TO gamerun;
RENAME TABLE gen TO genre;
RENAME TABLE convent TO convention;
RENAME TABLE sys TO gamesystem;
RENAME TABLE pre TO presentation;

RENAME TABLE asrel AS pgrel;
RENAME TABLE acrel AS pcrel;
RENAME TABLE csrel AS cgrel;
RENAME TABLE gsrel AS ggrel;

ALTER TABLE cgrel CHANGE convent_id convention_id int DEFAULT 0 NOT NULL;
ALTER TABLE cgrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;
ALTER TABLE cgrel CHANGE pre_id presentation_id int DEFAULT 1 NOT NULL;

ALTER TABLE pgrel CHANGE aut_id person_id int DEFAULT 0 NOT NULL;
ALTER TABLE pgrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;
ALTER TABLE pgrel CHANGE tit_id title_id int DEFAULT 1 NOT NULL;

ALTER TABLE pcrel CHANGE aut_extra person_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

ALTER TABLE ggrel CHANGE gen_id genre_id int DEFAULT 0 NOT NULL;
ALTER TABLE ggrel CHANGE sce_id game_id int DEFAULT 0 NOT NULL;

ALTER TABLE game CHANGE sys_id gamesystem_id int NULL;
ALTER TABLE game CHANGE sys_ext gamesystem_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;
ALTER TABLE game CHANGE aut_extra person_extra tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NULL;

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



-- Omdan `category`, `data_id` til eksplicitte felter for hver datatype aht. fremmednøgler
-- Links, Trivia, Updates, Files, Filedownloads, award_nominee_id, ...
