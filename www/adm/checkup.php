<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

htmladmstart("Checkup");

// Check of orphans isn't necessary anymore after using InnoDB and foreign keys

// Orphan, person<=>game
$query = "
	SELECT
		asrel.id,
		aut_id,
		sce_id,
		aut.id AS autid,
		sce.id AS sceid,
		CONCAT(firstname,' ',surname) AS name,
		sce.title
	FROM
		asrel
	LEFT JOIN
		aut ON aut.id = aut_id
	LEFT JOIN
		sce ON sce.id = sce_id
	WHERE
		sce.id IS NULL OR
		aut.id IS NULL
	";

$result = getall($query);

$htmlorpaut = "Check of orphans, person&lt;=&gt;game: ";

if ($result) {
	$htmlorpaut .=  "<table border=1 cellspacing=0 >";
	$htmlorpaut .= "<tr><th>ID</th><th>aut_id</th><th>sce_id</th><th>aut</th><th>sce</th></tr>";
	foreach($result AS $row) {
		$htmlorpaut .= "<tr>".
		               "<td align=\"right\">{$row['id']}</td>".
		               "<td align=\"right\">{$row['aut_id']}</td>".
		               "<td align=\"right\">{$row['sce_id']}</td>".
		               "<td align=\"right\">{$row['name']}</td>".
		               "<td align=\"right\">{$row['title']}</td>".
		               "</tr>";
	}

	$htmlorpaut .= "</table>";
} else {
	$htmlorpaut .= "<br><b>All good!</b>";
}

// Orphan, game<=>con
$query = "
	SELECT
		csrel.id,
		csrel.convent_id,
		sce_id,
		convent.id AS conid,
		sce.id AS sceid,
		convent.name,
		sce.title
	FROM
		csrel
	LEFT JOIN
		convent ON convent.id = csrel.convent_id
	LEFT JOIN
		sce ON sce.id = sce_id
	WHERE
		sce.id IS NULL OR
		convent.id IS NULL
	";

$result = getall($query);

$htmlorpsce .= "Check of orphans, game&lt;=&gt;con: ";

if ($result) {
	$htmlorpsce .= "<table border=1 cellspacing=0 >";
	$htmlorpsce .= "<tr><th>ID</th><th>convent_id</th><th>sce_id</th><th>convent</th><th>sce</th></tr>";
	foreach($result AS $row) {
		$htmlorpsce .= "<tr>".
		               "<td align=\"right\">$row[id]</td>".
		               "<td align=\"right\">$row[convent_id]</td>".
		               "<td align=\"right\">$row[sce_id]</td>".
		               "<td align=\"right\">$row[name]</td>".
		               "<td align=\"right\">$row[title]</td>".
		               "</tr>";
	}

	$htmlorpsce .= "</table>";
} else {
	$htmlorpsce .= "<br><b>All good!</b>";
}

// Orphan, game=>system
$query = "
	SELECT
		sce.id,
		title,
		sys_id
	FROM
		sce
	LEFT JOIN
		sys ON sce.sys_id = sys.id
	WHERE
		sys_id > 0 AND
		sys.id IS NULL
	";
$result = getall($query);
$htmlorpscesys .= "Check of orphans, game=&gt;system: ";
if ($result) {
	$htmlorpscesys .= "<table border=1 cellspacing=0 >";
	$htmlorpscesys .= "<tr><th>ID</th><th>title</th><th>sys_id</th></tr>";
	foreach($result AS $row) {
		$htmlorpscesys .= "<tr>".
		               "<td align=\"right\">$row[id]</td>".
		               "<td align=\"right\">$row[title]</td>".
		               "<td align=\"right\">$row[sys_id]</td>".
		               "</tr>";
	}

	$htmlorpscesys .= "</table>";
} else {
	$htmlorpscesys .= "<br><b>All good!</b>";
}

