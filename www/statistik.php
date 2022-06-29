<?php
require("./connect.php");
require("base.inc.php");

// Threshold:
$act_person = 25;
$exposure = 40;
$cooperation = 35;
$usedsystem = 50;
$mostcons = 7;
$mostauthors = 5;
$mostscenarios = 30;

$larp_id = 73;

/*
Statistik over antal gange, en bestemt forfatter har haft et scenarie afviklet:
SELECT COUNT(*) AS antal, p.id, p.name FROM person p, pgrel, game, cgrel WHERE p.id = pgrel.person_id AND pgrel.game_id = g.id AND g.id = cgrel.game_id AND pgrel.title_id = 1 GROUP BY p.id ORDER BY antal DESC LIMIT 15;
*/

$content = '';

$stat_aut_active = '
	<table class="tablestat">
';

/*
Ryan-forslag:

SELECT
COUNT(DISTINCT g.id) AS antal,
p.id,
CONCAT(p.firstname,' ',p.surname) AS name
FROM
person p,
pgrel,
game,
cgrel
WHERE
pgrel.person_id = p.id
AND pgrel.title_id = 1
AND pgrel.game_id = g.id
AND g.id = cgrel.game_id
AND cgrel.presentation_id = 1
GROUP BY
pgrel.person_id
HAVING
p.id = 1
ORDER BY
antal DESC,
name

*/

