<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'run';

$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$begin = trim((string) ($_REQUEST['begin'] ?? ''));
$end = trim((string) ($_REQUEST['end'] ?? ''));
$location = trim((string) ($_REQUEST['location'] ?? ''));
$country = trim((string) ($_REQUEST['country'] ?? ''));
$description = (string) ($_REQUEST['description'] ?? '');
$id = (int) ($_REQUEST['id'] ?? 0);
$run_id = (int) ($_REQUEST['run_id'] ?? 0);
$cancelled = (int) isset($_REQUEST['cancelled']);

$q = "SELECT title FROM game WHERE id = '$id'";
$title = getone($q);

function typechange($type)
{
	if ($type !== 'date') {
		return '';
	}
	$html = ' <span onclick="switchType($(this).prev());" class="changeTypeCog" title="Switch between date and text input">⚙️</span>';
	return $html;
}

function paddate($datestring)
{
	if (strlen($datestring) == 4) {
		$datestring .= "-00-00"; // add blank month+date
	}
	if (strlen($datestring) == 7) {
		$datestring .= "-00"; // add blank date
	}
	if ($datestring == "" || $datestring == '0000-00-00') {
		$datestring = NULL;
	}
	return $datestring;
}

// Update run
if ($action == "changerun" && $do != "Delete") {
	$begin = paddate($begin);
	$end = paddate($end);
	if (!$end) $end = $begin;
	$q = "UPDATE gamerun SET " .
		'begin = ' . sqlifnull($begin) . ', ' .
		'end = ' . sqlifnull($end) . ', ' .
		"location = '" . dbesc($location) . "', " .
		"country = '" . dbesc($country) . "', " .
		"description = '" . dbesc($description) . "', " .
		"cancelled = $cancelled " .
		"WHERE id = '$run_id'";
	$r = doquery($q);
	if ($r) {
		chlog($id, 'game', "Run updated");
	}
	$_SESSION['admin']['info'] = "Run updated! " . dberror();
	rexit($this_type, ['id' => $id]);
}

// Delete run
if ($action == "changerun" && $do == "Delete") {
	if (getone("SELECT COUNT(*) FROM pgrel WHERE gamerun_id = $run_id")) {
		$_SESSION['admin']['info'] = "Error: There are persons for the game connected to this run!";
		rexit($this_type, ['id' => $id]);
	}
	$q = "DELETE FROM gamerun WHERE id = $run_id";
	$r = doquery($q);
	if ($r) {
		chlog($id, 'game', "Run deleted");
	}
	$_SESSION['admin']['info'] = "Run deleted! " . dberror();
	rexit($this_type, ['id' => $id]);
}

// Tilføj afvikling
if ($action == "addrun") {
	$begin = paddate($begin);
	$end = paddate($end);
	if (!$end) $end = $begin;
	$q = "INSERT INTO gamerun " .
		"(game_id, begin, end, location, country, description, cancelled) VALUES " .
		"('$id', " . sqlifnull($begin) . ", " . sqlifnull($end) . ", '" . dbesc($location) . "', '" . dbesc($country) . "', '" . dbesc($description) . "', $cancelled)";
	$r = doquery($q);
	if ($r) {
		chlog($id, 'game', "Run created");
	}
	$_SESSION['admin']['info'] = "Run created! " . dberror();
	rexit($this_type, ['id' => $id]);
}

$query = "
	SELECT gamerun.id, gamerun.begin, gamerun.end, gamerun.location, gamerun.country, gamerun.description, gamerun.cancelled, COUNT(DISTINCT pgrel.person_id) AS personcount, COUNT(DISTINCT lrel.id) AS locationcount
	FROM gamerun
	LEFT JOIN pgrel ON gamerun.id = pgrel.gamerun_id 
	LEFT JOIN lrel ON gamerun.id = lrel.gamerun_id 
	WHERE gamerun.game_id = $id
	GROUP BY gamerun.id, gamerun.begin, gamerun.end, gamerun.location, gamerun.country, gamerun.description, gamerun.cancelled
	ORDER BY gamerun.begin, gamerun.end, gamerun.id
";

$result = getall($query);

htmladmstart("Run");

