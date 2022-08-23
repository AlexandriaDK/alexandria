<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

htmladmstart("Checkup");

$htmlisocodes = "<b>Possible wrong codes for countries and languages:</b><br>\n";
$languages = getall("SELECT COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id) AS data_id, CASE WHEN !ISNULL(game_id) THEN 'game' WHEN !ISNULL(convention_id) THEN 'convention' WHEN !ISNULL(conset_id) THEN 'conset' WHEN !ISNULL(gamesystem_id) THEN 'gamesystem' WHEN !ISNULL(tag_id) THEN 'tag' WHEN !ISNULL(issue_id) THEN 'issue' END AS category, language FROM files WHERE language REGEXP('^(dk|se|no)') OR language REGEXP '^..[a-z]'");
foreach ($languages as $language) {
	$htmlisocodes .= 'File <a href="files.php?category=' . $language['category'] . '&data_id=' . $language['data_id'] . '">' . $language['category'] . " " . $language['data_id'] . "</a> (" . htmlspecialchars($language['language']) . ")<br>";
}
$gamedescriptions = getall("SELECT gd.game_id, gd.language, g.title
	FROM game_description gd
	INNER JOIN game g ON gd.game_id = g.id
	WHERE gd.language REGEXP('^(dk|se|no)') OR gd.language REGEXP '^..[a-z]'
");
foreach ($gamedescriptions as $gamedescription) {
	$htmlisocodes .= 'Game description for <a href="game.php?game=' . $gamedescription['game_id'] . '">' . htmlspecialchars($gamedescription['title']) . "</a> (" . htmlspecialchars($gamedescription['language']) . ")<br>";
}
$countries = getall("
	SELECT * FROM (
		SELECT id, country, 'convention' AS category FROM convention c
		UNION ALL
		SELECT id, country, 'conset' AS category FROM conset
		UNION ALL
		SELECT game_id AS id, country, 'gamerun' FROM gamerun
	) a
	WHERE country IN('da','sv','nb','uk') OR country REGEXP '^..[a-z]'
");
foreach ($countries as $country) {
	$htmlisocodes .= '<a href="' . ($country['category'] == 'gamerun' ? 'run.php?id=' : ($country['category'] == 'convention' ? 'convention.php?con=' : 'conset.php?conset=')) . $country['id'] . '">';
	$htmlisocodes .= 'Dataset ' . $country['category'] . " " . $country['id'] . "</a> (" . htmlspecialchars($country['country']) . ")<br>";
}
if (count($languages) + count($countries) + count($gamedescriptions) === 0) {
	$htmlisocodes .= "<b>All good!</b>";
}

$htmlorganizer = "<b>Organizers without ID:</b><br>\n";

$query = "
	SELECT person_extra, c.id, c.name, c.year
	FROM pcrel
	INNER JOIN convention c ON pcrel.convention_id = c.id
	WHERE person_extra != ''
	ORDER BY c.year DESC, c.begin DESC, c.name ASC
";
$result = getall($query);
$nameid = 0;
$persons = [];
foreach ($result as $row) { // create tree
	$persons[$row['person_extra']][] = $row;
}
array_multisort(array_map('count', $persons), SORT_DESC, $persons);
foreach ($persons as $name => $data) {
	if (count($data) < 2) {
		continue;
	}
	$nameid++;
	$htmlorganizer .= "<div>";
	$htmlorganizer .= htmlspecialchars($name) . " (" . count($data) . ")";
	$htmlorganizer .= " <span onclick=\"document.getElementById('organizer_$nameid').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Show cons\">[+]</span>";
	$htmlorganizer .= "<div class=\"nomtext\" style=\"display: none;\" id=\"organizer_$nameid\">";
	foreach ($data as $row) {
		$htmlorganizer .= '<a href="organizers.php?category=convention&data_id=' . $row['id'] . '">' . $row['name'] . ' (' . $row['year'] . ')</a><br>';
	}
	$htmlorganizer .= "</div>" . PHP_EOL;
}

$htmlorganizermatch = "<b>Organizers without ID, perhaps existing?</b><br>\n";

$query = "SELECT COUNT(*) AS antal, GROUP_CONCAT(convention_id ORDER BY convention_id) AS convention_ids, person_extra AS name, p.id AS person_id FROM pcrel INNER JOIN person p ON pcrel.person_extra = CONCAT(p.firstname, ' ', p.surname) WHERE person_extra != '' GROUP BY person_extra ORDER BY antal DESC, name";
$result = getall($query);
foreach ($result as $row) {
	$htmlorganizermatch .= "<a href=\"person.php?person={$row['person_id']}\">{$row['name']}</a> ({$row['antal']})";
	foreach (explode(",", $row['convention_ids']) as $convention_id) {
		$htmlorganizermatch .= " <a href=\"organizers.php?category=convention&data_id=$convention_id\">#$convention_id</a>";
	}

	$htmlorganizermatch .= "<br>\n";
}

$htmlmagazine = "<b>Magazine content providers without ID:</b><br>\n";

$query = "
	SELECT contributor.person_extra, issue.title, magazine.name, issue.magazine_id, article.issue_id
	FROM contributor
	INNER JOIN article ON contributor.article_id = article.id
	INNER JOIN issue ON article.issue_id = issue.id
	INNER JOIN magazine ON issue.magazine_id = magazine.id
	WHERE contributor.person_extra != ''
	ORDER BY issue.releasedate DESC, issue.id DESC
";
$result = getall($query);
$nameid = 0;
$persons = [];
foreach ($result as $row) { // create tree
	$persons[$row['person_extra']][] = $row;
}
array_multisort(array_map('count', $persons), SORT_DESC, $persons);
foreach ($persons as $name => $data) {
	if (count($data) < 2) {
		continue;
	}
	$nameid++;
	$htmlmagazine .= "<div>";
	$htmlmagazine .= htmlspecialchars($name) . " (" . count($data) . ")";
	$htmlmagazine .= " <span onclick=\"document.getElementById('magazine_$nameid').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Show magazines\">[+]</span>";
	$htmlmagazine .= "<div class=\"nomtext\" style=\"display: none;\" id=\"magazine_$nameid\">";
	foreach ($data as $row) {
		$htmlmagazine .= '<a href="magazine.php?magazine_id=' . $row['magazine_id'] . '&amp;issue_id=' . $row['issue_id'] . '">' . $row['name'] . ', ' . $row['title'] . '</a><br>';
	}
	$htmlmagazine .= "</div>" . PHP_EOL;
}

$htmlmagazinematch = "<b>Magazine content providers without ID, perhaps existing?</b><br>\n";

$query = "
	SELECT COUNT(*) AS count, GROUP_CONCAT(DISTINCT issue_id ORDER BY issue_id) AS issue_ids, contributor.person_extra AS name, p.id AS person_id
	FROM contributor
	INNER JOIN person p ON contributor.person_extra = CONCAT(p.firstname, ' ', p.surname)
	INNER JOIN article ON contributor.article_id = article.id
	WHERE contributor.person_extra != ''
	GROUP BY contributor.person_extra, p.id
	ORDER BY count DESC, name
";
$result = getall($query);
foreach ($result as $row) {
	$htmlmagazinematch .= "<a href=\"person.php?person={$row['person_id']}\">{$row['name']}</a> ({$row['count']})";
	foreach (explode(",", $row['issue_ids']) as $issue_id) {
		$htmlmagazinematch .= " <a href=\"magazine.php?issue_id=$issue_id\">#$issue_id</a>";
	}

	$htmlmagazinematch .= "<br>\n";
}

// RPG SYSTEMS CHECK
$htmlgamenotregistered = "<b>Most used non-registered systems:</b><br>\n";

$minantal = 2;
$query = "SELECT COUNT(*) AS antal, gamesystem_extra FROM game g WHERE (gamesystem_id IS NULL OR gamesystem_id = 0) AND gamesystem_extra != '' GROUP BY gamesystem_extra HAVING antal >= $minantal ORDER BY antal DESC ";
$result = getall($query);
foreach ($result as $row) {
	$htmlgamenotregistered .= $row['gamesystem_extra'] . " ($row[antal])<br>\n";
}

// PERSONS WITHOUT ANY RELATIONS
$htmlloneper = "<b>Persons without relation to game, organizer, award or magazine:</b><br>\n";

// Checking game, organizer, awards, magazines
$query = "
	SELECT id, CONCAT(firstname,' ',surname) AS name
	FROM person p
	WHERE NOT EXISTS (SELECT 1 FROM pgrel WHERE p.id = pgrel.person_id)
	AND NOT EXISTS (SELECT 1 FROM pcrel WHERE p.id = pcrel.person_id)
	AND NOT EXISTS (SELECT 1 FROM contributor WHERE p.id = contributor.person_id)
	AND NOT EXISTS (SELECT 1 FROM article_reference WHERE p.id = article_reference.person_id)
	AND NOT EXISTS (SELECT 1 FROM award_nominee_entities WHERE p.id = award_nominee_entities.person_id)
";

$result = getall($query);
foreach ($result as $row) {
	$htmlloneper .= "<a href=\"person.php?person={$row['id']}\">{$row['name']}</a><br>\n";
}

// CHECK CONS WITHOUT START DATE
$htmlcondate = "<b>Conventions missing exact start date:</b><br>\n";

$query = "SELECT c.id, c.name, year, conset.name AS setname FROM convention c LEFT JOIN conset ON c.conset_id = conset.id WHERE begin IS NULL OR begin = '0000-00-00' ORDER BY setname, year, begin, name";

$result = getall($query);
foreach ($result as $row) {
	$htmlcondate .= "<a href=\"c.php?con={$row['id']}\">{$row['name']} ({$row['year']})</a><br>\n";
}

// Authors with most non-downloadable scenarios
$htmlnodownloadaut = "<b>Authors with most non-downloadable scenarios:</b><br>\n";
$query = "
	SELECT p.id, firstname, surname, COUNT(*) as missing
	FROM person p
	INNER JOIN pgrel ON p.id = pgrel.person_id AND pgrel.title_id = 1
	LEFT JOIN files ON pgrel.game_id = files.game_id
	WHERE files.id IS NULL
	GROUP BY p.id
	ORDER BY missing DESC
	LIMIT 40
";

$result = getall($query);
foreach ($result as $row) {
	$htmlnodownloadaut .= "<a href=\"person.php?person={$row['id']}\">{$row['firstname']} {$row['surname']}</a> ({$row['missing']})<br>\n";
}

// Same persons?
$names = getcolid("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM person p ORDER BY name");
$htmlnames = "<b>Possible duplicate authors (based on middle name):</b><br>\n";
foreach ($names as $id => $name) {
	$parts = explode(' ', $name);
	if (count($parts) > 2) {
		$newname = $parts[0] . ' ' . $parts[count($parts) - 1];
		$newid = array_search($newname, $names);
		if ($newid) {
			$htmlnames .= '<a href="person.php?person=' . $id . '">' . htmlspecialchars($name) . ' </a> =?= <a href="person.php?person=' . $newid . '">' . htmlspecialchars($newname) . ' </a><br>';
		}
	}
}

// OUTPUT DATA
print "<p>\n";
print "<table cellspacing=3 cellpadding=4>" .
	"<tr valign=\"top\">" .
	"<td>$htmlloneper</td>" .
	"<td>$htmlorganizer<br><br>$htmlorganizermatch<br><br>$htmlisocodes</td>" .
	"<td>$htmlmagazine<br><br>$htmlmagazinematch</td>" .
	"</tr><tr valign=\"top\">" .
	"<td>$htmlnodownloadaut<br><br>$htmlgamenotregistered</td>" .
	"<td>$htmlcondate</td>" .
	"<td>$htmlnames</td>" .
	"</tr></table>";
?>
</body>

</html>