<?php
// Fetch scenarios with descriptions from old ARCON websites and 
require("../../www/connect.php");
require("../../www/base.inc.php");

$originalurl = 'https://www.spillfestival.no/arcon15/rolle.html';

$convent_id = 805;
$convent_setname = "ARCON 15";

$html = file_get_contents($originalurl);
$html = utf8_encode($html);

function chlog($data_id, $category, $note="") {
	$user = 'Peter Brodersen';
	$authuserid = 4;
	$ip = '127.0.0.1';
	$ip_forward = '';
	$data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
	$note = dbesc($note);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,ip,ip_forward,note) " .
	         "VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$ip','$ip_forward','$note')";
	$result = doquery($query);
	return $result;
	
}

function cleanField ($html) {
    $html = str_replace("\n", " ", $html);
    $html = str_replace("<BR>", PHP_EOL, $html);
    return html_entity_decode( strip_tags($html) );
}

$regexp = '_' .
          '<TR><TD CLASS="head".*?<A NAME="(?<short>.*?)">(?<title>.*?)</A>.*?' .
          '<TR>.*?' .
          '<TD.*?>(?<person>.*?)</TD>.*?' .
          '<TD.*?>(?<timeslot>.*?)</TD>.*?' .
          '<TD.*?>(?<participants>.*?)</TD>.*?' .
          '<TR><TD.*?>(?<description>.*?)</TD>' .
          '_msi';

preg_match_all($regexp, $html, $matches, PREG_SET_ORDER);

// get data
foreach( $matches AS $match) {
	$data = [
		'person'   => cleanField($match['person']),
		'title'   => cleanField($match['title']),
		'system' => cleanField($match['title']),
		'description' => cleanField($match['description']),
        'intern' => 'Autoimport by PB from:' . PHP_EOL .
                    $originalurl . PHP_EOL . PHP_EOL .
                    'Code: ' . cleanField($match['short']) . PHP_EOL .
                    'Timeslot: ' . cleanField($match['timeslot']) . PHP_EOL .
                    'Total participants: ' . cleanField($match['participants']) . PHP_EOL
	];
	$data['title'] .= " (" . $convent_setname . ")";

	// participants
	$players_min = $players_max = "NULL";
#	if (is_numeric($data['participants']) ) {
#		$players_max = $data['participants'];
#	}

	// authors
	$aut_id = NULL;
	$aut_extra = '';
#	if (strpos($data['organizer'], " ") === FALSE) {
	if (FALSE) {
		$aut_extra = $data['organizer'];
		print "EXTRA: " . $aut_extra . PHP_EOL;
	} else { // find author
        preg_match('_(.*) (.*)_', $data['person'], $names);
        if ($names[1] == "" || $names[2] == "") {
            $aut_extra = $data['person'];
        } else {
            $aut_id = getone("SELECT id FROM aut WHERE firstname = '" . dbesc($names[1]). "' AND surname = '" . dbesc($names[2]) . "'");
            if (!$aut_id) {
                $intern = "Autoimport from ARCON data by PB" . PHP_EOL;
                $sql = "INSERT INTO aut (firstname, surname, intern) VALUES ('" . dbesc($names[1]). "', '" . dbesc($names[2]) . "', '" . dbesc($intern) . "')";
                $aut_id = doquery($sql);
                chlog($aut_id, 'aut', 'Person oprettet');
                print "Created person $aut_id" . PHP_EOL;
            } else {
                print "Got person $aut_id" . PHP_EOL;
            }
        }
    }

	// system
	$sys_extra = "";
	$sys_id = getone("SELECT id FROM sys WHERE name = '" . dbesc($data['system']) . "'");
	if (!$sys_id) {
		$sys_id = 0;
		$sys_extra = $data['system'];
	}

	// insert scenario
	$scenario_id_sql = "INSERT INTO sce (title, description, intern, sys_id, sys_ext, aut_extra, players_min, players_max, rlyeh_id, boardgame) " .
	                   "VALUES ('" . dbesc($data['title']) . "', '" . dbesc($data['description']) . "', '" . dbesc($data['intern']) ."', $sys_id, '" . dbesc($sys_extra) ."', '" . dbesc($aut_extra) . "', $players_min, $players_max, 0, 0)";
    print $scenario_id_sql . PHP_EOL . PHP_EOL;

	$sce_id = doquery($scenario_id_sql);
	chlog($sce_id, 'sce', 'Scenarie oprettet');

	$desc_sql = "INSERT INTO game_description (game_id, description, language) VALUES ($sce_id, '" . dbesc($data['description']) . "', 'nb')";
	doquery($desc_sql);

	$cssql = "INSERT INTO csrel (convent_id, sce_id, pre_id) VALUES ($convent_id, $sce_id, 1)";
	doquery($cssql);

	if ($aut_id) {
		$cssql = "INSERT INTO asrel (aut_id, sce_id, tit_id) VALUES ($aut_id, $sce_id, 1)";
		doquery($cssql);
		
	}

}
?>
