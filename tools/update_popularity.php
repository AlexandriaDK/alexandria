<?php
# Update popularity field for tables `aut` and `sce`
# Popularity helps order search results and previews for a result (e.g. most popular scenarios for an autor and so on)
# This script should be run nightly

require( __DIR__ . "/../www/connect.php");
require( __DIR__ . "/../www/base.inc.php");

// Update popularity for persons
// Simply award 1 point for every distinct user who has added any of the person's scenario to their log (read, gmed, played)
// :TODO: Add points for awards, organizer roles, ...
doquery("
	UPDATE aut, (
		SELECT a.id, COUNT(DISTINCT c.user_id) AS point
		FROM aut a
		LEFT JOIN asrel b ON a.id = b.aut_id
		LEFT JOIN userlog c ON c.category = 'sce' AND c.data_id = b.sce_id
		GROUP BY a.id
	) calc
	SET aut.popularity = calc.point
	WHERE aut.id = calc.id
");

// Update popularity for scenarios
// Simply award 1 point for every distinct user who has added the scenario to their log (read, gmed, played)
// :TOOD: Add points for awards, reruns, ...
doquery("
	UPDATE sce, (
		SELECT s.id, COUNT(DISTINCT c.user_id) AS point
		FROM sce s
		LEFT JOIN userlog c ON c.category = 'sce' AND c.data_id = s.id
		GROUP BY s.id
	) calc
	SET sce.popularity = calc.point
	WHERE sce.id = calc.id
");
?>
