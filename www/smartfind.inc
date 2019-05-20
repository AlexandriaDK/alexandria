<?php

define("URLAUT","data?person=");
define("URLSCE","data?scenarie=");
define("URLSYS","data?system=");
define("URLCON","data?con=");

function log_search($find, $found="") {
	$referer = dbesc($_SERVER['HTTP_REFERER']);
	doquery("INSERT INTO searches (find, found, referer, searchtime, ip) VALUES ('$find','$found','$referer',NOW(),'{$_SERVER['REMOTE_ADDR']}')");
}

function category_search ($find, $searchfield, $category) {
	global $match, $id_data, $link_a, $link_b, $id_a, $id_b;
	$q = getall("SELECT id, $searchfield FROM $category");
	foreach($q AS $row) $id_data[$category][$row[0]] = $row[1];
//	while ($row = mysql_fetch_row($q)) $id_data[$category][$row[0]] = $row[1];
	
	switch($category) {
		case 'aut':
		$linkurl = URLAUT;
		$getfunction = "getautidbyname";
		break;

		case 'sce':
		$linkurl = URLSCE;
		$getfunction = "getsceidbytitle";
		break;

		case 'sys':
		$linkurl = URLSYS;
		$getfunction = "getsysidbyname";
		break;

		case 'convent':
		$linkurl = URLCON;
		$getfunction = "getconidbyname";
		break;

		default:
		$linkurl = URLAUT;
		$getfunction = "getautidbyname";
	}

	list($a,$b,$c,$d) = $getfunction($find);

	foreach($a AS $id) { $link_a[] = $linkurl.$id; $id_a[] = $id; }
	foreach($b AS $id) { $link_b[] = $linkurl.$id; $id_b[] = $id; }
	$match[$category] = $d;

	list($a,$b,$c,$d) = getidbyalias($find,$category);
	foreach($a AS $id) { $link_a[] = $linkurl.$id; $id_a[] = $id; }
	foreach($b AS $id) { $link_b[] = $linkurl.$id; $id_b[] = $id; }
	$match[$category] = array_merge($match[$category],$d);

}



// find every key in array with specific value
// array array_same ( array input, string search_value )

function array_same ($array, $fixedvalue) {
	$newarray = array();
	foreach($array AS $key => $value) {
		if ($value == $fixedvalue) {
			$newarray[$key] = $value;
		}
		
	}
	return $newarray;
}


// find every key in array with specific (or larger-than) value

function array_larger ($array, $fixedvalue) {
	foreach($array AS $key => $value) {
		if ($value >= $fixedvalue) {
			$newarray[$key] = $value;
		}
		
	}
	return $newarray;
}


function array_shorten ($array) {
	reset($array);

	list($key,$value) = each($array);
	$newarray[$key] = $value;
	list($key,$value) = each($array);
	$newarray[$key] = $value;
	list($key,$value) = each($array);
	$newarray[$key] = $value;

	$match = $value;

	list($key,$value) = each($array);
	while ($value == $match) {
		$newarray[$key] = $value;
		list($key,$value) = each($array);
	}

	return $newarray;	
}


// Denne funktion returnerer et array med fire arrays i:
// array ((array) $match['a'], (array) $match['b'], (array) $match['c'], (array) $match_all);
// $match['a'] er perfekte matches,
// $match['b'] er gode matches,
// $match['c'] er nogenlunde matches
// $match_all er et unikt indhold af alle tre matches

