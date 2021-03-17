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

$htmlcodes = "<b>Possible wrong codes for countries and languages:</b><br>\n";
$languages = getall("SELECT data_id, category, language FROM files WHERE language = 'se' OR language REGEXP '^..[a-z]'");
foreach ( $languages AS $language ) {
	$htmlcodes .= 'File <a href="files.php?category=' . $language['category'] . '&data_id=' . $language['data_id'] . '">'.$language['category'] . " " . $language['data_id'] . "</a> (" . htmlspecialchars($language['language']) . ")<br>";
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
	$htmlcodes .= '<a href="' . ($country['category'] == 'scerun' ? 'run.php?id=' : ( $country['category'] == 'convent' ? 'convent.php?con=' : 'conset.php?conset=') ) . $country['id'] . '">';
	$htmlcodes .= 'Dataset ' .$country['category'] . " " . $country['id'] . "</a> (" . htmlspecialchars($country['country']) . ")<br>";
}
if (count($languages) === 0 && count($countries) === 0) {
	$htmlcodes .= "<b>All good!</b>";
}

$htmlorganizer = "<b>Organizers without ID:</b><br>\n";

// html inside SQL - yuck!
$query = "SELECT COUNT(*) AS antal, aut_extra AS navn, GROUP_CONCAT(CONCAT('<a href=\"organizers.php?category=convent&data_id=', convent.id, '\">', convent.name, ' (', convent.year, ')', '</a>' ) ORDER BY convent.year DESC, convent.begin DESC, convent.name ASC SEPARATOR '\n') AS convents FROM acrel INNER JOIN convent ON acrel.convent_id = convent.id WHERE aut_extra != '' GROUP BY aut_extra HAVING antal >= 2 ORDER BY antal DESC, navn";
$result = getall($query);
$nameid = 0;
foreach($result AS $row) {
	$nameid++;
	$htmlorganizer .= "<div>";
	$htmlorganizer .= "{$row['navn']} ({$row['antal']})\n";
	$htmlorganizer .= " <span onclick=\"document.getElementById('$nameid').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Vis kongresser\">[+]</span>";
	$htmlorganizer .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nameid\">" . nl2br($row['convents'], FALSE) . "</div>" . PHP_EOL;
	$htmlorganizer .= "</div>" . PHP_EOL;
}

$htmlorganizermatch = "<b>Organizers without ID, perhaps existing?</b><br>\n";

$query = "SELECT COUNT(*) AS antal, GROUP_CONCAT(convent_id ORDER BY convent_id) AS convent_ids, aut_extra AS navn, aut.id AS aut_id FROM acrel INNER JOIN aut ON acrel.aut_extra = CONCAT(aut.firstname, ' ', aut.surname) WHERE aut_extra != '' GROUP BY aut_extra ORDER BY antal DESC, navn";
$result = getall($query);
foreach($result AS $row) {
	$htmlorganizermatch .= "<a href=\"person.php?person={$row['aut_id']}\">{$row['navn']}</a> ({$row['antal']})";
	foreach(explode(",",$row['convent_ids']) AS $convent_id) {
		$htmlorganizermatch .= " <a href=\"organizers.php?category=convent&data_id=$convent_id\">#$convent_id</a>";
	}

	$htmlorganizermatch .= "<br>\n";
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
$htmlloneper = "<b>Persons without relation to game, organizer or award:</b><br>\n";

// Tjekker både scenarier og arrangør-poster
$query = "
	SELECT a.id, a.name FROM
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id WHERE asrel.id IS NULL) a
	INNER JOIN 
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN acrel ON aut.id = acrel.aut_id WHERE acrel.id IS NULL) b
	ON a.id = b.id
	INNER JOIN 
	(SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut LEFT JOIN award_nominee_entities ON aut.id = award_nominee_entities.data_id AND award_nominee_entities.category = 'aut' WHERE award_nominee_entities.id IS NULL) c
	ON a.id = c.id
";
$result = getall($query);
foreach($result AS $row) {
	$htmlloneper .= "<a href=\"person.php?person={$row['id']}\">{$row['name']}</a><br>\n";
}


// TJEK AF SCENARIER UDEN CON ELLER PERSON

#$query = "SELECT sce.id, sce.title FROM sce LEFT JOIN asrel ON sce_id = sce.id WHERE sce_id IS NULL AND (aut_extra IS NULL OR aut_extra = '') ORDER BY title";
#$result = getall($query);
#
#$htmllone = "<b>Scenarier uden personer:</b> (".count($result).")<br>\n";
#foreach($result AS $row) {
#	$htmllone .= "<a href=\"game.php?game={$row['id']}\">{$row['title']}</a><br>\n";
#}

// TJEK AF KONGRESSER UDEN STARTDATO
$htmlcondate = "<b>Conventions missing exact start date:</b><br>\n";

$query = "SELECT convent.id, convent.name, year, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE begin IS NULL OR begin = '0000-00-00' ORDER BY setname, year, begin, name";

$result = getall($query);
foreach($result AS $row) {
	$htmlcondate .= "<a href=\"convent.php?con={$row['id']}\">{$row['name']} ({$row['year']})</a><br>\n";
}

// Forfattere med flest scenarier, man ikke kan downlaode
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
      "<td>$htmlorganizer</td>".
      "<td>$htmlorganizermatch<br><br>$htmlcodes</td>".
      "</tr><tr valign=\"top\">".
      "<td>$htmlnodownloadaut<br><br>$htmlgamenotregistered</td>".
      "<td>$htmlcondate</td>".
      "<td>$htmlnames</td>" .
	  "</tr></table>";
?>
</body>
</html>