$htmlisocodes = "<b>Possible wrong codes for countries and languages:</b><br>\n";
$languages = getall("SELECT data_id, category, language FROM files WHERE language = 'se' OR language REGEXP '^..[a-z]'");
foreach ( $languages AS $language ) {
	$htmlisocodes .= 'File <a href="files.php?category=' . $language['category'] . '&data_id=' . $language['data_id'] . '">'.$language['category'] . " " . $language['data_id'] . "</a> (" . htmlspecialchars($language['language']) . ")<br>";
}
$countries = getall("
	SELECT * FROM (
		SELECT id, country, 'convent' AS category FROM convent
		UNION ALL
		SELECT id, country, 'conset' AS category FROM conset
		UNION ALL
		SELECT sce_id AS id, country, 'scerun' FROM scerun
	) a
	WHERE country = 'sv' OR country REGEXP '^..[a-z]'
");
foreach ( $countries AS $country ) {
	$htmlisocodes .= '<a href="' . ($country['category'] == 'scerun' ? 'run.php?id=' : ( $country['category'] == 'convent' ? 'convent.php?con=' : 'conset.php?conset=') ) . $country['id'] . '">';
	$htmlisocodes .= 'Dataset ' .$country['category'] . " " . $country['id'] . "</a> (" . htmlspecialchars($country['country']) . ")<br>";
}
if (count($languages) === 0 && count($countries) === 0) {
	$htmlisocodes .= "<b>All good!</b>";
}

$htmlorganizer = "<b>Organizers without ID:</b><br>\n";

$query = "
	SELECT aut_extra, convent.id, convent.name, convent.year
	FROM acrel
	INNER JOIN convent ON acrel.convent_id = convent.id
	WHERE aut_extra != ''
	ORDER BY convent.year DESC, convent.begin DESC, convent.name ASC
";
$result = getall($query);
$nameid = 0;
$persons = [];
foreach($result AS $row) { // create tree
	$persons[$row['aut_extra']][] = $row;
}
array_multisort(array_map('count', $persons), SORT_DESC, $persons);
foreach($persons AS $name => $data) {
	if (count($data) < 2) {
		continue;
	}
	$nameid++;
	$htmlorganizer .= "<div>";
	$htmlorganizer .= htmlspecialchars($name) . " (" . count($data) . ")";
	$htmlorganizer .= " <span onclick=\"document.getElementById('organizer_$nameid').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Show cons\">[+]</span>";
	$htmlorganizer .= "<div class=\"nomtext\" style=\"display: none;\" id=\"organizer_$nameid\">";
	foreach ($data AS $row) {
		$htmlorganizer .= '<a href="organizers.php?category=convent&data_id=' . $row['id'] . '">' . $row['name'] . ' (' . $row['year'] . ')</a><br>';
	}
	$htmlorganizer .= "</div>" . PHP_EOL;
}

$htmlorganizermatch = "<b>Organizers without ID, perhaps existing?</b><br>\n";

$query = "SELECT COUNT(*) AS antal, GROUP_CONCAT(convent_id ORDER BY convent_id) AS convent_ids, aut_extra AS name, aut.id AS aut_id FROM acrel INNER JOIN aut ON acrel.aut_extra = CONCAT(aut.firstname, ' ', aut.surname) WHERE aut_extra != '' GROUP BY aut_extra ORDER BY antal DESC, name";
$result = getall($query);
foreach($result AS $row) {
	$htmlorganizermatch .= "<a href=\"person.php?person={$row['aut_id']}\">{$row['name']}</a> ({$row['antal']})";
	foreach(explode(",",$row['convent_ids']) AS $convent_id) {
		$htmlorganizermatch .= " <a href=\"organizers.php?category=convent&data_id=$convent_id\">#$convent_id</a>";
	}

	$htmlorganizermatch .= "<br>\n";
}

$htmlmagazine = "<b>Magazine content providers without ID:</b><br>\n";

$query = "
	SELECT contributor.aut_extra, issue.title, magazine.name, issue.magazine_id, article.issue_id
	FROM contributor
	INNER JOIN article ON contributor.article_id = article.id
	INNER JOIN issue ON article.issue_id = issue.id
	INNER JOIN magazine ON issue.magazine_id = magazine.id
	WHERE contributor.aut_extra != ''
	ORDER BY issue.releasedate DESC, issue.id DESC
";
$result = getall($query);
$nameid = 0;
$persons = [];
foreach($result AS $row) { // create tree
	$persons[$row['aut_extra']][] = $row;
}
array_multisort(array_map('count', $persons), SORT_DESC, $persons);
foreach($persons AS $name => $data) {
	if (count($data) < 2) {
		continue;
	}
	$nameid++;
	$htmlmagazine .= "<div>";
	$htmlmagazine .= htmlspecialchars($name) . " (" . count($data) . ")";
	$htmlmagazine .= " <span onclick=\"document.getElementById('magazine_$nameid').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Show magazines\">[+]</span>";
	$htmlmagazine .= "<div class=\"nomtext\" style=\"display: none;\" id=\"magazine_$nameid\">";
	foreach ($data AS $row) {
		$htmlmagazine .= '<a href="magazine.php?magazine_id=' . $row['magazine_id'] . '&amp;issue_id=' . $row['issue_id'] . '">' . $row['name'] . ', ' . $row['title'] . '</a><br>';
	}
	$htmlmagazine .= "</div>" . PHP_EOL;
}

$htmlmagazinematch = "<b>Magazine content providers without ID, perhaps existing?</b><br>\n";

$query = "
	SELECT COUNT(*) AS count, GROUP_CONCAT(DISTINCT issue_id ORDER BY issue_id) AS issue_ids, contributor.aut_extra AS name, aut.id AS aut_id
	FROM contributor
	INNER JOIN aut ON contributor.aut_extra = CONCAT(aut.firstname, ' ', aut.surname)
	INNER JOIN article ON contributor.article_id = article.id
	WHERE contributor.aut_extra != ''
	GROUP BY contributor.aut_extra, aut.id
	ORDER BY count DESC, name
";
$result = getall($query);
foreach($result AS $row) {
	$htmlmagazinematch .= "<a href=\"person.php?person={$row['aut_id']}\">{$row['name']}</a> ({$row['count']})";
	foreach(explode(",",$row['issue_ids']) AS $issue_id) {
		$htmlmagazinematch .= " <a href=\"magazine.php?issue_id=$issue_id\">#$issue_id</a>";
	}

	$htmlmagazinematch .= "<br>\n";
}

// RPG SYSTEMS CHECK
$htmlgamenotregistered = "<b>Most used non-registered systems:</b><br>\n";

$minantal = 2;
$query = "SELECT COUNT(*) AS antal, sys_ext FROM sce WHERE (sys_id IS NULL OR sys_id = 0) AND sys_ext != '' GROUP BY sys_ext HAVING antal >= $minantal ORDER BY antal DESC ";
$result = getall($query);
foreach($result AS $row) {
	$htmlgamenotregistered .= $row['sys_ext']." ($row[antal])<br>\n";
}

// PERSONS WITHOUT ANY RELATIONS
$htmlloneper = "<b>Persons without relation to game, organizer, award or magazine:</b><br>\n";

// Checking game, organizer, awards, magazines
$query = "
	SELECT a.id, a.name FROM
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id WHERE asrel.id IS NULL) a
	INNER JOIN 
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN acrel ON aut.id = acrel.aut_id WHERE acrel.id IS NULL) b
	ON a.id = b.id
	INNER JOIN 
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN award_nominee_entities ON aut.id = award_nominee_entities.data_id AND award_nominee_entities.category = 'aut' WHERE award_nominee_entities.id IS NULL) c
	ON a.id = c.id
	INNER JOIN 
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN contributor ON aut.id = contributor.aut_id WHERE contributor.id IS NULL) d
	ON a.id = d.id