if ($id) {

	print "<table align=\"center\" border=0>" .
		"<tr><th colspan=10>Edit runs for: <a href=\"game.php?game=$id\" accesskey=\"q\">$title</a></th></tr>\n" .
		"<tr>\n" .
		"<th>ID</th>" .
		"<th>Persons</th>" .
		"<th>Map</th>" .
		"<th>Start date</th>" .
		"<th>End date</th>" .
		"<th>Location</th>" .
		"<th title=\"Two letter ISO code\">Country</th>" .
		"<th>Note</th>" .
		"<th>Run cancelled?</th>" .
		"</tr>\n";

	foreach ($result as $row) {
		$gamerun_locations = getall("
			SELECT l.name, l.city, l.country
			FROM lrel
			INNER JOIN locations l ON lrel.location_id = l.id
			WHERE lrel.gamerun_id = " . $row['id']
		);
		$locationlist = [];
		foreach ($gamerun_locations AS $runlocation) {
			$label = $runlocation['name'];
			if ($runlocation['city']) {
				$label .= ', ' . $runlocation['city'];
			}
			if ($runlocation['country']) {
				$label .= ', ' . getCountryName($runlocation['country']);
			}
			$locationlist[] = $label;
		}
		$locationtitle = implode("\n",$locationlist);
		$typebegin = (preg_match('/-00$/', $row['begin']) ? 'text' : 'date');
		$typeend = (preg_match('/-00$/', $row['end']) ? 'text' : 'date');
		$mapclass = ($row['locationcount'] == 0 && $row['location'] ? 'nolocations' : '');
		print '<form action="run.php" method="post">' .
			'<input type="hidden" name="action" value="changerun">' .
			'<input type="hidden" name="id" value="' . $id . '">' .
			'<input type="hidden" name="run_id" value="' . $row['id'] . '">';
		print "<tr>\n" .
			'<td class="number">' . $row['id'] . '</td>' .
			'<td style="text-align: center">' . $row['personcount'] . '</td>' .
			'<td style="text-align: center"><a href="locations.php?gamerun_id=' . $row['id'] . '" title="' . htmlspecialchars($locationtitle) . '" class="' . $mapclass . '">' . $row['locationcount'] . '</a></td>' .
			'<td><input type="' . $typebegin . '" name="begin" value="' . htmlspecialchars($row['begin']) . '" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange($typebegin) . '</td>' .
			'<td><input type="' . $typeend . '" name="end" value="' . htmlspecialchars($row['end']) . '" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange($typeend) . '</td>' .
			'<td><input type="text" name="location" value="' . htmlspecialchars($row['location']) . '" size="30" maxlength="80"></td>' .
			'<td><input type="text" id="country" name="country" value="' . htmlspecialchars($row['country']) . '" placeholder="E.g. se" size="4"></td>' .
			'<td><input type="text" name="description" value="' . htmlspecialchars($row['description']) . '" size="30" ></td>' .
			'<td style="text-align: center"><input type="checkbox" name="cancelled" value="yes" ' . ($row['cancelled'] ? 'checked' : '') . '></td>' .
			'<td><input type="submit" name="do" value="Update"></td>' .
			'<td><input type="submit" name="do" value="Delete"></td>' .
			"</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="run.php" method="post">' .
		'<input type="hidden" name="action" value="addrun">' .
		'<input type="hidden" name="id" value="' . $id . '">';
	print "<tr>\n" .
		'<td class="number">New</td>' .
		'<td></td>' .
		'<td></td>' .
		'<td><input type="date" name="begin" value="" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange('date') . '</td>' .
		'<td><input type="date" name="end" value="" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange('date') . '</td>' .
		'<td><input type="text" name="location" value="" size="30" maxlength="80"></td>' .
		'<td><input type="text" id="country" name="country" value="" placeholder="E.g. se" size="4"></td>' .
		'<td><input type="text" name="description" value="" size="30" ></td>' .
		'<td align="center"><input type="checkbox" name="cancelled" value="yes"></td>' .
		'<td colspan=2><input type="submit" name="do" value="Create"></td>' .
		"</tr>\n";
	print "</form>\n\n";


	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";
