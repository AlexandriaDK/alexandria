<?php
# Update popularity field for tables `person` and `game`
# Popularity helps order search results and previews for a result (e.g. most popular games for an author and so on)
# This script should be run nightly

require( __DIR__ . "/../www/connect.php");
require( __DIR__ . "/../www/base.inc.php");

// Update popularity for persons
// Simply award 1 point for every distinct user who has added any of the person's game to their log (read, gmed, played)
// :TODO: Add points for awards, organizer roles, ...
doquery("
	UPDATE person, (
		SELECT a.id, COUNT(DISTINCT c.user_id) AS point
		FROM person a
		LEFT JOIN pgrel b ON a.id = b.person_id
		LEFT JOIN userlog c ON c.category = 'sce' AND c.data_id = b.game_id
		GROUP BY a.id
	) calc
	SET person.popularity = calc.point
	WHERE person.id = calc.id
");

// Update popularity for games
// Simply award 1 point for every distinct user who has added the games to their log (read, gmed, played)
// :TOOD: Add points for awards, reruns, ...
doquery("
	UPDATE game, (
		SELECT s.id, COUNT(DISTINCT c.user_id) AS point
		FROM game s
		LEFT JOIN userlog c ON c.category = 'sce' AND c.data_id = s.id
		GROUP BY s.id
	) calc
	SET game.popularity = calc.point
	WHERE game.id = calc.id
");
?>