";

$query = "
	SELECT id, CONCAT(firstname,' ',surname) AS name
	FROM aut
	WHERE NOT EXISTS (SELECT 1 FROM asrel WHERE aut.id = asrel.aut_id)
	AND NOT EXISTS (SELECT 1 FROM acrel WHERE aut.id = acrel.aut_id)
	AND NOT EXISTS (SELECT 1 FROM contributor WHERE aut.id = contributor.aut_id)
	AND NOT EXISTS (SELECT 1 FROM award_nominee_entities WHERE aut.id = award_nominee_entities.data_id AND category = 'aut')
";


$result = getall($query);
foreach($result AS $row) {
	$htmlloneper .= "<a href=\"person.php?person={$row['id']}\">{$row['name']}</a><br>\n";
}

// CHECK CONS WITHOUT START DATE
$htmlcondate = "<b>Conventions missing exact start date:</b><br>\n";

$query = "SELECT convent.id, convent.name, year, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE begin IS NULL OR begin = '0000-00-00' ORDER BY setname, year, begin, name";

$result = getall($query);
foreach($result AS $row) {
	$htmlcondate .= "<a href=\"convent.php?con={$row['id']}\">{$row['name']} ({$row['year']})</a><br>\n";
}

// Authors with most non-downloadable scenarios
$htmlnodownloadaut = "<b>Authors with most non-downloadable scenarios:</b><br>\n";
$query = "
	SELECT aut.id, firstname, surname, COUNT(*) as missing
	FROM aut
	INNER JOIN asrel ON aut.id = asrel.aut_id AND asrel.tit_id = 1
	LEFT JOIN files ON asrel.sce_id = files.data_id AND files.category = 'sce'
	WHERE files.id IS NULL
	GROUP BY aut.id
	ORDER BY missing DESC
	LIMIT 40
";

$result = getall($query);
foreach($result AS $row) {
	$htmlnodownloadaut .= "<a href=\"person.php?person={$row['id']}\">{$row['firstname']} {$row['surname']}</a> ({$row['missing']})<br>\n";
}

// Same persons?
$names = getcolid("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM aut ORDER BY name");
$htmlnames = "<b>Possible duplicate authors (based on middle name):</b><br>\n";
foreach ($names AS $id => $name) {
    $parts = explode(' ', $name);
    if (count($parts) > 2) {
        $newname = $parts[0] . ' ' . $parts[count($parts)-1];
        $newid = array_search($newname, $names);
        if ($newid) {
			$htmlnames .= '<a href="person.php?person=' . $id . '">' . htmlspecialchars($name) . ' </a> =?= <a href="person.php?person=' . $newid . '">' . htmlspecialchars($newname) . ' </a><br>';
        }
    }
}

// OUTPUT DATA
print "<p>\n";
print "<table cellspacing=3 cellpadding=4>".
      "<tr valign=\"top\">".
      "<td>$htmlorpaut</td>".
      "<td>$htmlorpsce</td>".
      "<td>$htmlorpscesys</td>".
      "</tr><tr valign=\"top\">".
      "<td>$htmlloneper</td>".
      "<td>$htmlorganizer<br><br>$htmlorganizermatch<br><br>$htmlisocodes</td>".
      "<td>$htmlmagazine<br><br>$htmlmagazinematch</td>".
      "</tr><tr valign=\"top\">".
      "<td>$htmlnodownloadaut<br><br>$htmlgamenotregistered</td>".
      "<td>$htmlcondate</td>".
      "<td>$htmlnames</td>" .
	  "</tr></table>";
?>
</body>
</html>
