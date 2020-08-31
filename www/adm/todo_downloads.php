<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
chdir("adm");

function getkeyint($key, $default) {
	if (isset($_REQUEST[$key])) {
		return (int) $_REQUEST[$key];
	} else {
		return (int) $default;
	}
}

$maxshowusers = 100;

$action = (string) $_REQUEST['action'];
$w_scenarios = getkeyint('w_scenarios',10);
$w_runs = getkeyint('w_runs',10);
$w_award_nominees = getkeyint('w_award_nominees',50);
$w_award_winners = getkeyint('w_award_winners',100);
$w_userlogs = getkeyint('w_userlogs',2);

htmladmstart("Manglende scenarier til download");
?>
<form action="">
<p>
Prioriteret liste over forfattere, vi bør kontakte for scenarier, der ikke er online. Scenarier, der allerede kan downloades, indgår ikke i scoren pt.
</p>
<p>
<input type="number" name="w_scenarios" min="-10000" max="10000" value="<?php print $w_scenarios; ?>"> point for hvert scenarie<br>
<input type="number" name="w_runs" min="-10000" max="10000" value="<?php print $w_runs; ?>"> point for hver afvikling på con m.m. (inkl. aflytninger)<br>
<input type="number" name="w_award_nominees" min="-10000" max="10000" value="<?php print $w_award_nominees; ?>"> point for hver prisnominering<br>
<input type="number" name="w_award_winners" min="-10000" max="10000" value="<?php print $w_award_winners; ?>"> point for hver prisvinder<br>
<input type="number" name="w_userlogs" min="-10000" max="10000" value="<?php print $w_userlogs; ?>"> point for hver Alexandria-bruger, der har markeret scenariet<br>
<input type="hidden" name="action" value="calculate">
<input type="submit">

</form>

<?php

if ($action == "calculate") {
	$authordata = [];
	$authorscore = [];
	$authors = getall("SELECT id, firstname, surname FROM aut ORDER BY id");
	foreach ($authors AS $author) {
		$aid = $author['id'];
		$scenarios = getcol("SELECT sce_id FROM asrel LEFT JOIN files ON asrel.sce_id = files.data_id AND files.category = 'sce' WHERE files.id IS NULL AND asrel.tit_id = 1 AND asrel.aut_id = $aid");
		$count_scenarios = count($scenarios);
		if ($count_scenarios) {
			$in = implode(",", $scenarios);
			$titles = getcol("SELECT title FROM sce WHERE id IN ($in)");
			$runs = getone("SELECT COUNT(*) FROM csrel WHERE sce_id IN ($in)");
			$award_nominees = getone("SELECT COUNT(*) FROM award_nominees WHERE sce_id IN ($in) AND winner = 0");
			$award_winners = getone("SELECT COUNT(*) FROM award_nominees WHERE sce_id IN ($in) AND winner = 1");
			$userlogs = getone("SELECT COUNT(*) FROM userlog WHERE category = 'sce' AND data_id IN ($in)");
			$authordata[$aid] = [
				'name' => $author['firstname'] . " " . $author['surname'],
				'titles' => $titles,
				'scenarios' => $count_scenarios,
				'runs' => $runs,
				'nominations' => $award_nominees,
				'winners' => $award_winners,
				'userlogs' => $userlogs,
				'ids' => $in,
			];
			$score = 
				($count_scenarios * $w_scenarios) +
				($runs * $w_runs) +
				($award_nominees * $w_award_nominees) +
				($award_winners * $w_award_winners) +
				($userlogs * $w_userlogs)
			;
			$userscore[$aid] = $score;
		}
	}
	arsort($userscore);
	$showcount = 0;
	print "<h3>Vigtigste forfattere:</h3>" . PHP_EOL;

	$htmlresult = "";
	$csvresult = "\"Forfatter\"\t\"Score\"\t\"Scenarier\"" . PHP_EOL;
	foreach($userscore AS $user => $score) {
		$showcount++;
		$htmlresult .= "<b>" . htmlspecialchars($authordata[$user]['name']) . " ($score point)</b><br>";
		$htmlresult .= implode(", ", $authordata[$user]['titles']) . "<br><br>" . PHP_EOL;
		$csvresult .= "\"" . $authordata[$user]['name'] . "\"\t\"" . $score . "\"\t\"" . implode(", ", $authordata[$user]['titles']) . "\"" . PHP_EOL;

		if ($showcount >= $maxshowusers) break;
	}
#	print $htmlresult;
	print "<p>" . $htmlresult . "</p>";
	print "<pre>" . htmlspecialchars($csvresult) . "</pre>";
	var_dump(file_put_contents("alex_contact_authors.csv", $csvresult));
}

htmladmend();
?>
