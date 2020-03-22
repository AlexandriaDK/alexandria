<?php
require("./connect.php");
require("base.inc");

// Threshold:
$akt_forfatter = 25;
$eksponering = 40;
$samarbejde = 35;
$brugtsystem = 50;
$flestcons = 7;
$flestforfattere = 10;
$flestscenarier = 30;

$live_id = 73;

/*
Statistik over antal gange, en bestemt forfatter har haft et scenarie afviklet:
SELECT COUNT(*) AS antal, aut.id, aut.name FROM aut, asrel, sce, csrel WHERE aut.id = asrel.aut_id AND asrel.sce_id = sce.id AND sce.id = csrel.sce_id AND asrel.tit_id = 1 GROUP BY aut.id ORDER BY antal DESC LIMIT 15;
*/


$content = '';

/*
$content .= '
	<table border="1" cellspacing="0" cellpadding="1">
	<tr valign="top">
';

$content .= '
	<td style="padding-right: 20px;">Mest aktive forfattere:<br />
';
*/

$stat_aut_active = '
	<table class="tablestat">
';

/*
Ryan-forslag:

SELECT
COUNT(DISTINCT sce.id) AS antal,
aut.id,
CONCAT(aut.firstname,' ',aut.surname) AS name
FROM
aut,
asrel,
sce,
csrel
WHERE
asrel.aut_id = aut.id
AND asrel.tit_id = 1
AND asrel.sce_id = sce.id
AND sce.id = csrel.sce_id
AND csrel.pre_id = 1
GROUP BY
asrel.aut_id
HAVING
aut.id = 1
ORDER BY
antal DESC,
name

*/

