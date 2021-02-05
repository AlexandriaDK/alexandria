<?php
require("../../www/connect.php");
require("../../www/base.inc.php");

$originalurl = 'https://www.spillfestival.no/arcon36/program.php';

$convent_id = 995;
$convent_setname = "ARCON";

$glob = 'tmp/turnering.php?*';

function chlog($data_id, $category, $note="") {
	$user = 'Peter Brodersen';
	$authuserid = 4;
	$data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
	$note = dbesc($note);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,note) " .
	         "VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$note')";
	$result = doquery($query);
	return $result;
}

function getField ($re, $html) {
	if ( preg_match( $re, $html, $match) ) {
		return strip_tags(html_entity_decode( $match[1] ));
	} else {
		return '';
	}
	
}

$re = [
	'is_rpg' => '_<td width="40%"><b>Type:</b></td>.*?<td width="60%">Rollespill</td>_s',
	'contact' => '_<td width="40%"><b>Kontakt:</b></td>.*?<td width="60%">(.*?)</td>_s',
	'participants' => '_<td width="40%"><b>Maks deltagere:</b></td>.*?<td width="60%">(.*?)</td>_s',
	'organizer' => '_<td><b>Arrangør:</b></td>.*?<td width="60%">(.*?)</td>_s',
	'description' => '_<td colspan=2><b>Beskrivelse:</b></td>.*?<td colspan=2>(.*?)</td>_s',
	'system' => '_<td colspan="2"><h1>(.*?)</h1></td>_',
	'title' => '_<tr><td colspan="2"><h3>&quot;(.*?)&quot;</h3></td>_'

];

// get data
foreach( glob( $glob ) AS $file) {
	$html = file_get_contents( $file );
	if ( ! preg_match( $re['is_rpg'], $html) ) {
		continue;
	}
	$data = [
		'title'   => getField( $re['title'], $html),
		'system' => getField( $re['system'], $html),
		'organizer' => getField( $re['organizer'], $html),
		'description' => getField( $re['description'], $html),
		'contact' => getField( $re['contact'], $html),
		'participants' => getField( $re['participants'], $html),
		'intern' => 'Autoimport by PB from:' . PHP_EOL . pathinfo($originalurl)['dirname'] . '/' . pathinfo($file)['basename'] . PHP_EOL
	];
	if (!$data['title']) {
		$data['title'] = $data['system'] . " (" . $convent_setname . ")";
	}

	if ($data['contact']) {
		$data['intern'] .= 'Kontakt: ' . $data['contact'] . PHP_EOL;
	}
	if ($data['participants']) {
		$data['intern'] .= 'Maks deltagere: ' . $data['participants'] . PHP_EOL;
	}
	// participants
	$players_max = "NULL";
	if (is_numeric($data['participants']) ) {
		$players_max = $data['participants'];
	}

	// authors
	$aut_id = NULL;
	$aut_extra = '';
	if (strpos($data['organizer'], " ") === FALSE) {
		$aut_extra = $data['organizer'];
		print "EXTRA: " . $aut_extra . PHP_EOL;
	} else { // find author
		preg_match('_(.*) (.*)_', $data['organizer'], $names);
		$aut_id = getone("SELECT id FROM aut WHERE firstname = '" . dbesc($names[1]). "' AND surname = '" . dbesc($names[2]) . "'");
		if (!$aut_id) {
			$intern = "Autoimport from ARCON data by PB" . PHP_EOL;
			$sql = "INSERT INTO aut (firstname, surname, intern) VALUES ('" . dbesc($names[1]). "', '" . dbesc($names[2]) . "', '" . dbesc($intern) . "')";
			$aut_id = doquery($sql);
			chlog($aut_id, 'aut', 'Person oprettet');
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