// Active authors
$r = getall("
	SELECT
		COUNT(*) AS antal,
		p.id,
		CONCAT(p.firstname,' ',p.surname) AS name
	FROM
		person p,
		pgrel,
		game g
	WHERE
		pgrel.person_id = p.id
		AND pgrel.title_id = 1
		AND pgrel.game_id = g.id
	GROUP BY
		pgrel.game_id
	HAVING
		antal >= $act_person
	ORDER BY
		antal DESC,
		name
");

$placering = 0;
$lastantal = "";
foreach($r AS $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? "$placering." : "");
	$lastantal = $row['antal'];
	$stat_aut_active .= "<tr><td class=\"statnumber\">$placeringout</td><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_aut_active .= "</table>\n";

// Most exposed authors
$stat_aut_exp = '<table class="tablestat">';

$r = getall("
	SELECT
		COUNT(*) AS antal,
		p.id,
		CONCAT(p.firstname,' ',p.surname) AS name
	FROM
		person p,
		pgrel,
		game g,
		cgrel
	WHERE
		p.id = pgrel.person_id AND
		pgrel.game_id = g.id AND
		g.id = cgrel.game_id AND
		pgrel.title_id = 1 AND
		cgrel.presentation_id IN (1,2,3,42)
	GROUP BY
		p.id
	HAVING 
		antal >= $exposure
	ORDER BY
		antal DESC,
		name
");

$placering = 0;
foreach($r AS $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? "$placering." : "");
	$lastantal = $row['antal'];
	$stat_aut_exp .= "<tr><td class=\"statnumber\">$placeringout</td><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}
$stat_aut_exp .= '
	</table>
';

$stat_aut_workwith = '
	<table class="tablestat">
';

// Authors with most cooperation with other authors
$r = getall("
	SELECT
		COUNT(DISTINCT t2.person_id) AS antal,
		a1.id,
		CONCAT(a1.firstname,' ',a1.surname) AS name
	FROM
		pgrel AS t1,
		pgrel AS t2,
		person AS a1
	WHERE
		t1.game_id = t2.game_id AND
		t1.person_id != t2.person_id AND
		t1.person_id = a1.id AND
		t1.title_id = 1 AND
		t2.title_id = 1
	GROUP BY 
		t1.person_id
	HAVING
		antal >= $cooperation
	ORDER BY
		antal DESC,
		name
");
foreach($r AS $row) {
	$stat_aut_workwith .= "<tr><td><a href=\"data?person={$row['id']}\" class=\"person\">{$row['name']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_aut_workwith .= '
	</table>
';

$stat_sys_used = '
		<table class="tablestat">
';

// Popularity of RPG Systems
// We are currently disregarding LARP
$r = getall("
	SELECT
		COUNT(*) AS antal,
		gs.id,
		gs.name
	FROM
		game g,
		gamesystem gs
	WHERE
		g.gamesystem_id = gs.id AND
		gs.id != '$larp_id'
	GROUP BY
		gs.id
	HAVING
		antal >= $usedsystem
	ORDER BY
		antal DESC,
		name
");
foreach($r AS $row) {
	$stat_sys_used .= "<tr><td><a href=\"data?system={$row['id']}\" class=\"system\">".htmlspecialchars($row['name'])."</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_sys_used .= '
		</table>
';

$stat_sce_replay = '
	<table class="tablestat">
';

$r = getall("
	SELECT
		COUNT(*) AS antal,
		g.id,
		g.title AS origtitle,
		COALESCE(alias.label, g.title) AS title_translation
	FROM
		game g
	INNER JOIN cgrel ON g.id = cgrel.game_id
	LEFT JOIN alias ON g.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE
		cgrel.presentation_id IN (1,2,3,42)
	GROUP BY
		g.id
	HAVING
		antal >= $mostcons
	ORDER BY
		antal DESC,
		title
");
foreach($r AS $row) {
	$stat_sce_replay .= '<tr><td><a href="data?scenarie=' . $row['id'] . '" class="scenarie" title="' . htmlspecialchars( $row['origtitle']) . '">' . htmlspecialchars( $row['title_translation'] ) . '</a></td><td class="statnumber">' . $row['antal'] . '</td></tr>' . PHP_EOL;
}

$stat_sce_replay .= '
	</table>
';

$stat_sce_auts = '
		<table class="tablestat">
';

// We are currently disregarding LARP
$r = getall("SELECT COUNT(*) AS antal, g.id, g.title AS origtitle, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	INNER JOIN pgrel ON pgrel.game_id = g.id AND pgrel.title_id = 1
	LEFT JOIN alias ON g.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE g.sys_id != '$larp_id'
	GROUP BY pgrel.game_id
	HAVING antal >= $mostauthors
	ORDER BY antal DESC, title
");
foreach($r AS $row) {
	$stat_sce_auts .= '<tr><td><a href="data?scenarie=' . $row['id'] . '" class="scenarie" title="' . htmlspecialchars( $row['origtitle']) . '">' . htmlspecialchars( $row['title_translation'] ) . '</a></td><td class="statnumber">' . $row['antal'] . '</td></tr>' . PHP_EOL;
#	$stat_sce_auts .= "<tr><td><a href=\"data?scenarie={$row['id']}\" class=\"scenarie\">{$row['title']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_sce_auts .= '
		</table>
';

$stat_con_sce = '
	<table class="tablestat">
';

// Conventions with most scenarios
$r = getall("
	SELECT
		COUNT(*) AS antal,
		c.id,
		c.name,
		c.year,
		c.begin,
		c.end,
		c.cancelled
	FROM
		convent,
		cgrel,
		game g
	WHERE
		c.id = cgrel.convent_id AND
		cgrel.game_id = g.id AND
		cgrel.presentation_id IN (1,2,3,42) AND
		g.boardgame = 0
	GROUP BY
		c.id
	HAVING
		antal >= $mostscenarios
	ORDER BY
		antal DESC,
		c.year,
		c.name
");
$placering = 0;
$lastantal = "";
foreach($r AS $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? $placering . "." : "");
	$lastantal = $row['antal'];
	$stat_con_sce .= "<tr><td class=\"statnumber\">$placeringout</td><td>" . smarty_function_con($row) . "</td><td class=\"statnumber\">" . $row['antal'] . "</td></tr>\n";
}

$stat_con_sce .= '
	</table>
';

// Cons by year
$yearstat = $yearstatpart = [];
$r = getall("
	SELECT COUNT(*) AS antal, year
	FROM convent
	GROUP BY year
	HAVING antal >= 1
	ORDER BY year
");
foreach($r AS $row) {
	$yearstat[$row['year']]['cons'] = $row['antal'];
}

$r = getall("
	SELECT
		COUNT(*) AS antal,
		c.year 
	FROM
		cgrel,
		convent c
	WHERE
		cgrel.presentation_id = 1 AND
		cgrel.convent_id = c.id
	GROUP BY
		c.year
");
foreach($r AS $row) {
	$yearstat[$row['year']]['game'] = $row['antal'];
}

foreach($yearstat AS $year => $row) {
	$yearstatpart[] = [ 'year' => $year, 'cons' => (int) $row['cons'], 'games' => $row['game'] ?? 0 ];
}

// Countries, cons
$concountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, COALESCE(a.country, b.country) AS ccountry FROM convent a INNER JOIN conset b ON a.conset_id = b.id GROUP BY COALESCE(a.country, b.country) HAVING ccountry IS NOT NULL ORDER BY count DESC, ISNULL(ccountry), ccountry");
foreach ($r AS $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$concountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['ccountry'], 'localecountry' => getCountryName($row['ccountry']) ];
}

// Countries, runs
$runcountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, country FROM gamerun WHERE country IS NOT NULL AND country != '' GROUP BY country ORDER BY count DESC, country");
foreach ($r AS $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$runcountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['country'], 'localecountry' => getCountryName($row['country']) ];
}

// Description languages
$descriptionlanguage = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, LEFT(language, 2) AS language FROM game_description GROUP BY LEFT(language, 2) ORDER BY count DESC, language");
foreach ($r AS $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$descriptionlanguage[] = ['count' => $row['count'], 'placeout' => $placeout, 'lcode' => $row['language'], 'localecountry' => getLanguageName( $row['language'] ) ];
}

award_achievement(51); // visit statistics page

$t->assign('content',$content);
$t->assign('stat_aut_active',$stat_aut_active);
$t->assign('stat_aut_exp',$stat_aut_exp);
$t->assign('stat_aut_workwith',$stat_aut_workwith);
$t->assign('stat_sys_used',$stat_sys_used);
$t->assign('stat_sce_replay',$stat_sce_replay);
$t->assign('stat_sce_auts',$stat_sce_auts);
$t->assign('stat_con_sce',$stat_con_sce);
$t->assign('stat_con_year',$yearstatpart);
$t->assign('stat_con_country',$concountry);
$t->assign('stat_run_country',$runcountry);
$t->assign('stat_description_language',$descriptionlanguage);

$t->display('statistics.tpl');

?>