$r = getall("
	SELECT
		COUNT(*) AS antal,
		aut.id,
		CONCAT(aut.firstname,' ',aut.surname) AS name
	FROM
		aut,
		asrel,
		sce
	WHERE
		asrel.aut_id = aut.id
		AND asrel.tit_id = 1
		AND asrel.sce_id = sce.id
	GROUP BY
		asrel.aut_id
	HAVING
		antal >= $akt_forfatter
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

$stat_aut_exp = '<table class="tablestat">';

// 16. maj: Tager nu højde for aflysninger
$r = getall("
	SELECT
		COUNT(*) AS antal,
		aut.id,
		CONCAT(aut.firstname,' ',aut.surname) AS name
	FROM
		aut,
		asrel,
		sce,
		csrel
	WHERE
		aut.id = asrel.aut_id AND
		asrel.sce_id = sce.id AND
		sce.id = csrel.sce_id AND
		asrel.tit_id = 1 AND
		csrel.pre_id IN (1,2,3)
	GROUP BY
		aut.id
	HAVING 
		antal >= $eksponering
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

$r = getall("
	SELECT
		COUNT(DISTINCT t2.aut_id) AS antal,
		a1.id,
		CONCAT(a1.firstname,' ',a1.surname) AS name
	FROM
		asrel AS t1,
		asrel AS t2,
		aut AS a1
	WHERE
		t1.sce_id = t2.sce_id AND
		t1.aut_id != t2.aut_id AND
		t1.aut_id = a1.id AND
		t1.tit_id = 1 AND
		t2.tit_id = 1
	GROUP BY 
		t1.aut_id
	HAVING
		antal >= $samarbejde
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
// vi blanker live, id #73, ud
$r = getall("
	SELECT
		COUNT(*) AS antal,
		sys.id,
		sys.name
	FROM
		sce,
		sys
	WHERE
		sce.sys_id = sys.id AND
		sys.id != '$live_id'
	GROUP BY
		sys.id
	HAVING
		antal >= $brugtsystem
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
		sce.id,
		sce.title
	FROM
		sce,
		csrel
	WHERE
		sce.id = csrel.sce_id AND
		csrel.pre_id IN (1,2,3)
	GROUP BY
		sce.id
	HAVING
		antal >= $flestcons
	ORDER BY
		antal DESC,
		title
");
foreach($r AS $row) {
	$stat_sce_replay .= "<tr><td><a href=\"data?scenarie={$row['id']}\" class=\"scenarie\">{$row['title']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_sce_replay .= '
	</table>
';

$stat_sce_auts = '
		<table class="tablestat">
';

// vi blanker live, id #73, ud
$r = getall("
	SELECT
		COUNT(*) AS antal,
		sce.id,
		sce.title
	FROM
		asrel,
		sce
	WHERE
		asrel.sce_id = sce.id AND
		asrel.tit_id = 1 AND
		sce.sys_id != '$live_id'
	GROUP BY
		asrel.sce_id
	HAVING
		antal >= $flestforfattere
	ORDER BY
		antal DESC,
		title
");
foreach($r AS $row) {
	$stat_sce_auts .= "<tr><td><a href=\"data?scenarie={$row['id']}\" class=\"scenarie\">{$row['title']}</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_sce_auts .= '
		</table>
';

$stat_con_sce = '
	<table class="tablestat">
';

$r = getall("
	SELECT
		COUNT(*) AS antal,
		convent.id,
		convent.name,
		convent.year
	FROM
		convent,
		csrel,
		sce
	WHERE
		convent.id = csrel.convent_id AND
		csrel.sce_id = sce.id AND
		csrel.pre_id IN (1,2,3) AND
		sce.boardgame = 0
	GROUP BY
		convent.id
	HAVING
		antal >= $flestscenarier
	ORDER BY
		antal DESC,
		convent.year,
		convent.name
");
$placering = 0;
$lastantal = "";
foreach($r AS $row) {
	$placering++;
	$placeringout = ($lastantal != $row['antal'] ? $placering . "." : "");
	$lastantal = $row['antal'];
	$stat_con_sce .= "<tr><td class=\"statnumber\">$placeringout</td><td><a href=\"data?con={$row['id']}\" class=\"con\">{$row['name']} ({$row['year']})</a>&nbsp;</td><td class=\"statnumber\">{$row['antal']}</td></tr>\n";
}

$stat_con_sce .= '
	</table>
';

$yearstat = $yearstatpart = [];
$r = getall("
	SELECT
		COUNT(*) AS antal,
		year
	FROM
		convent
	GROUP BY
		year
	HAVING
		antal >= 1
	ORDER BY
		year
");
foreach($r AS $row) {
	$yearstat[$row['year']]['cons'] = $row['antal'];
}

$r = getall("
	SELECT
		COUNT(*) AS antal,
		convent.year 
	FROM
		csrel,
		convent
	WHERE
		csrel.pre_id = 1 AND
		csrel.convent_id = convent.id
	GROUP BY
		convent.year
");
foreach($r AS $row) {
	$yearstat[$row['year']]['sce'] = $row['antal'];
}

foreach($yearstat AS $year => $row) {
	$yearstatpart[] = [ 'year' => $year, 'cons' => (int) $row['cons'], 'games' => $row['sce'] ?? 0 ];
}

$concountry = [];
$place = 0;
$lastcount = 0;
$r = getall("SELECT COUNT(*) AS count, COALESCE(a.country, b.country) AS ccountry FROM convent a INNER JOIN conset b ON a.conset_id = b.id GROUP BY COALESCE(a.country, b.country) HAVING ccountry IS NOT NULL ORDER BY count DESC, ISNULL(ccountry), ccountry");
foreach ($r AS $row) {
	$place++;
	$placeout = ($lastcount != $row['count'] ? "$place." : "");
	$lastcount = $row['count'];
	$concountry[] = ['count' => $row['count'], 'placeout' => $placeout, 'ccode' => $row['ccountry'], 'localecountry' => Locale::getDisplayRegion("-" . $row['ccountry'], LANG) ];
}
$stat_con_country = $r;

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

$t->display('statistics.tpl');

?>
