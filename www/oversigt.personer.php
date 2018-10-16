<?php

require("./connect.php");
require("base.inc");
require("template.inc");

$backurl = "/gfx/corner-person";

print pagebegin("Oversigt over personer",$backurl);
print pagehead("Person-oversigt");

// find ud af hvad, der skal sorteres efter - efternavn er default
switch ($_SERVER['QUERY_STRING']) {
	case "fornavn":
	$orderby = "firstname, surname, id";
	$orderfield = "firstname";
	$wrapletters = "AHMR";
	break;
	
	case "efternavn":
	$orderby = "surname, firstname, id";
	$orderfield = "surname";
	$wrapletters = "AHLR";
	break;
	
	default:
	$orderby = "surname, firstname, id";
	$orderfield = "surname";
	$wrapletters = "AHLR";
}

// hent bogstaver
// vi hiver bare alle bogstaver ud nu...
$chars = array();
$chars['1'] = "0-9";
$charstring = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ";
for ($i=0;$i<strlen($charstring);$i++) {
	$chars[$charstring[$i]] = $charstring[$i];
}

print '<p style="text-align: center; font-size: 14px;"><a href="personer?fornavn">Sortér pr. fornavn</a> &nbsp; <a href="personer?efternavn">Sortér pr. efternavn</a><br /><br />';
foreach($chars AS $key => $value) {
	print "\n\t\t\t<a href=\"#".strtoupper($key)."\">$value</a>";
}
print "</p>\n\n";

$q = mysql_query("SELECT id, firstname, surname FROM aut ORDER BY $orderby") OR die("Fejl: ".mysql_error());
$ll = "";
$part = 1;


while ($r = mysql_fetch_array($q)) {
	if ($ll != strtoupper($r[$orderfield][0])) {
		if ($ll != "") {
			$data[$part] .= '</div>';
		}
		$ll = strtoupper($r[$orderfield][0]);
		if ($ll == $wrapletters[1]) {
			$part = 2;
		} elseif ($ll == $wrapletters[2]) {
			$part = 3;
		} elseif ($ll == $wrapletters[3]) {
			$part = 4;
		}
		$data[$part] .= "<h2><a name=\"$ll\" id=\"$ll\">$ll</a></h2>\n";
		$data[$part] .= "<div class=\"person\">\n";
	}
	if ($orderfield == "surname") {
		$data[$part] .= "<a href=\"/data?person={$r['id']}\">{$r['surname']}, {$r['firstname']}</a><br />\n";
	} elseif ($orderfield == "firstname") {
		$data[$part] .= "<a href=\"/data?person={$r['id']}\">{$r['firstname']} {$r['surname']}</a><br />\n";
	}
	
}
$data[$part] .= '</div>';

print '<table align="center" cellspacing=1 cellpadding=2>';

print '<tr valign="top">';
print '<td>'.$data['1'].'</td>';
print '<td>'.$data['2'].'</td>';
print '<td>'.$data['3'].'</td>';
print '<td>'.$data['4'].'</td>';
print '</tr>';

print '</table>';

print pageend();
?>