function getalphaidbybeta ($find, $table, $string, $idfield = "id", $dataid = "") {

	$match['a'] = $match['b'] = $match['c'] = array();
	$listetegn = $listeprocent = array();
	
// af hensyn til aliaser
	if ($dataid) {
		$whereextend = " AND category = '$dataid'";
		$whereextendshort = " WHERE category = '$dataid'";
	}

// Vi prøver først at matche direkte (a-match)
	$query = "SELECT $idfield AS id FROM $table WHERE $string = '".dbesc($find)."' $whereextend";

	$match['a'] = getcol($query);
/*
	$r = mysql_query($query);
	while ($row = mysql_fetch_array($r)) {
		$match['a'][] = $row['id'];
	}
*/


// Okay, så prøver vi at matche en del af teksten - hvis teksten er lang,
// er det et okay match (b-match) - ellers vil det være et nogenlunde (c-match).
	if (strlen($find) >= 3) {
		$r = getcol("SELECT $idfield AS id FROM $table WHERE $string LIKE '%".dbesc($find)."%' $whereextend");
		$match['b'] = $r;
/*
		while ($row = mysql_fetch_array($r)) {
			$match['b'][] = $row['id'];
		}
*/
	} elseif (strlen($find) >= 1 && strlen($find) < 3) {
		$r = getcol("SELECT $idfield AS id FROM $table WHERE $string LIKE '%".dbesc($find)."%' $whereextend");
		$match['c'] = $r;
/*
		while ($row = mysql_fetch_array($r)) {
			$match['c'][] = $row['id'];
		}
*/
	}


// Så prøver vi at lave en lyd-match
	$r = getcol("SELECT $idfield AS id FROM $table WHERE SOUNDEX($string) = SOUNDEX('".dbesc($find)."') $whereextend");
	foreach ($r AS $id) {
		$match['b'][] = $id;
	}
/*
	while ($row = mysql_fetch_array($r)) {
		$match['b'][] = $row['id'];
	}
*/

// Godt, der skal skrappere midler til. Vi checker lige nærliggende tekst for alle entries
	if (!$whereextend) {
		$rall = getall("SELECT $idfield AS id, $string FROM $table ORDER BY id");
	} else {
		$rall = getall("SELECT $idfield AS id, $string FROM $table $whereextendshort ORDER BY id");
	}
	foreach($rall AS $r) {
//	while (list($rid,$rstring) = mysql_fetch_row($r)) {
		$rid = $r['id'];
		$rstring = $r['string'];
		$row['id'] = $rid;
		$row['string'] = $rstring;
#		$i = similar_text(strtolower($row[$string]),strtolower($find),$proc);
		$i = similar_text(strtolower($row['string']),strtolower($find),$proc);
		$listetegn[$row['id']] = $i;
		$listeprocent[$row['id']] = $proc;
	}
	arsort($listetegn);
	arsort($listeprocent);
	reset($listetegn);
	reset($listeprocent);

	list($keyproc,$valueproc)=each($listeprocent);
	list($keytegn,$valuetegn)=each($listetegn);

// Lad os se om der kun er én entry over 80% match
	$toptegn = array_larger($listeprocent,80);
	if (count($toptegn) == 1) {
		$match['b'][] = key($toptegn);
	}

// Okay, så checker vi lige dem, der har flest matchende tegn
// og checker om nogen af dem har over 70% match
	$toptegn = array_same($listetegn,$valuetegn);
	foreach ($toptegn AS $key => $value) {
		if ($listeprocent[$key] > 70) $positive[] = $key;
	}
	if (count($positive) == 1) {
		$match['b'][] = array_shift($positive);
	} elseif (count($positive) > 1) {
		foreach($positive AS $pkey) {
			$match['c'][] = $pkey;
		}
	}

// Hvis vi ikke fandt nogen her, checker vi en større mængde af matchende tegn
// og igen checker om nogen af dem har over 70% match

	foreach ($listeprocent AS $key => $value) {
		if ($listeprocent[$key] > 65) {
			$match['c'][] = $key;
		}
	}

// Okay, så ender vi med at returnere, hvad vi har fundet indtil videre
	return array(
		array_unique($match['a']),
		array_unique($match['b']),
		array_unique($match['c']),
		array_unique(array_merge($match['a'],$match['b'],$match['c']))
	);
}


function getsceidbytitle ($find) {
	return getalphaidbybeta ($find, "sce", "title");
}

// Denne tager nu højde for at fornavn og efternavn er i to ord
function getautidbyname ($find) {
	return getalphaidbybeta ($find, "aut", "CONCAT(firstname,' ',surname)");
}

function getsysidbyname ($find) {
	return getalphaidbybeta ($find, "sys", "name");
}

function getconidbyname ($find) {
	return getalphaidbybeta ($find, "convent", "name");
}

function getconsetidbyname ($find) {
	return getalphaidbybeta ($find, "conset", "name");
}

// Og aliaser...
function getidbyalias ($find, $category) {
	return getalphaidbybeta ($find, "alias", "label","data_id",$category);
}

?>
