<?php
define("URLAUT","data?person=");
define("URLSCE","data?scenarie=");
define("URLSYS","data?system=");
define("URLCON","data?con=");
define("URLMAGAZINE","magazines?id=");

function log_search($find, $found="") {
	$referer = dbesc($_SERVER['HTTP_REFERER'] ?? '');
	doquery("INSERT INTO searches (find, found, referer, searchtime) VALUES ('$find','$found','" . dbesc($referer) ."',NOW())");
}

function category_search ($find, $searchfield, $category) {
	global $match, $id_data, $link_a, $link_b, $id_a, $id_b;
	$categoryfind = ($category == 'conventwithyear' ? 'convent' : $category);
	$q = getall("SELECT id, $searchfield FROM $categoryfind");
	foreach($q AS $row) $id_data[$category][$row[0]] = $row[1];
	
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

		case 'conventwithyear':
			$linkurl = URLCON;
			$getfunction = "getconidbynameandyear";
			break;
			
		case 'magazine':
			$linkurl = URLMAGAZINE;
			$getfunction = "getmagazineidbyname";
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

	return TRUE; // Uses global variables for search - yuck
}

// find every key in array with specific value
// array array_same ( array input, string search_value )
function array_same ($array, $fixedvalue) {
	$newarray = [];
	foreach($array AS $key => $value) {
		if ($value == $fixedvalue) {
			$newarray[$key] = $value;
		}
		
	}
	return $newarray;
}


// find every key in array with specific (or larger-than) value
function array_larger ($array, $fixedvalue) {
	$newarray = [];
	foreach($array AS $key => $value) {
		if ($value >= $fixedvalue) {
			$newarray[$key] = $value;
		}
		
	}
	return $newarray;
}

// This function returns an array containing four arrays:
// array ((array) $match['a'], (array) $match['b'], (array) $match['c'], (array) $match_all);
// $match['a'] are perfect matches,
// $match['b'] are good matches,
// $match['c'] are mediocre matches
// $match_all is a unique list of all three matches
function getalphaidbybeta ($find, $table, $string, $idfield = "id", $dataid = "") {
	$match = [];
	$match['a'] = $match['b'] = $match['c'] = [];
	$listetegn = $listeprocent = [];
	$whereextend = $whereextendshort = "";
	
// Due to aliases
	if ($dataid) {
		$whereextend = " AND category = '$dataid'";
		$whereextendshort = " WHERE category = '$dataid'";
	}

// Let's try a direct match ("a" match)
	$query = "SELECT $idfield AS id FROM $table WHERE $string = '".dbesc($find)."' $whereextend";
	$match['a'] = getcol($query);

// Let's try to match a part of the text
// if the text is long this is an okay match ("b" match) - otherwise it's mediocre ("c" match)
	if (strlen($find) >= 3) {
		$r = getcol("SELECT $idfield AS id FROM $table WHERE $string LIKE '%".likeesc($find)."%' $whereextend");
		$match['b'] = $r;
	} elseif (strlen($find) >= 1 && strlen($find) < 3) {
		$r = getcol("SELECT $idfield AS id FROM $table WHERE $string LIKE '%".likeesc($find)."%' $whereextend");
		$match['c'] = $r;
	}


// Let's go for a SOUNDEX match
	$r = getcol("SELECT $idfield AS id FROM $table WHERE SOUNDEX($string) = SOUNDEX('".dbesc($find)."') $whereextend");
	foreach ($r AS $id) {
		$match['b'][] = $id;
	}

// Even more tests. Let's check all entries using similar_text()
	if (!$whereextend) {
		$rall = getall("SELECT $idfield AS id, $string AS string FROM $table ORDER BY id");
	} else {
		$rall = getall("SELECT $idfield AS id, $string AS string FROM $table $whereextendshort ORDER BY id");
	}
	foreach($rall AS $r) {
		$rid = $r['id'];
		$rstring = $r['string'];
		$row['id'] = $rid;
		$row['string'] = $rstring;
		$i = similar_text(mb_strtolower($row['string']), mb_strtolower($find), $proc);
		$listetegn[$row['id']] = $i;
		$listeprocent[$row['id']] = $proc;
	}
	arsort($listetegn);
	arsort($listeprocent);
	reset($listetegn);
	reset($listeprocent);

	// list($keyproc,$valueproc)=each($listeprocent);
	$valuetegn = reset($listetegn);

	// Is there a single match with a match percentage above 80?
	$toptegn = array_larger($listeprocent,80);
	if (is_array($toptegn) && count($toptegn) == 1) {
		$match['b'][] = key($toptegn);
	}

	// Let's check those with most matching characters
	// and see if any has above 70 % match.
	$toptegn = array_same($listetegn,$valuetegn);
	$positive = [];
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

	// If none found let's check a larger amount of matching characters
	// and again check if any has above 70 % match
	foreach ($listeprocent AS $key => $value) {
		if ($listeprocent[$key] > 65) {
			$match['c'][] = $key;
		}
	}

	// Let's return what we have found so far
	return [
		array_unique($match['a']),
		array_unique($match['b']),
		array_unique($match['c']),
		array_unique(array_merge($match['a'],$match['b'],$match['c']))
	];
}

function getsceidbytitle($find) {
	return getalphaidbybeta ($find, "sce", "title");
}

function getautidbyname($find) {
	return getalphaidbybeta ($find, "aut", "CONCAT(firstname,' ',surname)");
}

function getsysidbyname($find) {
	return getalphaidbybeta ($find, "sys", "name");
}

function getconidbyname($find) {
	return getalphaidbybeta ($find, "convent", "name");
}

function getconidbynameandyear($find) {
	$escapequery = dbesc($find);
	$likeescapequery = likeesc($find);
	
	$match = [];
	$match['a'] = $match['b'] = $match['c'] = [];
	$query = "
		SELECT convent.id FROM convent
		INNER JOIN conset ON convent.conset_id = conset.id
		WHERE CONCAT(convent.name,' (',year,')') LIKE '$likeescapequery%'
		OR CONCAT(convent.name,' ',year) LIKE '$likeescapequery%'
		OR CONCAT(conset.name, ' ', convent.year) LIKE '$likeescapequery%'
		OR (
			'$escapequery' REGEXP ' [0-9][0-9]$' AND
			CONCAT(conset.name, ' ', RIGHT(convent.year,2) ) = CONCAT(LEFT('$escapequery', (LENGTH('$escapequery') -3)), ' ', RIGHT('$escapequery', 2))
		)
		OR CONCAT(conset.name,' (',year,')') LIKE '$likeescapequery%'
	";
	$match['a'] = getcol($query);
	return [
		array_unique($match['a']),
		array_unique($match['b']),
		array_unique($match['c']),
		array_unique(array_merge($match['a'],$match['b'],$match['c']))
	];
}

function getconsetidbyname($find) {
	return getalphaidbybeta ($find, "conset", "name");
}

function getmagazineidbyname($find) {
	return getalphaidbybeta ($find, "magazine", "name");
}

// And aliases...
function getidbyalias ($find, $category) {
	return getalphaidbybeta ($find, "alias", "label","data_id",$category);
}
?>
