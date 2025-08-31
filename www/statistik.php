<?php
require_once "./connect.php";
require_once "base.inc.php";

// Threshold:
$act_person = 25;
$exposure = 40;
$cooperation = 35;
$usedsystem = 50;
$mostcons = 7;
$mostauthors = 5;
$mostscenarios = 40;

$larp_id = 73;

/*
Statistik over antal gange, en bestemt forfatter har haft et scenarie afviklet:
SELECT COUNT(*) AS antal, p.id, p.name FROM person p, pgrel, game, cgrel WHERE p.id = pgrel.person_id AND pgrel.game_id = g.id AND g.id = cgrel.game_id AND pgrel.title_id = 1 GROUP BY p.id ORDER BY antal DESC LIMIT 15;
*/

$content = '';

$stat_person_active = '
	<table class="tablestat">
';

// Active authors
$r = getall("
	SELECT COUNT(*) AS antal, p.id, CONCAT(p.firstname,' ',p.surname) AS name
	FROM person p
	INNER JOIN pgrel ON pgrel.person_id = p.id AND pgrel.title_id = 1
	INNER JOIN game g ON pgrel.game_id = g.id
	GROUP BY p.id
	HAVING antal >= $act_person
	ORDER BY antal DESC, name
");

$placering = 0;
$lastantal = "";
foreach ($r as $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? "$placering." : "");
	$lastantal = $row['antal'];
	$stat_person_active .= "<tr><td class=\"statnumber\">$placeringout</td><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_person_active .= "</table>\n";

// Most exposed authors
$stat_person_exp = '<table class="tablestat">';

$r = getall("
	SELECT COUNT(*) AS antal, p.id, CONCAT(p.firstname,' ',p.surname) AS name
	FROM person p, pgrel, game g, cgrel
	WHERE p.id = pgrel.person_id
	AND pgrel.game_id = g.id
	AND g.id = cgrel.game_id
	AND pgrel.title_id = 1
	AND cgrel.presentation_id IN (1,2,3,42)
	GROUP BY p.id
	HAVING antal >= $exposure
	ORDER BY antal DESC, name
");

$placering = 0;
foreach ($r as $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? "$placering." : "");
	$lastantal = $row['antal'];
	$stat_person_exp .= "<tr><td class=\"statnumber\">$placeringout</td><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}
$stat_person_exp .= '
	</table>
';

$stat_person_workwith = '
	<table class="tablestat">
';

// Authors with most cooperation with other authors
$r = getall("
	SELECT COUNT(DISTINCT t2.person_id) AS antal, a1.id, CONCAT(a1.firstname,' ',a1.surname) AS name
	FROM pgrel AS t1, pgrel AS t2, person AS a1
	WHERE t1.game_id = t2.game_id
	AND t1.person_id != t2.person_id
	AND t1.person_id = a1.id
	AND t1.title_id = 1
	AND t2.title_id = 1
	GROUP BY  t1.person_id
	HAVING antal >= $cooperation
	ORDER BY antal DESC, name
");
foreach ($r as $row) {
	$stat_person_workwith .= "<tr><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_person_workwith .= '
	</table>
';

$stat_gamesystem_used = '
		<table class="tablestat">
';

// Popularity of RPG Systems
// We are currently disregarding LARP
$r = getall("
	SELECT COUNT(*) AS antal, gs.id, gs.name
	FROM game g, gamesystem gs
	WHERE g.gamesystem_id = gs.id
	AND gs.id != '$larp_id'
	GROUP BY gs.id
	HAVING antal >= $usedsystem
	ORDER BY antal DESC, name
");
foreach ($r as $row) {
	$stat_gamesystem_used .= "<tr><td><a href=\"data?system={$row['id']}\" class=\"system\">" . htmlspecialchars($row['name']) . "</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_gamesystem_used .= '
		</table>
';

$stat_game_replay = '
	<table class="tablestat">
';

$r = getall("
	SELECT COUNT(*) AS antal, g.id, g.title AS origtitle, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	INNER JOIN cgrel ON g.id = cgrel.game_id
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE cgrel.presentation_id IN (1,2,3,42)
	GROUP BY g.id
	HAVING antal >= $mostcons
	ORDER BY antal DESC, title
");
foreach ($r as $row) {
	$stat_game_replay .= '<tr><td><a href="data?scenarie=' . $row['id'] . '" class="game" title="' . htmlspecialchars($row['origtitle']) . '">' . htmlspecialchars($row['title_translation']) . '</a></td><td class="statnumber">' . $row['antal'] . '</td></tr>' . PHP_EOL;
}

$stat_game_replay .= '
	</table>
';

$stat_game_auts = '
		<table class="tablestat">
';

// We are currently disregarding LARP
$r = getall("SELECT COUNT(*) AS antal, g.id, g.title AS origtitle, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	INNER JOIN pgrel ON pgrel.game_id = g.id AND pgrel.title_id = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE g.gamesystem_id != '$larp_id'
	GROUP BY pgrel.game_id
	HAVING antal >= $mostauthors
	ORDER BY antal DESC, title
");
foreach ($r as $row) {
	$stat_game_auts .= '<tr><td><a href="data?scenarie=' . $row['id'] . '" class="game" title="' . htmlspecialchars($row['origtitle']) . '">' . htmlspecialchars($row['title_translation']) . '</a></td><td class="statnumber">' . $row['antal'] . '</td></tr>' . PHP_EOL;
	#	$stat_game_auts .= "<tr><td><a href=\"data?scenarie={$row['id']}\" class=\"game\">{$row['title']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_game_auts .= '
		</table>
';

$stat_con_game = '
	<table class="tablestat">
';

// Conventions with most scenarios
$r = getall("
	SELECT COUNT(*) AS antal, c.id, c.name, c.year, c.begin, c.end, c.cancelled
	FROM convention c, cgrel, game g
	WHERE c.id = cgrel.convention_id
	AND	cgrel.game_id = g.id
	AND	cgrel.presentation_id IN (1,2,3,42)
	AND g.boardgame = 0
	GROUP BY c.id
	HAVING antal >= $mostscenarios
	ORDER BY antal DESC, c.year, c.name
");
$placering = 0;
$lastantal = "";
foreach ($r as $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? $placering . "." : "");
	$lastantal = $row['antal'];
	$stat_con_game .= "<tr><td class=\"statnumber\">$placeringout</td><td>" . smarty_function_con($row) . "</td><td class=\"statnumber\">" . $row['antal'] . "</td></tr>\n";
}

$stat_con_game .= '
	</table>
';

// Cons by year
$yearstat = $yearstatpart = [];
$r = getall("
	SELECT COUNT(*) AS antal, year
	FROM convention c
	GROUP BY year
	HAVING antal >= 1
	ORDER BY year
");
foreach ($r as $row) {
	$yearstat[$row['year']]['cons'] = $row['antal'];
}

$r = getall("
	SELECT COUNT(*) AS antal, c.year 
	FROM cgrel, convention c
	WHERE cgrel.presentation_id = 1
	AND cgrel.convention_id = c.id
	GROUP BY c.year
");
foreach ($r as $row) {
	$yearstat[$row['year']]['game'] = $row['antal'];
}

foreach ($yearstat as $year => $row) {
	$yearstatpart[] = ['year' => $year, 'cons' => (int) $row['cons'], 'games' => $row['game'] ?? 0];
}

// Countries, cons
$concountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, COALESCE(a.country, b.country) AS ccountry FROM convention a INNER JOIN conset b ON a.conset_id = b.id GROUP BY COALESCE(a.country, b.country) HAVING ccountry IS NOT NULL ORDER BY count DESC, ISNULL(ccountry), ccountry");
foreach ($r as $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$concountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['ccountry'], 'localecountry' => getCountryName($row['ccountry'])];
}

// Countries, runs
$runcountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, country FROM gamerun WHERE country IS NOT NULL AND country != '' GROUP BY country ORDER BY count DESC, country");
foreach ($r as $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$runcountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['country'], 'localecountry' => getCountryName($row['country'])];
}

// Locations
$locationscountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, country FROM locations WHERE country != '' GROUP BY country ORDER BY count DESC, country");
foreach ($r as $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$locationscountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['country'], 'localecountry' => getCountryName($row['country'])];
}

// Description languages
$descriptionlanguage = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, LEFT(language, 2) AS language FROM game_description GROUP BY LEFT(language, 2) ORDER BY count DESC, language");
foreach ($r as $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$descriptionlanguage[] = ['count' => $row['count'], 'placeout' => $placeout, 'lcode' => $row['language'], 'localelanguage' => getLanguageName($row['language'])];
}

// Downloadable scenarios, languages
$r = getall("SELECT COUNT(DISTINCT game_id) AS count, language FROM files WHERE language != '' AND downloadable = 1 GROUP BY language HAVING count > 0 ORDER BY count DESC", false);

$place = $lastcount = 0;
$downloadablelanguage = [];
foreach ($r as $row) {
	$languagename = getLanguageName($row['language']);
	if ($languagename != $row['language']) { // only accept known languages
		$place++;
		$placeout = ($lastcount != $row['count'] ? "$place." : "");
		$lastcount = $row['count'];
		$downloadablelanguage[] = ['count' => $row['count'], 'placeout' => $placeout, 'code' => $row['language'], 'localelanguage' => $languagename];
	}
}


award_achievement(51); // visit statistics page

$t->assign('content', $content);
$t->assign('stat_person_active', $stat_person_active);
$t->assign('stat_person_exp', $stat_person_exp);
$t->assign('stat_person_workwith', $stat_person_workwith);
$t->assign('stat_gamesystem_used', $stat_gamesystem_used);
$t->assign('stat_game_replay', $stat_game_replay);
$t->assign('stat_game_auts', $stat_game_auts);
$t->assign('stat_con_game', $stat_con_game);
$t->assign('stat_con_year', $yearstatpart);
$t->assign('stat_con_country', $concountry);
$t->assign('stat_run_country', $runcountry);
$t->assign('stat_location_country', $locationscountry);
$t->assign('stat_description_language', $descriptionlanguage);
$t->assign('stat_downloadable_language', $downloadablelanguage);

$t->display('statistics.tpl');
